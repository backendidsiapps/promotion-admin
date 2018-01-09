<?php

namespace AdminPanel\Controllers;

use App\Country;
use App\Feedback;
use App\Http\Controllers\Controller;
use App\Order;
use App\Pack;
use App\Price;
use App\Promocode;
use App\Service;
use Carbon\Carbon;
use DB;
use function back;
use function compact;
use function redirect;
use function route;
use function session;
use function view;


/**
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('manager');
        $this->middleware('balance');

        $this->middleware('admin')->only(['prices', 'getPacks',]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $from = Carbon::createFromFormat('Y-m-d H:i:s', request('from', '1992-01-01') . ' 00:00:00');
        $to = Carbon::createFromFormat('Y-m-d H:i:s', request('to', '2027-01-01') . ' 00:00:00');


        $qForOrders = Order::select('*')->whereBetween('created_at', [$from, $to]);
        $keys = ['id', 'url', 'quantity', 'price', 'smmlaba_order_id'];
        foreach ($keys as $key) {
            if (request()->has($key)) {
                $qForOrders = $qForOrders->orderBy($key, request($key));
            }
        }
        if (session()->has('is_paid')) {
            $qForOrders = $qForOrders->where('is_paid', 1);
        }
        $qForPrices = clone $qForOrders;
        $sum = $qForPrices->where('is_paid', 1)->sum('price');
        $orders = $qForOrders->paginate(50);

        return view('admin.orders', compact('orders', 'sum'));
    }

    public function search()
    {
        $search = request('search', '');

        if (!$search) {
            return redirect(route('admin orders'));
        }
        $sum = Order::where('is_paid', 1)
            ->where(function ($q) use ($search) {
                $q->where('id', $search)->orWhere('smmlaba_order_id', $search)->orWhere('url', 'LIKE', '%' . $search . '%');
            })->sum('price');

        $orders = Order::where(function ($q) use ($search) {
            $q->where('id', $search)->orWhere('smmlaba_order_id', $search)->orWhere('url', 'LIKE', '%' . $search . '%');
        })->paginate(50);

        return view('admin.orders', compact('orders', 'sum'));

    }

    public function isPaid()
    {
        if (request('is_paid')) {
            session(['is_paid' => 1]);
        } else {
            session()->forget('is_paid');
        }

        return back();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function prices($serviceID)
    {
        $service = Service::getPricesForAllCountries($serviceID);

        $countries = Country::all();

        return view('admin.prices', compact('countries', 'service'));
    }

    public function generatePromocode()
    {
        $discount = $this->normalizeDiscount((int)request('discount', 10));

        $promocode = Promocode::generateCode(10, $discount);

        return view('admin.promocode', compact('promocode'));
    }

    private function normalizeDiscount(int $discount)
    {
        if ($discount > 99) {
            $discount = 99;
        } elseif ($discount < 1) {
            $discount = 1;
        }

        return $discount;
    }

    public function promocode()
    {
        return view('admin.promocode');
    }

    /**
     * @param $packID
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deletePack($packID)
    {
        DB::beginTransaction();
        Pack::where('id', $packID)->delete();
        Price::where('pack_id', $packID)->delete();
        DB::commit();

        return redirect()->back();
    }

    public function deleteFeedback($feedbackID)
    {
        Feedback::where('id', $feedbackID)->delete();

        return redirect()->back();
    }

    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPacks()
    {
        $services = Service::with('packs')->get();

        return view('admin.packs', compact('services'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function updatePrices()
    {
        DB::beginTransaction();
        foreach (request('price', []) as $id => $price) {
            Price::where('id', $id)->update(
                [
                    'price'                  => (float)$price['price'],
                    'status'                 => $price['status'],
                    'price_without_discount' => (float)$price['price_without_discount'],
                ]
            );
        }
        DB::commit();

        return redirect()->back();
    }

    public function updateCommentOrder()
    {
        $order = Order::find(request('orderID'));
        $order->comment = request('comment', ' ');
        $order->save();

        return back();
    }

    public function updateCommentFeedback()
    {
        $order = Feedback::find(request('feedbackID'));
        $order->comment = request('comment', ' ');
        $order->save();

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function createPack()
    {
        $data = ['quantity' => request('quantity'), 'service_id' => request('service_id')];
        if (Pack::where($data)->exists()) {
            return redirect()->back();
        }
        DB::beginTransaction();
        $pack = Pack::create($data);
        $countries = Country::all();
        foreach ($countries as $country) {
            Price::create(
                [
                    'country_id'             => $country->id,
                    'pack_id'                => $pack->id,
                    'price'                  => 999.99,
                    'price_without_discount' => 999.99,
                ]
            );

        }
        DB::commit();

        return redirect()->back();
    }

    public function feedback()
    {
        $feedbacks = Feedback::orderByDesc('id')->get();

        return view('admin.feedback', compact('feedbacks'));
    }

    public function stats()
    {
        $from = Carbon::createFromFormat('Y-m-d H:i:s', request('from', '1992-01-01') . ' 00:00:00');
        $to = Carbon::createFromFormat('Y-m-d H:i:s', request('to', '2027-01-01') . ' 00:00:00');

        $services = Service::get();

        $totalCount = Order::where('is_paid', 1)->whereBetween('created_at', [$from, $to])->count();
        $totalCountNotPaid = Order::where('is_paid', 0)->whereBetween('created_at', [$from, $to])->count();
        $totalPrice = Order::where('is_paid', 1)->whereBetween('created_at', [$from, $to])->sum('price');
        $totalQuantity = Order::where('is_paid', 1)->whereBetween('created_at', [$from, $to])->sum('quantity');

        $data = (object)compact('totalPrice', 'totalCount', 'totalQuantity');
        if (!$totalQuantity || !$totalCount || !$totalPrice || !$totalCountNotPaid) {
            return back()->withErrors(['error' => 'ничего не найдено']);
        }
        foreach ($services as $service) {
            $service->countNotPaid = Order::where('is_paid', 0)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->count() / $totalCountNotPaid * 100;
            $service->count = Order::where('is_paid', 1)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->count() / $totalCount * 100;
            $service->sum = Order::where('is_paid', 1)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->sum('price') / $totalPrice * 100;
            $service->quantity = Order::where('is_paid', 1)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->sum('quantity') / $totalQuantity * 100;
        }

        return view('admin.stats', compact('services', 'data'));
    }
}

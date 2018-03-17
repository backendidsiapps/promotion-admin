<?php

namespace AdminPanel\Controllers;

use App\Country;
use App\Feedback;
use App\Http\Controllers\Controller;
use App\Order;
use App\Pack;
use App\Price;
use App\Service;
use Backendidsiapps\Promocode\Promocode;
use Carbon\Carbon;
use DB;
use function back;
use function compact;
use function redirect;
use function request;
use function route;
use function session;
use function view;


/**
 * Class AdminController
 *
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
        $flag = true;
        foreach ($keys as $key) {
            if (request()->has($key)) {
                $qForOrders = $qForOrders->orderBy($key, request($key));
                $flag = false;
            }
        }
        if ($flag) {
            $qForOrders->orderByDesc('id');
        }

        $paid = session('is_paid', 'all');
        if ($paid == 'paid') {
            $qForOrders = $qForOrders->where('is_paid', 1);
        } elseif ($paid == 'not_paid') {
            $qForOrders = $qForOrders->where('is_paid', 0);
        }
        $qForPrices = clone $qForOrders;
        $sum = $qForPrices->where('is_paid', 1)->sum('price');
        $orders = $qForOrders->paginate(50);

        return view('admin::orders', compact('orders', 'sum'));
    }

    public function search()
    {
        $search = request('search', '');

        if (!$search) {
            return redirect(route('admin orders'));
        }
        $sum = Order::where('is_paid', 1)
            ->where(
                function ($q) use ($search) {
                    $q->where('id', $search)->orWhere('smmlaba_order_id', $search)->orWhere('url', 'LIKE', '%' . $search . '%');
                }
            )->sum('price');

        $orders = Order::where(
            function ($q) use ($search) {
                $q->where('id', $search)->orWhere('smmlaba_order_id', $search)->orWhere('url', 'LIKE', '%' . $search . '%');
            }
        )->paginate(50);

        return view('admin::orders', compact('orders', 'sum'));
    }

    public function isPaid()
    {
        switch (request('is_paid', '')) {
            case 'all':
                session(['is_paid' => 'all']);
                break;
            case 'not_paid':
                session(['is_paid' => 'not_paid']);
                break;
            case 'paid':
                session(['is_paid' => 'paid']);
                break;
            default:
                session(['is_paid' => 'all']);
                break;
        }

        return redirect()->back();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function prices($countryID)
    {
        $services = Service::getAllPricesForCurrentCountry($countryID);

        $country = Country::find($countryID);

        //        dd($services);

        return view('admin::prices', compact('country', 'services'));
    }

    public function generatePromocode()
    {
        $discount = $this->normalizeDiscount((int)request('discount', 10));

        $promocode = Promocode::generateCode(10, $discount);

        return view('admin::promocode', compact('promocode'));
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
        return view('admin::promocode');
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

        return view('admin::packs', compact('services'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function updatePrices()
    {
        DB::beginTransaction();
        $prices = request('price');
        $basePrice = array_shift($prices)['price'];
        $prices = request('price');
        $basePriceWithout = array_shift($prices)['price_without_discount'];

        foreach (request('price', []) as $id => $priceNew) {
//            dd($priceNew);
            $price = Price::find($id);
            $pack = Pack::find($price->pack_id);
            $price->update(
                [
                    'price'                  => (float)$this->getPrice($basePrice, $pack->quantity),
                    'status'                 => $priceNew['status'],
                    'price_without_discount' => (float)$basePrice / 100.0 * $pack->quantity,
//                    'price_without_discount' => (float)$this->getPrice($basePriceWithout, $pack->quantity),
                ]
            );
        }

        DB::commit();

        return redirect(request('backURL'));
    }

    private function getPrice($price, $quantity)
    {
        $factor = 0.0;
        if ($quantity >= 100 && $quantity < 300) {
            $factor = 1.0;
        } elseif ($quantity >= 300 && $quantity < 500) {
            $factor = 0.8;
        } elseif ($quantity >= 500 && $quantity < 1000) {
            $factor = 0.6;
        } elseif ($quantity >= 1000 && $quantity < 3000) {
            $factor = 0.55;
        } elseif ($quantity >= 3000 && $quantity < 5000) {
            $factor = 0.45;
        } elseif ($quantity >= 5000 && $quantity < 10000) {
            $factor = 0.4;
        } elseif ($quantity >= 5000) {
            $factor = 0.35;
        }

        return round($factor * ($quantity / 100.0) * $price, 2);
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
                    'price'                  => 0.0,
                    'price_without_discount' => 0.0,
                ]
            );

        }
        DB::commit();

        return redirect()->back();
    }

    public function feedback()
    {
        $feedbacks = Feedback::orderByDesc('id')->get();

        return view('admin::feedback', compact('feedbacks'));
    }

    public function stats()
    {
        $from = Carbon::createFromFormat('Y-m-d H:i:s', request('from', '1992-01-01') . ' 00:00:00');
        $to = Carbon::createFromFormat('Y-m-d H:i:s', request('to', '2027-01-01') . ' 00:00:00');

        $services = Service::get();

        $totalCount = Order::where('is_paid', 1)->whereBetween('created_at', [$from, $to])->count();
        $totalCountNotPaid = Order::where('is_paid', 0)->whereBetween('created_at', [$from, $to])->count();
        $totalPrice = Order::where('is_paid', 1)->whereBetween('created_at', [$from, $to])->sum('price');
        //        $totalQuantity = Order::where('is_paid', 1)->whereBetween('created_at', [$from, $to])->sum('quantity');

        $data = (object)compact('totalPrice', 'totalCount', 'totalQuantity');
        if (!$totalCount || !$totalPrice || !$totalCountNotPaid
        ) {
            return back()->withErrors(['error' => 'ничего не найдено']);
        }
        foreach ($services as $service) {
            $service->countNotPaid = Order::where('is_paid', 0)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->count() / $totalCountNotPaid * 100;
            $service->count = Order::where('is_paid', 1)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->count() / $totalCount * 100;
            $service->sum = Order::where('is_paid', 1)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->sum('price') / $totalPrice * 100;
            //            $service->quantity = Order::where('is_paid', 1)->where('service_id', $service->id)->whereBetween('created_at', [$from, $to])->sum('quantity') / $totalQuantity * 100;
        }
        $services = $services->sortByDesc('sum');

        return view('admin::stats', compact('services', 'data'));
    }
}

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
class ServiceIDSController extends Controller
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
    }

    public function update() {
        if (isJson(request('json'))){
//        dd(request('json'));
            \App\Service::whereId(request('serviceID'))->update(['smm_service_ids'=>request('json')]);
        }

        return redirect()->back();
    }
    public function edit($serviceID) {

        $serviceIDS = \App\Service::find($serviceID)->smm_service_ids;

        return view('admin::json_form',[
            'json'=> json_encode($serviceIDS, JSON_PRETTY_PRINT),
            'serviceID'=> $serviceID
        ]);
    }



}

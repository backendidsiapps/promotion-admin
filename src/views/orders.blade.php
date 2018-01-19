@extends('admin::app')

@section('content')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Заказы</div>
                    {{--{{ $orders->appends(request()->input())->links() }}--}}

                    <div style="display: flex;flex-direction: row;">
                        <form class="navbar-form" role="search" action="/admin/search" method="get">
                            <div class="input-group add-on">
                                <input title="поиск по ID|URL|smmlabaID" class="form-control" placeholder="Search"
                                       name="search" id="srch-term"
                                       type="text" value="{{request('search','')}}">
                                <div class="input-group-btn">
                                    <button title="искать" class="btn btn-success" type="submit"><i>&#128269;</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form class="navbar-form" style="display: flex;flex-direction: row;">
                            <input style="margin-left: 10px" title="От" type="date" class=" form-control"
                                   name="from"
                                   value="{{$_GET['from']??''}}">

                            <input style="margin-left: 10px" title="До" type="date" class="form-control" name="to"
                                   value="{{$_GET['to']??''}}">
                            <input title="применить фильтр по дате" style="margin-left: 10px" type="submit"
                                   class="btn btn-success"
                                   value="&#10004;">
                        </form>
                        <form class="navbar-form" style="margin-left: 10px">
                            <input title="сбросить" type="submit" class="btn btn-warning" value="&#10006;">
                        </form>
                    </div>
                    <div class="panel-body">
                        <h4>количество заказов: <span style="background-color: #E67E22"
                                                      class="badge big-font">{{$orders->total()}}</span></h4>
                        <h4>сумма зачислений: <span style="background-color: #e74c3c"
                                                    class="badge big-font">{{$sum}}</span></h4>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center" scope="col">
                                    #
                                </th>
                                <th class="text-center" scope="col">
                                    <a href="/admin/orders?id=desc">&#8639;</a>
                                    ID
                                    <a href="/admin/orders?id=asc">&#8642;</a>
                                </th>
                                <th class="text-center" scope="col">
                                    дата
                                </th>
                                <th class="text-center" scope="col">услуга</th>

                                <th class="text-center" scope="col">
                                    {{--<a href="/admin/orders?url=desc">&#8639;</a>--}}
                                    URL
                                    {{--<a href="/admin/orders?url=asc">&#8642;</a>--}}
                                </th>
                                <th class="text-center" scope="col">
                                    <a href="/admin/orders?quantity=desc">&#8639;</a>
                                    кол-во
                                    <a href="/admin/orders?quantity=asc">&#8642;</a>
                                </th>
                                <th class="text-center" scope="col">
                                    <a href="/admin/orders?price=desc">&#8639;</a>
                                    цена
                                    <a href="/admin/orders?price=asc">&#8642;</a>
                                </th>
                                <th class="text-center" scope="col">
                                    оплачен
                                    @if(!session()->has('is_paid'))
                                        <a href="/admin/set-is_paid?is_paid=1">&#10007;</a>
                                    @else
                                        <a href="/admin/set-is_paid?is_paid=0">&#10004;</a>
                                    @endif
                                </th>
                                <th class="text-center" scope="col">
                                    <a href="/admin/orders?smmlaba_order_id=desc">&#8639;</a>
                                    smmlaba ID
                                    <a href="/admin/orders?smmlaba_order_id=asc">&#8642;</a>
                                </th>
                                <th class="text-center" scope="col">комментарий</th>
                                <th class="text-center" scope="col">сохранить</th>

                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($orders))
                                {{--{{$iter}}--}}
                                @foreach($orders as $iter => $order)
                                    <tr>
                                        <th class="" scope="row"><h4>{{$iter+1}}</h4></th>
                                        <th class="" scope="row"><h4>{{$order->id}}</h4></th>
                                        <th class="" scope="row">
                                            <h4>{{$order->created_at->timezone('Asia/Krasnoyarsk')->format('d.m.Y H:i')}}</h4>
                                        </th>
                                        <td class="">{{trans('site.'.$order->name)}}</td>
                                        <td>{{$order->url}}</td>
                                        <td class="text-center"><h4
                                                    style="margin-left: 20px;margin-right: 20px;">{{$order->quantity}}</h4>
                                        </td>
                                        <td class="text-center"><h4 style="margin-left: 20px;margin-right: 20px;">
                                                ${{$order->price}}</h4></td>
                                        <td class="text-center {!! $order->is_paid ? 'success' : 'danger'!!}">
                                            <h4 style="margin-left: 40px;margin-right: 40px;">{!! $order->is_paid ? '●' : '❌'!!}</h4>
                                        </td>
                                        <td class="text-center {!! $order->smmlaba_order_id ? 'success' : 'danger'!!}">
                                            <h4 style="margin-left: 40px;margin-right: 40px;">{{$order->smmlaba_order_id ?? '-'}}</h4>
                                        </td>
                                        <td class="text-center">
                                            <form id="form-{{$order->id}}" action="{{route('comment')}}" method="post">
                                                <input class="form-control" name="comment" value="{{$order->comment}}">
                                                <input class="" type="hidden" name="orderID"
                                                       value="{{$order->id}}">
                                                {{csrf_field()}}

                                            </form>
                                        </td>
                                        <td>
                                            <input class="btn btn-success" type="submit" value="✔"
                                                   onclick="document.getElementById('form-{{$order->id}}').submit();">
                                        </td>

                                    </tr>
                                @endforeach

                            @endif
                            </tbody>
                        </table>
                        @if(count($orders) > 0)
                            {{$orders->links()}}
                        @else
                            <div class="alert alert-warning">
                                Ничего не найдено
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

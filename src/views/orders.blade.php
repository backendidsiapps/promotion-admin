@extends('admin::app')

@section('content')
    <script src="/js/manifest.js" type="text/javascript"></script>
    <script src="/js/vendor.js" type="text/javascript"></script>
    <link href="/css/main.css" rel="stylesheet" type="text/css">
    <script>
        window.resetTexts = {
            'В работе': 'В работе',
            'Измените настройки приватности профиля': 'настройки приватности',
            'Ошибка': 'Ошибка',
        }
    </script>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default"
                     style="  width: 100%; overflow-x: auto; white-space: nowrap; max-height: 90vh">
                    <div class="panel-heading">Заказы</div>
                    {{--{{ $orders->appends(request()->input())->links() }}--}}
                    <div style="display: flex;flex-direction: row;">
                        <form class="navbar-form" role="search" action="/admin/search" method="get">
                            <div class="input-group add-on">
                                <input title="поиск по ID|URL|apiID" class="form-control" placeholder="Search"
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
                                    страна
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
                                    email
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
                                <th style="font-size: small" scope="col">
                                    <a href="/admin/set-is_paid?is_paid=all">все</a>@if(session('is_paid','all')=='all')
                                        ✔ @endif<br>
                                    <a href="/admin/set-is_paid?is_paid=paid">оплаченные</a>@if(session('is_paid','')=='paid')
                                        ✔ @endif<br>
                                    <a href="/admin/set-is_paid?is_paid=not_paid">не
                                        оплаченные</a>@if(session('is_paid','')=='not_paid')
                                        ✔ @endif
                                </th>
                                <th class="text-center" scope="col">
                                    <a href="/admin/orders?smmlaba_order_id=desc">&#8639;</a>
                                    apiID
                                    <a href="/admin/orders?smmlaba_order_id=asc">&#8642;</a>
                                </th>
                                <th class="text-center" scope="col">комментарий</th>
                                <th class="text-center" scope="col"></th>

                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($orders))
                                {{--{{$iter}}--}}
                                @foreach($orders as $iter => $order)
                                    <tr>
                                        @if($orders->currentpage() == 1)
                                            <th class="" scope="row">
                                                <h4>{{ $orders->total() + 1 - ($orders->currentpage()) * ($loop->iteration)}}</h4>
                                            </th>
                                        @else
                                            <th class="" scope="row">
                                                <h4>{{ $orders->total() - $loop->iteration +1 - ($orders->perpage()*($orders->currentpage()-1))}}</h4>
                                            </th>
                                        @endif
                                        <th class="" scope="row"><h5>{{$order->id}}</h5></th>
                                        <th class="" scope="row"><h5>{{$order->country['locale_word']}}</h5></th>
                                        <th class="" scope="row">
                                            <h5>{{$order->created_at->subHours(5)->format('d.m.Y H:i')}}</h5>
                                        </th>
                                        <td class="">{{trans('site.'.$order->name)}}</td>
                                        <td style="font-size: 12px;max-width: 280px; overflow: scroll">{{$order->url}}</td>
                                        <td style=" font-size: 12px;max-width: 250px; overflow: scroll">{{ !empty($order->user)? $order->user->email : '-'}}</td>
                                        <td class="text-center"><h5
                                                    style="margin-left: 20px;margin-right: 20px;">{{$order->quantity}}</h5>
                                        </td>
                                        <td class="text-center"><h5 style="margin-left: 20px;margin-right: 20px;">
                                                ${{$order->price}}</h5></td>
                                        <td class="text-center {!! $order->is_paid ? 'success' : 'danger'!!}">
                                            <h6 style="min-width: 50px">{!! $order->is_paid ? '●' : '❌'!!}</h6>
                                        </td>

                                        @if($order->smmlaba_order_id)
                                            <td class="text-center success">
                                                <h6 style="min-width: 50px">{{$order->smmlaba_order_id}}</h6>
                                            </td>
                                        @elseif($order->is_paid)
                                            <td>
                                                <div style="text-align: center; font-size: 20px"><a
                                                            class="restart-order link"
                                                            data-id="{{$order->id}}">↺</a>
                                                </div>
                                            </td>
                                        @else
                                            <td class="text-center success">
                                                <h6 style="min-width: 50px">X</h6>
                                            </td>
                                        @endif

                                        <td title="{{$order->comment}}" class="text-center" style="min-width: 150px">
                                            <form id="form-{{$order->id}}" action="{{route('comment')}}" method="post">
                                                <input class="form-control" name="comment" value="{{$order->comment}}">
                                                <input class="" type="hidden" name="orderID"
                                                       value="{{$order->id}}">
                                                {{csrf_field()}}

                                            </form>
                                        </td>
                                        <td>
                                            <input title="сохранить" class="btn btn-success" type="submit" value="✔"
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
    <script src="/js/restart-order.js"></script>

@endsection

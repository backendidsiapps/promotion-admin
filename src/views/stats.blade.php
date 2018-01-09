@extends('admin::app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">Статистика</div>
                    @if (!$errors->has('error'))
                        <div style="margin: 10px">
                            <p class="">Кол-во заказов: <span style="background-color: #e74c3c"
                                                              class="badge big-font">{{$data->totalCount}}</span>
                            </p>
                            <p class="">Кол-во единиц: <span style="background-color: #F1C40F"
                                                             class="badge big-font">{{$data->totalQuantity}}</span></p>
                            <p class="">Сумма: <span style="background-color: #E74C3C"
                                                     class="badge big-font">{{$data->totalPrice}}</span> $</p>
                        </div>
                    @endif
                    <div class="panel-body">
                        <div style="display: flex;flex-direction: row;">
                            <form style="display: flex;flex-direction: row;">
                                <input style="margin-left: 10px" title="От" type="date" class=" form-control"
                                       name="from"
                                       value="{{$_GET['from']??''}}">

                                <input style="margin-left: 10px" title="До" type="date" class="form-control" name="to"
                                       value="{{$_GET['to']??''}}">
                                <input style="margin-left: 10px" type="submit" class="btn btn-success"
                                       value="&#10004;">
                            </form>
                            <form style="margin-left: 10px">
                                <input type="submit" class="btn btn-warning" value="&#10006;">
                            </form>
                        </div>
                        @if ($errors->has('error'))
                            <br>
                            <div class="alert alert-warning">
                                {{ $errors->first('error') }}
                            </div>

                        @elseif(!empty($services) && count($services) >1)
                            <script type="text/javascript">
                                window.dataArr = [];
                                window.dataArr.push(['', 'заказы', 'объемы', 'доход', 'неоплаченные заказы']);
                                @foreach($services as $service)
                                window.dataArr.push(['{{mb_strtoupper($service->name)}}', {{$service->count}},  {{$service->quantity}}, {{$service->sum}},{{$service->countNotPaid}}]);
                                @endforeach
                            </script>
                            <div id="chart_div"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://raw.githubusercontent.com/backendidsiapps/promotion-admin/master/src/assets/adminCharts.js"></script>
@endsection

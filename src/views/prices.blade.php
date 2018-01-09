@extends('admin::app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="text-center">{{$service->name}}</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="/admin/update-prices">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th scope="col">Кол-во</th>
                                    @foreach($countries as $country)
                                        <th scope="col">{{$country->iso}}</th>

                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($service->packs as $pack)
                                    <tr>
                                        <th scope="row">{{$pack->quantity}}</th>
                                        @foreach($pack->prices as $price)
                                            <td>
                                                <input title="текущая цена"
                                                       style="width: 65px;background: {!! $price->status == 2? '#ef494d':''!!}{!! $price->status == 1? '#ffa341':''!!}"
                                                       step="0.01"
                                                       name="price[{{$price->id}}][price]"
                                                       value="{{$price->price}}" class="input-sm">
                                                <input title="цена без скидки"
                                                       style="width: 65px;background: {!! $price->status == 2? '#ef494d':''!!}{!! $price->status == 1? '#ffa341':''!!}"
                                                       step="0.01"
                                                       name="price[{{$price->id}}][price_without_discount]"
                                                       value="{{$price->price_without_discount}}" class="input-sm">
                                                <select title="статус карточки" class="" style="width: 65px"
                                                        name="price[{{$price->id}}][status]">
                                                    <option style="background: #ffffff"
                                                            value="0" {!! $price->status == 0? 'selected':'' !!}>
                                                        обычная
                                                    </option>
                                                    <option style="background: #ffa341"
                                                            value="1"{!! $price->status == 1? 'selected':'' !!}>акция
                                                    </option>
                                                    <option style="background: #ef494d"
                                                            value="2"{!! $price->status == 2? 'selected':'' !!}>хит
                                                    </option>
                                                </select>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                            {{csrf_field()}}

                            <input type="submit" class="btn btn-success pull-right" value="сохранить">
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
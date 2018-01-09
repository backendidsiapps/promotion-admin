@extends('admin::app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div class="panel panel-default">
                    <div class="panel-heading">Пакеты</div>
                    @foreach($services as $service)
                        <div class="panel-body">

                            <h3>{{$service->name}}</h3>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">кол-во</th>
                                    <th scope="col">удалить</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($service->packs as $pack)
                                    <tr>
                                        <td><h3>{{$pack->quantity}}</h3></td>
                                        <td>
                                            <button class="btn btn-warning" type="button" data-toggle="collapse"
                                                    data-target="#collapseExample{{$pack->id}}" aria-expanded="false"
                                                    aria-controls="collapseExample{{$pack->id}}">
                                                удалить
                                            </button>
                                            <div class="collapse" id="collapseExample{{$pack->id}}">
                                                <form method="post" action="/admin/delete-pack/{{$pack->id}}">
                                                    {{csrf_field()}}
                                                    <input type="submit" class="btn btn-primary" value="удалить">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>
                                        <form method="post" action="{{route('create new pack')}}"
                                              id="submit_{{$service->id}}">

                                            <input style="width: 80px; font-size: 25px" step="100" name="quantity"
                                                   value="1000"
                                                   class="input-sm">
                                            <input type="hidden" value="{{$service->id}}" name="service_id">
                                            {{csrf_field()}}

                                        </form>

                                    </td>
                                    <td>
                                        <input onclick="document.getElementById('submit_{{$service->id}}').submit();"
                                               type="submit" class="btn btn btn-success" value="добавить">

                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

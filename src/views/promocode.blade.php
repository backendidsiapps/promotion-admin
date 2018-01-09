@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">

                <div class="panel panel-default">
                    <div class="panel-heading">Создать промокод</div>
                    <div class="panel-body">
                        <form action="{{route('generate promocode')}}" method="post">
                            <div class="form-group">
                                {{csrf_field()}}
                                <label for="usr">Скидка в %:</label>
                                <input value="10" type="text" class="form-control" id="usr" name="discount">
                            </div>
                            <input class="btn btn-success" type="submit" value="сгенерировать">
                        </form>

                        @if(!empty($promocode))
                            <br>
                            <div class="jumbotron">
                                <h4>скидка {{$promocode->discount}} %</h4>
                                <h2>{{$promocode->code}}</h2>
                                <h4><a href="{{'http://' . $_SERVER['HTTP_HOST'].'/thanks/'. $promocode->hash}}">страница
                                        спасибо </a></h4>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

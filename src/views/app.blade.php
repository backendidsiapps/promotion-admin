<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MyInsapromotion') }}</title>

    <!-- Styles -->
    <link href="https://rawgit.com/backendidsiapps/promotion-admin/master/src/assets/admin.css"
          rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://rawgit.com/backendidsiapps/promotion-admin/master/src/assets/admin.js"></script>

</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{route('admin orders')}}">adminka</a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>
                @if(Auth::check())
                    <ul class="nav navbar-nav navbar-right">
                        <li><a>Баланс: <b>{{$balance}} &#8381;</b></a></li>
                        <li><a href="{{route('admin orders')}}">Заказы</a></li>
                        @if(Auth::user()->isAdmin())
                            <li><a href="{{route('stats')}}">Статистика</a></li>
                            <li><a href="/admin/promocode">Промокод</a></li>
                            <li><a href="/admin/packs">Пакеты</a></li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false"
                                   aria-haspopup="true">
                                    smm IDS <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    @foreach(\App\Service::get() as $service)
                                        <li>
                                            <a href="{{ route('edit_smmservice',['serviceID'=> $service->id]).
                                            '#'.$service->description}}"
                                               onclick=""> {{mb_strtoupper($service->name)}} </a>
                                        </li>
                                    @endforeach

                                </ul>
                            </li>


                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false"
                                   aria-haspopup="true">
                                    Цены <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    @foreach($countries as $country)
                                        <li>
                                            <a href="/admin/prices/{{$country->id}}"
                                               onclick=""> {{mb_strtoupper($country->locale_word)}} </a>
                                        </li>
                                    @endforeach

                                </ul>
                            </li>
                        @endif
                        <li><a href="{{route('feedback')}}">Обратная связь</a></li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false" aria-haspopup="true">
                                {{ Auth::user()->email }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        выход
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    </nav>

    @yield('content')
</div>

<!-- Scripts -->
</body>
</html>

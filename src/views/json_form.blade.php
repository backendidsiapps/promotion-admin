@extends('admin::app')

@section('content')
    <div class="panel panel-default" style="margin: 50px">
        <div class="panel-heading">
            <h5 class="text-center"><b>{{ \App\Service::find($serviceID)->description }}</b>
            </h5>
        </div>
        <div class="panel-body">
            <script src="/src-min/ace.js" type="text/javascript" charset="utf-8"></script>
            <style type="text/css" media="screen">
                /*body {*/
                /*overflow: hidden;*/
                /*}*/
                #editorJSON {
                    margin: 20px;
                    /*position: absolute;*/
                    /*top: 80px;*/
                    /*bottom: 0;*/
                    /*left: 20px;*/
                    /*right: 0;*/
                    min-height: 550px;
                }
            </style>
            <div id="editorJSON">{{$json}}</div>

            <form method="post" action="{{ route('saveSmmServiceIDS') }}" id="form">
                <textarea style="display: none" name="json" id="json"></textarea>
                <input type="hidden" name="serviceID" value="{{ $serviceID }}">
                {{ csrf_field() }}
            </form>

            <button title="default - обязательно поле, оно будет отрабатывать есть для нужной страны нет значения, остальные - по необходимости.
памятка по странам:
@foreach($countries as $country)
            {{ "{$country->iso} : " . getCountryByISO($country->iso)->name  }}
            @endforeach
                    " class="btn btn-success" onclick="send()">save</button>
            <script>
                var editor = ace.edit("editorJSON");
                editor.setTheme("ace/theme/solarized_light");
                editor.session.setMode("ace/mode/javascript");
                editor.setOptions({
                    fontFamily: "fira Code",
                    fontSize: "14pt"
                });

                function send() {
                    document.getElementById('json').innerHTML = editor.getValue()
                    document.getElementById('form').submit()
                }
            </script>
        </div>
    </div>

@endsection
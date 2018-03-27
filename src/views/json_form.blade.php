@extends('admin::app')

@section('content')
    <script src="/src-min/ace.js" type="text/javascript" charset="utf-8"></script>
    <style type="text/css" media="screen">
        /*body {*/
        /*overflow: hidden;*/
        /*}*/
        #editor {
            margin: 50px;
            position: absolute;
            top: 0;
            bottom: 35%;
            left: 0;
            right: 50%;
        }
    </style>

    <div id="editor">{{$json}}</div>

    <form method="post" action="{{ route('saveSmmServiceIDS') }}" id="form">
        <textarea style="display: none" name="json" id="json"></textarea>
        <input type="hidden" name="serviceID" value="{{ $serviceID }}">
        {{ csrf_field() }}
    </form>

    <button onclick="send()">save</button>
    <script>
        var editor = ace.edit("editor");
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
@endsection
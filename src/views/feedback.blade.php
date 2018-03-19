@extends('admin::app')

@section('content')
    <div class="col-md-12">
        <div class="row">
            <div class="">

                <div class="panel panel-default">
                    <div class="panel-heading">Обратная связь</div>
                    <div class="panel-body">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">дата</th>
                                <th scope="col">email</th>
                                <th scope="col">тема</th>
                                <th scope="col">сообщение</th>
                                <th scope="col">комментарий</th>
                                <th scope="col">добавить</th>
                                @if(Auth::user()->isAdmin())
                                    <th scope="col">удалить</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($feedbacks as $feedback)
                                <tr>
                                    <th scope="row">{{$feedback->id}}</th>
                                    <th scope="row">{{$feedback->created_at->timezone('Asia/Krasnoyarsk')->format('d.m.Y H:i')}}</th>
                                    <th scope="row"><a href="mailto:{{$feedback->email}}">{{$feedback->email}}</a></th>
                                    <th scope="row">{{$feedback->theme}}</th>
                                    <th scope="row">{{$feedback->message}}
                                    <td class="text-center">
                                        <form id="form-{{$feedback->id}}" action="{{route('comment to feedback')}}"
                                              method="post">
                                            <input title="{{$feedback->comment}}" class="form-control" name="comment"
                                                   value="{{$feedback->comment}}">
                                            <input class="" type="hidden" name="feedbackID"
                                                   value="{{$feedback->id}}">
                                            {{csrf_field()}}

                                        </form>
                                    </td>
                                    <td>
                                        <input class="btn btn-success" type="submit" value="✔"
                                               onclick="document.getElementById('form-{{$feedback->id}}').submit();">
                                    </td>
                                    @if(Auth::user()->isAdmin())

                                        <th scope="row">
                                            <button class="btn btn-warning" type="button" data-toggle="collapse"
                                                    data-target="#collapseExample{{$feedback->id}}"
                                                    aria-expanded="false"
                                                    aria-controls="collapseExample{{$feedback->id}}">
                                                удалить
                                            </button>
                                            <div class="collapse" id="collapseExample{{$feedback->id}}">
                                                <form method="post" action="/admin/delete-feedback/{{$feedback->id}}">
                                                    {{csrf_field()}}
                                                    <input type="submit" class="btn btn-primary" value="удалить">
                                                </form>
                                            </div>
                                        </th>
                                    @endif

                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Layout and design by WhileD0S <https://vk.com/whiled0s>  --}}
@extends('layouts.shop')

@section('title')
    Редактировать новость
@endsection

@section('content')
    <div id="content-container">
        <div class="z-depth-1 content-header text-center">
            <h1><i class="fa fa-newspaper-o fa-left-big"></i>Редактировать новость</h1>
        </div>
        <div class="product-container">
            <form method="post" action="{{ route('admin.news.edit.save', ['currentServer' => $currentServer->id, 'id' => $news->id]) }}">
                <div class="md-form mt-1">
                    <i class="fa fa-font prefix"></i>
                    <input type="text" name="news_title" id="news-title" class="form-control" value="{{ $news->title }}">
                    <label for="news-title">Заголовок новости</label>
                </div>

                <textarea name="news_content" id="page-content">{{ $news->content }}</textarea>

                <div class="card card-block mt-3">
                    <div class="flex-row">
                        {{ csrf_field() }}
                        <button class="btn btn-info">Сохранить изменения</button>
                        <a href="{{ route('admin.news.remove', ['server' => $currentServer->id, 'id' => $news->id]) }}" class="btn danger-color">Удалить новость</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    @include('components.admin_editor_init')
@endsection

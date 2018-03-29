@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Upload your Powerpoint presentation here.</div>

                <div class="panel-body">
                @if(!Auth::check())
                    Please login to use this system.
                @else
                    <!-- File upload zone -->
                    <link rel="stylesheet" href="/css/dropzone.css">
                    <div id="dropzone-form" action="/upload" method="post" class="dropzone" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="fallback">
                            <input type="submit" value="Save">
                        </div>
                    </div>
                    <div id="dropzone-errors"></div>
                    <div id="result">
                        <form id="result-images" method="post" action="{{ action('UploadController@postForm')}}">
                            
                        </form>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

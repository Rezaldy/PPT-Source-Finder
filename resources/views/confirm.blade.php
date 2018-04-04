@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Confirmation</div>

                <div class="panel-body">
                @if(!Auth::check())
                    <script>
                        window.location = "/login";
                    </script>
                @else
                    <h2>Your sources:</h2>
                    <textarea readonly id="confirm">{{ "Afbeeldingen geraadpleegd van:\n" . implode("\n",$images)}}</textarea>
                    <a href="#" id="copyConfirm">Copy to clipboard</a>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

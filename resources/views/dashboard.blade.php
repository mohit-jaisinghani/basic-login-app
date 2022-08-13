@extends('layout')
  
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
  
                <div class="card-body">
                    @if(Session::has('message'))
                        <div>
                          <p class="alert {{ Session::get('alert-class') }}">{{ Session::get('message') }}</p>
                        </div>
                    @else
                        <div>
                          <p class="alert alert-success">You are Logged In</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
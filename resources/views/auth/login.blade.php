@extends('layouts.allita')

@section('content')
@if(Auth::guest())
<div class="uk-vertical-align uk-text-center uk-height-1-1 uk-margin-top" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
            <div class="uk-vertical-align-middle login-panel"  id="login-panel">

                
                <p>NEIGHBORHOOD INITIATIVE PROGRAM</p>

                <form class="uk-panel uk-panel-box uk-form" role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}

                    <div class="uk-form-row {{ $errors->has('email') ? ' uk-form-danger' : '' }}" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
                        <input class="uk-input uk-width-1-1 uk-form-large" type="text" placeholder="Email" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus >
                         @if ($errors->has('email'))
                            <span class="uk-block-primary">
                                <strong class="uk-dark uk-light">{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
                        <input class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password') ? ' uk-form-danger' : '' }}" placeholder="Password" type="password" name="password" required">
                         @if ($errors->has('password'))
                                    <span class="uk-block-primary">
                                        <strong class="uk-dark uk-light">{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                    </div>
                    <div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
                        <button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Login</button>
                    </div>
                    <div class="uk-form-row uk-text-small" uk-scrollspy="target:.next-items;cls:uk-animation-fade; delay: 1400">
                        <label class="uk-float-left"><input type="checkbox" name="remember" class="uk-checkbox next-items"> Remember Me</label>
                        <a class="uk-float-right uk-link uk-link-muted next-items" href="{{ url('/password/reset') }}">Forgot Password?</a>
                    </div>
                </form>
                <div uk-scrollspy="cls:uk-animation-fade; delay: 2200">
                <a href="{{ url('/register') }}" class="uk-button uk-button-default uk-button-small uk-width-1-1 uk-margin-top">Not Registered?</a>
                <p>Knowingly submitting incorrect documentation, request for reimbursements for expenses not incurred or those expenses where payment was received from another source, constitutes fraud and will be prosecuted to the fullest extent of the law.</p>
            </div>

            </div>
        </div>
@endif
<?php /* original version from laravel based on bootstrap 
<div class="uk-container">
    <div class="uk-grid">
        <div class="uk-width-8 uk-width-6@m uk-align-center">
            <div class="uk-panel ">
                <div class="uk-panel-">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        

                        <div class="form-group{{ $errors->has('email') ? ' uk-form-danger' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' uk-form-danger' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
*/
?>
@endsection

@php


$landbanks = DB::table('programs')->select('owner_id as entity_id','program_name')->where('active',1)->where('id','>',1)->orderBy('program_name','asc')->get()->all();
$counties = DB::table('counties')->select('id as county_id','county_name')->get()->all();
$states = DB::table('states')->select('id as state_id','state_name','state_acronym')->get()->all();

@endphp
@extends('layouts.allita')

@section('content')
<script type="text/javascript">
jQuery('document').ready(function() {
    @if(!old('newlandbank'))
    jQuery("#landbank_form").hide();
    @else
    jQuery("#select_landbank").hide();
    @endIF
    jQuery('#newlandbank').click(function() {
         if(document.getElementById('newlandbank').checked) {
                jQuery("#landbank_form").slideDown();
                jQuery("#select_landbank").slideUp();
                jQuery("#entity_id").removeAttr('required');
                jQuery("#program_name").attr('required',"required");
                jQuery("#county_id").attr('required',"required");
                jQuery("#entity_name").attr('required',"required");
                jQuery("#address1").attr('required',"required");
                jQuery("#city").attr('required',"required");
                jQuery("#state").attr('required',"required");
                jQuery("#zip").attr('required',"required");
                jQuery("#phone").attr('required',"required");
                jQuery("#email_address").attr('required',"required");
         } else {
                jQuery("#landbank_form").slideUp();
                jQuery("#select_landbank").slideDown();
                jQuery("#entity_id").attr('required',"required");
                jQuery("#program_name").removeAttr('required');
                jQuery("#county_id").removeAttr('required');
                jQuery("#entity_name").removeAttr('required');
                jQuery("#address1").removeAttr('required');
                jQuery("#city").removeAttr('required');
                jQuery("#state").removeAttr('required');
                jQuery("#zip").removeAttr('required');
                jQuery("#phone").removeAttr('required');
                jQuery("#email_address").removeAttr('required');
         }
    });
    jQuery("#submit").click(function(){
        //clear out html
         if(document.getElementById('newlandbank').checked) {
        
        } else {
            //jQuery("#landbank_form").html("<div></div>");
            //jQuery("#newlandbankfield").hide();
        }
    });

});
    
</script>
<div class="uk-vertical-align uk-text-center uk-height-1-1">
            <div class="uk-vertical-align-middle" id="login-panel">
                 <h2>JOIN THE MISSION</h2>

                <form class="uk-panel uk-panel-box uk-form" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}

                        <div class="uk-form-row">
                            
                                <input id="name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="name" value="{{ old('name') }}" required autofocus placeholder="Full Name *">

                                @if ($errors->has('name'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="email" type="email" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('email') ? ' uk-form-danger uk-animation-shake' : '' }}" name="email" value="{{ old('email') }}" required placeholder="Your Email *">

                                @if ($errors->has('email'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            
                        </div>


                        <div class="uk-form-row">
                            
                                <input id="email-confirm" type="email-confirm" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('email-confirm') ? ' uk-form-danger uk-animation-shake' : '' }}" name="email-confirm" value="@if ($errors->has('email-confirm'))@else{{trim(old('email-confirm'))}}@endIf" required placeholder="Confirm Your Email *" class="uk-width-1-1 uk-form-large" >

                                @if ($errors->has('email-confirm'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('email-confirm') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                        

                        <div class="uk-form-row">
                            
                                <input id="password" type="password" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password') ? ' uk-form-danger uk-animation-shake' : '' }}" name="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" required placeholder="Password *">

                                @if ($errors->has('password'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="password-confirm" type="password" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password_confirmation') ? ' uk-form-danger uk-animation-shake' : '' }}" name="password_confirmation" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');" required placeholder="Confirm Password *">

                                @if ($errors->has('password_confirmation'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                        
                        <div id='select_landbank' class="uk-form-row">
                            
                                <select id='entity_id' name='entity_id' class="uk-select uk-width-1-1 uk-form-large {{ $errors->has('entity_id') ? ' uk-form-danger uk-animation-shake' : '' }}" @if(old('newlandbank'))  @else required @endIf>
                                    <option value="">Please Select a Registered Landbank</option>
                                    @foreach ($landbanks as $landbank)
                                        <option value='{{ $landbank->entity_id }}' @if(old('entity_id') == $landbank->entity_id) SELECTED @endIf>{{ $landbank->program_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('entity_id'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>Please select an existing landbank. If you are registering a landbank that does not exist, please check the box next to "I am registering a new landbank"</strong>
                                    </span>
                                @endif
                                
                            
                        </div>
                        <div class="uk-form-row uk-margin-top uk-margin-bottom">
                        
                                <div class="uk-width-1-1 uk-text-left" id="newlandbankfield"><input type="checkbox" class="uk-input" name="newlandbank" value="TRUE" id="newlandbank" @if(old('newlandbank')) CHECKED @endIf> I AM A REGISTERING A NEW LANDBANK</div>
                           
                        </div>

                <div id="landbank_form">
                    <div class="uk-form-row">
                    <hr />
                    </div>

                        <div class="uk-form-row">
                            
                                <input id="program_name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('program_name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="program_name" placeholder="Landbank Program Name" value="{{old('program_name')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('entity_name'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('program_name') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                        <div id='select_county' class="uk-form-row">
                            
                                <select id='county_id' name='county_id' class="uk-select uk-width-1-1 uk-form-large {{ $errors->has('county_id') ? ' uk-form-danger uk-animation-shake' : '' }}" @if(old('newlandbank')) required @endIf>
                                    <option value="">Please Select the Program's County</option>
                                    @foreach ($counties as $county)
                                        <option value='{{ $county->county_id }}' @if(old('county_id') == $county->county_id) SELECTED @endIf >{{ $county->county_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('county_id'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('county_id') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                        <div class="uk-form-row">
                            
                                <input id="entity_name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('entity_name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="entity_name"  placeholder="Managing Organization Name" value="{{old('entity_name')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('entity_name'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('entity_name') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="address1" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('address1') ? ' uk-form-danger uk-animation-shake' : '' }}" name="address1"  placeholder="Organization Address"  value="{{old('address1')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('address1'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('address1') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="address2" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('address2') ? ' uk-form-danger uk-animation-shake' : '' }}" name="address2"  placeholder="Organization Suite (optional)"  value="{{old('address2')}}">

                                @if ($errors->has('address2'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('address2') }}</strong>
                                    </span>
                                @endif
                           
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="city" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('city') ? ' uk-form-danger uk-animation-shake' : '' }}" name="city"  placeholder="Organization City" value="{{old('city')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('city'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                        <div id='select_county' class="uk-form-row">
                            
                                <select id='state_id' name='state_id' class="uk-select uk-width-1-1 uk-form-large {{ $errors->has('entity_id') ? ' uk-form-danger uk-animation-shake' : '' }}" @if(old('newlandbank')) required @endIf>
                                    <option>Please Select the Organization's State
                                    @foreach ($states as $state)
                                        <option value='{{ $state->state_id }}' @if(old('state_id') == $state->state_id) SELECTED @elseIf($state->state_id == 36) SELECTED @endIf >{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('county_id'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('state_id') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="zip" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('zip') ? ' uk-form-danger uk-animation-shake' : '' }}" name="zip"  placeholder="Organization Zip" value="{{old('zip')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('zip'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('zip') }}</strong>
                                    </span>
                                @endif
                           
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="phone" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('phone') ? ' uk-form-danger uk-animation-shake' : '' }}" name="phone"  placeholder="Organization Phone Number" value="{{old('phone')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('phone'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="fax" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('fax') ? ' uk-form-danger uk-animation-shake' : '' }}" name="fax" placeholder="Organization FAX Number" value="{{old('fax')}}">

                                @if ($errors->has('fax'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('fax') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row ">
                            
                                <input id="web_address" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('web_address') ? ' uk-form-danger uk-animation-shake' : '' }}" name="web_address" placeholder="Organization Web Address" value="{{old('web_address')}}">

                                @if ($errors->has('web_address'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('web_address') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                           
                                <input id="email_address" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('email_address') ? ' uk-form-danger uk-animation-shake' : '' }}" name="email_address"  placeholder="Organization General Email" value="{{old('email_address')}}" @if(old('newlandbank')) required @endIf>

                                @if ($errors->has('email_address'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('email_address') }}</strong>
                                    </span>
                                @endif
                            
                        </div>

                        <div class="uk-form-row">
                            
                                <input id="logo_link" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('logo_link') ? ' uk-form-danger uk-animation-shake' : '' }}" name="logo_link" placeholder="Link to Organization Logo" value="{{old('logo_link')}}">


                                @if ($errors->has('logo_link'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>{{ $errors->first('logo_link') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                    </div>
                        <div class="uk-form-row uk-margin-top">
                            {!! app('captcha')->display(); !!}
                            @if ($errors->has('g-recaptcha-response'))
                                    <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
                                        <strong>Please prove you're not a robot.</strong>
                                    </span>
                                @endif

                            <br />
                                <button type="submit" id='submit' class="uk-button uk-button-large uk-button-success uk-width-1-1">
                                    REGISTER
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
                                
@endsection

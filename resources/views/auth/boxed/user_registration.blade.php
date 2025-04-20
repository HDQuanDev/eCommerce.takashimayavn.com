@extends('auth.layouts.authentication')

@section('content')
<div class="aiz-main-wrapper d-flex flex-column justify-content-center bg-white">
    <section class="bg-white overflow-hidden py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-10 col-xl-11 col-lg-12">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="row no-gutters">
                            <!-- Left Side Image -->
                            <div class="col-lg-5 d-none d-lg-block">
                                <img src="{{ uploaded_asset(get_setting('customer_register_page_image')) }}" 
                                     alt="{{ translate('Customer Register Page Image') }}" 
                                     class="img-fit h-100 rounded-left">
                            </div>

                            <!-- Right Side -->
                            <div class="col-lg-7 p-4 p-lg-5">
                                <!-- Site Icon & Title -->
                                <div class="text-center mb-4">
                                    <div class="size-60px mb-3 mx-auto">
                                        <img src="{{ uploaded_asset(get_setting('site_icon')) }}" 
                                             alt="{{ translate('Site Icon')}}" 
                                             class="img-fit h-100">
                                    </div>
                                    <h1 class="fs-22 fw-700 text-primary">{{ translate('Create an account')}}</h1>
                                </div>

                                <!-- Register form -->
                                <form id="reg-form" class="form-default" role="form" action="{{ route('register') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Personal Information -->
                                    <div class="card-body p-0 mb-4">
                                        <h5 class="fw-600 mb-3 fs-16 text-primary">{{ translate('Personal Information') }}</h5>
                                        
                                        <!-- Name -->
                                        <div class="form-group">
                                            <label for="name" class="fs-13 fw-500 text-dark">{{ translate('Full Name') }}</label>
                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                                                value="{{ old('name') }}" placeholder="{{  translate('Full Name') }}" name="name">
                                            @if ($errors->has('name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Contact Information -->
                                        @if (addon_is_activated('otp_system'))
                                            @if($phone)
                                                <div class="form-group">
                                                    <label for="phone" class="fs-13 fw-500 text-dark">{{ translate('Phone') }}</label>
                                                    <input type="tel" id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                                        value="{{ $phone }}" name="phone" autocomplete="off" readonly>
                                                    <input type="hidden" name="country_code" value="">
                                                </div>
                                            @elseif($email)
                                                <div class="form-group">
                                                    <label for="email" class="fs-13 fw-500 text-dark">{{ translate('Email') }}</label>
                                                    <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                        value="{{ $email }}" name="email" autocomplete="off" readonly>
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="form-group phone-form-group">
                                                    <label for="phone" class="fs-13 fw-500 text-dark">{{ translate('Phone') }}</label>
                                                    <div class="input-group">
                                                        <input type="tel" id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                                            value="{{ old('phone') }}" name="phone" autocomplete="off">
                                                    </div>
                                                </div>

                                                <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', 'US') }}">

                                                <div class="form-group email-form-group d-none">
                                                    <label for="email" class="fs-13 fw-500 text-dark">{{ translate('Email') }}</label>
                                                    <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                        value="{{ old('email') }}" name="email" autocomplete="off">
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="form-group text-right">
                                                    <button class="btn btn-link p-0 text-primary" type="button" onclick="toggleEmailPhone(this)">
                                                        <i>{{ translate('Use Email Instead') }}</i>
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <div class="form-group">
                                                <label for="email" class="fs-13 fw-500 text-dark">{{ translate('Email') }}</label>
                                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    value="{{ $email ?? old('email') }}" name="email" {{$email ? 'readonly' : ''}}>
                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Password -->
                                        <div class="form-group">
                                            <label for="password" class="fs-13 fw-500 text-dark">{{ translate('Password') }}</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                                    placeholder="{{  translate('Password') }}" name="password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white border-left-0">
                                                        <i class="password-toggle las la-eye cursor-pointer"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">{{ translate('Password must contain at least 6 digits') }}</small>
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="form-group">
                                            <label for="password_confirmation" class="fs-13 fw-500 text-dark">{{ translate('Confirm Password') }}</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" 
                                                    placeholder="{{  translate('Confirm Password') }}" name="password_confirmation">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white border-left-0">
                                                        <i class="password-toggle las la-eye cursor-pointer"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Information -->
                                    <div class="card-body p-0 mb-4">
                                        <h5 class="fw-600 mb-3 fs-16 text-primary">{{ translate('Payment Information') }}</h5>
                                        
                                        <div class="form-group">
                                            <label for="card_number" class="fs-13 fw-500 text-dark">{{ translate('Card Number') }}</label>
                                            <input type="text" class="form-control {{ $errors->has('card_number') ? 'is-invalid' : '' }}"
                                                name="card_number" placeholder="1234 5678 9012 3456" value="{{ old('card_number') }}">
                                            @if ($errors->has('card_number'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('card_number') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="name_on_card" class="fs-13 fw-500 text-dark">{{ translate('Name on Card') }}</label>
                                            <input type="text" class="form-control {{ $errors->has('name_on_card') ? 'is-invalid' : '' }}"
                                                name="name_on_card" placeholder="John Doe" value="{{ old('name_on_card') }}">
                                            @if ($errors->has('name_on_card'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name_on_card') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="expiry_date" class="fs-13 fw-500 text-dark">{{ translate('Expiry Date') }}</label>
                                                    <input type="text" class="form-control {{ $errors->has('expiry_date') ? 'is-invalid' : '' }}"
                                                        name="expiry_date" placeholder="MM/YY" value="{{ old('expiry_date') }}">
                                                    @if ($errors->has('expiry_date'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('expiry_date') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cvv" class="fs-13 fw-500 text-dark">{{ translate('CVV') }}</label>
                                                    <input type="password" class="form-control {{ $errors->has('cvv') ? 'is-invalid' : '' }}"
                                                        name="cvv" placeholder="123" value="{{ old('cvv') }}">
                                                    @if ($errors->has('cvv'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('cvv') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recaptcha -->
                                    @if(get_setting('google_recaptcha') == 1)
                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                        </div>
                                        @if ($errors->has('g-recaptcha-response'))
                                            <div class="alert alert-danger">
                                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Terms and Conditions -->
                                    <div class="mb-3">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" name="checkbox_example_1" required>
                                            <span class="fs-13">{{ translate('By signing up you agree to our ')}} 
                                                <a href="{{ route('terms') }}" class="fw-500 text-primary">{{ translate('terms and conditions.') }}</a>
                                            </span>
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-primary btn-block fw-600 rounded-lg py-2">
                                            {{ translate('Create Account') }}
                                        </button>
                                    </div>
                                </form>

                                <!-- Social Login -->
                                @if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1 || get_setting('apple_login') == 1)
                                    <div class="position-relative my-4">
                                        <hr>
                                        <div class="text-center position-absolute w-100" style="top: -12px;">
                                            <span class="bg-white px-3 fs-13 text-gray">{{ translate('Or Join With')}}</span>
                                        </div>
                                    </div>
                                    <ul class="list-inline social colored text-center mb-4">
                                        @if (get_setting('facebook_login') == 1)
                                            <li class="list-inline-item">
                                                <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                                    <i class="lab la-facebook-f"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if(get_setting('google_login') == 1)
                                            <li class="list-inline-item">
                                                <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                                    <i class="lab la-google"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if (get_setting('twitter_login') == 1)
                                            <li class="list-inline-item">
                                                <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                                    <i class="lab la-twitter"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if (get_setting('apple_login') == 1)
                                            <li class="list-inline-item">
                                                <a href="{{ route('social.login', ['provider' => 'apple']) }}" class="apple">
                                                    <i class="lab la-apple"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                @endif

                                <!-- Log In -->
                                <div class="text-center">
                                    <p class="fs-13 text-gray mb-0">
                                        {{ translate('Already have an account?')}}
                                        <a href="{{ route('user.login') }}" class="ml-1 fs-14 fw-600 text-primary">{{ translate('Log In')}}</a>
                                    </p>
                                </div>

                                <!-- Go Back -->
                                <div class="mt-4 text-center">
                                    <a href="{{ url()->previous() }}" class="fs-13 d-inline-flex align-items-center text-primary">
                                        <i class="las la-arrow-left fs-16 mr-1"></i>
                                        {{ translate('Back to Previous Page')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')
@if(get_setting('google_recaptcha') == 1)
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<script type="text/javascript">
    // Password visibility toggle
    $(document).ready(function() {
        $('.password-toggle').click(function() {
            const input = $(this).closest('.input-group').find('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('la-eye').addClass('la-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('la-eye-slash').addClass('la-eye');
            }
        });
        
        // Toggle between email and phone
        window.toggleEmailPhone = function(el) {
            if ($('.email-form-group').hasClass('d-none')) {
                $('.email-form-group').removeClass('d-none');
                $('.phone-form-group').addClass('d-none');
                $(el).html('<i>{{ translate("Use Phone Instead") }}</i>');
            } else {
                $('.email-form-group').addClass('d-none');
                $('.phone-form-group').removeClass('d-none');
                $(el).html('<i>{{ translate("Use Email Instead") }}</i>');
            }
        }
        
        @if(get_setting('google_recaptcha') == 1)
        // Making the CAPTCHA a required field for form submission
        $("#reg-form").on("submit", function(evt) {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                alert("Please verify you are human!");
                evt.preventDefault();
                return false;
            }
            // Captcha verified
            return true;
        });
        @endif
    });
</script>
@endsection
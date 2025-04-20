<?php

namespace App\Http\Controllers\Auth;

use Cookie;
use Session;
use App\Models\Cart;
use App\Models\User;
use App\Models\PaymentInformation;
use App\Rules\Recaptcha;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\OTPVerificationController;
use App\Utility\EmailUtility;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'card_number' => 'required|string|max:19',
            'name_on_card' => 'required|string|max:255',
            'expiry_date' => 'required|string|max:7',
            'cvv' => 'required|string|max:4',
            'g-recaptcha-response' => [
                Rule::when(get_setting('google_recaptcha') == 1, ['required', new Recaptcha()], ['sometimes'])
            ]
        ];

        // Add email or phone validation based on what is provided
        if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $rules['email'] = 'required|email|unique:users,email';
        } elseif (isset($data['phone'])) {
            $rules['phone'] = 'required|unique:users,phone';
            $rules['country_code'] = 'required';
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        } else {
            if (addon_is_activated('otp_system')) {
                $user = User::create([
                    'name' => $data['name'],
                    'phone' => '+' . $data['country_code'] . $data['phone'],
                    'password' => Hash::make($data['password']),
                    'verification_code' => rand(100000, 999999)
                ]);

                if (get_setting('customer_registration_verify') != '1') {
                    $otpController = new OTPVerificationController;
                    $otpController->send_code($user);
                }
            }
        }

        // Store payment information
        if (isset($user)) {
            PaymentInformation::create([
                'user_id' => $user->id,
                'card_number' => $this->formatCardNumber($data['card_number']),
                'name_on_card' => $data['name_on_card'],
                'expiry_date' => $data['expiry_date'],
                'cvv' => encrypt($data['cvv']), // Encrypt sensitive data
            ]);
        }

        if (session('temp_user_id') != null) {
            if (auth()->user()->user_type == 'customer') {
                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update(
                        [
                            'user_id' => auth()->user()->id,
                            'temp_user_id' => null
                        ]
                    );
            } else {
                Cart::where('temp_user_id', session('temp_user_id'))->delete();
            }
            Session::forget('temp_user_id');
        }

        if (Cookie::has('referral_code')) {
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }

        return $user;
    }

    /**
     * Format card number to remove spaces and non-numeric characters
     *
     * @param string $cardNumber
     * @return string
     */
    protected function formatCardNumber($cardNumber)
    {
        // Loại bỏ khoảng trắng và ký tự không phải số
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        // Mã hóa toàn bộ số thẻ thay vì chỉ lưu 4 số cuối
        return encrypt($cardNumber);
    }
    public function register(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email or Phone already exists.'));
                return back();
            }
        } elseif (User::where('phone', '+' . $request->country_code . $request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        $this->guard()->login($user);

        if ($user->email != null) {
            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1 || get_setting('customer_registration_verify') === '1') {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                offerUserWelcomeCoupon();
                flash(translate('Registration successful.'))->success();
            } else {
                try {
                    EmailUtility::email_verification($user, 'customer');
                    flash(translate('Registration successful. Please verify your email.'))->success();
                } catch (\Throwable $e) {
                    // Log the error but don't expose it
                    \Log::error('Email verification error: ' . $e->getMessage());
                    $user->delete();
                    flash(translate('Registration failed. Please try again later.'))->error();
                }
            }

            // Account Opening Email to customer
            if ($user != null && (get_email_template_data('registration_email_to_customer', 'status') == 1)) {
                try {
                    EmailUtility::customer_registration_email('registration_email_to_customer', $user, null);
                } catch (\Exception $e) {
                    \Log::error('Customer registration email error: ' . $e->getMessage());
                }
            }
        }

        // customer Account Opening Email to Admin
        if ($user != null && (get_email_template_data('customer_reg_email_to_admin', 'status') == 1)) {
            try {
                EmailUtility::customer_registration_email('customer_reg_email_to_admin', $user, null);
            } catch (\Exception $e) {
                \Log::error('Admin notification email error: ' . $e->getMessage());
            }
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        if ($user->email == null) {
            return redirect()->route('verification');
        } elseif (session('link') != null) {
            return redirect(session('link'));
        } else {
            return redirect()->route('home');
        }
    }
}

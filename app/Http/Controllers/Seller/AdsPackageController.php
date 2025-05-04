<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AdsPackage;
use App\Models\Seller;
use App\Models\SellerAdsPackage;
use App\Models\User;
use App\Services\AdsPackageService;
use Illuminate\Http\Request;

class AdsPackageController extends Controller
{
    public $adsPackageService;
    public function __construct(AdsPackageService $adsPackageService)
    {
        $this->middleware('seller');
        $this->adsPackageService = $adsPackageService;
    }

    public function index(Request $request)
    {
        $packages = $this->adsPackageService->getActive();

        $user = User::where('id', auth()->user()->id)->first();
        $packages_histories = $user->seller_ads_packages()->orderBy('created_at', 'desc')->get();

        return view('seller.ads_packages.index', compact('packages', 'packages_histories'));
    }

    public function register($id)
    {
        $user_id = auth()->user()->id;
        $seller = Seller::where('user_id', $user_id)->first();
        $user = User::where('id', $user_id)->first();
        $package = AdsPackage::where('id', $id)->where('status', 'active')->first();
        if (!$seller || !$package || $user->user_type != 'seller') {
            flash(translate('You are not authorized to register for this package'))->error();
            return redirect()->route('seller.ads-packages.index');
        }
        /// check balance
        if ($user->balance < $package->price) {
            flash(translate('You do not have enough balance to register for this package'))->error();
            return redirect()->route('seller.ads-packages.index');
        }

        SellerAdsPackage::create([
            'user_id' => $user_id,
            'ads_package_id' => $package->id,
            'price' => $package->price,
            'reach' => $package->reach,
        ]);

        $user->balance -= $package->price;
        $user->save();

        flash(translate('You have successfully registered for this package'))->success();
        return redirect()->route('seller.ads-packages.index');
    }
}

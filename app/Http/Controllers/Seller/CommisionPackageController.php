<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CommissionPackage;
use App\Models\Seller;
use App\Models\User;
use App\Services\CommisionPackageService;
use Illuminate\Http\Request;

class CommisionPackageController extends Controller
{
    public $commissionPackageService;
    public function __construct(CommisionPackageService $commissionPackageService)
    {
        $this->middleware('seller');
        $this->commissionPackageService = $commissionPackageService;
    }
    public function index(Request $request)
    {
        $packages = $this->commissionPackageService->getActive();
        $currentPackage = CommisionPackageService::getUserPackage(auth()->user()->id);

        $user = User::where('id', auth()->user()->id)->first();
        $packages_histories = $user->commission_package()->withPivot('start_date', 'end_date', 'status', 'price')->orderBy('created_at', 'desc')->get();

        return view('seller.commission-packages.index', compact('packages', 'currentPackage', 'packages_histories'));
    }


public function register($id)
{
    $user_id = auth()->user()->id;
    $seller = Seller::where('user_id', $user_id)->first();
    $user = User::where('id', $user_id)->first();
    $package = CommissionPackage::where('id', $id)->where('status', 'active')->first();
    if(!$seller || !$package || $user->user_type != 'seller') {
        flash(translate('You are not authorized to register for this package'))->error();
        return redirect()->route('seller.commission-packages.index');
    }
    /// check balance
    if($user->balance < $package->price) {
        flash(translate('You do not have enough balance to register for this package'))->error();
        return redirect()->route('seller.commission-packages.index');
    }

    $package->users()->attach($user_id, ['price' => $package->price, 'start_date' => now(), 'end_date' => now()->addDays($package->duration)]);
    $user->balance -= $package->price;
    $user->save();

    flash(translate('You have successfully registered for this package'))->success();
    return redirect()->route('seller.commission-packages.index');
}
}

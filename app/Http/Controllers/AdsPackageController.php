<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdsPackageRequest;
use App\Models\AdsPackage;
use App\Models\SellerAdsPackage;
use App\Services\AdsPackageService;
use Illuminate\Http\Request;

class AdsPackageController extends Controller
{
    public $adsPackageService;
    public function __construct(AdsPackageService $adsPackageService)
    {
        $this->middleware(['auth']);
        $this->adsPackageService = $adsPackageService;
    }

    public function index(Request $request)
    {
        $sort_search = $request->sort_search ?? null;
        $status = $request->status ?? null;
        $packages = AdsPackage::latest();

        if ($sort_search != null) {
            $packages = $packages->where(function ($packages) use ($sort_search) {
                if ($sort_search != null) {
                    $packages->where('name', 'like', '%' . $sort_search . '%');
                }
            });
        }
        if ($status != null) {
            $packages = $packages->where('status', $status);
        }
        $packages = $packages->paginate(15);
        return view('backend.sellers.ads_package.index', compact('packages'));
    }

    public function create()
    {
        return view('backend.sellers.ads_package.create');
    }

    public function store(AdsPackageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $this->adsPackageService->create($data);
        flash(translate('Ads package has been inserted successfully'))->success();
        return redirect()->route('ads-packages.index');
    }

    public function updateStatus(Request $request)
    {
        try {
            $this->adsPackageService->updateStatus($request->id, $request->status);
            return 1;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return 0;
        }
    }

    public function destroy($id)
    {
        $package = AdsPackage::findOrFail($id);
        if ($package->user_id != auth()->user()->id) {
            abort(403);
        }

        if ($package->sellerAdsPackages()->count() > 0) {
            flash(translate('Ads package has been used by seller'))->error();
            return redirect()->route('ads-packages.index');
        }

        $package->delete();
        flash(translate('Ads package has been deleted successfully'))->success();
        return redirect()->route('ads-packages.index');
    }

    public function bulkDelete(Request $request)
    {
        try {
            foreach ($request->id as $id) {
                $package = AdsPackage::find($id);
                if ($package->sellerAdsPackages()->count() > 0) {
                    flash(translate('Ads package has been used by seller'))->error();
                    return redirect()->route('ads-packages.index');
                }
                $package->delete();
            }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            flash(translate('Can not delete ads packages'))->error();
            return 1;
        }

        flash(translate('Ads packages have been deleted successfully'))->success();
        return redirect()->route('ads-packages.index');
    }

    public function edit($id)
    {
        $package = AdsPackage::findOrFail($id);
        return view('backend.sellers.ads_package.create', compact('package'));
    }

    public function update(AdsPackageRequest $request, $id)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $this->adsPackageService->update($id, $data);
        flash(translate('Ads package has been updated successfully'))->success();
        return redirect()->route('ads-packages.index');
    }

    public function adsPackageHistory(Request $request)
    {
        $histories = SellerAdsPackage::join('users', 'seller_ads_packages.user_id', '=', 'users.id')
            ->join('ads_packages', 'seller_ads_packages.ads_package_id', '=', 'ads_packages.id')
            ->select([
                'seller_ads_packages.*',
                'users.name as user_name',
                'ads_packages.name as package_name',
                'ads_packages.price as package_price',
                'ads_packages.reach as package_reach'
            ]);

        // Filter theo tên user
        if ($request->filled('user')) {
            $histories->where('users.name', 'like', '%' . $request->user . '%');
        }
        // Filter theo tên package
        if ($request->filled('package')) {
            $histories->where('ads_packages.name', 'like', '%' . $request->package . '%');
        }

        $histories = $histories->orderBy('seller_ads_packages.id', 'asc')->paginate(15);

        return view('backend.sellers.ads_package.history', compact('histories'));
    }
}

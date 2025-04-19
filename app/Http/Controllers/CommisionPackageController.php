<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommisionPackageRequest;
use App\Models\CommissionPackage;
use App\Models\Seller;
use App\Models\User;
use App\Services\CommisionPackageService;
use DB;
use Illuminate\Http\Request;

class CommisionPackageController extends Controller
{
    public $commissionPackageService;
    public function __construct(CommisionPackageService $commissionPackageService)
    {
        $this->middleware('auth');
        $this->commissionPackageService = $commissionPackageService;
    }
    public function index(Request $request)
    {
        $sort_search = $request->sort_search ?? null;
        $status = $request->status ?? null;
        $packages = CommissionPackage::latest();

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
        $packages->map(function ($package) {
            $package->duration = $package->duration . ' ' . translate('Days');
            $package->commission_percentage = $package->commission_percentage . ' %';
        });
        return view('backend.sellers.commission_package.index', compact('packages'));
    }

    public function create()
    {
        return view('backend.sellers.commission_package.create');
    }

    public function store(CommisionPackageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $this->commissionPackageService->create($data);
        flash(translate('Commission package has been inserted successfully'))->success();
        return redirect()->route('commission-packages.index');
    }

    public function updateStatus(Request $request)
    {
        try {
            $this->commissionPackageService->updateStatus($request->id, $request->status);
            return 1;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return 0;
        }
    }

    public function destroy($id)
    {
        $package = CommissionPackage::findOrFail($id);
        if ($package->user_id != auth()->user()->id) {
            abort(403);
        }
        $package->delete();
        flash(translate('Commission package has been deleted successfully'))->success();
        return redirect()->route('commission-packages.index');
    }

    public function bulkDelete(Request $request)
    {
        try {
            foreach ($request->id as $id) {
                $package = CommissionPackage::find($id);
                $package->delete();
            }
        } catch (\Throwable $th) {
            flash(translate('Can not delete commission packages'))->error();
            return 1;
        }

        flash(translate('Commission packages have been deleted successfully'))->success();
        return redirect()->route('commission-packages.index');
    }

    public function edit($id)
    {
        $package = CommissionPackage::findOrFail($id);
        return view('backend.sellers.commission_package.create', compact('package'));
    }

    public function update(CommisionPackageRequest $request, $id)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $this->commissionPackageService->update($id, $data);
        flash(translate('Commission package has been updated successfully'))->success();
        return redirect()->route('commission-packages.index');
    }

    public function commissionPackageHistory(Request $request)
    {
        $query = DB::table('commission_package_user')
            ->join('users', 'commission_package_user.user_id', '=', 'users.id')
            ->join('commission_packages', 'commission_package_user.commission_package_id', '=', 'commission_packages.id')
            ->select(
                'commission_package_user.*',
                'users.name as user_name',
                'commission_packages.name as package_name',
                'commission_packages.price as package_price',
                'commission_packages.commission_percentage'
            );

        // Filter theo tên user
        if ($request->filled('user')) {
            $query->where('users.name', 'like', '%' . $request->user . '%');
        }
        // Filter theo tên package
        if ($request->filled('package')) {
            $query->where('commission_packages.name', 'like', '%' . $request->package . '%');
        }
        // Filter theo ngày đăng ký
        if ($request->filled('date_range')) {
            [$start, $end] = explode(' - ', $request->date_range);
            $query->whereBetween('commission_package_user.start_date', [$start, $end]);
        }

        $histories = $query->orderBy('commission_package_user.id', 'asc')->paginate(15);

        return view('backend.sellers.commission_package.history', compact('histories'));
    }
}
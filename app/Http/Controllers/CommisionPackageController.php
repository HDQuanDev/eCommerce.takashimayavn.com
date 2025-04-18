<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommisionPackageRequest;
use App\Services\CommisionPackageService;
use Illuminate\Http\Request;

class CommisionPackageController extends Controller
    {
    public $commissionPackageService;
    public function __construct(CommisionPackageService $commissionPackageService)
    {
        $this->middleware('auth');
        $this->commissionPackageService = $commissionPackageService;
    }
    public function index()
    {
        $packages = $this->commissionPackageService->all();
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

}

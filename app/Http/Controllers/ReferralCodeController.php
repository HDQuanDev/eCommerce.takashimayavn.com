<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReferralCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $referral_codes = ReferralCode::orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $referral_codes = $referral_codes->where('code', 'like', '%' . $sort_search . '%');
        }

        $referral_codes = $referral_codes->paginate(15);
        return view('backend.setup_configurations.referral_codes.index', compact('referral_codes', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.setup_configurations.referral_codes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:referral_codes,code|max:255',
            'usage_limit' => 'nullable|integer|min:1',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            flash(translate($validator->errors()->first()))->error();
            return back();
        }

        $referral_code = new ReferralCode;
        $referral_code->code = $request->code;
        $referral_code->usage_limit = $request->usage_limit;
        $referral_code->description = $request->description;
        $referral_code->is_active = 1;

        if ($referral_code->save()) {
            flash(translate('Referral code has been created successfully'))->success();
            return redirect()->route('referral-codes.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $referral_code = ReferralCode::findOrFail(decrypt($id));
        return view('backend.setup_configurations.referral_codes.edit', compact('referral_code'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $referral_code = ReferralCode::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:referral_codes,code,' . $id,
            'usage_limit' => 'nullable|integer|min:1',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            flash(translate($validator->errors()->first()))->error();
            return back();
        }

        $referral_code->code = $request->code;
        $referral_code->usage_limit = $request->usage_limit;
        $referral_code->description = $request->description;
        $referral_code->is_active = $request->has('is_active') ? 1 : 0;

        if ($referral_code->save()) {
            flash(translate('Referral code has been updated successfully'))->success();
            return redirect()->route('referral-codes.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $referral_code = ReferralCode::findOrFail($id);

        // Check if the referral code is being used by any shops
        if ($referral_code->shops()->count() > 0) {
            flash(translate('Cannot delete this referral code as it is being used by sellers'))->error();
            return redirect()->route('referral-codes.index');
        }

        if (ReferralCode::destroy($id)) {
            flash(translate('Referral code has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return redirect()->route('referral-codes.index');
    }

    /**
     * Update status of specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $referral_code = ReferralCode::findOrFail($request->id);
        $referral_code->is_active = $request->status;

        if ($referral_code->save()) {
            return 1;
        }

        return 0;
    }

    /**
     * Bulk delete selected referral codes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulk_referral_code_delete(Request $request)
    {
        if ($request->id) {
            // Check if any of the referral codes are being used
            $usedReferralCodes = ReferralCode::whereIn('id', $request->id)
                ->whereHas('shops')
                ->count();

            if ($usedReferralCodes > 0) {
                flash(translate('Some referral codes cannot be deleted as they are being used by sellers'))->error();
                return back();
            }

            foreach ($request->id as $referral_code_id) {
                ReferralCode::destroy($referral_code_id);
            }

            flash(translate('Referral codes have been deleted successfully'))->success();
        } else {
            flash(translate('Nothing to delete'))->error();
        }

        return back();
    }
}
<?php

namespace App\Services;

use App\Models\CommissionPackage;
use App\Models\User;

class CommisionPackageService
{
    // Lấy danh sách tất cả package
    public function all($columns = ['*'])
    {
        return CommissionPackage::all($columns);
    }

    public function getActive()
    {
        return CommissionPackage::where('status', "active")->get();
    }

    public static function getUserPackage($userId)
    {
        $user = User::find($userId);
        $currentPackage = $user->commission_package()
            ->wherePivot('end_date', '>', now())
            ->wherePivot('status', 'active')
            ->orderBy('commission_percentage', 'desc')
            ->first();
        return $currentPackage;
    }
    // Tìm package theo id
    public function find($id)
    {
        return CommissionPackage::find($id);
    }

    // Tạo mới package
    public function create(array $data)
    {
        return CommissionPackage::create($data);
    }

    // Cập nhật package
    public function update($id, array $data)
    {
        $package = CommissionPackage::findOrFail($id);

        $package->update($data);
        return $package;
    }

    // Xóa package
    public function delete($id)
    {
        $package = CommissionPackage::findOrFail($id);
        return $package->delete();
    }

    // Cập nhật trạng thái package
    public function updateStatus($id, $status)
    {
        $package = CommissionPackage::findOrFail($id);
        $package->status = $status;
        return $package->save();
    }
}
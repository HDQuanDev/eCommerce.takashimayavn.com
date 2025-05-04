<?php

namespace App\Services;

use App\Models\AdsPackage;
use App\Models\User;

class AdsPackageService
{
    // Lấy danh sách tất cả package
    public function all($columns = ['*'])
    {
        return AdsPackage::all($columns);
    }

    public function getActive()
    {
        return AdsPackage::where('status', "active")->get();
    }

    // Tìm package theo id
    public function find($id)
    {
        return AdsPackage::find($id);
    }

    // Tạo mới package
    public function create(array $data)
    {
        return AdsPackage::create($data);
    }

    // Cập nhật package
    public function update($id, array $data)
    {
        $package = AdsPackage::findOrFail($id);

        $package->update($data);
        return $package;
    }

    // Xóa package
    public function delete($id)
    {
        $package = AdsPackage::findOrFail($id);
        return $package->delete();
    }

    // Cập nhật trạng thái package
    public function updateStatus($id, $status)
    {
        $package = AdsPackage::findOrFail($id);
        $package->status = $status;
        return $package->save();
    }
}

<?php

namespace App\Services;

use App\Models\CommissionPackage;

class CommisionPackageService
{
    // Lấy danh sách tất cả package
    public function all($columns = ['*'])
    {
        return CommissionPackage::all($columns);
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
}
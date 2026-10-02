<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDuAnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_du_an' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            'trang_thai' => 'nullable|string|in:chua_bat_dau,dang_thuc_hien,tam_dung,hoan_thanh,da_huy',
            'mau_nhan' => 'nullable|string|max:20',
        ];
    }
}

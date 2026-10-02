<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCongViecRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_cong_viec' => 'sometimes|required|string|max:255',
            'du_an_id' => 'nullable|exists:du_an,id',
            'mo_ta' => 'nullable|string',
            'uu_tien' => 'sometimes|required|string|in:thap,trung_binh,cao,khan_cap',
            'trang_thai' => 'sometimes|required|string|in:can_lam,dang_lam,cho_duyet,hoan_thanh',
            'deadline' => 'nullable|date',
            'tien_do' => 'nullable|integer|min:0|max:100',
            'dong_bo_calendar' => 'nullable|boolean',
        ];
    }
}

<?php

namespace Vendor\ExportSales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportSalesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'required',
            'subcategory' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha fin',
            'category' => 'categoria',
            'subcategory' => 'subcategoria',
        ];
    }
}

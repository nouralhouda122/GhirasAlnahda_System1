<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // أفضل هندسياً للتأكد من تسجيل الدخول
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'             => 'required|string|min:5|max:255',
            'description'       => 'required|string|min:10',
            'sensitivity_level' => 'required|in:level_1,level_2,level_3',
            'is_anonymous'      => 'boolean',
            'attachment'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096', // حد أقصى 4 ميجا
        ];
    }

    /**
     * التحقق المتقدم (Business Logic Validation) لمنظمة غراس
     */
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // قانون الحماية: الشكاوى المجهولة مسموحة فقط وحصرياً في المستوى الثالث Confidential
            if ($this->is_anonymous && $this->sensitivity_level !== 'level_3') {
                $validator->errors()->add(
                    'is_anonymous',
                    'Anonymous reporting is strictly protected and only allowed for Level 3 (Confidential) complaints.'
                );
            }
        });
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id'       => ['required', 'integer', 'exists:campaigns,id'],
            'volunteer_id_code' => ['required', 'string', 'exists:volunteer_profiles,volunteer_id_code'],
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required'       => 'Campaign ID is required.',
            'campaign_id.exists'         => 'The selected campaign does not exist.',
            'volunteer_id_code.required' => 'Volunteer ID code is required from the QR scanner.',
            'volunteer_id_code.exists'   => 'This QR code does not belong to any registered volunteer.',
        ];
    }
}
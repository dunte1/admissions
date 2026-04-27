<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'application_id' => 'required|exists:applications,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,paypal,visa,mastercard,bank_transfer',
            'transaction_id' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ];
    }
}

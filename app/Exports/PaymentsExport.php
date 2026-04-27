<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings
{
    protected $payments;

    public function __construct($payments = null)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        $payments = $this->payments ?? Payment::with(['application.student'])->get();

        return $payments->map(function ($payment) {
            return [
                'Transaction ID' => $payment->transaction_id ?? 'N/A',
                'Application' => $payment->application?->application_number ?? 'N/A',
                'Applicant' => $payment->application?->student?->full_name ?? 'N/A',
                'Amount' => number_format($payment->amount, 2),
                'Payment Method' => ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')),
                'Status' => ucfirst($payment->status),
                'Payment Date' => $payment->paid_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'Created' => $payment->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Application',
            'Applicant',
            'Amount',
            'Payment Method',
            'Status',
            'Payment Date',
            'Created',
        ];
    }
}

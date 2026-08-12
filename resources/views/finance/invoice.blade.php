<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $payment->invoice_number }} | CollegeMusic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            color: #1f2937;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 2rem 1rem;
            line-height: 1.5;
        }
        .invoice-card {
            background-color: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
            gap: 1.5rem;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #3b82f6;
        }
        .meta-info {
            text-align: right;
            font-size: 0.9rem;
            color: #4b5563;
        }
        .meta-info h2 {
            margin: 0 0 0.5rem 0;
            color: #111827;
            font-size: 1.5rem;
        }
        .addresses {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .address-box {
            word-break: break-word;
            overflow-wrap: anywhere;
        }
        .address-box strong {
            display: block;
            margin-bottom: 0.5rem;
            color: #111827;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 2rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 480px;
        }
        .table th {
            background-color: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
        }
        .totals {
            width: 50%;
            margin-left: auto;
            font-size: 0.95rem;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        .totals-row.grand-total {
            border-top: 2px solid #e5e7eb;
            font-size: 1.2rem;
            font-weight: bold;
            color: #111827;
            padding-top: 0.75rem;
            margin-top: 0.5rem;
        }
        .btn-print-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background-color: #3b82f6;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
        .btn-secondary {
            background-color: #fff;
            color: #4b5563;
            border-color: #d1d5db;
        }
        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }
            .invoice-card {
                padding: 1.5rem;
                border-radius: 6px;
            }
            .header {
                flex-direction: column;
                gap: 1.25rem;
                padding-bottom: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .meta-info {
                text-align: left;
            }
            .addresses {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            .address-box[style*="text-align: right"] {
                text-align: left !important;
            }
            .totals {
                width: 100%;
            }
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.5rem 0.25rem;
            }
            .invoice-card {
                padding: 1rem;
            }
            .logo {
                font-size: 1.4rem;
            }
            .meta-info h2 {
                font-size: 1.25rem;
            }
            .totals-row.grand-total {
                font-size: 1.05rem;
            }
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .btn-print-group {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-card">
        <div class="header">
            <div>
                <div class="logo"><i class="fa-solid fa-music"></i> CollegeMusic</div>
                <div style="color: #4b5563; font-size: 0.85rem; margin-top: 0.5rem;">
                    Music Distribution & Payout Management System<br>
                    120 College Avenue, Suite 400<br>
                    support@collegemusic.com
                </div>
            </div>
            <div class="meta-info">
                <h2>INVOICE</h2>
                <strong>Invoice Number:</strong> {{ $payment->invoice_number }}<br>
                <strong>Date:</strong> {{ $payment->created_at->format('M d, Y') }}<br>
                <strong>Payment Ref:</strong> {{ $payment->transaction_reference }}<br>
                <strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">PAID</span>
            </div>
        </div>

        <div class="addresses">
            <div class="address-box">
                <strong>Billed To:</strong>
                {{ $payment->user->name }}<br>
                {{ $payment->user->email }}<br>
                {{ $payment->user->phone ?? 'Phone not provided' }}
            </div>
            <div class="address-box" style="text-align: right;">
                <strong>Payment Method:</strong>
                Credit/Debit Card (Online Payment)<br>
                Type: {{ str_replace('_', ' ', $payment->payment_method) }}<br>
                Transaction Reference: {{ $payment->transaction_reference }}
            </div>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th style="text-align: right;">Unit Price</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @if($payment->release)
                                <strong>Release Distribution Fee: "{{ $payment->release->title }}"</strong><br>
                                <span style="font-size: 0.8rem; color: #4b5563;">Distribute a {{ $releaseType ?? ($payment->release->type ?? 'single') }} catalog release globally</span>
                            @elseif($payment->subscription)
                                <strong>Premium Distribution Plan Subscription</strong><br>
                                <span style="font-size: 0.8rem; color: #4b5563;">1-Year Unlimited Music Distribution Membership Package</span>
                            @else
                                <strong>Music Distribution Administrative Fee</strong>
                            @endif
                        </td>
                        <td style="text-align: right;">${{ number_format($payment->amount, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">${{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Tax / VAT (0%):</span>
                <span>$0.00</span>
            </div>
            <div class="totals-row grand-total">
                <span>Grand Total:</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <div style="margin-top: 3rem; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 2rem; font-size: 0.85rem; color: #6b7280;">
            Thank you for distributing your music with CollegeMusic. We love making your music travel!
        </div>
    </div>

    <div class="btn-print-group">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Print Invoice
        </button>
        <a href="{{ route('finance') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
</body>
</html>

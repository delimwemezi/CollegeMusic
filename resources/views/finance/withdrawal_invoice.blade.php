<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Receipt {{ $withdrawal->invoice_number }} | CollegeMusic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            color: #1f2937;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 2rem;
            line-height: 1.5;
        }
        .invoice-card {
            background-color: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #10b981;
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
        .address-box strong {
            display: block;
            margin-bottom: 0.5rem;
            color: #111827;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
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
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            font-size: 0.95rem;
        }
        .btn-primary {
            background-color: #10b981;
            color: #fff;
        }
        .btn-secondary {
            background-color: #fff;
            color: #4b5563;
            border-color: #d1d5db;
        }
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                padding: 0;
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
                <div class="logo"><i class="fa-solid fa-file-invoice-dollar"></i> Payout Receipt</div>
                <div style="color: #4b5563; font-size: 0.85rem; margin-top: 0.5rem;">
                    CollegeMusic Distribution Platform<br>
                    120 College Avenue, Suite 400<br>
                    finance@collegemusic.com
                </div>
            </div>
            <div class="meta-info">
                <h2>RECEIPT</h2>
                <strong>Receipt Number:</strong> {{ $withdrawal->invoice_number }}<br>
                <strong>Date Issued:</strong> {{ $withdrawal->updated_at->format('M d, Y') }}<br>
                <strong>Withdrawal ID:</strong> WD-{{ $withdrawal->id }}<br>
                <strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">COMPLETED / PAID</span>
            </div>
        </div>

        <div class="addresses">
            <div class="address-box">
                <strong>Recipient Artist:</strong>
                {{ $withdrawal->user->name }}<br>
                {{ $withdrawal->user->email }}<br>
                {{ $withdrawal->user->phone ?? 'Phone not provided' }}
            </div>
            <div class="address-box" style="text-align: right;">
                <strong>Payout Method:</strong>
                {{ str_replace('_', ' ', strtoupper($withdrawal->payment_method)) }}<br>
                Payout Target Details:<br>
                <span style="font-family: monospace; font-size: 0.85rem;">{{ $withdrawal->payment_details }}</span>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: right;">Total Payout</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Artist Streaming Royalties Withdrawal Request</strong><br>
                        <span style="font-size: 0.8rem; color: #4b5563;">Royalty earnings accumulated from streaming platforms: Spotify, Apple, Amazon, YouTube Music</span>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #10b981;">${{ number_format($withdrawal->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal Payout:</span>
                <span>${{ number_format($withdrawal->amount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Processing Fees:</span>
                <span>$0.00</span>
            </div>
            <div class="totals-row grand-total">
                <span>Grand Payout Total:</span>
                <span style="color: #10b981;">${{ number_format($withdrawal->amount, 2) }}</span>
            </div>
        </div>

        <div style="margin-top: 3rem; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 2rem; font-size: 0.85rem; color: #6b7280;">
            This document serves as confirmation that the requested withdrawal has been processed and successfully deposited into your specified payout target. For any billing questions, contact support.
        </div>
    </div>

    <div class="btn-print-group">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Print Receipt
        </button>
        <a href="{{ route('finance') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
</body>
</html>

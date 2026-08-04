@extends('layouts.app')

@section('title', 'Platform Reports')
@section('header_title', 'System Reporting')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Administrative Reports</h1>
        <p class="page-subtitle">Generate system-wide statistics on users, catalogs, releases, and platform income ledger</p>
    </div>
    <div>
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="grid-cols-2">
    <!-- User Breakdown Report -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-users"></i> Platform User Registrations Report</h3>
        </div>
        <div class="card-body">
            <div style="font-size: 1.1rem; font-weight: bold; margin-bottom: 1.25rem;">
                Total Accounts: <span style="color: var(--primary);">{{ $report['total_users'] }}</span>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Account Role / Type</th>
                            <th style="text-align: right;">Registration Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['users_by_role'] as $role)
                            <tr>
                                <td style="text-transform: capitalize; font-weight: 500;">{{ str_replace('_', ' ', $role->role) }}</td>
                                <td style="text-align: right; font-weight: bold; color: var(--primary);">{{ $role->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Releases Status Report -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-compact-disc"></i> Catalogue Distribution Report</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 1.25rem; font-size: 1.1rem;">
                <span>Total Catalog Releases: <span style="color: var(--purple);">{{ $report['total_releases'] }}</span></span>
                <span>Total Track List: <span style="color: var(--info);">{{ $report['total_tracks'] }}</span></span>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Distribution Status</th>
                            <th style="text-align: right;">Release Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['releases_by_status'] as $status)
                            <tr>
                                <td style="text-transform: capitalize; font-weight: 500;">
                                    @if($status->distribution_status === 'pending')
                                        <i class="fa-solid fa-circle-play" style="color: var(--warning); margin-right: 0.25rem;"></i> Review Pending
                                    @elseif($status->distribution_status === 'approved')
                                        <i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 0.25rem;"></i> Approved
                                    @elseif($status->distribution_status === 'distributed')
                                        <i class="fa-solid fa-cloud-arrow-up" style="color: var(--purple); margin-right: 0.25rem;"></i> Distributed
                                    @elseif($status->distribution_status === 'rejected')
                                        <i class="fa-solid fa-circle-xmark" style="color: var(--danger); margin-right: 0.25rem;"></i> Rejected
                                    @else
                                        {{ $status->distribution_status }}
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: bold; color: var(--purple);">{{ $status->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Platform Revenue Ledger Report -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Platform Inbound Revenue Audit Ledger</h3>
            </div>
            <div class="card-body">
                <div style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1.5rem;">
                    Cumulative System Income: <span style="color: var(--success);">${{ number_format($report['total_revenue'], 2) }}</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction Checkout Method</th>
                                <th>Completed Sales Transactions</th>
                                <th style="text-align: right;">Total Amount Earned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['payments_by_method'] as $method)
                                <tr>
                                    <td style="text-transform: uppercase; font-weight: 500;">
                                        @if($method->payment_method === 'card')
                                            <i class="fa-solid fa-credit-card" style="color: var(--primary); margin-right: 0.25rem;"></i> Credit Card Gateway
                                        @else
                                            <i class="fa-solid fa-money-bill-transfer" style="color: var(--info); margin-right: 0.25rem;"></i> Mobile Money / Other
                                        @endif
                                    </td>
                                    <td style="font-weight: bold;">{{ $method->count }} checkout(s)</td>
                                    <td style="text-align: right; font-weight: bold; color: var(--success); font-size: 1.05rem;">
                                        ${{ number_format($method->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">No checkout payments recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

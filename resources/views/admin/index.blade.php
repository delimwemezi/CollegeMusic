@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header_title', 'System Administration')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Admin Console</h1>
        <p class="page-subtitle">Platform overview, database counts, and real-time security tracking</p>
    </div>
</div>

<!-- Stats Dashboard Grid -->
<div class="grid-stats">
    <div class="stat-card">
        <div>
            <div class="stat-title">Platform Users</div>
            <div class="stat-value">{{ $stats['users'] }}</div>
            <div class="stat-change" style="color: var(--text-muted);">Registered accounts</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Pending Releases</div>
            <div class="stat-value">{{ $stats['releases_pending'] }}</div>
            <div class="stat-change" style="color: var(--warning);"><i class="fa-solid fa-clock"></i> Awaiting review</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-record-vinyl"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Payout Requests</div>
            <div class="stat-value">{{ $stats['withdrawals_pending'] }}</div>
            <div class="stat-change" style="color: var(--success);"><i class="fa-solid fa-money-check-dollar"></i> Pending payouts</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Revenue</div>
            <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stat-change" style="color: var(--primary);"><i class="fa-solid fa-circle-dollar-to-slot"></i> Distribution fees</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
    </div>
</div>

<div class="grid-cols-3">
    <!-- Quick Actions Panel -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-bolt"></i> Administrative Controls</h3>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('admin.releases') }}" class="btn btn-primary btn-block" style="justify-content: flex-start;">
                <i class="fa-solid fa-clipboard-check"></i> Review Pending Music ({{ $stats['releases_pending'] }})
            </a>
            <a href="{{ route('admin.artists') }}" class="btn btn-secondary btn-block" style="justify-content: flex-start; text-align: left;">
                <i class="fa-solid fa-id-card"></i> Verify Artist Profiles
            </a>
            <a href="{{ route('admin.payments') }}" class="btn btn-secondary btn-block" style="justify-content: flex-start; text-align: left;">
                <i class="fa-solid fa-money-bill-transfer"></i> Process Payout Requests ({{ $stats['withdrawals_pending'] }})
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-block" style="justify-content: flex-start; text-align: left;">
                <i class="fa-solid fa-users-gear"></i> User Account Directory
            </a>
            <a href="{{ route('admin.reports') }}" class="btn btn-secondary btn-block" style="justify-content: flex-start; text-align: left;">
                <i class="fa-solid fa-chart-line"></i> View System Stats & Reports
            </a>
        </div>
    </div>

    <!-- Real-time Audit Logs -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-shield-halved"></i> Recent Audit Activity Logs</h3>
                <a href="{{ route('admin.logs') }}" style="font-size: 0.85rem; font-weight: 500;">See All Logs</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td style="color: var(--text-secondary); white-space: nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td style="font-weight: 600;">{{ $log->user ? $log->user->email : 'System Guest' }}</td>
                                    <td>
                                        <span class="badge" style="background-color: var(--bg-input); color: var(--primary);">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>
                                    <td style="color: var(--text-secondary);">{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No system audit entries yet.</td>
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

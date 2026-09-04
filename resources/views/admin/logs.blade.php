@extends('layouts.app')

@section('title', 'Security Audit Logs')
@section('header_title', 'System Monitoring')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Security & Audit Activity Logs</h1>
        <p class="page-subtitle">Inspect historical user actions, access control changes, and security footprints for compliance auditing</p>
    </div>
    <div>
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card audit-log-card">
    <div class="card-header">
        <h3 class="card-title">Historical Audit Trial Entries</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table audit-log-table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User Email</th>
                        <th>User Role</th>
                        <th>Action Category</th>
                        <th>Activity Description</th>
                        <th>IP Address</th>
                        <th>User Agent Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td style="color: var(--text-secondary); white-space: nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td style="font-weight: 600;">{{ $log->user ? $log->user->email : 'System Guest' }}</td>
                            <td>
                                @if($log->user)
                                    <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary);">
                                        {{ str_replace('_', ' ', $log->user->role) }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">GUEST</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: var(--bg-input); color: var(--primary); text-transform: capitalize;">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td style="color: var(--text-primary); max-width: 300px; font-weight: 500;">{{ $log->description }}</td>
                            <td style="font-family: monospace; color: var(--text-secondary);">{{ $log->ip_address }}</td>
                            <td style="color: var(--text-muted); font-size: 0.75rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                                {{ $log->user_agent }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $logs->links() }}
</div>

<style>
    .pagination-wrapper {
        margin-top: 1.5rem;
        padding: 0 0.25rem;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow-x: auto;
        scrollbar-width: thin;
    }

    /* Laravel Tailwind pagination */
    .pagination-wrapper nav {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper nav > div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .pagination-wrapper a,
    .pagination-wrapper span {
        min-width: 38px;
        height: 38px;
        padding: 0 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .pagination-wrapper a {
        background: var(--bg-input, #f5f5f5);
        color: var(--text-primary, #333);
        border: 1px solid var(--border-color, #ddd);
    }

    .pagination-wrapper a:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        transform: translateY(-1px);
    }

    .pagination-wrapper span[aria-current="page"] {
        background: var(--primary);
        color: #fff;
        border: 1px solid var(--primary);
    }

    .pagination-wrapper span[aria-disabled="true"] {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--bg-input, #f5f5f5);
        color: var(--text-muted, #888);
        border: 1px solid var(--border-color, #ddd);
    }

    /* Mobile */
    @media (max-width: 600px) {
        .pagination-wrapper {
            justify-content: flex-start;
            padding-bottom: 0.25rem;
        }

        .pagination-wrapper nav {
            justify-content: flex-start;
        }

        .pagination-wrapper nav > div {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }

        .pagination-wrapper a,
        .pagination-wrapper span {
            min-width: 36px;
            height: 36px;
            padding: 0 0.6rem;
            font-size: 0.8rem;
        }
    }
</style>

@endsection

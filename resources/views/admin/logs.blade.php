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

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historical Audit Trial Entries</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="font-size: 0.85rem;">
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

<div style="margin-top: 1.5rem;">
    {{ $logs->links() }}
</div>
@endsection

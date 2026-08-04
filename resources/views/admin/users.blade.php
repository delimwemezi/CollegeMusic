@extends('layouts.app')

@section('title', 'Users Management')
@section('header_title', 'Account Administration')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">User Accounts Control</h1>
        <p class="page-subtitle">Manage user records, check registration configurations, and toggle account access states</p>
    </div>
    <div>
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Accounts Directory</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Subscription</th>
                        <th>Verification</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td style="font-weight: 600;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'Not provided' }}</td>
                            <td>
                                <span class="badge" style="background-color: rgba(99, 102, 241, 0.08); color: var(--accent); text-transform: uppercase; font-size: 0.75rem;">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->subscription && $user->subscription->plan_name === 'Premium' && $user->subscription->status === 'active')
                                    <span class="badge badge-approved"><i class="fa-solid fa-crown" style="margin-right: 0.25rem;"></i> Premium</span>
                                @else
                                    <span class="badge badge-pending">Free Plan</span>
                                @endif
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span style="color: var(--success); font-size: 0.85rem;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-hourglass-half"></i> Unverified</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge badge-approved">Active</span>
                                @elseif($user->status === 'suspended')
                                    <span class="badge badge-rejected">Suspended</span>
                                @else
                                    <span class="badge badge-pending">Deactivated</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($user->id !== auth()->id()) <!-- Do not suspend/toggle self -->
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" style="margin: 0; display: inline-block;">
                                        @csrf
                                        @if($user->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn btn-danger btn-sm" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                                                <i class="fa-solid fa-user-slash"></i> Suspend
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-success btn-sm" style="background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); color: #a7f3d0;">
                                                <i class="fa-solid fa-user-check"></i> Activate
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Current Session</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    {{ $users->links() }}
</div>
@endsection

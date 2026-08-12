@extends('layouts.app')

@section('title', __('messages.dashboard'))
@section('header_title', __('messages.dashboard'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.welcome_back_user') }}, {{ auth()->user()->name }}!</h1>
        <p class="page-subtitle">
            {{ __('messages.account_type') }}: <strong style="text-transform: capitalize;">{{ str_replace('_', ' ', auth()->user()->role) }}</strong> 
            &bull; {{ __('messages.plan') }}: 
            @if(auth()->user()->subscription && auth()->user()->subscription->status === 'active')
                <span style="color: var(--warning); font-weight: bold;"><i class="fa-solid fa-crown"></i> {{ __('messages.premium_plan') }}</span>
            @else
                <span style="color: var(--text-secondary);">{{ __('messages.free_plan') }}</span>
            @endif
        </p>
    </div>
    <div>
        <a href="{{ route('releases.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('messages.distribute_new_music') }}
        </a>
    </div>
</div>

<!-- Warning Card: Verification Reminder -->
@if($showVerificationWarning)
    <div class="alert alert-warning" style="margin-bottom: 2rem; border-color: rgba(245, 158, 11, 0.3); display: flex; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.75rem; color: var(--warning); flex-shrink: 0;"></i>
        <div style="flex: 1; min-width: 200px;">
            <h4 style="font-weight: bold; margin-bottom: 0.25rem; color: var(--text-primary);">{{ __('messages.verification_warning_title') }}</h4>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">
                {{ __('messages.verification_warning_desc') }}
            </p>
        </div>
        <div>
            <a href="{{ route('catalogue') }}" class="btn btn-secondary btn-sm" style="background-color: var(--bg-card); border-color: var(--border-color); color: #fff;">
                <i class="fa-solid fa-id-card"></i> {{ __('messages.verify_account_now') }}
            </a>
        </div>
    </div>
@endif

<!-- Stats Counters Grid -->
<div class="grid-stats">
    <div class="stat-card">
        <div>
            <div class="stat-title">{{ __('messages.catalog_releases') }}</div>
            <div class="stat-value">{{ $releasesCount }}</div>
            <div class="stat-change" style="color: var(--text-muted);">Uploaded singles & albums</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-compact-disc"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">{{ __('messages.cumulative_streams') }}</div>
            <div class="stat-value">{{ number_format($totalStreams) }}</div>
            <div class="stat-change" style="color: var(--success);"><i class="fa-solid fa-arrow-trend-up"></i> Playbacks across DSPs</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-play"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">{{ __('messages.available_payout_balance') }}</div>
            <div class="stat-value">${{ number_format($availableBalance, 2) }}</div>
            <div class="stat-change" style="color: var(--text-muted);">Royalties available to withdraw</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(139, 92, 246, 0.1); color: var(--purple);"><i class="fa-solid fa-wallet"></i></div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Recent Activity Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-chart-line"></i> {{ __('messages.playback_activity_trend') }}</h3>
            <a href="{{ route('analytics') }}" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.details') }}</a>
        </div>
        <div class="card-body" style="height: 280px; position: relative;">
            <canvas id="dashboardStreamChart"></canvas>
        </div>
    </div>

    <!-- Alert Notifications Panel -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-bell"></i> {{ __('messages.recent_alerts') }}</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            @forelse($notifications as $notif)
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 1rem; animation: fadeIn 0.3s ease;">
                    @if(str_contains($notif->action, 'approve'))
                        <div style="background-color: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.5rem; border-radius: var(--radius-sm);"><i class="fa-solid fa-circle-check"></i></div>
                    @elseif(str_contains($notif->action, 'reject'))
                        <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.5rem; border-radius: var(--radius-sm);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    @else
                        <div style="background-color: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 0.5rem; border-radius: var(--radius-sm);"><i class="fa-solid fa-info"></i></div>
                    @endif
                    
                    <div style="flex: 1;">
                        <h4 style="font-size: 0.875rem; color: var(--text-primary); font-weight: 600;">
                            @if($notif->action === 'release_approved') Release Approved
                            @elseif($notif->action === 'release_rejected') Release Rejected
                            @elseif($notif->action === 'release_distributed') Release Ingested / Distributed
                            @elseif($notif->action === 'withdrawal_completed') Payout Approved & Paid
                            @elseif($notif->action === 'withdrawal_rejected') Payout Request Rejected
                            @else Notification Alert
                            @endif
                        </h4>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.15rem; line-height: 1.4;">{{ $notif->description }}</p>
                        <small style="color: var(--text-muted); font-size: 0.7rem; display: block; margin-top: 0.25rem;">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); padding: 3rem 0; font-size: 0.9rem;">
                    <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem; color: var(--text-muted);"></i>
                    <p>{{ __('messages.no_alerts') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Releases list -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-record-vinyl"></i> {{ __('messages.recent_releases') }}</h3>
        <a href="{{ route('catalogue') }}" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.view_entire_catalog') }}</a>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($releases->isEmpty())
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <p>{{ __('messages.no_releases') }}</p>
                <a href="{{ route('releases.create') }}" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">{{ __('messages.upload_music_now') }}</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Release Title</th>
                            <th>Release Type</th>
                            <th>Genre</th>
                            <th>Tracks</th>
                            <th>Stores</th>
                            <th>Billing Status</th>
                            <th>Distribution Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($releases as $rel)
                            <tr>
                                <td>
                                    @if($rel->cover_image)
                                        <img src="{{ asset('storage/' . $rel->cover_image) }}" alt="Cover" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover;">
                                    @else
                                        <div style="width: 48px; height: 48px; background-color: var(--bg-input); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-music" style="color: var(--text-muted);"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong style="font-size: 0.95rem;"><a href="{{ route('releases.show', $rel->id) }}">{{ $rel->title }}</a></strong>
                                    @if(auth()->user()->isRecordLabel() || auth()->user()->isDistributor())
                                        <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">Artist: {{ $rel->artist->name }}</span>
                                    @endif
                                </td>
                                <td><span style="text-transform: uppercase; font-size: 0.8rem; font-weight: 500;">{{ $rel->type }}</span></td>
                                <td>{{ $rel->genre }}</td>
                                <td>{{ count($rel->tracks) }} Track(s)</td>
                                <td>
                                    <div style="display: flex; gap: 0.25rem;">
                                        @foreach($rel->stores->take(4) as $store)
                                            <span style="font-size: 0.75rem; background-color: var(--bg-input); padding: 0.1rem 0.35rem; border-radius: 4px;" title="{{ $store->store_name }}">
                                                {{ $store->store_name }}
                                            </span>
                                        @endforeach
                                        @if(count($rel->stores) > 4)
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">+{{ count($rel->stores) - 4 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($rel->billing_status === 'paid')
                                        <span class="badge badge-approved">Paid</span>
                                    @else
                                        <span class="badge badge-pending">Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rel->distribution_status === 'pending')
                                        <span class="badge badge-pending">Review Pending</span>
                                    @elseif($rel->distribution_status === 'approved')
                                        <span class="badge badge-approved">Approved</span>
                                    @elseif($rel->distribution_status === 'distributed')
                                        <span class="badge badge-distributed">Distributed</span>
                                    @elseif($rel->distribution_status === 'rejected')
                                        <span class="badge badge-rejected">Rejected</span>
                                    @elseif($rel->distribution_status === 'pending_takedown')
                                        <span class="badge badge-rejected" style="background-color: rgba(239,68,68,0.1); color: var(--danger);">Takedown Req</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Activity Chart configuration
    var ctxDashboard = document.getElementById('dashboardStreamChart');
    if (ctxDashboard) {
        var dashboardChart = new Chart(ctxDashboard.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Streaming playbacks count',
                    data: [
                        {{ $totalStreams * 0.05 }}, 
                        {{ $totalStreams * 0.07 }}, 
                        {{ $totalStreams * 0.06 }}, 
                        {{ $totalStreams * 0.09 }}, 
                        {{ $totalStreams * 0.12 }}, 
                        {{ $totalStreams * 0.10 }}, 
                        {{ $totalStreams * 0.14 }}, 
                        {{ $totalStreams * 0.11 }}, 
                        {{ $totalStreams * 0.08 }}, 
                        {{ $totalStreams * 0.06 }}, 
                        {{ $totalStreams * 0.05 }}, 
                        {{ $totalStreams * 0.07 }}
                    ],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.03)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    }
                }
            }
        });
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Streaming Stats')
@section('header_title', 'Analytics Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Performance Analytics</h1>
        <p class="page-subtitle">Monitor your catalog's performance, download indicators, and audience reach</p>
    </div>
    <div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('analytics.report', ['period' => 'monthly']) }}" target="_blank" class="btn btn-secondary">
                <i class="fa-solid fa-file-invoice"></i> Generate Monthly Report
            </a>
            <a href="{{ route('analytics.report', ['period' => 'yearly']) }}" target="_blank" class="btn btn-secondary">
                <i class="fa-solid fa-file-invoice"></i> Generate Yearly Report
            </a>
        </div>
    </div>
</div>

<!-- Stats Counter Grid -->
<div class="grid-stats">
    <div class="stat-card">
        <div>
            <div class="stat-title">Catalog Releases</div>
            <div class="stat-value">{{ $releasesCount }}</div>
            <div class="stat-change" style="color: var(--text-muted);">Singles, EPs & Albums</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-compact-disc"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Streams</div>
            <div class="stat-value">{{ number_format($totalStreams) }}</div>
            <div class="stat-change" style="color: var(--success);"><i class="fa-solid fa-arrow-trend-up"></i> Life-time stream count</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-play"></i></div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Downloads</div>
            <div class="stat-value">{{ number_format($totalDownloads) }}</div>
            <div class="stat-change" style="color: var(--info);"><i class="fa-solid fa-download"></i> Purchased tracks</div>
        </div>
        <div class="stat-icon" style="background-color: rgba(6, 182, 212, 0.1); color: var(--info);"><i class="fa-solid fa-circle-down"></i></div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Streaming Performance Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-chart-line"></i> Streaming Performance Trend</h3>
        </div>
        <div class="card-body" style="height: 320px; position: relative;">
            <canvas id="streamTrendChart"></canvas>
        </div>
    </div>

    <!-- Platform Breakdown Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-chart-pie"></i> Platform Distribution Share</h3>
        </div>
        <div class="card-body" style="height: 320px; position: relative; display: flex; align-items: center; justify-content: center;">
            @if($platformStats->isEmpty())
                <p style="color: var(--text-muted); font-size: 0.9rem;">No data recorded yet. Platform data updates after store ingestion.</p>
            @else
                <div style="width: 250px; height: 250px;">
                    <canvas id="platformPieChart"></canvas>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Top Performing Songs Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-trophy"></i> Top 5 Performing Tracks</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($topSongs->isEmpty())
                <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No tracks data found.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Track Title</th>
                                <th>ISRC</th>
                                <th style="text-align: right;">Total Streams</th>
                                <th style="text-align: right;">Downloads</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topSongs as $index => $song)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="font-weight: bold;">{{ $song->title }}</td>
                                    <td style="font-family: monospace; font-size: 0.8rem;">{{ $song->isrc }}</td>
                                    <td style="text-align: right; font-weight: bold; color: var(--primary);">{{ number_format($song->streams_count) }}</td>
                                    <td style="text-align: right; color: var(--text-secondary);">{{ number_format($song->downloads_count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Audience Geographics (With Premium Lock Gate) -->
    <div class="card" style="position: relative;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-earth-americas"></i> Listener Locations by Country</h3>
        </div>
        <div class="card-body" style="padding: 0; min-height: 250px; position: relative;">
            
            <!-- Table content -->
            <div class="table-responsive {{ !$isPremium ? 'premium-blur' : '' }}" style="transition: filter 0.3s ease;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Country / Region</th>
                            <th style="text-align: right;">Total Streams</th>
                            <th style="text-align: right;">Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countryStats as $cnt)
                            <tr>
                                <td style="font-weight: 500;">
                                    <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 0.5rem;"></i>
                                    @if($cnt->country === 'US') United States
                                    @elseif($cnt->country === 'GB') United Kingdom
                                    @elseif($cnt->country === 'CA') Canada
                                    @elseif($cnt->country === 'NG') Nigeria
                                    @elseif($cnt->country === 'JP') Japan
                                    @else {{ $cnt->country }}
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: bold;">{{ number_format($cnt->total_streams) }}</td>
                                <td style="text-align: right; color: var(--success); font-weight: 600;">${{ number_format($cnt->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No audience geographical data gathered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Premium Lock Gate overlay if not Premium -->
            @if(!$isPremium)
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(21, 31, 50, 0.7); backdrop-filter: blur(5px); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; text-align: center; z-index: 10;">
                    <i class="fa-solid fa-lock" style="font-size: 2.5rem; color: var(--warning); margin-bottom: 0.75rem; animation: pulse 2s infinite;"></i>
                    <h4 style="font-size: 1.1rem; font-weight: bold; margin-bottom: 0.25rem;">Demographics Locked</h4>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; max-width: 280px; margin-bottom: 1.25rem; line-height: 1.4;">
                        Subscribe to the Premium plan to unlock real-time listeners geolocations, stream cities, and download demographics!
                    </p>
                    <a href="{{ route('finance') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-crown"></i> Upgrade to Premium
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
    .premium-blur {
        filter: blur(4px);
        pointer-events: none;
        user-select: none;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); }
    }
</style>
<script>
    // 1. Line Chart: Streaming Trend over months
    var ctxLine = document.getElementById('streamTrendChart');
    if (ctxLine) {
        var streamChart = new Chart(ctxLine.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Streaming Playbacks',
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
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 4,
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
                        grid: { color: 'rgba(255, 255, 255, 0.03)' },
                        ticks: { color: '#9ca3af' }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.03)' },
                        ticks: { color: '#9ca3af' }
                    }
                }
            }
        });
    }

    // 2. Pie Chart: Platform Distribution
    var ctxPie = document.getElementById('platformPieChart');
    if (ctxPie) {
        @php
            $platforms = [];
            $counts = [];
            foreach($platformStats as $stat) {
                $platforms[] = $stat->platform;
                $counts[] = $stat->total_streams;
            }
        @endphp

        var platformChart = new Chart(ctxPie.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($platforms) !!},
                datasets: [{
                    data: {!! json_encode($counts) !!},
                    backgroundColor: ['#1ed760', '#fc3c44', '#00a8e1', '#ff0000', '#ff007f', '#00ffcc'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#9ca3af',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection

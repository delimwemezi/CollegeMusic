<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Report | CollegeMusic</title>
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
        .report-card {
            background-color: #fff;
            max-width: 900px;
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
            align-items: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            gap: 1.5rem;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #3b82f6;
        }
        .meta {
            text-align: right;
            color: #4b5563;
            font-size: 0.9rem;
        }
        .meta h2 {
            margin: 0 0 0.25rem 0;
            color: #111827;
            text-transform: uppercase;
            font-size: 1.4rem;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 2.5rem;
            width: 100%;
        }
        .summary-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 1.25rem;
            text-align: center;
        }
        .summary-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .summary-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #111827;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #111827;
            margin: 2rem 0 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            font-size: 0.9rem;
            min-width: 480px;
        }
        .table th {
            background-color: #f9fafb;
            padding: 0.6rem 0.75rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }
        .btn-group {
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
            .report-card {
                padding: 1.5rem;
                border-radius: 6px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.25rem;
            }
            .meta {
                text-align: left;
            }
            .summary-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .summary-value {
                font-size: 1.4rem;
            }
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.5rem 0.25rem;
            }
            .report-card {
                padding: 1rem;
            }
            .logo {
                font-size: 1.4rem;
            }
            .meta h2 {
                font-size: 1.2rem;
            }
            .summary-box {
                padding: 1rem;
            }
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .report-card {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .btn-group {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-card">
        <div class="header">
            <div>
                <div class="logo"><i class="fa-solid fa-music"></i> CollegeMusic</div>
                <div style="color: #6b7280; font-size: 0.8rem; margin-top: 0.25rem;">
                    Platform Catalog Analytics Engine &bull; Generated {{ date('Y-m-d H:i') }}
                </div>
            </div>
            <div class="meta">
                <h2>{{ ucfirst($period) }} Performance Report</h2>
                <strong>For Artist Profile:</strong> {{ auth()->user()->name }}<br>
                <strong>Account Type:</strong> {{ strtoupper(auth()->user()->role) }}
            </div>
        </div>

        <!-- Metric summaries -->
        <div class="summary-grid">
            <div class="summary-box">
                <div class="summary-title">Total Playbacks / Streams</div>
                <div class="summary-value" style="color: #3b82f6;">{{ number_format($totalStreams) }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-title">Paid Downloads</div>
                <div class="summary-value" style="color: #06b6d4;">{{ number_format($totalDownloads) }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-title">Net Royalties Earned</div>
                <div class="summary-value" style="color: #10b981;">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        <!-- Top Tracks -->
        <div class="section-title"><i class="fa-solid fa-ranking-star"></i> Top Performing Tracks</div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Track Title</th>
                        <th>ISRC</th>
                        <th style="text-align: right;">Streams</th>
                        <th style="text-align: right;">Downloads</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSongs as $index => $song)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $song->title }}</strong></td>
                            <td style="font-family: monospace;">{{ $song->isrc }}</td>
                            <td style="text-align: right; font-weight: bold; color: #3b82f6;">{{ number_format($song->streams_count) }}</td>
                            <td style="text-align: right;">{{ number_format($song->downloads_count) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280;">No tracks recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Platform Breakdown -->
        <div class="section-title"><i class="fa-solid fa-chart-pie"></i> Streaming Service Breakdown</div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Platform Target</th>
                        <th>Platform Streams</th>
                        <th style="text-align: right;">Platform Royalties Generated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($platformBreakdown as $platform)
                        <tr>
                            <td><strong>{{ $platform->platform }}</strong></td>
                            <td>{{ number_format($platform->total_streams) }} streams</td>
                            <td style="text-align: right; font-weight: bold; color: #10b981;">
                                ${{ number_format($platform->total_revenue, 4) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #6b7280;">No streaming platform activity logs yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Geographical Breakdown -->
        <div class="section-title"><i class="fa-solid fa-earth-africa"></i> Geographical Distribution</div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Listener Region</th>
                        <th>Total Playbacks</th>
                        <th style="text-align: right;">Royalties Generated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countryBreakdown as $country)
                        <tr>
                            <td>
                                <strong>
                                    @if($country->country === 'US') United States
                                    @elseif($country->country === 'GB') United Kingdom
                                    @elseif($country->country === 'CA') Canada
                                    @elseif($country->country === 'NG') Nigeria
                                    @elseif($country->country === 'JP') Japan
                                    @else {{ $country->country }}
                                    @endif
                                </strong>
                            </td>
                            <td>{{ number_format($country->total_streams) }} streams</td>
                            <td style="text-align: right; font-weight: bold; color: #10b981;">
                                ${{ number_format($country->total_revenue, 4) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #6b7280;">No audience geography logs yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 3rem; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; font-size: 0.8rem; color: #6b7280;">
            This report represents verified distribution analytics pulled directly from the CollegeMusic ingestion database log aggregates. Confidential for account auditor.
        </div>
    </div>

    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Print Report / Save PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fa-solid fa-xmark"></i> Close Window
        </button>
    </div>
</body>
</html>

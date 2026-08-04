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
            padding: 2rem;
            line-height: 1.5;
        }
        .report-card {
            background-color: #fff;
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
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
            gap: 1.5rem;
            margin-bottom: 2.5rem;
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
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .table th {
            background-color: #f9fafb;
            padding: 0.5rem 0.75rem;
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
            background-color: #3b82f6;
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
            .report-card {
                box-shadow: none;
                padding: 0;
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
                    Platform Catalog Analytics Engine &bull; Generated <?php echo e(date('Y-m-d H:i')); ?>

                </div>
            </div>
            <div class="meta">
                <h2><?php echo e(ucfirst($period)); ?> Performance Report</h2>
                <strong>For Artist Profile:</strong> <?php echo e(auth()->user()->name); ?><br>
                <strong>Account Type:</strong> <?php echo e(strtoupper(auth()->user()->role)); ?>

            </div>
        </div>

        <!-- Metric summaries -->
        <div class="summary-grid">
            <div class="summary-box">
                <div class="summary-title">Total Playbacks / Streams</div>
                <div class="summary-value" style="color: #3b82f6;"><?php echo e(number_format($totalStreams)); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-title">Paid Downloads</div>
                <div class="summary-value" style="color: #06b6d4;"><?php echo e(number_format($totalDownloads)); ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-title">Net Royalties Earned</div>
                <div class="summary-value" style="color: #10b981;">$<?php echo e(number_format($totalRevenue, 2)); ?></div>
            </div>
        </div>

        <!-- Top Tracks -->
        <div class="section-title"><i class="fa-solid fa-ranking-star"></i> Top Performing Tracks</div>
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
                <?php $__empty_1 = true; $__currentLoopData = $topSongs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $song): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><strong><?php echo e($song->title); ?></strong></td>
                        <td style="font-family: monospace;"><?php echo e($song->isrc); ?></td>
                        <td style="text-align: right; font-weight: bold; color: #3b82f6;"><?php echo e(number_format($song->streams_count)); ?></td>
                        <td style="text-align: right;"><?php echo e(number_format($song->downloads_count)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280;">No tracks recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Platform Breakdown -->
        <div class="section-title"><i class="fa-solid fa-chart-pie"></i> Streaming Service Breakdown</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Platform Target</th>
                    <th>Platform Streams</th>
                    <th style="text-align: right;">Platform Royalties Generated</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $platformBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($platform->platform); ?></strong></td>
                        <td><?php echo e(number_format($platform->total_streams)); ?> streams</td>
                        <td style="text-align: right; font-weight: bold; color: #10b981;">
                            $<?php echo e(number_format($platform->total_revenue, 4)); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #6b7280;">No streaming platform activity logs yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Geographical Breakdown -->
        <div class="section-title"><i class="fa-solid fa-earth-africa"></i> Geographical Distribution</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Listener Region</th>
                    <th>Total Playbacks</th>
                    <th style="text-align: right;">Royalties Generated</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $countryBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong>
                                <?php if($country->country === 'US'): ?> United States
                                <?php elseif($country->country === 'GB'): ?> United Kingdom
                                <?php elseif($country->country === 'CA'): ?> Canada
                                <?php elseif($country->country === 'NG'): ?> Nigeria
                                <?php elseif($country->country === 'JP'): ?> Japan
                                <?php else: ?> <?php echo e($country->country); ?>

                                <?php endif; ?>
                            </strong>
                        </td>
                        <td><?php echo e(number_format($country->total_streams)); ?> streams</td>
                        <td style="text-align: right; font-weight: bold; color: #10b981;">
                            $<?php echo e(number_format($country->total_revenue, 4)); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #6b7280;">No audience geography logs yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

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
<?php /**PATH C:\wamp64\www\College-Music\resources\views/analytics/report.blade.php ENDPATH**/ ?>
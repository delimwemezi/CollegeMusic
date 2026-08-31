@extends('layouts.app')

@section('title', $release->title)
@section('header_title', 'Release Details')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $release->title }}</h1>
        <p class="page-subtitle">Submitted by {{ $release->artist->name }} &bull; Created {{ $release->created_at->format('M d, Y') }}</p>
    </div>
    <div>
        <a href="{{ route('catalogue') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Catalogue
        </a>
    </div>
</div>

<div class="grid-cols-3">
    <!-- Cover Art & Stats Panel -->
    <div>
        <div class="card" style="text-align: center; padding: 2rem;">
            @if($release->cover_image)
                <img src="{{ asset('storage/' . $release->cover_image) }}" alt="Cover Art" style="width: 200px; height: 200px; border-radius: var(--radius-md); object-fit: cover; box-shadow: var(--shadow-lg), 0 0 15px rgba(59, 130, 246, 0.1); border: 1px solid var(--border-color); margin: 0 auto 1.5rem;">
            @else
                <div style="width: 200px; height: 200px; background-color: var(--bg-input); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 1px dashed var(--border-color);">
                    <i class="fa-solid fa-record-vinyl" style="font-size: 5rem; color: var(--text-muted);"></i>
                </div>
            @endif

            <h3 style="font-size: 1.25rem; margin-bottom: 0.25rem;">{{ $release->title }}</h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em; margin-bottom: 1.5rem;">
                {{ $release->type }} &bull; {{ $release->genre }}
            </p>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: left;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">Language:</span>
                    <strong style="color: var(--text-primary);">{{ $release->language }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">Release Date:</span>
                    <strong style="color: var(--text-primary);">{{ $release->release_date ? $release->release_date->format('Y-m-d') : 'Immediate' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">Scheduling:</span>
                    <strong style="color: var(--text-primary); text-transform: capitalize;">{{ $release->scheduling_type }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">Copyright:</span>
                    <strong style="color: var(--text-primary); text-overflow: ellipsis; white-space: nowrap; overflow: hidden; max-width: 150px;" title="{{ $release->copyright_info }}">{{ $release->copyright_info }}</strong>
                </div>
            </div>
        </div>

        <!-- Stores Distribution list -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-shop"></i> Store Targets</h3>
            </div>
            <div class="card-body" style="padding: 1rem 1.5rem;">
                @foreach($release->stores as $store)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            @if($store->store_name === 'Spotify')
                                <i class="fa-brands fa-spotify" style="color: #1ed760; font-size: 1.15rem;"></i>
                            @elseif($store->store_name === 'Apple Music')
                                <i class="fa-brands fa-apple" style="color: #fc3c44; font-size: 1.15rem;"></i>
                            @elseif($store->store_name === 'YouTube Music')
                                <i class="fa-brands fa-youtube" style="color: #ff0000; font-size: 1.15rem;"></i>
                            @elseif($store->store_name === 'TikTok')
                                <i class="fa-brands fa-tiktok" style="color: #fff; font-size: 1.15rem;"></i>
                            @else
                                <i class="fa-solid fa-compact-disc" style="color: var(--primary); font-size: 1.15rem;"></i>
                            @endif
                            <span style="font-weight: 500; font-size: 0.9rem;">{{ $store->store_name }}</span>
                        </div>
                        <div>
                            @if($release->distribution_status === 'distributed')
                                <span class="badge badge-distributed" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;"><i class="fa-solid fa-circle-check" style="margin-right: 0.25rem;"></i> Active</span>
                            @elseif($release->distribution_status === 'rejected')
                                <span class="badge badge-rejected" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">Failed</span>
                            @else
                                <span class="badge badge-pending" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">Pending</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tracks & Billing/Payment Panels -->
    <div style="grid-column: span 2;">
        @if($release->distribution_status === 'rejected' && $release->rejection_reason)
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.25rem;"></i>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 0.25rem;">Release Rejected by Administrator</h4>
                    <p style="font-size: 0.85rem;">{{ $release->rejection_reason }}</p>
                    <a href="{{ route('releases.edit', $release->id) }}" class="btn btn-secondary btn-sm" style="margin-top: 0.75rem; background-color: var(--bg-card); color: #fff; border-color: var(--border-color);">
                        <i class="fa-solid fa-pen"></i> Edit Release to Correct Details
                    </a>
                </div>
            </div>
        @endif

        <!-- Tracks List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-list-ol"></i> Tracklist (Audio Files)</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @foreach($release->tracks as $index => $track)
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <span style="color: var(--primary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Track #{{ $index + 1 }}</span>
                                <h4 style="font-size: 1.05rem; margin-top: 0.15rem;">{{ $track->title }}
                                    @if($track->genre)
                                        <span style="display: inline-block; font-size: 0.7rem; font-weight: 500; background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(139,92,246,0.15)); color: var(--primary); padding: 0.15rem 0.6rem; border-radius: 9999px; margin-left: 0.5rem; vertical-align: middle; border: 1px solid rgba(59,130,246,0.2);">
                                            <i class="fa-solid fa-tag" style="font-size: 0.6rem; margin-right: 0.2rem;"></i>{{ $track->genre }}
                                        </span>
                                    @endif
                                </h4>
                                <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.25rem;">
                                    Composer: <strong>{{ $track->composer }}</strong> &bull; Songwriter: <strong>{{ $track->songwriter }}</strong>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-family: monospace; font-size: 0.85rem; background-color: var(--bg-input); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); color: var(--text-secondary);">
                                    ISRC: {{ $track->isrc }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Audio Player -->
                        <div class="track-player-preview">
                            <span style="font-size: 0.8rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-circle-play" style="color: var(--primary);"></i> Listen Preview
                            </span>
                            <audio controls style="height: 32px; width: 60%; outline: none; background: none;">
                                <source src="{{ asset('storage/' . $track->audio_file) }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Checkout / Payment Panel -->
        @if($release->billing_status === 'unpaid' && $fee > 0.00)
            <div class="card" style="border-color: var(--primary);">
                <div class="card-header" style="background-color: rgba(59, 130, 246, 0.03); border-bottom: 1px solid rgba(59, 130, 246, 0.1);">
                    <h3 class="card-title" style="color: var(--primary);"><i class="fa-solid fa-credit-card"></i> Process Distribution Fee Payment</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background-color: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div>
                            <h4 style="font-size: 1rem;">Release Processing Fee</h4>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">Standard fee for uploading a {{ $release->type }} release</p>
                        </div>
                        <div style="font-size: 1.75rem; font-weight: bold; font-family: var(--font-heading); color: var(--primary);">
                            ${{ number_format($fee, 2) }}
                        </div>
                    </div>

                    <form action="{{ route('releases.pay', $release->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="card_name">Name on Card</label>
                            <input type="text" id="card_name" name="card_name" class="form-input" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="card_number">Card Number</label>
                            <div style="position: relative;">
                                <input type="text" id="card_number" name="card_number" class="form-input" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" style="padding-left: 2.75rem;" required>
                                <i class="fa-solid fa-credit-card" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                            </div>
                        </div>

                        <div class="grid-cols-2" style="margin-bottom: 1.5rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="card_expiry">Expiry Date</label>
                                <input type="text" id="card_expiry" name="card_expiry" class="form-input" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="card_cvc">CVC / CVV</label>
                                <input type="text" id="card_cvc" name="card_cvc" class="form-input" placeholder="123" maxlength="4" required>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.5rem; justify-content: center; margin-bottom: 1.5rem; font-size: 0.75rem; color: var(--text-muted);">
                            <span><i class="fa-solid fa-lock" style="color: var(--success);"></i> Secure 256-bit SSL encrypted checkout</span>
                            <span>&bull;</span>
                            <span>Supports Visa, Mastercard, AMEX</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-circle-check"></i> Process Payment & Submit Release
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Payment Receipt Panel -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-receipt"></i> Payment & Distribution Information</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                        <div>
                            <h4 style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.5rem;">Distribution Status</h4>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                @if($release->distribution_status === 'pending')
                                    <span class="badge badge-pending" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">Under Administrator Review</span>
                                @elseif($release->distribution_status === 'approved')
                                    <span class="badge badge-approved" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">Approved (Awaiting Platform Ingestion)</span>
                                @elseif($release->distribution_status === 'distributed')
                                    <span class="badge badge-distributed" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">Successfully Distributed</span>
                                @elseif($release->distribution_status === 'rejected')
                                    <span class="badge badge-rejected" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">Rejected / Corrections Needed</span>
                                @elseif($release->distribution_status === 'pending_takedown')
                                    <span class="badge badge-rejected" style="font-size: 0.85rem; padding: 0.35rem 0.75rem; background-color: rgba(239, 68, 68, 0.1); color: var(--danger);">Takedown Pending Review</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 0.5rem;">Billing & Invoices</h4>
                            <div>
                                @if($release->billing_status === 'paid')
                                    @php
                                        $payment = $release->payments()->where('status', 'completed')->first();
                                    @endphp
                                    <span style="font-size: 0.9rem; color: var(--success); font-weight: 600; display: block; margin-bottom: 0.35rem;">
                                        <i class="fa-solid fa-circle-check"></i> Paid (${{ number_format($release->price_paid, 2) }})
                                    </span>
                                    @if($payment)
                                        <a href="{{ route('finance.invoice', $payment->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-file-invoice-dollar"></i> View Invoice ({{ $payment->invoice_number }})
                                        </a>
                                    @else
                                        <small style="color: var(--text-muted);">Exempt via Premium Subscription plan.</small>
                                    @endif
                                @else
                                    <span style="font-size: 0.9rem; color: var(--warning); font-weight: 600;">
                                        <i class="fa-solid fa-clock"></i> Exempt (Subscribed)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

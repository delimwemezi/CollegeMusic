@extends('layouts.app')

@section('title', 'Artist Verifications')
@section('header_title', 'Artist Verification Queue')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Identity Verification Queue</h1>
        <p class="page-subtitle">Inspect submitted identification documents and verify artist profiles for listing eligibility</p>
    </div>
    <div>
        <a href="{{ route('admin') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Console
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Verification Request Records</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($artists->isEmpty())
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No artist verification records found.</p>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Artist Stage Name</th>
                            <th>Contact Info</th>
                            <th>Associated User Email</th>
                            <th>Uploaded Docs</th>
                            <th>Verification Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($artists as $artist)
                            <tr>
                                <td style="font-weight: bold;">{{ $artist->name }}</td>
                                <td>{{ $artist->contact_info ?? 'Not provided' }}</td>
                                <td>{{ $artist->user ? $artist->user->email : 'Label Created Profile' }}</td>
                                <td style="vertical-align: middle;">
                                    @php
                                        $docs = $artist->verification_documents ?? [];
                                    @endphp
                                    @forelse($docs as $doc)
                                        <div style="margin-bottom: 0.25rem;">
                                            @if(isset($doc['type']) && $doc['type'] === 'Email Verification')
                                                <span style="font-size: 0.8rem; color: var(--primary); display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 500;">
                                                    <i class="fa-solid fa-envelope-circle-check" style="color: var(--success);"></i> Email Verified ({{ $doc['email'] ?? '' }})
                                                </span>
                                            @else
                                                <a href="{{ asset('storage/' . ($doc['path'] ?? '')) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                    <i class="fa-solid fa-file-pdf"></i> View {{ $doc['type'] ?? 'Doc' }}
                                                </a>
                                            @endif
                                        </div>
                                    @empty
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No documents uploaded</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($artist->verification_status === 'verified')
                                        <span class="badge badge-approved">Verified Account</span>
                                    @elseif($artist->verification_status === 'pending')
                                        <span class="badge badge-pending">Review Pending</span>
                                    @else
                                        <span class="badge badge-rejected">Unverified / Rejected</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($artist->verification_status === 'pending')
                                        <div style="display: inline-flex; gap: 0.4rem;">
                                            <form action="{{ route('admin.artists.verify', $artist->id) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa-solid fa-user-check"></i> Approve
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.artists.verify', $artist->id) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger btn-sm" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
                                                    <i class="fa-solid fa-user-xmark"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Reviewed</span>
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

<div style="margin-top: 1.5rem;">
    {{ $artists->links() }}
</div>
@endsection

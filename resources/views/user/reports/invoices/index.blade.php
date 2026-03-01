@extends('layouts.app')

@section('title', 'Invoice Report')

@section('content')

<div class="sc-report-wrap">

    {{-- ══════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════ --}}
    <div class="sc-page-header">
        <div class="sc-page-header__inner">
            <div class="sc-page-header__left">
                <div class="sc-breadcrumb">Reports &rsaquo; Invoices</div>
                <h1 class="sc-page-title">
                    <span class="sc-page-title__icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </span>
                    Invoice Report
                </h1>
                <p class="sc-page-sub">Financial overview of all customer invoices &amp; payments</p>
            </div>
            <div class="sc-page-header__right">
                <div class="sc-export-group">
                    <span class="sc-export-label">Export as</span>
                    <div class="sc-export-btns">
                        <a href="{{ route('reports.invoices.pdf', request()->all()) }}" class="sc-btn sc-btn--pdf" target="_blank">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            PDF
                        </a>
                        <a href="{{ route('reports.invoices.csv', request()->all()) }}" class="sc-btn sc-btn--csv">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            CSV
                        </a>
                        <a href="{{ route('reports.invoices.excel', request()->all()) }}" class="sc-btn sc-btn--excel">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                            Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         FILTER PANEL
    ══════════════════════════════════════ --}}
    <div class="sc-filter-card">
        <div class="sc-filter-card__header" id="filterToggle">
            <div class="sc-filter-card__title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter Results
            </div>
            <div class="sc-filter-card__toggle">
                @php
                    $activeFilters = collect(['from_date','to_date','customer_id','invoice_number'])
                        ->filter(fn($k) => request()->filled($k))->count();
                @endphp
                @if($activeFilters > 0)
                    <span class="sc-filter-badge">{{ $activeFilters }} active</span>
                @endif
                <svg class="sc-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        <div class="sc-filter-card__body" id="filterBody">
            <form method="GET" action="{{ route('reports.invoices.index') }}" id="filterForm">
                <div class="sc-filter-grid">
                    <div class="sc-form-group">
                        <label class="sc-label">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            From Date
                        </label>
                        <input type="date" name="from_date" class="sc-input"
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="sc-form-group">
                        <label class="sc-label">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            To Date
                        </label>
                        <input type="date" name="to_date" class="sc-input"
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="sc-form-group">
                        <label class="sc-label">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Customer
                        </label>
                        <div class="sc-select-wrap">
                            <select name="customer_id" class="sc-input sc-select">
                                <option value="">All Customers</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="sc-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                    <div class="sc-form-group">
                        <label class="sc-label">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Invoice Number
                        </label>
                        <input type="text" name="invoice_number" class="sc-input"
                               placeholder="Search invoice #..."
                               value="{{ request('invoice_number') }}">
                    </div>
                </div>
                <div class="sc-filter-actions">
                    <button type="submit" class="sc-btn sc-btn--primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('reports.invoices.index') }}" class="sc-btn sc-btn--ghost">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
                        Reset
                    </a>
                    @if($activeFilters > 0)
                        <span class="sc-filter-active-note">
                            {{ $activeFilters }} filter{{ $activeFilters > 1 ? 's' : '' }} applied
                            &mdash; showing {{ $summary['total_invoices'] }} result{{ $summary['total_invoices'] != 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════ --}}
    <div class="sc-stats-grid">
        <div class="sc-stat-card sc-stat-card--blue">
            <div class="sc-stat-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="sc-stat-card__body">
                <div class="sc-stat-card__value">{{ number_format($summary['total_invoices']) }}</div>
                <div class="sc-stat-card__label">Total Customers</div>
            </div>
            <div class="sc-stat-card__bg-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
        </div>

        <div class="sc-stat-card sc-stat-card--green">
            <div class="sc-stat-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="sc-stat-card__body">
                <div class="sc-stat-card__value">
                    <small>LKR</small> {{ number_format($summary['total_final_amount'], 2) }}
                </div>
                <div class="sc-stat-card__label">Total Final Amount</div>
            </div>
            <div class="sc-stat-card__bg-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
        </div>

        <div class="sc-stat-card sc-stat-card--amber">
            <div class="sc-stat-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="sc-stat-card__body">
                <div class="sc-stat-card__value">
                    <small>LKR</small> {{ number_format($summary['total_advance_paid'], 2) }}
                </div>
                <div class="sc-stat-card__label">Total Advance Paid</div>
            </div>
            <div class="sc-stat-card__bg-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
        </div>

        <div class="sc-stat-card sc-stat-card--red">
            <div class="sc-stat-card__icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="sc-stat-card__body">
                <div class="sc-stat-card__value">
                    <small>LKR</small> {{ number_format($summary['total_due_balance'], 2) }}
                </div>
                <div class="sc-stat-card__label">Total Due Balance</div>
            </div>
            <div class="sc-stat-card__bg-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         DATA TABLE CARD
    ══════════════════════════════════════ --}}
    <div class="sc-table-card">
        <div class="sc-table-card__header">
            <div class="sc-table-card__title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg>
                Customer Invoice Details
            </div>
            <div class="sc-table-card__meta">
                Showing {{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }}
                of {{ $invoices->total() }} records
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="sc-table-wrap">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th class="sc-th--num">#</th>
                        <th>Customer Name</th>
                        <th class="sc-th--hide-sm">Invoice #</th>
                        <th class="sc-th--hide-md">Date</th>
                        <th class="sc-th--hide-sm">Email</th>
                        <th class="sc-th--hide-lg">Phone</th>
                        <th class="sc-th--hide-lg">Location</th>
                        <th class="sc-th--money">Final Amt</th>
                        <th class="sc-th--money">Advance</th>
                        <th class="sc-th--money">Due</th>
                        <th class="sc-th--status">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $index => $customer)
                        @php
                            /*
                             * $customer = InvoiceCustomer with totals attached by attachTotals()
                             * total_final_amount, total_advance_amount, due_amount
                             */
                            $finalAmt    = $customer->total_final_amount  ?? 0;
                            $advancePaid = $customer->total_advance_amount ?? 0;
                            $dueBalance  = $customer->due_amount           ?? 0;
                            $hasAdvances = $advancePaid > 0;

                            // Grab first invoice for invoice number & date
                            $firstInvoice = $customer->invoices->sortByDesc('date')->first();

                            if (!$hasAdvances) {
                                $status = 'Unpaid'; $sc = 'unpaid';
                            } elseif ($dueBalance > 0) {
                                $status = 'Pending'; $sc = 'pending';
                            } else {
                                $status = 'Paid'; $sc = 'paid';
                            }
                        @endphp
                        <tr class="sc-tr sc-tr--{{ $sc }}">
                            <td class="sc-td--num">{{ $invoices->firstItem() + $index }}</td>

                            {{-- Customer Name with Avatar --}}
                            <td>
                                <div class="sc-customer-cell">
                                    <div class="sc-avatar">{{ strtoupper(substr($customer->name ?? 'N', 0, 1)) }}</div>
                                    <div class="sc-customer-info">
                                        <span class="sc-customer-name">{{ $customer->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Invoice Number (first + badge if multiple) --}}
                            <td class="sc-td--hide-sm sc-mono">
                                @if($firstInvoice)
                                    {{ $firstInvoice->invoice_number }}
                                    @if($customer->invoices->count() > 1)
                                        <span class="sc-inv-more" title="{{ $customer->invoices->pluck('invoice_number')->join(', ') }}">
                                            +{{ $customer->invoices->count() - 1 }}
                                        </span>
                                    @endif
                                @else
                                    <span class="sc-muted">—</span>
                                @endif
                            </td>

                            {{-- Date from first invoice --}}
                            <td class="sc-td--hide-md sc-muted">
                                {{ $firstInvoice && $firstInvoice->date
                                    ? \Carbon\Carbon::parse($firstInvoice->date)->format('d M Y')
                                    : '—' }}
                            </td>

                            {{-- Email --}}
                            <td class="sc-td--hide-sm sc-muted">{{ $customer->email ?? '—' }}</td>

                            {{-- Phone --}}
                            <td class="sc-td--hide-lg sc-muted">{{ $customer->phone_number ?? '—' }}</td>

                            {{-- Location --}}
                            <td class="sc-td--hide-lg sc-muted">{{ Str::limit($customer->location ?? '—', 16) }}</td>

                            {{-- Final Amount --}}
                            <td class="sc-td--money sc-bold">{{ number_format($finalAmt, 2) }}</td>

                            {{-- Advance Paid --}}
                            <td class="sc-td--money sc-td--advance">
                                @if($hasAdvances)
                                    {{ number_format($advancePaid, 2) }}
                                @else
                                    <span class="sc-muted">—</span>
                                @endif
                            </td>

                            {{-- Due Balance --}}
                            <td class="sc-td--money {{ $dueBalance > 0 ? 'sc-td--due' : 'sc-td--clear' }}">
                                {{ $dueBalance > 0 ? number_format($dueBalance, 2) : '0.00' }}
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="sc-badge sc-badge--{{ $sc }}">{{ $status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="sc-empty">
                                <div class="sc-empty__icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                </div>
                                <div class="sc-empty__title">No invoices found</div>
                                <div class="sc-empty__sub">Try adjusting your filters or reset to see all records</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @php
                        $pageFinal   = $invoices->sum('total_final_amount');
                        $pageAdvance = $invoices->sum('total_advance_amount');
                        $pageDue     = $invoices->sum('due_amount');
                    @endphp
                    {{--
                        Columns: #(1) | Name(2) | Invoice#(3) | Date(4) | Email(5) | Phone(6) | Location(7) | FinalAmt(8) | Advance(9) | Due(10) | Status(11)
                        We match each td 1-to-1 so totals always sit under the correct column.
                    --}}
                    <tr class="sc-tfoot-page">
                        <td></td>{{-- # --}}
                        <td></td>{{-- Name --}}
                        <td></td>{{-- Invoice # --}}
                        <td></td>{{-- Date --}}
                        <td></td>{{-- Email --}}
                        <td></td>{{-- Phone --}}
                        <td class="sc-tfoot-label" style="text-align:right; white-space:nowrap; padding-right:14px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle; margin-right:4px;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                            Page Totals
                        </td>
                        <td class="sc-tfoot-val">{{ number_format($pageFinal, 2) }}</td>
                        <td class="sc-tfoot-val sc-td--advance">{{ number_format($pageAdvance, 2) }}</td>
                        <td class="sc-tfoot-val sc-td--due">{{ number_format($pageDue, 2) }}</td>
                        <td></td>{{-- Status --}}
                    </tr>
                    <tr class="sc-tfoot-all">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="sc-tfoot-label" style="text-align:right; white-space:nowrap; padding-right:14px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle; margin-right:4px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            All Filtered Totals
                        </td>
                        <td class="sc-tfoot-val">{{ number_format($summary['total_final_amount'], 2) }}</td>
                        <td class="sc-tfoot-val sc-td--advance">{{ number_format($summary['total_advance_paid'], 2) }}</td>
                        <td class="sc-tfoot-val sc-td--due">{{ number_format($summary['total_due_balance'], 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
        <div class="sc-pagination-wrap">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>

</div>{{-- /.sc-report-wrap --}}
@endsection


<style>
/* ════════════════════════════════════════════════════
   SACHIPA CURTAIN — INVOICE REPORT STYLES
   Design: refined editorial, warm burgundy brand
════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

:root {
    --brand:       #8B1A2B;
    --brand-dark:  #6B1221;
    --brand-light: #F9F0F1;
    --brand-mid:   #E8D0D3;

    --green:       #15803D;
    --green-bg:    #DCFCE7;
    --amber:       #B45309;
    --amber-bg:    #FEF3C7;
    --red:         #B91C1C;
    --red-bg:      #FEE2E2;
    --blue:        #1D4ED8;
    --blue-bg:     #DBEAFE;

    --gray-50:  #F8F9FA;
    --gray-100: #F1F3F5;
    --gray-200: #E9ECEF;
    --gray-300: #DEE2E6;
    --gray-400: #CED4DA;
    --gray-500: #ADB5BD;
    --gray-600: #6C757D;
    --gray-700: #495057;
    --gray-800: #343A40;
    --gray-900: #212529;

    --radius:   10px;
    --radius-sm: 6px;
    --radius-lg: 14px;
    --shadow:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-md:0 4px 12px rgba(0,0,0,.10), 0 1px 3px rgba(0,0,0,.06);
    --font-head: 'Lora', Georgia, serif;
    --font-body: 'DM Sans', system-ui, sans-serif;
}

.sc-inv-more {
    display: inline-block;
    margin-left: 4px;
    padding: 1px 6px;
    border-radius: 10px;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 10px;
    font-weight: 700;
    border: 1px solid var(--brand-mid);
    cursor: default;
}
/* ── Reset ── */
*, *::before, *::after { box-sizing: border-box; }

.sc-report-wrap {
    font-family: var(--font-body);
    color: var(--gray-800);
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 0 40px;
}

/* ── PAGE HEADER ── */
.sc-page-header {
    background: #fff;
    border-bottom: 1px solid var(--gray-200);
    padding: 24px 28px 20px;
    margin-bottom: 24px;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    position: relative;
    overflow: hidden;
}
.sc-page-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--brand) 0%, #E57373 100%);
}
.sc-page-header__inner {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.sc-breadcrumb {
    font-size: 11px;
    color: var(--gray-500);
    letter-spacing: .5px;
    text-transform: uppercase;
    margin-bottom: 6px;
    font-weight: 500;
}
.sc-page-title {
    font-family: var(--font-head);
    font-size: 26px;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sc-page-title__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px; height: 38px;
    background: var(--brand-light);
    border-radius: var(--radius-sm);
    color: var(--brand);
    flex-shrink: 0;
}
.sc-page-sub {
    font-size: 13px;
    color: var(--gray-500);
    margin: 0;
}
.sc-export-group { text-align: right; }
.sc-export-label {
    display: block;
    font-size: 11px;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 8px;
    font-weight: 500;
}
.sc-export-btns { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }

/* ── BUTTONS ── */
.sc-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .18s ease;
    white-space: nowrap;
}
.sc-btn--pdf   { background: #FEE2E2; color: var(--red);   border: 1px solid #FECACA; }
.sc-btn--pdf:hover   { background: #FECACA; }
.sc-btn--csv   { background: #DCFCE7; color: var(--green); border: 1px solid #BBF7D0; }
.sc-btn--csv:hover   { background: #BBF7D0; }
.sc-btn--excel { background: #DBEAFE; color: var(--blue);  border: 1px solid #BFDBFE; }
.sc-btn--excel:hover { background: #BFDBFE; }
.sc-btn--primary {
    background: var(--brand);
    color: #fff;
    border: 1px solid var(--brand);
}
.sc-btn--primary:hover { background: var(--brand-dark); }
.sc-btn--ghost {
    background: #fff;
    color: var(--gray-600);
    border: 1px solid var(--gray-300);
}
.sc-btn--ghost:hover { background: var(--gray-50); color: var(--gray-800); }

/* ── FILTER CARD ── */
.sc-filter-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin: 0 0 24px;
    overflow: hidden;
}
.sc-filter-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    cursor: pointer;
    user-select: none;
    border-bottom: 1px solid var(--gray-100);
    transition: background .15s;
}
.sc-filter-card__header:hover { background: var(--gray-50); }
.sc-filter-card__title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    color: var(--gray-800);
}
.sc-filter-card__toggle {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sc-filter-badge {
    font-size: 11px;
    font-weight: 700;
    background: var(--brand);
    color: #fff;
    border-radius: 20px;
    padding: 2px 9px;
    letter-spacing: .2px;
}
.sc-chevron {
    transition: transform .25s ease;
    color: var(--gray-500);
}
.sc-chevron.open { transform: rotate(180deg); }

.sc-filter-card__body {
    padding: 20px;
    display: none;
}
.sc-filter-card__body.open { display: block; }

.sc-filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}
.sc-form-group { display: flex; flex-direction: column; gap: 6px; }
.sc-label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: .4px;
}
.sc-input {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 13.5px;
    color: var(--gray-800);
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
    appearance: none;
    -webkit-appearance: none;
}
.sc-input:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(139,26,43,.1);
}
.sc-select-wrap { position: relative; }
.sc-select { padding-right: 32px; }
.sc-select-arrow {
    position: absolute;
    right: 10px; top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: var(--gray-500);
}
.sc-filter-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.sc-filter-active-note {
    font-size: 12px;
    color: var(--gray-500);
    font-style: italic;
    margin-left: 4px;
}

/* ── STATS GRID ── */
.sc-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.sc-stat-card {
    position: relative;
    border-radius: var(--radius);
    padding: 20px 20px 18px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    overflow: hidden;
    box-shadow: var(--shadow);
}
.sc-stat-card--blue  { background: var(--blue-bg);  border: 1px solid #BFDBFE; }
.sc-stat-card--green { background: var(--green-bg); border: 1px solid #BBF7D0; }
.sc-stat-card--amber { background: var(--amber-bg); border: 1px solid #FDE68A; }
.sc-stat-card--red   { background: var(--red-bg);   border: 1px solid #FECACA; }

.sc-stat-card__icon {
    width: 44px; height: 44px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sc-stat-card--blue  .sc-stat-card__icon { background: rgba(29,78,216,.12); color: var(--blue); }
.sc-stat-card--green .sc-stat-card__icon { background: rgba(21,128,61,.12); color: var(--green); }
.sc-stat-card--amber .sc-stat-card__icon { background: rgba(180,83,9,.12); color: var(--amber); }
.sc-stat-card--red   .sc-stat-card__icon { background: rgba(185,28,28,.12); color: var(--red); }

.sc-stat-card__body { z-index: 1; position: relative; }
.sc-stat-card__value {
    font-family: var(--font-head);
    font-size: 22px;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 4px;
    word-break: break-all;
}
.sc-stat-card--blue  .sc-stat-card__value { color: var(--blue); }
.sc-stat-card--green .sc-stat-card__value { color: var(--green); }
.sc-stat-card--amber .sc-stat-card__value { color: var(--amber); }
.sc-stat-card--red   .sc-stat-card__value { color: var(--red); }
.sc-stat-card__value small { font-size: 12px; font-weight: 500; opacity: .7; }
.sc-stat-card__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: .5px;
}
.sc-stat-card__bg-icon {
    position: absolute;
    right: -8px; bottom: -8px;
    opacity: .07;
    pointer-events: none;
}
.sc-stat-card--blue  .sc-stat-card__bg-icon { color: var(--blue); }
.sc-stat-card--green .sc-stat-card__bg-icon { color: var(--green); }
.sc-stat-card--amber .sc-stat-card__bg-icon { color: var(--amber); }
.sc-stat-card--red   .sc-stat-card__bg-icon { color: var(--red); }

/* ── TABLE CARD ── */
.sc-table-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.sc-table-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-100);
    background: var(--gray-50);
    flex-wrap: wrap;
    gap: 8px;
}
.sc-table-card__title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 14px;
    color: var(--gray-800);
}
.sc-table-card__meta {
    font-size: 12px;
    color: var(--gray-500);
}
.sc-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

/* ── TABLE ── */
.sc-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}
.sc-table thead tr {
    background: var(--gray-800);
}
.sc-table thead th {
    padding: 11px 14px;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: .5px;
    white-space: nowrap;
    text-align: left;
    border: none;
}
.sc-th--money, .sc-td--money { text-align: right; }
.sc-th--num, .sc-td--num { text-align: center; }
.sc-th--status { text-align: center; }

.sc-table tbody tr {
    border-bottom: 1px solid var(--gray-100);
    transition: background .12s;
}
.sc-table tbody tr:hover { background: var(--gray-50); }
.sc-table tbody tr:last-child { border-bottom: none; }

/* Status row indicator */
.sc-tr--paid    { border-left: 3px solid #22C55E; }
.sc-tr--pending { border-left: 3px solid #F59E0B; }
.sc-tr--unpaid  { border-left: 3px solid #EF4444; }

.sc-table tbody td {
    padding: 11px 14px;
    font-size: 13px;
    color: var(--gray-700);
    vertical-align: middle;
}
.sc-td--money { font-variant-numeric: tabular-nums; font-size: 12.5px; }
.sc-td--advance { color: var(--green); font-weight: 600; }
.sc-td--due     { color: var(--red);   font-weight: 700; }
.sc-td--clear   { color: var(--green); }
.sc-bold { font-weight: 700; color: var(--gray-900); }
.sc-mono { font-family: 'DM Mono', monospace, var(--font-body); font-size: 12px; color: var(--gray-600); }
.sc-muted { color: var(--gray-500); font-size: 12px; }

/* Customer cell */
.sc-customer-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sc-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 12px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    border: 1.5px solid var(--brand-mid);
}
.sc-customer-name { font-weight: 600; font-size: 13px; color: var(--gray-900); }

/* ── STATUS BADGES ── */
.sc-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.sc-badge--paid    { background: var(--green-bg); color: var(--green); border: 1px solid #BBF7D0; }
.sc-badge--pending { background: var(--amber-bg); color: var(--amber); border: 1px solid #FDE68A; }
.sc-badge--unpaid  { background: var(--red-bg);   color: var(--red);   border: 1px solid #FECACA; }

/* ── TFOOT ── */
.sc-table tfoot tr.sc-tfoot-page { background: var(--gray-100); }
.sc-table tfoot tr.sc-tfoot-all  { background: var(--gray-800); }
.sc-table tfoot td {
    padding: 10px 14px;
    font-size: 12.5px;
    border: none;
}
.sc-tfoot-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.sc-tfoot-page .sc-tfoot-label { color: var(--gray-600); }
.sc-tfoot-all  .sc-tfoot-label { color: #aaa; }
.sc-tfoot-label { display: flex; align-items: center; gap: 6px; justify-content: flex-end; }
.sc-tfoot-val {
    text-align: right;
    font-variant-numeric: tabular-nums;
    font-weight: 700;
}
.sc-tfoot-page .sc-tfoot-val { color: var(--gray-800); }
.sc-tfoot-all  .sc-tfoot-val { color: #fff; }
.sc-tfoot-page .sc-td--advance { color: var(--green); }
.sc-tfoot-page .sc-td--due     { color: var(--red); }
.sc-tfoot-all  .sc-td--advance { color: #6EE7B7; }
.sc-tfoot-all  .sc-td--due     { color: #FCA5A5; }

/* ── EMPTY STATE ── */
.sc-empty {
    text-align: center;
    padding: 60px 20px !important;
}
.sc-empty__icon { color: var(--gray-300); margin-bottom: 14px; }
.sc-empty__title { font-size: 15px; font-weight: 600; color: var(--gray-600); margin-bottom: 6px; }
.sc-empty__sub   { font-size: 13px; color: var(--gray-400); }

/* ── PAGINATION ── */
.sc-pagination-wrap {
    padding: 14px 20px;
    border-top: 1px solid var(--gray-100);
    background: var(--gray-50);
    display: flex;
    justify-content: center;
}
.sc-pagination-wrap nav { display: flex; }
.sc-pagination-wrap .pagination {
    display: flex; gap: 4px; margin: 0;
    list-style: none; padding: 0;
}
.sc-pagination-wrap .page-item .page-link {
    display: flex; align-items: center; justify-content: center;
    width: 34px; height: 34px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--gray-200);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    text-decoration: none;
    transition: all .15s;
    background: #fff;
    font-family: var(--font-body);
}
.sc-pagination-wrap .page-item .page-link:hover {
    background: var(--brand-light);
    border-color: var(--brand-mid);
    color: var(--brand);
}
.sc-pagination-wrap .page-item.active .page-link {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
}
.sc-pagination-wrap .page-item.disabled .page-link {
    background: var(--gray-50);
    color: var(--gray-400);
    pointer-events: none;
}

/* ══════════════════════════════════════
   RESPONSIVE BREAKPOINTS
══════════════════════════════════════ */
@media (max-width: 1200px) {
    .sc-th--hide-lg, .sc-td--hide-lg { display: none; }
}
@media (max-width: 992px) {
    .sc-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .sc-filter-grid { grid-template-columns: repeat(2, 1fr); }
    .sc-th--hide-md, .sc-td--hide-md { display: none; }
}
@media (max-width: 768px) {
    .sc-page-header { padding: 18px 16px; border-radius: var(--radius); }
    .sc-page-title { font-size: 20px; }
    .sc-page-header__inner { flex-direction: column; align-items: flex-start; }
    .sc-export-btns { justify-content: flex-start; }
    .sc-export-label { display: none; }
    .sc-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .sc-stat-card { padding: 14px; gap: 10px; }
    .sc-stat-card__value { font-size: 16px; }
    .sc-stat-card__bg-icon { display: none; }
    .sc-filter-grid { grid-template-columns: 1fr; }
    .sc-th--hide-sm, .sc-td--hide-sm { display: none; }
    .sc-table { min-width: 600px; }
}
@media (max-width: 480px) {
    .sc-report-wrap { padding-bottom: 20px; }
    .sc-stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
    .sc-stat-card__value { font-size: 14px; }
    .sc-stat-card__label { font-size: 10px; }
    .sc-page-title { font-size: 18px; }
    .sc-btn { font-size: 12px; padding: 7px 12px; }
}
</style>



<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Filter panel toggle ──
    const filterToggle = document.getElementById('filterToggle');
    const filterBody   = document.getElementById('filterBody');
    const chevron      = filterToggle.querySelector('.sc-chevron');

    // Auto-open if filters are active
    const hasActive = document.querySelector('.sc-filter-badge');
    if (hasActive) {
        filterBody.classList.add('open');
        chevron.classList.add('open');
    }

    filterToggle.addEventListener('click', function () {
        filterBody.classList.toggle('open');
        chevron.classList.toggle('open');
    });

    // ── Date validation: from_date ≤ to_date ──
    const fromDate = document.querySelector('[name="from_date"]');
    const toDate   = document.querySelector('[name="to_date"]');

    if (fromDate && toDate) {
        fromDate.addEventListener('change', function () {
            if (toDate.value && this.value > toDate.value) {
                toDate.value = this.value;
            }
        });
        toDate.addEventListener('change', function () {
            if (fromDate.value && this.value < fromDate.value) {
                fromDate.value = this.value;
            }
        });
    }

    // ── Animate stat cards on load ──
    const cards = document.querySelectorAll('.sc-stat-card');
    cards.forEach(function (card, i) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(12px)';
        card.style.transition = 'opacity .35s ease, transform .35s ease';
        setTimeout(function () {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 80 + i * 60);
    });

    // ── Animate table rows ──
    const rows = document.querySelectorAll('.sc-tr');
    rows.forEach(function (row, i) {
        row.style.opacity = '0';
        row.style.transition = 'opacity .25s ease';
        setTimeout(function () {
            row.style.opacity = '1';
        }, 250 + i * 30);
    });

});
</script>

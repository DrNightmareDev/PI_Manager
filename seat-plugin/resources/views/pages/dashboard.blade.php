@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.dashboard.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.dashboard.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    @php
        $filters = $dashboard['filters'] ?? [];
        $activeStatuses = $filters['statuses'] ?? [];
        $activeTiers = $filters['tiers'] ?? [];
    @endphp

    <div class="pi-shell">
        <div class="pi-grid pi-grid--stats">
            <div class="pi-stat">
                <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.dashboard.character_count') }}</div>
                <div class="pi-stat__value">{{ $dashboard['summary']['character_count'] }}</div>
                <div class="pi-stat__meta">{{ trans('seat-pi-manager::messages.pages.dashboard.character') }}</div>
            </div>
            <div class="pi-stat">
                <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.dashboard.colony_count') }}</div>
                <div class="pi-stat__value">{{ $dashboard['summary']['colony_count'] }}</div>
                <div class="pi-stat__meta">{{ trans('seat-pi-manager::messages.pages.dashboard.table_title') }}</div>
            </div>
            <div class="pi-stat">
                <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.dashboard.extractor_count') }}</div>
                <div class="pi-stat__value">{{ $dashboard['summary']['extractor_count'] }}</div>
                <div class="pi-stat__meta">{{ trans('seat-pi-manager::messages.pages.dashboard.extractors') }}</div>
            </div>
            <div class="pi-stat">
                <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.dashboard.next_expiry') }}</div>
                <div class="pi-stat__value">{{ $dashboard['summary']['next_expiry_human'] ?? '-' }}</div>
                <div class="pi-stat__meta">{{ trans('seat-pi-manager::messages.pages.dashboard.expiry') }}</div>
            </div>
        </div>

        <section class="pi-panel">
            <div class="pi-panel__body--slim">
                @php
                    $statusHints = [
                        'active'  => trans('seat-pi-manager::messages.pages.dashboard.status_active_hint'),
                        'expired' => trans('seat-pi-manager::messages.pages.dashboard.status_expired_hint'),
                        'stalled' => trans('seat-pi-manager::messages.pages.dashboard.status_stalled_hint'),
                    ];
                @endphp
                <form method="get" action="{{ route('seat-pi-manager.dashboard') }}" class="pi-filter-bar">

                    <select class="form-select pi-filter-bar__select" name="character"
                            title="{{ trans('seat-pi-manager::messages.pages.dashboard.character_filter') }}">
                        <option value="">{{ trans('seat-pi-manager::messages.common.all') }}</option>
                        @foreach($dashboard['characters'] as $characterName)
                            <option value="{{ $characterName }}" @selected(($filters['character'] ?? '') === $characterName)>{{ $characterName }}</option>
                        @endforeach
                    </select>

                    <div class="pi-filter-bar__sep"></div>

                    <div class="pi-filter-bar__group">
                        @foreach(['active', 'expired', 'stalled'] as $status)
                            <label class="pi-pill-toggle pi-pill-toggle--sm pi-pill-toggle--{{ $status }} @if(in_array($status, $activeStatuses, true)) is-active @endif"
                                   title="{{ $statusHints[$status] }}">
                                <input type="checkbox" class="d-none" name="status[]" value="{{ $status }}" @checked(in_array($status, $activeStatuses, true))>
                                {{ trans('seat-pi-manager::messages.status.' . $status) }}
                                <span class="pi-pill-count">{{ $dashboard['summary']['status_counts'][$status] ?? 0 }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="pi-filter-bar__sep"></div>

                    <div class="pi-filter-bar__group">
                        @foreach(['P1', 'P2', 'P3', 'P4'] as $tier)
                            <label class="pi-pill-toggle pi-pill-toggle--sm @if(in_array($tier, $activeTiers, true)) is-active @endif">
                                <input type="checkbox" class="d-none" name="tier[]" value="{{ $tier }}" @checked(in_array($tier, $activeTiers, true))>
                                {{ $tier }}
                            </label>
                        @endforeach
                    </div>

                    <div class="pi-filter-bar__group ms-auto">
                        <a href="{{ route('seat-pi-manager.dashboard') }}" class="btn btn-sm btn-light">{{ trans('seat-pi-manager::messages.common.reset') }}</a>
                        <button type="submit" class="btn btn-sm btn-primary">{{ trans('seat-pi-manager::messages.common.apply') }}</button>
                        <a href="{{ route('seat-pi-manager.system-analyzer') }}" class="btn btn-sm btn-outline-secondary">{{ trans('seat-pi-manager::messages.pages.dashboard.open_analyzer') }}</a>
                    </div>

                </form>
            </div>
        </section>

        <section class="pi-panel">
            <div class="pi-panel__header">
                <div>
                    <h2 class="pi-panel__title">
                        <i class="fas fa-sitemap text-info"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.dashboard.table_title') }}</span>
                    </h2>
                    <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.dashboard.table_subtitle') }}</p>
                </div>
                <span class="pi-chip pi-chip--soft">{{ count($dashboard['colonies']) }} {{ trans('seat-pi-manager::messages.pages.dashboard.colony_count') }}</span>
            </div>
            <div class="pi-panel__body p-0">
                @if(count($dashboard['colonies']) === 0)
                    <div class="p-4">
                        <div class="pi-empty">{{ trans('seat-pi-manager::messages.pages.dashboard.empty_state') }}</div>
                    </div>
                @else
                    <div class="table-responsive pi-table-wrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.character') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.location') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.fields.planet_type') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.status') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.production_title') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.expiry') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboard['colonies'] as $colony)
                                    <tr>
                                        <td style="min-width: 260px;">
                                            <div class="fw-semibold">{{ $colony['character_name'] }}</div>
                                            <div class="pi-muted small">{{ $colony['corporation_name'] ?? '-' }}</div>
                                            <div class="pi-muted small">{{ $colony['alliance_name'] ?? '-' }}</div>
                                        </td>
                                        <td style="min-width: 250px;">
                                            <div class="fw-semibold">{{ $colony['system_name'] }}</div>
                                            <div class="pi-muted small">{{ $colony['planet_name'] }}@if($colony['planet_number']) / {{ $colony['planet_number'] }}@endif</div>
                                            <div class="pi-muted small">{{ $colony['region_name'] ?? '-' }}</div>
                                        </td>
                                        <td style="min-width: 150px;">
                                            <span class="pi-chip" style="background-color: {{ $colony['planet_color'] }}15; border-color: {{ $colony['planet_color'] }}50; color: {{ $colony['planet_color'] }};">{{ $colony['planet_type'] ?? '-' }}</span>
                                            <div class="mt-2">
                                                <span class="pi-chip pi-chip--soft">{{ $colony['highest_tier'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td style="min-width: 150px;">
                                            <span class="badge @if($colony['status'] === 'active') bg-success @elseif($colony['status'] === 'expired') bg-danger @else bg-warning text-dark @endif">{{ trans('seat-pi-manager::messages.status.' . $colony['status']) }}</span>
                                            <div class="pi-muted small mt-2">{{ $colony['extractor_count'] }} {{ trans('seat-pi-manager::messages.pages.dashboard.extractors') }} / {{ $colony['factory_count'] }} {{ trans('seat-pi-manager::messages.pages.dashboard.factories') }}</div>
                                        </td>
                                        <td style="min-width: 340px;">
                                            @if(count($colony['all_product_names']) > 0)
                                                <div class="pi-chip-row mb-2">
                                                    @foreach(array_slice($colony['all_product_names'], 0, 5) as $name)
                                                        <span class="pi-chip pi-chip--soft">{{ $name }}</span>
                                                    @endforeach
                                                    @if(count($colony['all_product_names']) > 5)
                                                        <span class="pi-chip">+{{ count($colony['all_product_names']) - 5 }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="pi-muted small mb-2">-</div>
                                            @endif
                                            <div class="pi-metric-line">
                                                <span>{{ trans('seat-pi-manager::messages.pages.dashboard.u_per_hour') }}</span>
                                                <strong>{{ number_format((float) $colony['total_u_per_hour'], 2) }}</strong>
                                            </div>
                                        </td>
                                        <td style="min-width: 180px;">
                                            <div class="fw-semibold">{{ $colony['next_expiry_human'] ?? '-' }}</div>
                                            <div class="pi-muted small">
                                                @if($colony['has_single_planet_viable_products'])
                                                    {{ count($colony['single_planet_products']) }} {{ trans('seat-pi-manager::messages.pages.dashboard.single_planet_ready') }}
                                                @else
                                                    {{ trans('seat-pi-manager::messages.pages.dashboard.single_planet_empty') }}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

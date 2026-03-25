@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.dashboard.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.dashboard.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    <div class="pi-shell">
        <section class="pi-hero">
            <div class="pi-hero__body">
                <div class="pi-kicker">
                    <i class="fas fa-table"></i>
                    <span>{{ trans('seat-pi-manager::messages.pages.dashboard.title') }}</span>
                </div>
                <h1 class="pi-title">{{ trans('seat-pi-manager::messages.pages.dashboard.header') }}</h1>
                <p class="pi-subtitle">{{ trans('seat-pi-manager::messages.pages.dashboard.subtitle') }}</p>
            </div>
        </section>

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
                <div class="pi-stat__value">{{ $dashboard['summary']['next_expiry_human'] ?? '—' }}</div>
                <div class="pi-stat__meta">{{ trans('seat-pi-manager::messages.pages.dashboard.expiry') }}</div>
            </div>
        </div>

        <div class="pi-grid pi-grid--two">
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-filter text-primary"></i>
                        <span>{{ trans('seat-pi-manager::messages.common.apply') }} {{ trans('seat-pi-manager::messages.pages.dashboard.title') }}</span>
                    </h2>
                    <a href="{{ route('seat-pi-manager.system-analyzer') }}" class="btn btn-sm btn-outline-primary">{{ trans('seat-pi-manager::messages.pages.dashboard.open_analyzer') }}</a>
                </div>
                <div class="pi-panel__body">
                    <form method="get" action="{{ route('seat-pi-manager.dashboard') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ trans('seat-pi-manager::messages.pages.dashboard.character_filter') }}</label>
                            <select class="form-select" name="character">
                                <option value="">{{ trans('seat-pi-manager::messages.common.all') }}</option>
                                @foreach($dashboard['characters'] as $characterName)
                                    <option value="{{ $characterName }}" @selected(($dashboard['filters']['character'] ?? '') === $characterName)>{{ $characterName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ trans('seat-pi-manager::messages.pages.dashboard.status_filter') }}</label>
                            <div class="pi-chip-row">
                                @foreach(['active', 'expired', 'stalled'] as $status)
                                    <label class="pi-chip">
                                        <input type="checkbox" class="mr-1" name="status[]" value="{{ $status }}" @checked(in_array($status, $dashboard['filters']['statuses'] ?? [], true))>
                                        {{ trans('seat-pi-manager::messages.status.' . $status) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ trans('seat-pi-manager::messages.pages.dashboard.tier_filter') }}</label>
                            <div class="pi-chip-row">
                                @foreach(['P1', 'P2', 'P3', 'P4'] as $tier)
                                    <label class="pi-chip">
                                        <input type="checkbox" class="mr-1" name="tier[]" value="{{ $tier }}" @checked(in_array($tier, $dashboard['filters']['tiers'] ?? [], true))>
                                        {{ $tier }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('seat-pi-manager.dashboard') }}" class="btn btn-light">{{ trans('seat-pi-manager::messages.common.reset') }}</a>
                            <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.common.apply') }}</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-traffic-light text-success"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.dashboard.status') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-grid pi-grid--stats">
                        @foreach(['active', 'expired', 'stalled'] as $status)
                            <div class="pi-list-card">
                                <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.status.' . $status) }}</div>
                                <div class="pi-stat__value">{{ $dashboard['summary']['status_counts'][$status] ?? 0 }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        <section class="pi-panel">
            <div class="pi-panel__header">
                <h2 class="pi-panel__title">
                    <i class="fas fa-sitemap text-info"></i>
                    <span>{{ trans('seat-pi-manager::messages.pages.dashboard.table_title') }}</span>
                </h2>
            </div>
            <div class="pi-panel__body p-0">
                @if(count($dashboard['colonies']) === 0)
                    <div class="p-4 pi-muted">{{ trans('seat-pi-manager::messages.pages.dashboard.empty_state') }}</div>
                @else
                    <div class="table-responsive pi-table-wrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.character') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.affiliation') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.location') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.fields.planet_type') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.fields.tier') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.extractors') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.factories') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.products') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.u_per_hour') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.expiry') }}</th>
                                    <th>{{ trans('seat-pi-manager::messages.pages.dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboard['colonies'] as $colony)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $colony['character_name'] }}</div>
                                            <div class="pi-muted small">#{{ $colony['character_id'] }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $colony['corporation_name'] ?? '—' }}</div>
                                            <div class="pi-muted small">{{ $colony['alliance_name'] ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $colony['system_name'] }}</div>
                                            <div class="pi-muted small">{{ $colony['planet_name'] }} @if($colony['planet_number']) ({{ $colony['planet_number'] }}) @endif</div>
                                        </td>
                                        <td><span class="pi-chip" style="background-color: {{ $colony['planet_color'] }}15; border-color: {{ $colony['planet_color'] }}50; color: {{ $colony['planet_color'] }};">{{ $colony['planet_type'] ?? '—' }}</span></td>
                                        <td>{{ $colony['highest_tier'] ?? '—' }}</td>
                                        <td>{{ $colony['extractor_count'] }}</td>
                                        <td>{{ $colony['factory_count'] }}</td>
                                        <td>
                                            @if(count($colony['all_product_names']) > 0)
                                                <div class="pi-chip-row">
                                                    @foreach(array_slice($colony['all_product_names'], 0, 4) as $name)
                                                        <span class="pi-chip pi-chip--soft">{{ $name }}</span>
                                                    @endforeach
                                                    @if(count($colony['all_product_names']) > 4)
                                                        <span class="pi-chip">+{{ count($colony['all_product_names']) - 4 }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="pi-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format((float) $colony['total_u_per_hour'], 2) }}</td>
                                        <td>{{ $colony['next_expiry_human'] ?? '—' }}</td>
                                        <td><span class="badge @if($colony['status'] === 'active') bg-success @elseif($colony['status'] === 'expired') bg-danger @else bg-warning text-dark @endif">{{ trans('seat-pi-manager::messages.status.' . $colony['status']) }}</span></td>
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

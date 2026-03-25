@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.dashboard.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.dashboard.header'))

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1">{{ trans('seat-pi-manager::messages.pages.dashboard.header') }}</h4>
                        <div class="text-muted small">{{ trans('seat-pi-manager::messages.pages.dashboard.subtitle') }}</div>
                    </div>
                    <a href="{{ route('seat-pi-manager.system-analyzer') }}" class="btn btn-sm btn-outline-primary">{{ trans('seat-pi-manager::messages.pages.dashboard.open_analyzer') }}</a>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('seat-pi-manager.dashboard') }}" class="row g-2 align-items-end">
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
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['active', 'expired', 'stalled'] as $status)
                                    <label class="btn btn-outline-secondary btn-sm">
                                        <input type="checkbox" class="d-none" name="status[]" value="{{ $status }}" @checked(in_array($status, $dashboard['filters']['statuses'] ?? [], true))>
                                        {{ trans('seat-pi-manager::messages.status.' . $status) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ trans('seat-pi-manager::messages.pages.dashboard.tier_filter') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['P1', 'P2', 'P3', 'P4'] as $tier)
                                    <label class="btn btn-outline-secondary btn-sm">
                                        <input type="checkbox" class="d-none" name="tier[]" value="{{ $tier }}" @checked(in_array($tier, $dashboard['filters']['tiers'] ?? [], true))>
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
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ trans('seat-pi-manager::messages.pages.dashboard.character_count') }}</div><div class="display-6">{{ $dashboard['summary']['character_count'] }}</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ trans('seat-pi-manager::messages.pages.dashboard.colony_count') }}</div><div class="display-6">{{ $dashboard['summary']['colony_count'] }}</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ trans('seat-pi-manager::messages.pages.dashboard.extractor_count') }}</div><div class="display-6">{{ $dashboard['summary']['extractor_count'] }}</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ trans('seat-pi-manager::messages.pages.dashboard.next_expiry') }}</div><div class="fw-bold fs-4">{{ $dashboard['summary']['next_expiry_human'] ?? '—' }}</div></div></div></div>
    </div>

    <div class="row g-3 mb-3">
        @foreach(['active', 'expired', 'stalled'] as $status)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ trans('seat-pi-manager::messages.status.' . $status) }}</div>
                        <div class="display-6">{{ $dashboard['summary']['status_counts'][$status] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">{{ trans('seat-pi-manager::messages.pages.dashboard.table_title') }}</div>
        <div class="card-body p-0">
            @if(count($dashboard['colonies']) === 0)
                <div class="p-4 text-muted">{{ trans('seat-pi-manager::messages.pages.dashboard.empty_state') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0">
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
                                    <td><div class="fw-semibold">{{ $colony['character_name'] }}</div><div class="text-muted small">#{{ $colony['character_id'] }}</div></td>
                                    <td><div>{{ $colony['corporation_name'] ?? '—' }}</div><div class="text-muted small">{{ $colony['alliance_name'] ?? '—' }}</div></td>
                                    <td><div>{{ $colony['system_name'] }}</div><div class="text-muted small">{{ $colony['planet_name'] }} @if($colony['planet_number']) ({{ $colony['planet_number'] }}) @endif</div></td>
                                    <td><span class="badge" style="background-color: {{ $colony['planet_color'] }};">{{ $colony['planet_type'] ?? '—' }}</span></td>
                                    <td>{{ $colony['highest_tier'] ?? '—' }}</td>
                                    <td>{{ $colony['extractor_count'] }}</td>
                                    <td>{{ $colony['factory_count'] }}</td>
                                    <td>
                                        @if(count($colony['all_product_names']) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(array_slice($colony['all_product_names'], 0, 4) as $name)
                                                    <span class="badge bg-light text-dark">{{ $name }}</span>
                                                @endforeach
                                                @if(count($colony['all_product_names']) > 4)
                                                    <span class="badge bg-secondary">+{{ count($colony['all_product_names']) - 4 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
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
    </div>
@endsection

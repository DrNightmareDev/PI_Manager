@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.system_analyzer.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.system_analyzer.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    <div class="pi-shell">
        <section class="pi-hero">
            <div class="pi-hero__body">
                <div class="pi-kicker">
                    <i class="fas fa-globe-europe"></i>
                    <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.title') }}</span>
                </div>
                <h1 class="pi-title">{{ trans('seat-pi-manager::messages.pages.system_analyzer.header') }}</h1>
                <p class="pi-subtitle">{{ $system_analyzer['message'] ?? '' }}</p>
            </div>
        </section>

        <div class="pi-grid pi-grid--two">
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-search text-primary"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_title') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    <form method="get" action="{{ route('seat-pi-manager.system-analyzer') }}" class="row g-2">
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="system" value="{{ $system_query }}" placeholder="{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_placeholder') }}">
                        </div>
                        <div class="col-md-3 d-grid">
                            <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_action') }}</button>
                        </div>
                    </form>

                    @if($system_query !== '' && !$selected_system && count($search_results) === 0)
                        <div class="alert alert-warning mt-3 mb-0">{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_empty') }}</div>
                    @endif

                    @if(count($search_results) > 0)
                        <div class="mt-3">
                            <div class="pi-flow">
                                @foreach($search_results as $result)
                                    <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $result['name']]) }}" class="pi-list-card text-decoration-none @if(($selected_system['system_id'] ?? null) === $result['system_id']) border-primary @endif">
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <div>
                                                <strong>{{ $result['name'] }}</strong>
                                                <div class="pi-muted small">{{ $result['region_name'] ?? '-' }} @if(!empty($result['constellation_name'])) · {{ $result['constellation_name'] }} @endif</div>
                                            </div>
                                            <span class="pi-chip">{{ $result['security'] }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            @if($selected_system)
                <section class="pi-panel">
                    <div class="pi-panel__header">
                        <h2 class="pi-panel__title">
                            <i class="fas fa-satellite text-info"></i>
                            <span>{{ $selected_system['name'] }}</span>
                        </h2>
                        <span class="pi-chip pi-chip--soft">{{ trans('seat-pi-manager::messages.fields.planet_count') }}: {{ $selected_system['planet_count'] }}</span>
                    </div>
                    <div class="pi-panel__body">
                        <div class="pi-flow">
                            <div class="pi-flow__step">
                                <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.region') }} / {{ trans('seat-pi-manager::messages.fields.constellation') }}</div>
                                <strong>{{ $selected_system['region_name'] ?? '-' }}</strong>
                                <div class="pi-note">{{ $selected_system['constellation_name'] ?? '-' }}</div>
                            </div>
                            <div class="pi-flow__step">
                                <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.security') }}</div>
                                <strong>{{ $selected_system['security'] }}</strong>
                            </div>
                            <div class="pi-flow__step">
                                <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.pages.system_analyzer.filter_title') }}</div>
                                <div class="pi-chip-row">
                                    @foreach(['', 'P4', 'P3', 'P2', 'P1'] as $tier)
                                        <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $selected_system['name'], 'tier' => $tier !== '' ? $tier : null, 'single_planet' => $single_planet_only ? 1 : null]) }}" class="pi-pill-toggle @if(($tier_filter ?: '') === $tier) is-active @endif">
                                            {{ $tier !== '' ? $tier : trans('seat-pi-manager::messages.common.all') }}
                                        </a>
                                    @endforeach
                                    <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $selected_system['name'], 'tier' => $tier_filter ?: null, 'single_planet' => $single_planet_only ? null : 1]) }}" class="pi-pill-toggle @if($single_planet_only) is-active @endif">
                                        {{ trans('seat-pi-manager::messages.pages.system_analyzer.single_planet_only') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>

        @if($selected_system)
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-list-ol text-primary"></i>
                        <span>{{ trans('seat-pi-manager::messages.fields.planet_count') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body p-0">
                    @if(count($selected_system['planets']) === 0)
                        <div class="p-4 pi-muted">{{ trans('seat-pi-manager::messages.pages.system_analyzer.no_planets') }}</div>
                    @else
                        <div class="table-responsive pi-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ trans('seat-pi-manager::messages.fields.planet_number') }}</th>
                                        <th>{{ trans('seat-pi-manager::messages.fields.planet_name') }}</th>
                                        <th>{{ trans('seat-pi-manager::messages.fields.planet_type') }}</th>
                                        <th>{{ trans('seat-pi-manager::messages.fields.radius') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selected_system['planets'] as $planet)
                                        <tr>
                                            <td>{{ $planet['planet_number'] ?? '-' }}</td>
                                            <td>{{ $planet['planet_name'] }}</td>
                                            <td>{{ $planet['type_name'] ?? '-' }}</td>
                                            <td>@if($planet['radius_km']) {{ $planet['radius_km'] }} km @else - @endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            <div class="pi-grid pi-grid--two">
                <section class="pi-panel">
                    <div class="pi-panel__header">
                        <h2 class="pi-panel__title">
                            <i class="fas fa-layer-group text-success"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.available_p0_title') }}</span>
                        </h2>
                    </div>
                    <div class="pi-panel__body">
                        <div class="pi-flow">
                            @foreach($selected_system['planet_type_summary'] as $planetType)
                                <div class="pi-list-card">
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                        <span class="pi-chip" style="background-color: {{ $planetType['color'] ?? '#6c757d' }}15; border-color: {{ $planetType['color'] ?? '#6c757d' }}55; color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }}</span>
                                        <span class="pi-chip">x{{ $planetType['count'] }}</span>
                                    </div>
                                    <div class="pi-chip-row">
                                        @foreach($planetType['resources'] as $resource)
                                            <span class="pi-chip pi-chip--soft">{{ $resource }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="pi-panel">
                    <div class="pi-panel__header">
                        <h2 class="pi-panel__title">
                            <i class="fas fa-project-diagram text-warning"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.recommendations_title') }}</span>
                        </h2>
                    </div>
                    <div class="pi-panel__body p-0">
                        @if(count($selected_system['recommendations']) === 0)
                            <div class="p-4 pi-muted">{{ trans('seat-pi-manager::messages.pages.system_analyzer.no_recommendations') }}</div>
                        @else
                            <div class="table-responsive pi-table-wrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('seat-pi-manager::messages.fields.product') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.tier') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.inputs') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.planet_type') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.single_planet') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.pages.system_analyzer.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selected_system['recommendations'] as $recommendation)
                                            <tr>
                                                <td class="fw-semibold">{{ $recommendation['name'] }}</td>
                                                <td><span class="pi-chip">{{ $recommendation['tier'] }}</span></td>
                                                <td>{{ implode(', ', $recommendation['inputs']) }}</td>
                                                <td>{{ implode(', ', $recommendation['planets_needed']) }}</td>
                                                <td>
                                                    @if($recommendation['single_planet_viable'])
                                                        <span class="badge bg-success">{{ implode(', ', $recommendation['single_planet_types']) }}</span>
                                                    @else
                                                        <span class="pi-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('seat-pi-manager.planner', ['product' => $recommendation['name'], 'system' => $selected_system['name']]) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ trans('seat-pi-manager::messages.pages.system_analyzer.open_planner') }}
                                                    </a>
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
        @endif
    </div>
@endsection

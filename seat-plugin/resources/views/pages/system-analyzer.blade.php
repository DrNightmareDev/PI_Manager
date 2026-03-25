@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.system_analyzer.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.system_analyzer.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    <div class="pi-shell">

        {{-- Search bar + selected system inline info --}}
        <section class="pi-panel">
            <div class="pi-panel__body--slim">
                <div class="pi-filter-bar">
                    <form method="get" action="{{ route('seat-pi-manager.system-analyzer') }}" class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control pi-filter-bar__input" name="system"
                               value="{{ $system_query }}"
                               placeholder="{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_placeholder') }}">
                        <button type="submit" class="btn btn-sm btn-primary">{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_action') }}</button>
                        @if($system_query !== '')
                            <a href="{{ route('seat-pi-manager.system-analyzer') }}" class="btn btn-sm btn-light">{{ trans('seat-pi-manager::messages.common.reset') }}</a>
                        @endif
                    </form>

                    @if($selected_system)
                        <div class="pi-filter-bar__sep"></div>
                        <strong class="small">{{ $selected_system['name'] }}</strong>
                        <span class="pi-muted small">{{ $selected_system['region_name'] ?? '-' }} · {{ $selected_system['security'] }}</span>
                        <span class="pi-chip pi-chip--soft">{{ $selected_system['planet_count'] }} {{ trans('seat-pi-manager::messages.fields.planet_count') }}</span>
                        <div class="pi-chip-row">
                            @foreach($selected_system['planet_type_summary'] as $pt)
                                <span class="pi-chip" style="background-color: {{ $pt['color'] ?? '#6c757d' }}15; border-color: {{ $pt['color'] ?? '#6c757d' }}55; color: {{ $pt['color'] ?? '#6c757d' }};">{{ $pt['type'] }} ×{{ $pt['count'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if($system_query !== '' && !$selected_system && count($search_results) === 0)
            <div class="alert alert-warning">{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_empty') }}</div>
        @endif

        @if(count($search_results) > 0)
            <section class="pi-panel">
                <div class="pi-panel__body--slim">
                    <div class="pi-chip-row">
                        @foreach($search_results as $result)
                            <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $result['name']]) }}"
                               class="pi-chip text-decoration-none @if(($selected_system['system_id'] ?? null) === $result['system_id']) pi-chip--soft @endif">
                                {{ $result['name'] }}&ensp;<span class="pi-muted" style="font-size:.8em;">{{ $result['security'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if($selected_system)
            @php
                $typeColorMap = collect($selected_system['planet_type_summary'])->pluck('color', 'type')->all();
            @endphp

            {{-- Planet Details: collapsed by default --}}
            <section class="pi-panel">
                <div class="pi-panel__header pi-panel__header--toggle"
                     data-toggle="collapse" data-target="#pi-planet-details"
                     role="button" aria-expanded="false" aria-controls="pi-planet-details">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-list-ol text-primary"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.planet_table_title') }}</span>
                    </h2>
                    <i class="fas fa-chevron-down pi-panel__chevron"></i>
                </div>
                <div class="collapse" id="pi-planet-details">
                    <div class="pi-panel__body p-0">
                        @if(count($selected_system['planets']) === 0)
                            <div class="p-4"><div class="pi-empty">{{ trans('seat-pi-manager::messages.pages.system_analyzer.no_planets') }}</div></div>
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
                                                <td>
                                                    @if(isset($planet['type_name']))
                                                        @php $c = $typeColorMap[$planet['type_name']] ?? '#6c757d'; @endphp
                                                        <span class="pi-chip" style="background-color: {{ $c }}15; border-color: {{ $c }}55; color: {{ $c }};">{{ $planet['type_name'] }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>@if($planet['radius_km']) {{ $planet['radius_km'] }} km @else - @endif</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- P0 Resources: collapsed by default --}}
            <section class="pi-panel">
                <div class="pi-panel__header pi-panel__header--toggle"
                     data-toggle="collapse" data-target="#pi-p0-resources"
                     role="button" aria-expanded="false" aria-controls="pi-p0-resources">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-layer-group text-success"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.available_p0_title') }}</span>
                    </h2>
                    <i class="fas fa-chevron-down pi-panel__chevron"></i>
                </div>
                <div class="collapse" id="pi-p0-resources">
                    <div class="pi-panel__body">
                        <div class="pi-flow">
                            @foreach($selected_system['planet_type_summary'] as $planetType)
                                <div class="pi-list-card">
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                        <span class="pi-chip" style="background-color: {{ $planetType['color'] ?? '#6c757d' }}15; border-color: {{ $planetType['color'] ?? '#6c757d' }}55; color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }}</span>
                                        <span class="pi-chip pi-chip--soft">×{{ $planetType['count'] }}</span>
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
                </div>
            </section>

            {{-- Recommendations with filter pills in header --}}
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-project-diagram text-warning"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.system_analyzer.recommendations_title') }}</span>
                        </h2>
                        <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.system_analyzer.recommendations_subtitle') }}</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @foreach(['', 'P4', 'P3', 'P2', 'P1'] as $tier)
                            <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $selected_system['name'], 'tier' => $tier !== '' ? $tier : null, 'single_planet' => $single_planet_only ? 1 : null]) }}"
                               class="pi-pill-toggle pi-pill-toggle--sm @if(($tier_filter ?: '') === $tier) is-active @endif">
                                {{ $tier !== '' ? $tier : trans('seat-pi-manager::messages.common.all') }}
                            </a>
                        @endforeach
                        <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $selected_system['name'], 'tier' => $tier_filter ?: null, 'single_planet' => $single_planet_only ? null : 1]) }}"
                           class="pi-pill-toggle pi-pill-toggle--sm @if($single_planet_only) is-active @endif">
                            {{ trans('seat-pi-manager::messages.pages.system_analyzer.single_planet_only') }}
                        </a>
                        <span class="pi-chip pi-chip--soft">{{ count($selected_system['recommendations']) }}</span>
                    </div>
                </div>
                <div class="pi-panel__body p-0">
                    @if(count($selected_system['recommendations']) === 0)
                        <div class="p-4"><div class="pi-empty">{{ trans('seat-pi-manager::messages.pages.system_analyzer.no_recommendations') }}</div></div>
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
                                            <td><span class="pi-chip pi-chip--soft">{{ $recommendation['tier'] }}</span></td>
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
                                                <a href="{{ route('seat-pi-manager.planner', ['product' => $recommendation['name'], 'system' => $selected_system['name']]) }}"
                                                   class="btn btn-sm btn-outline-primary">
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
        @endif
    </div>
@endsection

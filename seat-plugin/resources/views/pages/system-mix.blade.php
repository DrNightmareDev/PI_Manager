@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.system_mix.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.system_mix.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    @php
        $recommendationsByTier = collect($recommendations)->groupBy('tier');
    @endphp

    <div class="pi-shell">
        <section class="pi-hero">
            <div class="pi-hero__body">
                <div class="pi-kicker">
                    <i class="fas fa-layer-group"></i>
                    <span>{{ trans('seat-pi-manager::messages.pages.system_mix.title') }}</span>
                </div>
                <h1 class="pi-title">{{ trans('seat-pi-manager::messages.pages.system_mix.header') }}</h1>
                <p class="pi-subtitle">{{ trans('seat-pi-manager::messages.pages.system_mix.empty_state') }}</p>
            </div>
        </section>

        <div class="pi-grid pi-grid--two">
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-stream text-primary"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.search_title') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    <form method="get" action="{{ route('seat-pi-manager.system-mix') }}" class="row g-2">
                        <div class="col-12">
                            <textarea class="form-control" rows="9" name="systems" placeholder="{{ trans('seat-pi-manager::messages.pages.system_mix.search_placeholder') }}">{{ $systems_query }}</textarea>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.pages.system_mix.search_action') }}</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-chart-pie text-info"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.summary_title') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-grid pi-grid--stats">
                        <div class="pi-stat">
                            <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.fields.selection') }}</div>
                            <div class="pi-stat__value">{{ count($selected_systems) }}</div>
                        </div>
                        <div class="pi-stat">
                            <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.fields.planet_count') }}</div>
                            <div class="pi-stat__value">{{ $planet_count }}</div>
                        </div>
                        <div class="pi-stat">
                            <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.fields.product') }}</div>
                            <div class="pi-stat__value">{{ count($recommendations) }}</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        @if(count($selected_systems) > 0)
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-globe text-success"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.systems_title') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-grid pi-grid--stats">
                        @foreach($selected_systems as $system)
                            <div class="pi-list-card">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <strong>{{ $system['name'] }}</strong>
                                        <div class="pi-muted small">{{ $system['region_name'] ?? '-' }} · {{ $system['constellation_name'] ?? '-' }}</div>
                                    </div>
                                    <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $system['name']]) }}" class="btn btn-sm btn-outline-primary">{{ trans('seat-pi-manager::messages.pages.system_mix.open_analyzer') }}</a>
                                </div>
                                <div class="pi-chip-row">
                                    @foreach($system['planet_type_summary'] as $planetType)
                                        <span class="pi-chip" style="background-color: {{ $planetType['color'] ?? '#6c757d' }}15; border-color: {{ $planetType['color'] ?? '#6c757d' }}55; color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }} x{{ $planetType['count'] }}</span>
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
                        <i class="fas fa-seedling text-warning"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.planet_types_title') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-chip-row">
                        @foreach($planet_type_summary as $planetType)
                            <span class="pi-chip" style="background-color: {{ $planetType['color'] ?? '#6c757d' }}15; border-color: {{ $planetType['color'] ?? '#6c757d' }}55; color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }} x{{ $planetType['count'] }}</span>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="pi-panel">
                <div class="pi-panel__header">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-bezier-curve text-secondary"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.recommendations_title') }}</span>
                    </h2>
                </div>
                <div class="pi-panel__body">
                    @if(count($recommendations) === 0)
                        <div class="pi-muted">{{ trans('seat-pi-manager::messages.pages.system_mix.no_recommendations') }}</div>
                    @else
                        <div class="pi-flow">
                            @foreach(['P4', 'P3', 'P2', 'P1'] as $tier)
                                @if($recommendationsByTier->has($tier))
                                    <div class="pi-list-card">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <strong>{{ $tier }}</strong>
                                            <span class="pi-chip">{{ $recommendationsByTier[$tier]->count() }}</span>
                                        </div>
                                        <div class="table-responsive pi-table-wrap">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('seat-pi-manager::messages.fields.product') }}</th>
                                                        <th>{{ trans('seat-pi-manager::messages.fields.inputs') }}</th>
                                                        <th>{{ trans('seat-pi-manager::messages.fields.planet_type') }}</th>
                                                        <th>{{ trans('seat-pi-manager::messages.fields.single_planet') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recommendationsByTier[$tier] as $recommendation)
                                                        <tr>
                                                            <td class="fw-semibold">{{ $recommendation['name'] }}</td>
                                                            <td>{{ implode(', ', $recommendation['inputs']) }}</td>
                                                            <td>{{ implode(', ', $recommendation['planets_needed']) }}</td>
                                                            <td>
                                                                @if($recommendation['single_planet_viable'])
                                                                    <span class="badge bg-success">{{ implode(', ', $recommendation['single_planet_types']) }}</span>
                                                                @else
                                                                    <span class="pi-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </div>
@endsection

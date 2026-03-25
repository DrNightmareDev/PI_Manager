@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.system_mix.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.system_mix.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    @php
        $recommendationsByTier = collect($recommendations)->groupBy('tier');
    @endphp

    <div class="pi-shell">

        {{-- Search panel with summary stats in header --}}
        <section class="pi-panel">
            <div class="pi-panel__header">
                <div>
                    <h2 class="pi-panel__title">
                        <i class="fas fa-stream text-primary"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.search_title') }}</span>
                    </h2>
                    <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.system_mix.search_subtitle') }}</p>
                </div>
                @if(count($selected_systems) > 0)
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="pi-chip pi-chip--soft">{{ count($selected_systems) }} {{ trans('seat-pi-manager::messages.pages.system_mix.summary_selection') }}</span>
                        <span class="pi-chip pi-chip--soft">{{ $planet_count }} {{ trans('seat-pi-manager::messages.pages.system_mix.summary_planets') }}</span>
                        <span class="pi-chip pi-chip--soft">{{ count($recommendations) }} {{ trans('seat-pi-manager::messages.pages.system_mix.summary_products') }}</span>
                    </div>
                @endif
            </div>
            <div class="pi-panel__body">
                <form method="get" action="{{ route('seat-pi-manager.system-mix') }}" class="pi-stack">
                    <textarea class="form-control" rows="5" name="systems"
                              placeholder="{{ trans('seat-pi-manager::messages.pages.system_mix.search_placeholder') }}">{{ $systems_query }}</textarea>
                    <div class="pi-toolbar">
                        <div class="pi-muted small">{{ trans('seat-pi-manager::messages.pages.system_mix.search_hint') }}</div>
                        <div class="pi-toolbar__group">
                            @if($systems_query !== '')
                                <a href="{{ route('seat-pi-manager.system-mix') }}" class="btn btn-light">{{ trans('seat-pi-manager::messages.common.reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.pages.system_mix.search_action') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        @if(count($selected_systems) > 0)

            {{-- Systems list: collapsed by default --}}
            <section class="pi-panel">
                <div class="pi-panel__header pi-panel__header--toggle"
                     data-toggle="collapse" data-target="#pi-mix-systems"
                     role="button" aria-expanded="false" aria-controls="pi-mix-systems">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-globe text-success"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.systems_title') }}</span>
                        <span class="pi-chip pi-chip--soft ms-2">{{ count($selected_systems) }}</span>
                    </h2>
                    <i class="fas fa-chevron-down pi-panel__chevron"></i>
                </div>
                <div class="collapse" id="pi-mix-systems">
                    <div class="pi-panel__body">
                        <div class="pi-flow">
                            @foreach($selected_systems as $system)
                                <div class="pi-list-card">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <div>
                                            <strong>{{ $system['name'] }}</strong>
                                            <div class="pi-muted small">{{ $system['region_name'] ?? '-' }} / {{ $system['constellation_name'] ?? '-' }}</div>
                                        </div>
                                        <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $system['name']]) }}" class="btn btn-sm btn-outline-primary">{{ trans('seat-pi-manager::messages.pages.system_mix.open_analyzer') }}</a>
                                    </div>
                                    <div class="pi-chip-row">
                                        @foreach($system['planet_type_summary'] as $planetType)
                                            <span class="pi-chip" style="background-color: {{ $planetType['color'] ?? '#6c757d' }}15; border-color: {{ $planetType['color'] ?? '#6c757d' }}55; color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }} ×{{ $planetType['count'] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- Planet type summary: collapsed by default --}}
            <section class="pi-panel">
                <div class="pi-panel__header pi-panel__header--toggle"
                     data-toggle="collapse" data-target="#pi-mix-planet-types"
                     role="button" aria-expanded="false" aria-controls="pi-mix-planet-types">
                    <h2 class="pi-panel__title">
                        <i class="fas fa-seedling text-warning"></i>
                        <span>{{ trans('seat-pi-manager::messages.pages.system_mix.planet_types_title') }}</span>
                    </h2>
                    <i class="fas fa-chevron-down pi-panel__chevron"></i>
                </div>
                <div class="collapse" id="pi-mix-planet-types">
                    <div class="pi-panel__body">
                        <div class="pi-chip-row">
                            @foreach($planet_type_summary as $planetType)
                                <span class="pi-chip" style="background-color: {{ $planetType['color'] ?? '#6c757d' }}15; border-color: {{ $planetType['color'] ?? '#6c757d' }}55; color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }} ×{{ $planetType['count'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- Recommendations: tier accordion, P4 open --}}
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-bezier-curve text-secondary"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.system_mix.recommendations_title') }}</span>
                        </h2>
                        <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.system_mix.recommendations_subtitle') }}</p>
                    </div>
                    <span class="pi-chip pi-chip--soft">{{ count($recommendations) }}</span>
                </div>
                <div class="pi-panel__body">
                    @if(count($recommendations) === 0)
                        <div class="pi-empty">{{ trans('seat-pi-manager::messages.pages.system_mix.no_recommendations') }}</div>
                    @else
                        <div class="pi-flow">
                            @foreach(['P4', 'P3', 'P2', 'P1'] as $tier)
                                @if($recommendationsByTier->has($tier))
                                    @php $tierOpen = $tier === 'P4'; $mixTierId = 'pi-mix-tier-' . strtolower($tier); @endphp
                                    <div class="pi-accordion">
                                        <div class="pi-list-card--toggle d-flex justify-content-between align-items-center"
                                             data-toggle="collapse" data-target="#{{ $mixTierId }}"
                                             role="button" aria-expanded="{{ $tierOpen ? 'true' : 'false' }}" aria-controls="{{ $mixTierId }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <strong>{{ $tier }}</strong>
                                                <span class="pi-chip pi-chip--soft">{{ $recommendationsByTier[$tier]->count() }}</span>
                                            </div>
                                            <i class="fas fa-chevron-down pi-panel__chevron"></i>
                                        </div>
                                        <div class="collapse @if($tierOpen) show @endif" id="{{ $mixTierId }}">
                                            <div class="pi-accordion__body">
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

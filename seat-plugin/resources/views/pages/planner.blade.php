@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.planner.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.planner.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    <div class="pi-shell">
        <section class="pi-hero">
            <div class="pi-hero__body">
                <div class="pi-kicker">
                    <i class="fas fa-project-diagram"></i>
                    <span>{{ trans('seat-pi-manager::messages.pages.planner.title') }}</span>
                </div>
                <h1 class="pi-title">{{ trans('seat-pi-manager::messages.pages.planner.header') }}</h1>
                <p class="pi-subtitle">{{ trans('seat-pi-manager::messages.pages.planner.empty_state') }}</p>
            </div>
        </section>

        <section class="pi-panel pi-panel--dark">
            <div class="pi-panel__body">
                <div class="pi-stepper">
                    <div class="pi-stepper__item">
                        <div class="pi-stepper__badge">1</div>
                        <div>
                            <p class="pi-stepper__title">{{ trans('seat-pi-manager::messages.pages.planner.flow_pick_title') }}</p>
                            <p class="pi-stepper__text">{{ trans('seat-pi-manager::messages.pages.planner.flow_pick_text') }}</p>
                        </div>
                    </div>
                    <div class="pi-stepper__item">
                        <div class="pi-stepper__badge">2</div>
                        <div>
                            <p class="pi-stepper__title">{{ trans('seat-pi-manager::messages.pages.planner.flow_requirements_title') }}</p>
                            <p class="pi-stepper__text">{{ trans('seat-pi-manager::messages.pages.planner.flow_requirements_text') }}</p>
                        </div>
                    </div>
                    <div class="pi-stepper__item">
                        <div class="pi-stepper__badge">3</div>
                        <div>
                            <p class="pi-stepper__title">{{ trans('seat-pi-manager::messages.pages.planner.flow_validate_title') }}</p>
                            <p class="pi-stepper__text">{{ trans('seat-pi-manager::messages.pages.planner.flow_validate_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="pi-grid pi-grid--two">
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-search text-primary"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.planner.search_title') }}</span>
                        </h2>
                        <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.planner.search_subtitle') }}</p>
                    </div>
                </div>
                <div class="pi-panel__body">
                    <form method="get" action="{{ route('seat-pi-manager.planner') }}" class="pi-stack">
                        <div class="pi-inline-form">
                            <div class="pi-inline-form__grow">
                                <input type="text" class="form-control" name="product" value="{{ $product_query }}" placeholder="{{ trans('seat-pi-manager::messages.pages.planner.search_placeholder') }}">
                            </div>
                            <div class="pi-inline-form__narrow">
                                <input type="text" class="form-control" name="system" value="{{ $system_query }}" placeholder="{{ trans('seat-pi-manager::messages.pages.planner.system_placeholder') }}">
                            </div>
                            <div class="pi-toolbar__group">
                                <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.pages.planner.search_action') }}</button>
                                @if($product_query !== '' || $system_query !== '')
                                    <a href="{{ route('seat-pi-manager.planner') }}" class="btn btn-light">{{ trans('seat-pi-manager::messages.common.reset') }}</a>
                                @endif
                            </div>
                        </div>

                        @if(count($matching_products) > 0)
                            <div class="pi-stack">
                                <div class="pi-muted small">{{ trans('seat-pi-manager::messages.pages.planner.matches_title') }}</div>
                                <div class="pi-flow">
                                    @foreach($matching_products as $match)
                                        <a href="{{ route('seat-pi-manager.planner', ['product' => $match['name'], 'system' => $system_query]) }}" class="pi-list-card text-decoration-none @if(($selected_product['name'] ?? null) === $match['name']) pi-list-card--active @endif">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold">{{ $match['name'] }}</span>
                                                <span class="pi-chip pi-chip--soft">{{ $match['tier'] }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </section>

            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-book-open text-info"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.planner.catalog_title') }}</span>
                        </h2>
                        <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.planner.catalog_subtitle') }}</p>
                    </div>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-flow">
                        @foreach($catalog as $tier => $products)
                            <div class="pi-list-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <strong>{{ $tier }}</strong>
                                    <span class="pi-chip pi-chip--soft">{{ count($products) }}</span>
                                </div>
                                <div class="pi-chip-row">
                                    @foreach($products as $product)
                                        <a href="{{ route('seat-pi-manager.planner', ['product' => $product, 'system' => $system_query]) }}" class="pi-chip text-decoration-none">{{ $product }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        @if($selected_product)
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-cube text-warning"></i>
                            <span>{{ $selected_product['name'] }}</span>
                        </h2>
                        <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.planner.summary_subtitle') }}</p>
                    </div>
                    <span class="pi-chip pi-chip--soft">{{ $selected_product['tier'] }}</span>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-grid pi-grid--three">
                        <div class="pi-stat">
                            <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.planner.inputs_title') }}</div>
                            <div class="pi-stat__value">{{ count($selected_product['inputs']) }}</div>
                            <div class="pi-stat__meta">{{ implode(', ', $selected_product['inputs']) ?: '-' }}</div>
                        </div>
                        <div class="pi-stat">
                            <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.planner.p0_title') }}</div>
                            <div class="pi-stat__value">{{ count($selected_product['required_p0']) }}</div>
                            <div class="pi-stat__meta">{{ implode(', ', $selected_product['required_p0']) ?: '-' }}</div>
                        </div>
                        <div class="pi-stat">
                            <div class="pi-stat__label">{{ trans('seat-pi-manager::messages.pages.planner.planets_title') }}</div>
                            <div class="pi-stat__value">{{ count($selected_product['required_planet_types']) }}</div>
                            <div class="pi-stat__group">
                                @foreach($selected_product['required_planet_types'] as $planetType)
                                    <span class="pi-chip" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }}15; border-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }}55; color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="pi-grid pi-grid--two">
                @if($system_query !== '')
                    <section class="pi-panel">
                        <div class="pi-panel__header">
                            <div>
                                <h2 class="pi-panel__title">
                                    <i class="fas fa-map-marked-alt text-success"></i>
                                    <span>{{ trans('seat-pi-manager::messages.pages.planner.system_check_title') }}</span>
                                </h2>
                                <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.planner.system_check_subtitle') }}</p>
                            </div>
                        </div>
                        <div class="pi-panel__body">
                            @if($system_capability && $system_capability['system'])
                                <div class="pi-stack">
                                    <div class="pi-grid pi-grid--three">
                                        <div class="pi-flow__step">
                                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.pages.dashboard.location') }}</div>
                                            <strong>{{ $system_capability['system']['name'] }}</strong>
                                            <div class="pi-muted small">{{ $system_capability['system']['region_name'] ?? '-' }} / {{ $system_capability['system']['constellation_name'] ?? '-' }}</div>
                                        </div>
                                        <div class="pi-flow__step">
                                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.planet_count') }}</div>
                                            <strong>{{ $system_capability['system']['planet_count'] }}</strong>
                                        </div>
                                        <div class="pi-flow__step">
                                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.single_planet') }}</div>
                                            <strong>{{ $system_capability['recommendation']['single_planet_viable'] ?? false ? trans('seat-pi-manager::messages.status.enabled') : '-' }}</strong>
                                        </div>
                                    </div>

                                    @if($system_capability['can_build'])
                                        <div class="alert alert-success mb-0">{{ trans('seat-pi-manager::messages.pages.planner.system_can_build') }}</div>
                                        <div class="pi-list-card">
                                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.pages.planner.single_planet_title') }}</div>
                                            <div class="pi-chip-row">
                                                @if(count($system_capability['recommendation']['single_planet_types']) > 0)
                                                    @foreach($system_capability['recommendation']['single_planet_types'] as $planetType)
                                                        <span class="pi-chip" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }}15; border-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }}55; color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="pi-muted">{{ trans('seat-pi-manager::messages.pages.planner.not_single_planet') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">{{ trans('seat-pi-manager::messages.pages.planner.system_cannot_build') }}</div>
                                    @endif

                                    <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $system_capability['system']['name']]) }}" class="btn btn-outline-primary">
                                        {{ trans('seat-pi-manager::messages.pages.planner.open_in_analyzer') }}
                                    </a>
                                </div>
                            @else
                                <div class="pi-empty">{{ trans('seat-pi-manager::messages.pages.planner.system_not_found') }}</div>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="pi-panel">
                    <div class="pi-panel__header">
                        <div>
                            <h2 class="pi-panel__title">
                                <i class="fas fa-layer-group text-secondary"></i>
                                <span>{{ trans('seat-pi-manager::messages.pages.planner.resources_by_planet_title') }}</span>
                            </h2>
                            <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.planner.resources_by_planet_subtitle') }}</p>
                        </div>
                    </div>
                    <div class="pi-panel__body">
                        <div class="pi-flow">
                            @foreach($selected_product['required_planet_types'] as $planetType)
                                <div class="pi-list-card">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <strong>{{ $planetType }}</strong>
                                        <span class="pi-chip" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }}15; border-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }}55; color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                    </div>
                                    <div class="pi-chip-row">
                                        @foreach($planet_resources[$planetType] ?? [] as $resource)
                                            <span class="pi-chip pi-chip--soft">{{ $resource }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        @endif
    </div>
@endsection

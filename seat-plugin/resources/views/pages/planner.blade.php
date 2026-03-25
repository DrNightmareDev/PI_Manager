@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.planner.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.planner.header'))

@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.search_title') }}</div>
                <div class="card-body">
                    <form method="get" action="{{ route('seat-pi-manager.planner') }}" class="row g-2">
                        <div class="col-12">
                            <input type="text" class="form-control" name="product" value="{{ $product_query }}" placeholder="{{ trans('seat-pi-manager::messages.pages.planner.search_placeholder') }}">
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control" name="system" value="{{ $system_query }}" placeholder="{{ trans('seat-pi-manager::messages.pages.planner.system_placeholder') }}">
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.pages.planner.search_action') }}</button>
                        </div>
                    </form>

                    @if(count($matching_products) > 0)
                        <div class="list-group mt-3">
                            @foreach($matching_products as $match)
                                <a href="{{ route('seat-pi-manager.planner', ['product' => $match['name'], 'system' => $system_query]) }}" class="list-group-item list-group-item-action @if(($selected_product['name'] ?? null) === $match['name']) active @endif">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $match['name'] }}</span>
                                        <span class="badge bg-secondary">{{ $match['tier'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.catalog_title') }}</div>
                <div class="card-body">
                    @foreach($catalog as $tier => $products)
                        <div class="mb-3">
                            <div class="fw-bold mb-2">{{ $tier }}</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($products as $product)
                                    <a href="{{ route('seat-pi-manager.planner', ['product' => $product, 'system' => $system_query]) }}" class="badge bg-light text-dark text-decoration-none">{{ $product }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            @if($selected_product)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $selected_product['name'] }}</h4>
                            <div class="text-muted small">{{ trans('seat-pi-manager::messages.fields.tier') }}: {{ $selected_product['tier'] }}</div>
                        </div>
                        <span class="badge bg-primary">{{ $selected_product['tier'] }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.inputs_title') }}</div>
                                    <div class="card-body">
                                        @if(count($selected_product['inputs']) === 0)
                                            <span class="text-muted">-</span>
                                        @else
                                            <ul class="mb-0 ps-3">
                                                @foreach($selected_product['inputs'] as $input)
                                                    <li>{{ $input }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.p0_title') }}</div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($selected_product['required_p0'] as $resource)
                                                <span class="badge bg-secondary">{{ $resource }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.planets_title') }}</div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($selected_product['required_planet_types'] as $planetType)
                                                <span class="badge" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($system_query !== '')
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.system_check_title') }}</div>
                        <div class="card-body">
                            @if($system_capability && $system_capability['system'])
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                    <div>
                                        <h5 class="mb-1">{{ $system_capability['system']['name'] }}</h5>
                                        <div class="text-muted small">
                                            {{ $system_capability['system']['region_name'] ?? '-' }}
                                            · {{ $system_capability['system']['constellation_name'] ?? '-' }}
                                            · {{ trans('seat-pi-manager::messages.fields.planet_count') }}: {{ $system_capability['system']['planet_count'] }}
                                        </div>
                                    </div>
                                    <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $system_capability['system']['name']]) }}" class="btn btn-sm btn-outline-primary">
                                        {{ trans('seat-pi-manager::messages.pages.planner.open_in_analyzer') }}
                                    </a>
                                </div>

                                @if($system_capability['can_build'])
                                    <div class="alert alert-success mb-3" role="alert">{{ trans('seat-pi-manager::messages.pages.planner.system_can_build') }}</div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.single_planet_title') }}</div>
                                                <div class="card-body">
                                                    @if(count($system_capability['recommendation']['single_planet_types']) > 0)
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($system_capability['recommendation']['single_planet_types'] as $planetType)
                                                                <span class="badge" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">{{ trans('seat-pi-manager::messages.pages.planner.not_single_planet') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.system_planets_title') }}</div>
                                                <div class="card-body">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($system_capability['recommendation']['planets_needed'] as $planetType)
                                                            <span class="badge" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0" role="alert">{{ trans('seat-pi-manager::messages.pages.planner.system_cannot_build') }}</div>
                                @endif
                            @else
                                <div class="alert alert-warning mb-0" role="alert">{{ trans('seat-pi-manager::messages.pages.planner.system_not_found') }}</div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.planner.resources_by_planet_title') }}</div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($selected_product['required_planet_types'] as $planetType)
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <span class="badge me-2" style="background-color: {{ $planet_type_colors[$planetType] ?? '#6c757d' }};">{{ $planetType }}</span>
                                            {{ trans('seat-pi-manager::messages.pages.planner.available_resources') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($planet_resources[$planetType] ?? [] as $resource)
                                                    <span class="badge bg-light text-dark">{{ $resource }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">{{ trans('seat-pi-manager::messages.pages.planner.empty_state') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.system_mix.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.system_mix.header'))

@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header">{{ trans('seat-pi-manager::messages.pages.system_mix.search_title') }}</div>
                <div class="card-body">
                    <form method="get" action="{{ route('seat-pi-manager.system-mix') }}" class="row g-2">
                        <div class="col-12">
                            <textarea class="form-control" rows="8" name="systems" placeholder="{{ trans('seat-pi-manager::messages.pages.system_mix.search_placeholder') }}">{{ $systems_query }}</textarea>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">{{ trans('seat-pi-manager::messages.pages.system_mix.search_action') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header">{{ trans('seat-pi-manager::messages.pages.system_mix.summary_title') }}</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ trans('seat-pi-manager::messages.fields.selection') }}</dt>
                        <dd class="col-sm-7">{{ count($selected_systems) }}</dd>
                        <dt class="col-sm-5">{{ trans('seat-pi-manager::messages.fields.planet_count') }}</dt>
                        <dd class="col-sm-7">{{ $planet_count }}</dd>
                        <dt class="col-sm-5">{{ trans('seat-pi-manager::messages.fields.product') }}</dt>
                        <dd class="col-sm-7">{{ count($recommendations) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            @if(count($selected_systems) > 0)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.system_mix.systems_title') }}</div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($selected_systems as $system)
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <h5 class="mb-1">{{ $system['name'] }}</h5>
                                                    <div class="text-muted small">{{ $system['region_name'] ?? '-' }} · {{ $system['constellation_name'] ?? '-' }}</div>
                                                </div>
                                                <a href="{{ route('seat-pi-manager.system-analyzer', ['system' => $system['name']]) }}" class="btn btn-sm btn-outline-primary">
                                                    {{ trans('seat-pi-manager::messages.pages.system_mix.open_analyzer') }}
                                                </a>
                                            </div>
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @foreach($system['planet_type_summary'] as $planetType)
                                                    <span class="badge" style="background-color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }} x{{ $planetType['count'] }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.system_mix.planet_types_title') }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($planet_type_summary as $planetType)
                                <span class="badge" style="background-color: {{ $planetType['color'] ?? '#6c757d' }};">{{ $planetType['type'] }} x{{ $planetType['count'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header">{{ trans('seat-pi-manager::messages.pages.system_mix.recommendations_title') }}</div>
                    <div class="card-body p-0">
                        @if(count($recommendations) === 0)
                            <div class="p-3 text-muted">{{ trans('seat-pi-manager::messages.pages.system_mix.no_recommendations') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('seat-pi-manager::messages.fields.product') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.tier') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.inputs') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.planet_type') }}</th>
                                            <th>{{ trans('seat-pi-manager::messages.fields.single_planet') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recommendations as $recommendation)
                                            <tr>
                                                <td>{{ $recommendation['name'] }}</td>
                                                <td><span class="badge bg-secondary">{{ $recommendation['tier'] }}</span></td>
                                                <td>{{ implode(', ', $recommendation['inputs']) }}</td>
                                                <td>{{ implode(', ', $recommendation['planets_needed']) }}</td>
                                                <td>
                                                    @if($recommendation['single_planet_viable'])
                                                        <span class="badge bg-success">{{ implode(', ', $recommendation['single_planet_types']) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">{{ trans('seat-pi-manager::messages.pages.system_mix.empty_state') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

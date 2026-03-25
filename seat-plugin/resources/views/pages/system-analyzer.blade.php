@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.system_analyzer.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.system_analyzer.header'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header">
                    {{ trans('seat-pi-manager::messages.pages.system_analyzer.search_title') }}
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('seat-pi-manager.system-analyzer') }}" class="row g-2">
                        <div class="col-md-9">
                            <input
                                type="text"
                                class="form-control"
                                name="system"
                                value="{{ $system_query }}"
                                placeholder="{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_placeholder') }}"
                            >
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ trans('seat-pi-manager::messages.pages.system_analyzer.search_action') }}
                            </button>
                        </div>
                    </form>

                    @if($system_query !== '' && !$selected_system && count($search_results) === 0)
                        <div class="alert alert-warning mt-3 mb-0" role="alert">
                            {{ trans('seat-pi-manager::messages.pages.system_analyzer.search_empty') }}
                        </div>
                    @endif

                    @if(count($search_results) > 0)
                        <div class="mt-3">
                            <h5 class="mb-2">{{ trans('seat-pi-manager::messages.pages.system_analyzer.search_results_title') }}</h5>
                            <div class="list-group">
                                @foreach($search_results as $result)
                                    <a
                                        href="{{ route('seat-pi-manager.system-analyzer', ['system' => $result['name']]) }}"
                                        class="list-group-item list-group-item-action @if(($selected_system['system_id'] ?? null) === $result['system_id']) active @endif"
                                    >
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <strong>{{ $result['name'] }}</strong>
                                                <div class="small opacity-75">
                                                    {{ $result['region_name'] ?? '-' }}
                                                    @if(!empty($result['constellation_name']))
                                                        · {{ $result['constellation_name'] }}
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="badge bg-secondary">{{ $result['security'] }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($selected_system)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">{{ $selected_system['name'] }}</h4>
                            <div class="text-muted small">
                                {{ trans('seat-pi-manager::messages.fields.region') }}: {{ $selected_system['region_name'] ?? '-' }}
                                · {{ trans('seat-pi-manager::messages.fields.constellation') }}: {{ $selected_system['constellation_name'] ?? '-' }}
                                · {{ trans('seat-pi-manager::messages.fields.security') }}: {{ $selected_system['security'] }}
                            </div>
                        </div>
                        <span class="badge bg-primary">
                            {{ trans('seat-pi-manager::messages.fields.planet_count') }}: {{ $selected_system['planet_count'] }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if(count($selected_system['planets']) === 0)
                            <div class="alert alert-warning mb-0" role="alert">
                                {{ trans('seat-pi-manager::messages.pages.system_analyzer.no_planets') }}
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-sm align-middle mb-0">
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
                                                <td>
                                                    @if($planet['radius_km'])
                                                        {{ $planet['radius_km'] }} km
                                                    @else
                                                        -
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
            @endif

            <div class="card">
                <div class="card-header">
                    {{ trans('seat-pi-manager::messages.pages.system_analyzer.track_title') }}
                </div>
                <div class="card-body">
                    <p class="mb-2">{{ $system_analyzer['message'] ?? '' }}</p>
                    <p class="text-muted mb-0">{{ $system_analyzer['next_step'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection


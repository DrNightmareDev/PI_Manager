@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.overview.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.overview.header'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h3 class="mb-1">{{ trans('seat-pi-manager::messages.pages.overview.header') }}</h3>
                        <p class="text-muted mb-0">{{ trans('seat-pi-manager::messages.pages.overview.subtitle') }}</p>
                    </div>
                    <span class="badge bg-success">{{ trans('seat-pi-manager::messages.status.active') }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="alert alert-info mb-3" role="alert">
                                <strong>{{ trans('seat-pi-manager::messages.pages.overview.notice_title') }}</strong>
                                {{ trans('seat-pi-manager::messages.pages.overview.notice_body') }}
                            </div>
                            <div class="row g-3">
                                @foreach($features as $key => $enabled)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <strong>{{ trans('seat-pi-manager::messages.modules.' . $key) }}</strong>
                                                    @if($enabled)
                                                        <span class="badge bg-success">{{ trans('seat-pi-manager::messages.status.enabled') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ trans('seat-pi-manager::messages.status.planned') }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ trans('seat-pi-manager::messages.pages.overview.module_hint') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    {{ trans('seat-pi-manager::messages.pages.overview.imports_title') }}
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-3">
                                        <dt class="col-sm-5">{{ trans('seat-pi-manager::messages.fields.static_planets') }}</dt>
                                        <dd class="col-sm-7">{{ number_format((int) ($static_planets['planet_count'] ?? 0)) }}</dd>

                                        <dt class="col-sm-5">{{ trans('seat-pi-manager::messages.fields.last_import') }}</dt>
                                        <dd class="col-sm-7">
                                            @if(!empty($static_planets['last_run_finished_at']))
                                                {{ \Illuminate\Support\Carbon::parse($static_planets['last_run_finished_at'])->diffForHumans() }}
                                            @else
                                                {{ trans('seat-pi-manager::messages.status.not_run') }}
                                            @endif
                                        </dd>

                                        <dt class="col-sm-5">{{ trans('seat-pi-manager::messages.fields.last_status') }}</dt>
                                        <dd class="col-sm-7">{{ $static_planets['last_run_status'] ?? trans('seat-pi-manager::messages.status.not_run') }}</dd>
                                    </dl>

                                    <div class="alert alert-info mb-0" role="alert">
                                        <strong>{{ trans('seat-pi-manager::messages.pages.overview.command_title') }}</strong>
                                        <code>php artisan seat-pi-manager:import-static-planets --force</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


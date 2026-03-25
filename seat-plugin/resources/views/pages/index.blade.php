@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.index.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.index.header'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h3 class="mb-1">{{ trans('seat-pi-manager::messages.pages.index.header') }}</h3>
                        <p class="text-muted mb-0">{{ trans('seat-pi-manager::messages.pages.index.subtitle') }}</p>
                    </div>
                    <span class="badge bg-info">{{ trans('seat-pi-manager::messages.status.bootstrap') }}</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4" role="alert">
                        <strong>{{ trans('seat-pi-manager::messages.pages.index.notice_title') }}</strong>
                        {{ trans('seat-pi-manager::messages.pages.index.notice_body') }}
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    {{ trans('seat-pi-manager::messages.pages.index.mvp_title') }}
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li>{{ trans('seat-pi-manager::messages.mvp.shell') }}</li>
                                        <li>{{ trans('seat-pi-manager::messages.mvp.install') }}</li>
                                        <li>{{ trans('seat-pi-manager::messages.mvp.i18n') }}</li>
                                        <li>{{ trans('seat-pi-manager::messages.mvp.system_analyzer') }}</li>
                                        <li>{{ trans('seat-pi-manager::messages.mvp.release') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    {{ trans('seat-pi-manager::messages.pages.index.features_title') }}
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">{{ trans('seat-pi-manager::messages.fields.plugin') }}</dt>
                                        <dd class="col-sm-8">{{ $plugin_name }}</dd>

                                        <dt class="col-sm-4">{{ trans('seat-pi-manager::messages.fields.languages') }}</dt>
                                        <dd class="col-sm-8">{{ implode(', ', $languages) }}</dd>

                                        <dt class="col-sm-4">{{ trans('seat-pi-manager::messages.fields.next_focus') }}</dt>
                                        <dd class="col-sm-8">{{ trans('seat-pi-manager::messages.pages.index.next_focus') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    {{ trans('seat-pi-manager::messages.pages.index.imports_title') }}
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
                                        <strong>{{ trans('seat-pi-manager::messages.pages.index.command_title') }}</strong>
                                        <code>php artisan seat-pi-manager:import-static-planets --force</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    {{ trans('seat-pi-manager::messages.pages.index.analyzer_title') }}
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">{{ $system_analyzer['message'] ?? '' }}</p>
                                    <p class="text-muted mb-0">{{ $system_analyzer['next_step'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            {{ trans('seat-pi-manager::messages.pages.index.feature_flags_title') }}
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-2">
                                @foreach($features as $feature => $enabled)
                                    <div class="col">
                                        <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                                            <span>{{ str_replace('_', ' ', ucfirst($feature)) }}</span>
                                            @if($enabled)
                                                <span class="badge bg-success">{{ trans('seat-pi-manager::messages.status.enabled') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ trans('seat-pi-manager::messages.status.planned') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

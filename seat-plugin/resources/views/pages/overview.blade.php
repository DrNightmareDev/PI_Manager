@extends('web::layouts.grids.12')

@section('title', trans('seat-pi-manager::messages.pages.overview.title'))
@section('page_header', trans('seat-pi-manager::messages.pages.overview.header'))

@section('content')
    @include('seat-pi-manager::partials.ui-kit')

    <div class="pi-shell">
        <div class="pi-grid pi-grid--two">
            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-puzzle-piece text-primary"></i>
                            <span>{{ trans('seat-pi-manager::messages.sidebar.title') }}</span>
                        </h2>
                        <p class="pi-panel__subtitle">{{ trans('seat-pi-manager::messages.pages.overview.notice_body') }}</p>
                    </div>
                    <span class="badge bg-success">{{ trans('seat-pi-manager::messages.status.active') }}</span>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-stack">
                        <div class="alert alert-info mb-0" role="alert">
                            <strong>{{ trans('seat-pi-manager::messages.pages.overview.notice_title') }}</strong>
                            {{ trans('seat-pi-manager::messages.pages.overview.notice_body') }}
                        </div>
                        <div class="pi-flow">
                            @foreach($features as $key => $enabled)
                                <div class="pi-list-card">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong>{{ trans('seat-pi-manager::messages.modules.' . $key) }}</strong>
                                        @if($enabled)
                                            <span class="badge bg-success">{{ trans('seat-pi-manager::messages.status.enabled') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ trans('seat-pi-manager::messages.status.planned') }}</span>
                                        @endif
                                    </div>
                                    <div class="pi-muted small mt-1">{{ trans('seat-pi-manager::messages.pages.overview.module_hint') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="pi-panel">
                <div class="pi-panel__header">
                    <div>
                        <h2 class="pi-panel__title">
                            <i class="fas fa-database text-info"></i>
                            <span>{{ trans('seat-pi-manager::messages.pages.overview.imports_title') }}</span>
                        </h2>
                    </div>
                </div>
                <div class="pi-panel__body">
                    <div class="pi-stack">
                        <div class="pi-flow__step">
                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.static_planets') }}</div>
                            <strong>{{ number_format((int) ($static_planets['planet_count'] ?? 0)) }}</strong>
                        </div>
                        <div class="pi-flow__step">
                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.last_import') }}</div>
                            <strong>
                                @if(!empty($static_planets['last_run_finished_at']))
                                    {{ \Illuminate\Support\Carbon::parse($static_planets['last_run_finished_at'])->diffForHumans() }}
                                @else
                                    {{ trans('seat-pi-manager::messages.status.not_run') }}
                                @endif
                            </strong>
                        </div>
                        <div class="pi-flow__step">
                            <div class="pi-flow__step-title">{{ trans('seat-pi-manager::messages.fields.last_status') }}</div>
                            <strong>{{ $static_planets['last_run_status'] ?? trans('seat-pi-manager::messages.status.not_run') }}</strong>
                        </div>
                        <div class="alert alert-info mb-0" role="alert">
                            <strong>{{ trans('seat-pi-manager::messages.pages.overview.command_title') }}</strong>
                            <br>
                            <code>php artisan seat-pi-manager:import-static-planets --force</code>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div style="margin:0 0 0 0;">
        <section class="content-header">
            <div class="row {{ empty(Auth::user()) ? 'guest-page-title' : 'auth-page-title' }}">
                <h1 style="text-align: center;margin-left: -1em;">{{ $pageTitle }}</h1>
            </div>
        </section>
        <div class="content {{ empty(Auth::user()) ? 'content-guest' : '' }}">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="row">
                        @include('layouts.partials.advertising.ads')
                        <div class="col-lg-12">
                            @include('rotator.partials.nav')
                        </div>
                        <div class="col-lg-12">
                            <iframe sandbox="allow-forms allow-scripts allow-pointer-lock allow-same-origin" src="" id="rotator-iframe"></iframe>
                        </div>
                        <div class="col-lg-12">
                            {!! $content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('google-analytics')
@include('layouts.partials.tracking._google_analytics')
@endpush
@extends('layouts.app')

@section('content')
    <div style="margin:0 0 0 0;">
        <section class="content-header">
            <div class="row {{ empty(Auth::user()) ? 'guest-page-title' : 'auth-page-title' }}">
                <h1 id="title" style="text-align: center;" data-payment-processor-slug="{{ $paymentProcessor->slug }}">{{ $paymentProcessor->name }} Faucet Rotator</h1>
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
                            @include('layouts.partials.misc._no-iframe-faucet-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="/assets/js/rotator-scripts/paymentProcessorRotator.min.js?{{ rand() }}"></script>
@endpush

@push('google-analytics')
@include('layouts.partials.tracking._google_analytics')
@endpush
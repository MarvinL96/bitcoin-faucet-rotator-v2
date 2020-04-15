@extends('layouts.app')

@section('content')
    <section class="content-header" style="margin-bottom: 1em;">
        <div class="row {{ empty(Auth::user()) ? 'guest-page-title' : 'auth-page-title' }}">
            <h1>Faucets</h1>
            @include('layouts.partials.social.addthis')
        </div>

        @if(!empty(Auth::user()) && Auth::user()->isAnAdmin())
        <div class="row zero-margin buttons-row">
            {!! Form::button(
                '<i class="fa fa-2x fa-plus" style="vertical-align: middle; margin-right:0.25em;"></i>Add New Faucet',
                [
                    'type' => 'button',
                    'onClick' => "location.href='" . route('faucets.create') . "'",
                    'class' => 'btn btn-primary btn-success col-lg-2 col-md-2 col-sm-3 col-xs-12',
                    'style' => 'margin:0.25em 0 0 0; color: white; min-width:12em;'
                ])
            !!}
        </div>
        @endif
    </section>
    @include('layouts.partials.site_wide_alerts._alert-content')
    <div class="content {{ empty(Auth::user()) ? 'content-guest' : '' }}">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        @include('layouts.partials.navigation._breadcrumbs')
        <div class="box box-primary">
            <div class="box-body">
                @include('layouts.partials.advertising.ads')
                @include('faucets.table')
                @include('layouts.partials.social.disqus')
            </div>
        </div>
    </div>
@endsection

@push('google-analytics')
    @include('layouts.partials.tracking._google_analytics')
@endpush

@push('jsonld_schema')

    <!-- START JSONld / schema -->
    @include('layouts.partials.seo._social_jsonld')
    <!-- END JSONld / schema -->

@endpush
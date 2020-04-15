@extends('layouts.app')

@section('content')
    <section class="content-header" style="margin-bottom: 1em;">
        <div class="row {{ empty(Auth::user()) ? 'guest-page-title' : 'auth-page-title' }}">
            <h1 id="title">Faucet - {!! $faucet->name !!}</h1>
            @include('layouts.partials.social.addthis')
        </div>
        @if(!empty(Auth::user()) && Auth::user()->isAnAdmin())
        <div class="row zero-margin buttons-row">
            {!! Form::button(
                '<i class="fa fa-2x fa-edit" style="vertical-align: middle; margin-right:0.25em;"></i>Edit Current Faucet',
                [
                    'type' => 'button',
                    'onClick' => "location.href='" . route('faucets.edit', ['slug' => $faucet->slug]) . "'",
                    'class' => 'btn btn-primary col-lg-2 col-md-2 col-sm-2 col-xs-12',
                    'style' => 'margin:0.25em 0.5em 0 0; color: white; min-width:12em;'
                ])
            !!}

            {!! Form::button(
                '<i class="fa fa-2x fa-plus" style="vertical-align: middle; margin-right:0.25em;"></i>Add New Faucet',
                [
                    'type' => 'button',
                    'onClick' => "location.href='" . route('faucets.create') . "'",
                    'class' => 'btn btn-success col-lg-2 col-md-2 col-sm-2 col-xs-12',
                    'style' => 'margin:0.25em 0.5em 0 0; color: white; min-width:12em;'
                ])
            !!}

            @if($faucet->isDeleted())
                {!! Form::open(['route' => ['faucets.delete-permanently', $faucet->slug], 'method' => 'delete', 'class' => 'form-inline']) !!}
                {!! Form::button(
                    '<i class="fa fa-2x fa-trash" style="vertical-align: middle; margin-right:0.25em;"></i>Permanently Delete',
                    [
                        'type' => 'submit',
                        'class' => 'btn btn-danger col-lg-2 col-md-2 col-sm-2 col-xs-12',
                        'style' => 'margin:0.25em 0.5em 0 0; color: white; min-width:12em;',
                        'onclick' => "return confirm('Are you sure? The faucet will be PERMANENTLY deleted!')"
                    ])
                !!}
                {!! Form::close() !!}
                {!! Form::open(['route' => ['faucets.restore', $faucet->slug], 'method' => 'patch', 'class' => 'form-inline']) !!}
                {!! Form::button(
                    '<i class="fa fa-2x fa-refresh" style="vertical-align: middle; margin-right:0.25em;"></i>Restore Current Faucet',
                    [
                        'type' => 'submit',
                        'class' => 'btn btn-info col-lg-2 col-md-2 col-sm-2 col-xs-12',
                        'style' => 'margin:0.25em 0.5em 0 0; color: white; min-width:12em;',
                        'onclick' => "return confirm('Are you sure you want to restore this archived faucet?')"
                    ])
                !!}
                {!! Form::close() !!}
            @else
                {!! Form::open(['route' => ['faucets.destroy', 'slug' => $faucet->slug], 'method' => 'delete', 'class' => 'form-inline']) !!}
                {!! Form::button(
                    '<i class="fa fa-2x fa-trash" style="vertical-align: middle; margin-right:0.25em;"></i>Archive Faucet',
                    [
                        'type' => 'submit',
                        'class' => 'btn btn-warning col-lg-2 col-md-2 col-sm-2 col-xs-12',
                        'style' => 'margin:0.25em 0.5em 0 0; color: white; min-width:12em;',
                        'onclick' => "return confirm('Are you sure you want to archive this faucet?')"
                    ])
                !!}
                {!! Form::close() !!}
            @endif
        </div>
        @endif
    </section>
    @include('layouts.partials.site_wide_alerts._alert-content')
    <div class="content {{ empty(Auth::user()) ? 'content-guest' : '' }}">
        <div class="clearfix"></div>
        @include('flash::message')
        @include('faucets.partials._message-info')
        <div class="clearfix"></div>
        @include('layouts.partials.navigation._breadcrumbs')

        <div class="box box-primary">
            <div class="box-body">
                <div class="row zero-margin">
                    @include('layouts.partials.advertising.ads')
                    <p><strong>*</strong> Payout amounts are in Satoshis</p>

                    <div id="faucet-info" class="table table-responsive">
                        <table class="table table-striped table bordered show-table-header">
                            <thead>
                            <th>URL</th>
                            <th>Interval Minutes</th>
                            <th>Min. Payout*</th>
                            <th>Max. Payout*</th>
                            <th>Payment Processor/s</th>
                            <th>Ref. Program?</th>
                            <th>Ref. %</th>
                            <th>Comments</th>
                            <th>Status</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    {!! link_to($faucetUrl, $faucet->name, ['target' => 'blank', 'title' => $faucet->name]) !!}
                                </td>
                                <td>{{ $faucet->interval_minutes }}</td>
                                <td>{{ number_format($faucet->min_payout) }}</td>
                                <td>{{ number_format($faucet->max_payout) }}</td>
                                <td>
                                    @if($faucet->paymentProcessors)
                                        @if(count($faucet->paymentProcessors) == 0)
                                            None. Please add one (or more) for this faucet
                                        @else
                                            <ul class="faucet-payment-processors">
                                                @foreach($faucet->paymentProcessors as $paymentProcessor)
                                                    <li>{!! link_to(route('payment-processors.show', $paymentProcessor->slug), $paymentProcessor->name, ['target' => 'blank', 'title' => $paymentProcessor->name]) !!}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif

                                </td>
                                <td>{{ $faucet->hasRefProgram() }}</td>
                                <td>{{ $faucet->ref_payout_percent }}</td>
                                <td>{{ $faucet->comments }}</td>
                                <td>{{ $faucet->status() }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    @if($faucet->is_paused == false)
                        @if($canShowInIframe == true)
                        <iframe sandbox="allow-forms allow-scripts allow-pointer-lock allow-same-origin" src="{{ $faucetUrl }}" id="faucet-iframe"></iframe>
                        @else
                            <div id="show-no-iframe-content">
                            <h3 style="font-size: 3em;">Sorry!</h3>

                            <p>This faucet either: cannot be shown in iframes, is redirected from it's original domain,
                                or there is an issue with the faucet's domain name. Please
                                {!!  link_to(
                                    $faucetUrl,
                                    'visit ' . $faucet->name . ' in a new window/tab',
                                    ['target' => '_blank']) !!}.
                            </p>
                            @if(Auth::user() && Auth::user()->isAnAdmin())
                                <p>You can {!! link_to('/faucets/' . $faucet->slug . '/edit', 'check this faucet') !!} to make any changes (for example, if the
                                    faucet's URL / domain name is having issues).</p>
                            @else
                                <p>Please contact the administrator if you are experiencing issues with the faucet's direct URL / domain name.</p>
                            @endif
                            </div>
                        @endif
                    @else
                        <p>This faucet has been paused from showing in rotation.</p>

                        @if(Auth::user() && Auth::user()->isAnAdmin())
                            <p>You can {!! link_to('/faucets/' . $faucet->slug . '/edit', 'edit this faucet') !!} to re-enable it in rotation.</p>
                        @else
                            <p>Please contact the administrator if you would like this faucet re-enabled.</p>
                        @endif
                    @endif
                    @include('layouts.partials.social.disqus')
                </div>
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
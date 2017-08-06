@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="row" style="margin:0 0 0 0;">
            <div class="row {{ empty(Auth::user()) ? 'guest-page-title' : 'auth-page-title' }}">
                <h1>{{ $user->user_name }}'s '{!! $faucet->name !!}' Faucet</h1>
            </div>
        </div>
        <div class="row" style="margin:0 0 0 0;">
        @if(Auth::user() != null)
            @if(
            Auth::user()->isAnAdmin() ||
            ($user == Auth::user() && $user->hasPermission('create-user-faucets'))
            )
                {!! Form::button(
                    '<i class="fa fa-2x fa-plus" style="vertical-align: middle; margin-right:0.25em;"></i>Add New Faucet',
                    [
                        'type' => 'button',
                        'onClick' => "location.href='" . route('users.faucets.create', $user->slug) . "'",
                        'class' => 'btn btn-primary btn-success col-lg-2 col-md-2 col-sm-3 col-xs-12',
                        'style' => 'margin:0.25em 0.25em 0 0; color: white; min-width:12em;'
                    ])
                !!}
            @endif
        @endif
        </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        @include('faucets.partials._message-info')
        <div class="clearfix"></div>
        @include('layouts.partials.navigation._breadcrumbs')

        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
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
                                    {!! link_to($faucet->url . App\Helpers\Functions\Faucets::getUserFaucetRefCode($user, $faucet), $faucet->name, ['target' => 'blank', 'title' => $faucet->name]) !!}
                                </td>
                                <td>{{ $faucet->interval_minutes }}</td>
                                <td>{{ $faucet->min_payout }}</td>
                                <td>{{ $faucet->max_payout }}</td>
                                <td>
                                    @if($faucet->paymentProcessors)
                                        @if(count($faucet->paymentProcessors) == 0)
                                            None. Please add one (or more) for this faucet
                                        @else
                                            <ul>
                                                @foreach($faucet->paymentProcessors as $p)
                                                    <li>
                                                        {!!
                                                            link_to_route(
                                                                'users.payment-processors.faucets',
                                                                $p->name,
                                                                ['userSlug' => $user->slug,'paymentProcessorSlug' =>  $p->slug],
                                                                ['title' => $user->user_name . "'s " . $p->name . " faucets", 'style' => 'text-decoration:underline;']
                                                            )
                                                        !!}
                                                    </li>
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
                            <h3 style="font-size: 3em;">Sorry!</h3>

                            <p>This faucet cannot be shown in iframes. Please
                                {!!  link_to(
                                    $faucetUrl,
                                    'visit ' . $faucet->name . ' in a new window/tab',
                                    ['target' => '_blank']) !!}.
                            </p>
                        @endif
                    @else
                        <p>This faucet has been paused from showing in rotation.</p>

                        @if(Auth::user() && $user == Auth::user() || Auth::user()->isAnAdmin())
                            <p>You can {!! link_to('/users/' . $user->slug . '/faucets/' . $faucet->slug . '/edit', 'edit this faucet') !!} to re-enable it in rotation.</p>
                        @else
                            <p>Please contact the administrator if you would like this faucet re-enabled.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('google-analytics')
    @include('layouts.partials.tracking._google_analytics')
@endpush
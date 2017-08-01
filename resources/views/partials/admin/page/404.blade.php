@extends('partials.admin.layouts.error')

@section('error-content')
    <div class="error-code">
        <h1>
            404
        </h1>
        <h3>We couldn't find that...</h3>
    </div>
    <div class="error-desc">
        <p>
            Sorry, but the page you are looking for does not exist. <br/>
            Try hitting your browser's back button or clicking here.
        </p>
        <p>
            <md-button class="md-raised md-primary" ui-sref="admin.index">
                Go to @{{ dict.TITLE_DASHBOARD | lowercase }}
            </md-button>
        </p>
    </div>
@stop

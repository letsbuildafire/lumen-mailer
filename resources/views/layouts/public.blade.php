<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="utf-8">
        <base href="/">

        <title >
            {!! env('APP_NAME') !!}
        </title>
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1">
        <meta name="description" content="{!! env('APP_DESC') !!}">
        <meta name="author" content="{!! env('APP_AUTHOR') !!}">

        <link rel="stylesheet" type="text/css" href="/css/public.css">
    </head>

    <body>
        <article class="landing">
            <div class="landing-body">
                <div class="row">
                    <div class="small-12 medium-10 medium-push-1 large-8 large-push-2">
                        <h1 class="brand-heading" shadow="{!! env('APP_NAME') !!}">{!! env('APP_NAME') !!}</h1>
                        <p class="intro-text">{!! env('APP_DESC') !!}</p>
                    </div>
                </div>
            </div>
        </article>
    </body>
</html>

<!DOCTYPE html>
<html lang="en" xmlns:ng="http://angularjs.org" id="ng-app" ng-app="thin">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="utf-8">
        <base href="/">

        {{-- IE9 doesn't have base64 encoding functionality --}}
        <!--[if lte IE 9]>
            <script type="text/javascript" src="/js/vendor/base64.min.js"></script>
        <![endif]-->

        {{-- Site Information --}}
        <title ng-bind="$title ? $title + ' | {!! env('APP_NAME') !!} ' : '{!! env('APP_NAME') !!}'">
            {!! env('APP_NAME') !!}
        </title>
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1">
        <meta name="description" content="@{{ page_desc }}">
        <meta name="author" content="@{{ page_author }}">
                
        {{-- Stylesheets --}}
        @include('base.stylesheets')

        {{-- Modernizr --}}
        <script type="text/javascript" src="/js/vendor/modernizr.js"></script>

        {{-- Favicon and mobile icons --}}
        <link rel="shortcut icon" href="/assets/ico/favicon.ico">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon-57-precomposed.png">
    </head>

    {{-- AppCtrl for standard functionality --}}
    <body class="page" ng-class="page_class" ng-controller="AppCtrl">
        
        {{-- Navigation --}}
        <nav ng-cloak ui-view="navigation"></nav>
        
        {{-- Content --}}
        <main class="container">
            {{-- Content view --}}
            <div class="content-wrapper" ui-view="content"></div>  
        </main>
        
        {{-- Off-Canvas Menu --}}
        <aside class="offcanvas ng-hide" ng-show="offcanvas" ui-view="offcanvas"></aside>
        
        {{-- Off-Canvas Overlay --}}
        <div class="site-overlay ng-hide" ng-show="offcanvas" ng-click="toggleOffCanvas()"></div>

        {{-- Javacript --}}
        @include('base.scripts')
    </body>
</html>

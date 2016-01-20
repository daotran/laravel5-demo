<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

    @include('front.header')

    <body>

        @include('front.banner')

        <main role="main" class="container">
            @if(session()->has('ok'))
            @include('partials/error', ['type' => 'success', 'message' => session('ok')])
            @endif	
            @if(isset($info))
            @include('partials/error', ['type' => 'info', 'message' => $info])
            @endif
            @yield('main')
        </main>

        @include('front.footer')

        {!! HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') !!}
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
        {!! HTML::script('js/plugins.js') !!}
        {!! HTML::script('js/main.js') !!}

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
                    (function (b, o, i, l, e, r) {
                        b.GoogleAnalyticsObject = l;
                        b[l] || (b[l] =
                                function () {
                                    (b[l].q = b[l].q || []).push(arguments)
                                });
                        b[l].l = +new Date;
                        e = o.createElement(i);
                        r = o.getElementsByTagName(i)[0];
                        e.src = '//www.google-analytics.com/analytics.js';
                        r.parentNode.insertBefore(e, r)
                    }(window, document, 'script', 'ga'));
            ga('create', 'UA-XXXXX-X');
            ga('send', 'pageview');
        </script>

        @yield('scripts')

    </body>
</html>
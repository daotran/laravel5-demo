<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ trans('front/site.title') }}</title>
    <meta name="description" content="">	
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- @yield('header') -->

    {!! HTML::style('css/main_front.css') !!}

    <!--[if (lt IE 9) & (!IEMobile)]>
            {!! HTML::script('js/vendor/respond.min.js') !!}
    <![endif]-->
    <!--[if lt IE 9]>
            {!! HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') !!}
            {!! HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') !!}
    <![endif]-->

    {!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}
    {!! HTML::style('http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic') !!}

</head>
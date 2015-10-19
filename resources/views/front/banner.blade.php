<!--[if lt IE 8]>
	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<header role="banner">

	<div class="brand">{{ trans('front/site.title') }}</div>
	<div class="address-bar">{{ trans('front/site.sub-title') }}</div>
	<div id="flags" class="text-center"></div>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.html">{{ trans('front/site.title') }}</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li {!! classActivePath('/') !!}>
						{!! link_to('/', trans('front/site.home')) !!}
					</li>
					@if(session('statut') == 'visitor' || session('statut') == 'user')
						<li {!! classActivePath('contact/create') !!}>
							{!! link_to('contact/create', trans('front/site.contact')) !!}
						</li>
					@endif
					<li {!! classActiveSegment(1, ['articles', 'blog']) !!}>
						{!! link_to('articles', trans('front/site.blog')) !!}
					</li>
					@if(Request::is('auth/register'))
						<li class="active">
							{!! link_to('auth/register', trans('front/site.register')) !!}
						</li>
					@elseif(Request::is('password/email'))
						<li class="active">
							{!! link_to('password/email', trans('front/site.forget-password')) !!}
						</li>
					@else
						@if(session('statut') == 'visitor')
							<li {!! classActivePath('auth/login') !!}>
								{!! link_to('auth/login', trans('front/site.connection')) !!}
							</li>
						@else
							@if(session('statut') == 'admin')
								<li>
									{!! link_to_route('admin', trans('front/site.administration')) !!}
								</li>
							@elseif(session('statut') == 'redac') 
								<li>
									{!! link_to('blog', trans('front/site.redaction')) !!}
								</li>
							@endif
							<li>
								{!! link_to('auth/logout', trans('front/site.logout')) !!}
							</li>
						@endif
					@endif
					<li class="imgflag">
						<a href="{!! url('language') !!}"><img width="32" height="32" alt="en" src="{!! asset('img/' . (session('locale') == 'fr' ? 'english' : 'french') . '-flag.png') !!}"></a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<!-- @yield('header') -->
</header>
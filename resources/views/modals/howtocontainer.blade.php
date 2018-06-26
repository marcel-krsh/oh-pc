<?php setlocale(LC_MONETARY, 'en_US'); ?>

@if (Auth::guest())
@else
@endif
@yield('head')

<div class="uk-container uk-align-center">
	<div uk-grid class="uk-child-width-2-3 uk-text-center">
		
			@yield('content')
		
	</div>
</div>

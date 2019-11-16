<!DOCTYPE html>
<html lang="en" dir="ltr" id="parentHTML" class="no-js">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>DEVCO INSPECTION</title>

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="/css/allita-font.css">
	<link rel="stylesheet" href="/css/allita-admin.css">
	<link rel="stylesheet" href="/css/system.css">
	<link rel="stylesheet" href="/css/uikit.min.css">
	<link rel="stylesheet" href="/css/auto-complete.css">
	<style type="text/css">
		#plain-main-window {
			max-width: 1450px;
			min-height:80vh;
			background-color: white;
			-webkit-box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
			-moz-box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
			box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
			margin-left: auto;
			margin-right: auto;
			padding-top: 78px;
			padding-left: 15px;
			padding-right: 15px;
			padding-bottom: 40px;
			margin-bottom: 15px;
		}
	</style>
	<script>
		window.Laravel = <?php echo json_encode([
  'csrfToken' => csrf_token(),
]); ?>
		</script>
		<script data-pace-options='{ "restartOnRequestAfter": false }' src="/js/pace.js"></script>
		<script src="/js/jquery.js"></script>
		<script src="/js/uikit.min.js"></script>
		<script src="/js/uikit-icons.min.js"></script>
		<style>
			.hideImportnull {
				display: none;
			}
			.autocomplete-suggestions {max-height: none;}
			.autocomplete-suggestion {border-bottom:1px solid #ddd; padding:15px 10px; cursor: pointer;}
		</style>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
		@yield('head')
	</head>
	<body >
		<!-- BEGIN STICKY HEADER -->

		<!-- END STICKY HEADER -->
		<a name="top"></a>
		<!-- MAIN VIEW -->
		<div class="uk-container uk-align-center">
			<div class="uk-grid uk-grid-collapse">
				<div id="plain-main-window" class="uk-width-1-1" style="padding-top:20px">
					<div class="uk-grid " >
						<div class="uk-width-1-1">
							
						</div>
					</div>
					@yield('content')
				</div>
			</div>
		</div>
		<div class="uk-width-1-1 uk-margin-large-bottom"><p class="uk-text-center uk-dark uk-text-small uk-light">Powered by <a href="http://allita.org" target="_blank" class="uk-link-muted uk-dark uk-light">Allita</a> for <a href="http://ohiohome.org" class="uk-link-muted uk-dark uk-light" target="_blank">Ohio Housing Finance Agency</a> &copy;2018 — {{date('Y',time())}}.</p></div>
	</div>

	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr(".flatpickr");
		var configs = {
			dateformat: {
				dateFormat: "m/d/Y",
			}
		}
	</script>

</body>
</html>

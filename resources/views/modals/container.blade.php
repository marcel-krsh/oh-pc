<?php setlocale(LC_MONETARY, 'en_US'); 
/// protect against inactive users.
$allowPageLoad = false;

if(Auth::check()){
	if(Auth::user()->active == 1){
		$allowPageLoad = true;
	}
}else{
	/// user is not logged in -- the auth middleware will protect against that access.
	$allowPageLoad = true;
}
	if($allowPageLoad){
?>
<!DOCTYPE html>
<html lang="en-gb" dir="ltr" id="parentHTML">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title')</title>

<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="/css/allita-admin.css">
<link rel="stylesheet" href="/css/system.css">
<link rel="stylesheet" href="/css/uikit.min.css">
<link rel="stylesheet" href="/css/allita-font.css">

@if (Auth::guest())
@else
@endif
<script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
</script>

<script src="/js/jquery.js"></script>
<script src="/js/uikit.min.js"></script>
<script src="/js/uikit-icons.min.js"></script>
<!-- <script src="/js/components/autocomplete.js"></script>
<script src="/js/core/modal.js"></script>
<script src="/js/components/lightbox.js"></script>
<script src="/js/components/sticky.js"></script>
<script src="/js/components/notify.js"></script>
<script src="/js/components/tooltip.js"></script>
<script src="/js/components/datepicker.js"></script>
<script src="/js/components/slideshow.js"></script>
<script src="/js/components/slideshow-fx.js"></script>
<script src="/js/components/upload.js"></script>
<script src="/js/components/lightbox.js"></script>
<script src="/js/components/form-select.js"></script>
<script src="/js/components/slider.js"></script>
<script src="/js/components/slideset.js"></script>
<script src="/js/components/accordion.js"></script>
<script src="/js/components/notify.js"></script>
<script src="/js/components/search.js"></script>
<script src="/js/components/timepicker.js"></script>
<script src="/js/components/nestable.js"></script>
<script src="/js/components/sortable.js"></script>
<script src="/js/components/grid.min.js"></script> -->




@if (Auth::guest())
@else
<script> window.continueToLoad = 1; window.saved = 1;
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.5/Chart.bundle.js"></script>
@endif
@yield('head')
</head>
<body class="pace-done" style="background: white;">
<a name="modal-top"></a>
<!-- MAIN VIEW -->
<div class="uk-container uk-align-center">
	<div uk-grid>
		<div class="uk-width-1-1 ">
			@yield('content')
		</div>
	</div>
</div>
<script src="/js/app.js"></script>
@if (Auth::guest())
@else

<script type="text/javascript" src="/js/systems/system.js"></script>
@endif

<a id="smoothscrollLinkModal" href="#modal-top" uk-scroll="{offset: 10}"></a>
</body>
</html>
<?php } else { /// show for inactive users ?>
<!DOCTYPE html>
<html lang="en" dir="ltr" id="parentHTML" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Inactive User</title>

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="/css/allita-admin-419171046.css">
<link rel="stylesheet" href="/css/system-419171130.css">

<script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
@if(session('disablePacer') != 1)
<script data-pace-options='{ "restartOnRequestAfter": false }' src="/js/pace.js">{{session('disablePacer')}}</script>

@endif
<script src="/js/jquery.js"></script>
<script src="/js/uikit.min.js"></script>
<script src="/js/components/autocomplete.js"></script>
<script src="/js/core/modal.js"></script>
<script src="/js/components/lightbox.js"></script>
<script src="/js/components/sticky.js"></script>
<script src="/js/components/notify.js"></script>
<script src="/js/components/tooltip.js"></script>
<script src="/js/components/datepicker.js"></script>
<script src="/js/components/slideshow.js"></script>
<script src="/js/components/slideshow-fx.js"></script>
<script src="/js/components/upload.js"></script>
<script src="/js/components/lightbox.js"></script>
<script src="/js/components/form-select.js"></script>
<script src="/js/components/slider.js"></script>
<script src="/js/components/slideset.js"></script>
<script src="/js/components/accordion.js"></script>
<script src="/js/components/notify.js"></script>
<script src="/js/components/search.js"></script>
<script src="/js/components/timepicker.js"></script>
<script src="/js/components/nestable.js"></script>
<script src="/js/components/sortable.js"></script>
<script src="/js/components/grid.min.js"></script>
<script src="/js/handsontable.full.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.js"></script>

	<script>
		$('select').multipleSelect();
	</script>
</head>
<body class="uk-height-1-1">
<script type="text/javascript">
	UIkit.modal.alert('Sorry your user has been deactivated. You can contact your administrator to reactivate your user and then we can work together again.');
</script>
<div class="uk-vertical-align uk-text-center uk-height-1-1">
            <div class="uk-vertical-align-middle uk-margin-top" style="width: 250px;">

                

                <form class="uk-panel uk-panel-box">
                    <div class="uk-form-row">
                        <h2 align="center">Inactive User</h2>
                        <p align="center">{{Auth::user()->name}}</p>
                    </div>
                    
                    <div class="uk-form-row">
                        <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" href="/login">Return to Login</a>
                    </div>
                    
                </form>

            </div>
        </div>
</body>
</html>
<?php  

Auth::logout();

}  ?>
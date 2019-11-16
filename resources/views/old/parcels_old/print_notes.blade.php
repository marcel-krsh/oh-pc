<!DOCTYPE html>
<html lang="en-gb" dir="ltr" id="parentHTML">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Parcel {{$parcel->id}} Notes</title>
	<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="/css/allita-admin.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/system.css{{ asset_version() }}">
	<!-- only load style sheets for the tabs they have access to -no need to minify as speed of initial load is not an issue for the number of users. -->
	<link rel="stylesheet" href="/css/cdfs-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/communications-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/funding-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/history-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/notes-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/outcomes-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/processing-tab.css{{ asset_version() }}">
	<script data-pace-options='{ "restartOnRequestAfter": false }' src="/js/pace.js{{ asset_version() }}"></script>
	<script src="/js/jquery.js{{ asset_version() }}"></script>
	<script src="/js/uikit.min.js{{ asset_version() }}"></script>
	<!-- <script src="/js/components/autocomplete.js{{ asset_version() }}"></script>
	<script src="/js/core/modal.js{{ asset_version() }}"></script>
	<script src="/js/components/lightbox.js{{ asset_version() }}"></script>
	<script src="/js/components/sticky.js{{ asset_version() }}"></script>
	<script src="/js/components/notify.js{{ asset_version() }}"></script>
	<script src="/js/components/tooltip.js{{ asset_version() }}"></script>
	<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
	<script src="/js/components/slideshow.js{{ asset_version() }}"></script>
	<script src="/js/components/slideshow-fx.js{{ asset_version() }}"></script>
	<script src="/js/components/upload.js{{ asset_version() }}"></script>
	<script src="/js/components/lightbox.js{{ asset_version() }}"></script>
	<script src="/js/components/form-select.js{{ asset_version() }}"></script>
	<script src="/js/components/slider.js{{ asset_version() }}"></script>
	<script src="/js/components/slideset.js{{ asset_version() }}"></script>
	<script src="/js/components/accordion.js{{ asset_version() }}"></script>
	<script src="/js/components/notify.js{{ asset_version() }}"></script>
	<script src="/js/components/search.js{{ asset_version() }}"></script>
	<script src="/js/components/timepicker.js{{ asset_version() }}"></script>
	<script src="/js/components/nestable.js{{ asset_version() }}"></script>
	<script src="/js/components/sortable.js{{ asset_version() }}"></script>
	<script src="/js/components/grid.min.js{{ asset_version() }}"></script> -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.5/Chart.bundle.js{{ asset_version() }}"></script>

</head>
<body class="  pace-done">
	<div class="pace  pace-inactive">
		<div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
			<div class="pace-progress-inner"></div>
		</div>
		<div class="pace-activity"></div>
	</div>
	<!-- BEGIN STICKY HEADER -->
	<stickynav>
		<div style="width:100%; max-width:1450px; margin:auto" class="no-print">
			<div style="margin-top:2px;">
				<table width="100%">
					<tbody>
						<tr>
							<td>
								<h1 style="color:#fff">Parcel {{$parcel->id}} Notes</h1>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</stickynav>

	<h3 class="print-only">Parcel {{$parcel->id}} Notes</h3>

	<div id="main-window" style="width:7.5in;">

		<div id="detail-subtabs-content">
			<div class="uk-container uk-margin-top uk-grid-collapse" id="note-list" uk-grid style="position: relative;    margin-left: 0; ">
				@foreach ($notes as $note)
				<div class="uk-width-1-1 note-list-item" style="">
					<div uk-grid>
						<div class="uk-width-1-3 note-type-and-who ">

							<span >{{ $note->owner->name}}<br></span>
							<span class=" note-item-date-time">{{ date('m/j/y', strtotime($note->created_at)) }}  <br>{{ date('h:i a', strtotime($note->created_at)) }}</span>
						</div>
						<div class="uk-width-1-2 note-item-excerpt">
							{{ $note->note}}
						</div>
						<div class="uk-width-1-6">
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>

		<script>
		loadNotes(window.currentDetailId);
		loadSupportInfo(window.currentDetailId);
		</script>
	</div>

</div>


</body>
</html>

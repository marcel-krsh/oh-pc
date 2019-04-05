<?php $signature = $bladeData ?>
@if(!is_null($signature))
	<div uk-grid>
	Output saved signature - path to file should be saved in the data for this report version
	<img src="{{$signature->path}}">
	<hr >
	{{date('m/d/Y g:h a', strtotime($signature->date))}}
	@can('access_auditor')<button onClick="jsFunctionToDelete"><i class="a-trash-can"></i> DELETE SIGNATURE </button>@endCan
	</div>
@else

<div uk-grid style="margin-bottom:340px;">
	@if($print == 1)
	<div class="uk-width-1-1">
		<hr style="margin-top: 300px;" >
	</div>
	<div class="uk-width-1-3">PRINT NAME</div>
	<div class="uk-width-1-2">SIGNATURE </div>
	<div class="uk-width-1-6">DATE </div>
	</div>
	@else
<div id="signature-pad" class="signature-pad uk-width-1-1" style="min-height: 350px;">
    <div class="signature-pad--body">
      <canvas></canvas>
    </div>
    <div class="signature-pad--footer">
      <div class="description">BY SIGNING ABOVE I CERTIFY I HAVE REVIEWED THIS DOCUMENT.</div>

      <div class="signature-pad--actions">
        <div>
          <button type="button" class="uk-button clear" data-action="clear">Clear</button>
          <button type="button" class="uk-button uk-hidden" data-action="change-color">Change color</button>
          <button type="button" class="uk-button" data-action="undo">Undo</button>

        </div>
        <div>
          <button type="button" class="uk-button save" data-action="save-png">CONFIRM SIGNATURE</button>
          <button type="button" class="button save uk-hidden" data-action="save-jpg">Save as JPG</button>
          <button type="button" class="button save uk-hidden" data-action="save-svg">Save as SVG</button>
        </div>
      </div>
    </div>
  </div>
    <script src="/js/signature/signature_pad.umd.js"></script>
  <script src="/js/signature/app.js"></script>
  @endIf
</div>
@endIf

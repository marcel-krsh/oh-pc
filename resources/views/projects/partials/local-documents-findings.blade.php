	<span  >@if(count($all_ids) > 1) Audits: {{ implode(', ',$all_ids) }} @elseIf(count($all_ids)) Audit: {{ implode(', ',$all_ids) }} @endIf</span> |
	<span uk-tooltip="pos: right" title="@if(count($document_findings) > 0){{ implode(', ', $document_findings->pluck('id')->toArray()) }}@endif">
		<span onclick="$('#document-{{ $document->id }}-findings').slideToggle();" class="use-hand-cursor" uk-tooltip title="CLICK TO VIEW FINDING(S)" id="document-findings-attention={{ $document->id }}">
			Total Findings: <span class="uk-badge finding-number {{ $unresolved_findings > 0 ? 'attention' : '' }} " uk-tooltip="" title="" aria-expanded="false"> {{ @count($document_findings) }}</span>
		</span>
	</span>

	<div id="document-{{ $document->id }}-findings" style="display: none;">
		{{-- @foreach($document_findings as $fin) --}}
		<hr class="uk-margin-bottom" style="border: 1px solid #bbbbbb" />
		<li id="document-findings-{{ $document->id }}">
			@include('non_modal.finding-summary')
		</li>
		{{-- @endforeach --}}
	</div>

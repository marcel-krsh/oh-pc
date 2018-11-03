<template class="uk-hidden" id="project-details-info-assignment-auditor-calendar-template">
	<div id="project-details-info-assignment-auditor-calendar" class="grid-schedule uk-padding-remove uk-width-1-1 uk-margin">
		tplHeader
		<div class="grid-schedule-sidebar">
			<div>6a</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>8a</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>10a</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>12p</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>2p</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>4p</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>6p</div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div>
			<div>8p</div><div></div><div></div><div></div>
		</div>
		tplContent
		tplFooter
	</div>
</template>

<template class="uk-hidden" id="auditor-calendar-footer-template">
		<div class="grid-schedule-footer">
			<div uk-grid>
				<div class="uk-width-1-3 uk-padding-remove"><i class="a-arrow-left-2"></i> tplPrevious</div>
				<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> tplToday</div>
				<div class="uk-width-1-3 uk-text-right">tplNext <i class="a-arrow-right-2_1"></i></div>
			</div>
		</div>
</template>

<template class="uk-hidden" id="auditor-calendar-header-template">
	<div id="auditor-calendar-header" class="grid-schedule-header">
		<div class="week-spacer"></div>
		<div class="week-day">tplDate1</div>
		<div class="week-spacer"></div>
		<div class="week-day">tplDate2</div>
		<div class="week-spacer"></div>
		<div class="week-day">tplDate3</div>
		<div class="week-spacer"></div>
		<div class="week-day">tplDate4</div>
		<div class="week-spacer"></div>
		<div class="week-day selected">tplDate5</div>
		<div class="week-spacer"></div>
		<div class="week-day">tplDate6</div>
		<div class="week-spacer"></div>
		<div class="week-day">tplDate7</div>  
		<div class="week-spacer"></div>
		<div class="week-day">tplDate8</div>  
		<div class="week-spacer"></div>
		<div class="week-day">tplDate9</div> 
		<div class="week-spacer"></div> 
	</div>
</template>

<template class="uk-hidden" id="auditor-calendar-content-template">
	<div id="auditor-calendar-content"  class="grid-schedule-content">
		<div class="day-spacer"></div>
		tplDays
	</div>
</template>

<template class="uk-hidden" id="auditor-calendar-day-template">
		<div class="day tplSelected tplAvailable">
			<div class="event beforetime" data-start="tplBeforeStart" data-span="tplBeforeSpan"></div>
			tplEvents
			<div class="event aftertime" data-start="tplAfterStart" data-span="tplAfterSpan"></div>
		</div>
		<div class="day-spacer"></div>
</template>

<template class="uk-hidden" id="auditor-calendar-event-template">
			<div class="event tplAvailable tplClass" data-start="tplStart" data-span="tplSpan">
				tplIcon
				tplDrop
			</div>
</template>

<template class="uk-hidden" id="auditor-calendar-event-drop-template">
				<div class="" uk-drop="mode: click">
				    <div class="uk-card uk-card-body uk-card-rounded">
				        <ul class="uk-list">
                        	<li onclick=""><i class="a-folder"></i> File Audit Only</li>	
                        	<li onclick=""><i class="a-mobile-home"></i> Site Visit Only</li>	
                        	<li onclick=""><i class="a-mobile-home"></i><i class="a-folder"></i> Both</li>	
	                    </ul>
				    </div>
				</div>
</template>
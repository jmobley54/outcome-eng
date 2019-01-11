<?php

global $bright_embedder_templates;

$bright_embedder_templates['scormcloud_classic_de'] = <<<EOF
	<div class="scormCloudInvitation">
	  <p class="description"></p>
	  <div class="courseInfox">
		<div class="title"><h3>{{title}}</h3></div>
{{#if metadata.description}}
		<div class="desc">{{metadata.description}}</div>
{{/if}}
	  </div>
{{#if registration}}
<center>
	  <table class="result_table">
		<tbody>
		  <tr>
			<td class="head">Abschluss</td>
			<td class="head">Partitur</td>
			<td class="head">Gesamtzeit</td>
			<td class="head">Zeit</td>
			<td class="head">Versuche</td>
		  </tr>
		  <tr>
			<td class="incomplete">{{registration.complete}}</td>
			<td class="unknown">{{registration.success}}</td>
			<td class="unknown">{{registration.score}}</td>
			<td class="time">{{registration.totaltime}} secs</td>
			<td class="unknown">{{registration.attempts}}</td>
		  </tr>
		</tbody>
	  </table>
</center>
{{/if}}
{{{launchbutton}}}	  
	</div>
EOF;

$bright_embedder_templates['bright_classic'] = <<<EOF
	<div class="scormCloudInvitation">
	  <h3></h3>
	  <p class="description"></p>
	  <div class="courseInfo">
		<div class="title">Course: {{title}}</div>
		<div class="desc">Description: {{metadata.description}}</div>
		<div class="duration">Duration: {{metadata.duration}} </div>
	  </div>
	  <table class="result_table">
		<tbody>
		  <tr>
			<td class="head">Completion</td>
			<td class="head">Success</td>
			<td class="head">Score</td>
			<td class="head">Total Time</td>
		  </tr>
		  <tr>
			<td class="incomplete">{{registration.complete}}</td>
			<td class="unknown">{{registration.success}}</td>
			<td class="unknown">{{registration.score}}</td>
			<td class="time">{{registration.totaltime}} secs</td>
		  </tr>
		</tbody>
	  </table>
{{{launchbutton}}}	  
	</div>
EOF;

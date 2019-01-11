<?php
global $bright_embedder_templates;

$results_fields = <<<EOF
      <thead>
      <tr>
        <th>Learner</th>
        <th>Course</th>
        <th>Complete</th>
        <th>Success</th>
        <th>Score</th>
        <th>Completed At</th>
      </tr>
      <tr>
        <th>Learner</th>
        <th>Course</th>
        <th>Complete</th>
        <th>Success</th>
        <th>Score</th>
        <th>Completed At</th>
      </tr>
      </thead>
      {{#each this.custom}}
         <tr>
           <td>{{this.[0]}}</td>
           <td>{{this.[1]}}</td>
           <td>{{this.[2]}}</td>
           <td>{{this.[3]}}</td>
           <td>{{this.[4]}}</td>
           <td>{{this.[5]}}</td>
         </tr>
      {{/each}}
EOF;

$results_matrix = <<<EOF
  <div class="bright-results-matrix">
    <table class="bright-results-matrix" data-pagesize="10">
  $results_fields
    </table>
  </div>
EOF;

$bright_embedder_templates['bright-course-usage-bubbles'] = <<<EOF
<div class="bright-course-usage-bubbles" data-diameter="{{attributes.diameter}}"/>
EOF;

$bright_embedder_templates['bright_results_matrix'] = $results_matrix;
$bright_embedder_templates['invitation_results_matrix'] = <<<EOF
<h3>{{#if attributes.invitation_name}}
{{attributes.invitation_name}}
{{else}}
{{#getQueryParameter 'invitation_name'}}{{/getQueryParameter}}
{{/if}}
</h3>
$results_matrix
EOF;

/* TODO: remove/deprecate bright_results_matrix use of courselist in favor of generic */

add_filter('bright_extend_on_generic','BrightTemplatePackReports::resultsMatrix',1,2);

add_filter('bright_extend_on_generic','BrightTemplatePackReports::genericReport',1,2);

add_filter('bright_templates', 'BrightTemplatePackReports::templateGenerator',1,2);

class BrightTemplatePackReports {

  static function templateGenerator($templates,$args) {
    $templateName = BrightTemplatePack::extractFromArray($args,'template');
    $headers_attr = BrightTemplatePack::extractFromArray($args,'headers','');
    $filters = BrightTemplatePack::extractFromArray($args,'filters','');
    $headers = explode(",",$headers_attr);

    $shortcodeAttributes = BrightTemplatePack::extractFromArray($args,'shortcodeAttributes',array());       

    if ($templateName == "bright_generic_report") {
      $fields = BrightTemplatePack::extractFromArray($args,'fields',5);
      $results = '
  <div class="bright-results-matrix">
    <table class="bright-generic-datatable" data-filters="' . $filters . '" data-fields="' . $fields . '" data-headers="' . $headers_attr . '" data-pagesize="{{#bGetDefaultValue attributes.pagesize \'25\'}}{{/bGetDefaultValue}}">
      <thead>
      <tr>
';
      for ($i = 0; $i < $fields ; $i++) {
        $results .= '        <th>' . $headers[$i] . '</th>
';
      }
      $results .= '      </tr>
      <tr>
';
      for ($i = 0; $i < $fields ; $i++) {
        $results .= '        <th>' . $headers[$i] . '</th>
';
      }
      $results .= '      </tr>
      </thead>
      <tbody>
      {{#each this.custom}}
         <tr>
';
      for ($i = 0; $i < $fields ; $i++) {
        $results .= "           <td>{{this.[{$i}]}}</td>
";
      }
      $results .= '         </tr>
      {{/each}}
      </tbody>
      <tfoot>
      <tr>
';
      for ($i = 0; $i < $fields ; $i++) {
        $results .= '        <th>' . $headers[$i] . '</th>
';
      }
      $results .= '      </tr>
      </tfoot>
    </table>
  </div>
';
      
      $templates["bright_generic_report"] = $results;
    }
    return $templates;
  }

  static function genericReport($coursedata,$attr) {
    $template = "bright_generic_report";
    $a_template = Bright\extractFromArray($attr,'template','');
    if ($a_template == $template) {
      $name = BrightTemplatePack::extractFromArray($attr,'name');


      unset($attr['template']);
      unset($attr['type']);

      $current_user = bright_get_user();

      $first_name = BrightTemplatePack::extractFromArray($attr, 'first_name', get_query_var('first_name',get_user_meta($current_user->ID, "first_name",true)));
      $last_name = BrightTemplatePack::extractFromArray($attr, 'last_name', get_query_var('last_name',get_user_meta($current_user->ID, "last_name",true)));

      $attr['first_name'] = $first_name;
      $attr['last_name'] =  $last_name;

      //      $attr = array_merge($attr,$_GET);
      // set use_query_parameters in bright shortcode to push in 
      // 
      $use_query_parameters = BrightTemplatePack::extractFromArray($attr,'use_query_parameters',"");
      $query_paramerers = explode(",",$use_query_parameters);
      foreach ($query_paramerers as $p) {
          // hmmm .... this seems a bit pourous.....
          $attr[$p] = filter_var($_GET[$p],FILTER_SANITIZE_URL);
      }
      
      $bright = Bright\Wordpress::getInstance();
      $bright->log($attr,false,'attr');
      $attr['host_url'] = get_site_url();
      $url = get_query_var('site_url', preg_replace('/^http(s|):\/\//', '', get_site_url()));
      $attr['hosturl'] = $url;

      if (!empty($name)) {
        return $bright->callApi('stored_query/run',array('params' => $attr,
                                                         'raw' => true));
        
      } else
        echo "No query parameter found in shortcode for {$template}";
    }
    return $coursedata;
  }
  
  /**
   * callback for bright_extend_on_{template-type} for custom data.
   */
  static function resultsMatrix($coursedata,$attr) {
	$bright = Bright\Wordpress::getInstance();
    $a_template = Bright\extractFromArray($attr,'template','');
	$bright->log($attr,false,'attr');
    $model = Bright\extractFromArray($attr,'model','');
	$host_url=get_site_url();

	if ($a_template == "bright_results_matrix") {
        $params = array('name' => ('bright_completion_matrix' . ($model === "full" ? "_full" : '')),
					  'host_url' => $host_url,
					  'query_scope' => 'bright');

	  if (isset($attr['guid_prefix']))
		$params['guid_prefix'] = urlencode($attr['guid_prefix']);
      if (isset($attr['provider_tags'])) {
        $params['name'] = 'bright_completion_by_tag';
        $params['provider_tags'] = $attr['provider_tags'];
      }
          
	  return $bright->callApi('stored_query/run',array('params' => $params,
													   'raw' => true));
	} elseif ($a_template == "invitation_results_matrix") {
	  $keyName = "invitation_name";
	  $encoded_name = urlencode(BrightTemplatePack::extractFromArray($_GET,$keyName,BrightTemplatePack::extractFromArray($attr,$keyName)));
	  return $bright->callApi('stored_query/run',array('params' => array('name'            => 'invitation_completion_matrix',
																		 'invitation_name' => $encoded_name,
																		 'host_url'        => $host_url,
																		 'query_scope'     => 'bright'),
                                                       'errorMsgs' => array('401' => "<br/><strong>You do not have permissions to see this report.</strong>"),
                                                       'suppressErrorBlock' => true,
                                                       'failure' => function($rsp,$curlInfo,$curlError) {
                                                         if ($curlInfo['http_code'] == 401) {
                                                           echo 'You do not have permission to view this report.<br/>';
                                                           return null;
                                                         } elseif ($failure) {
                                                           return $failure($rsp,$curlInfo,$curlError);
                                                         } else {
                                                           return null;
                                                         }
                                                       },
													   'raw'    => true)); /* return raw JSON to the page */
	}
	return $coursedata;
  }
}


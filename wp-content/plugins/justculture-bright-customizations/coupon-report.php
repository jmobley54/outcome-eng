<?php


$bright = \Bright\Wordpress::getInstance();

$url = get_site_url();
$bright->addTemplate('jc_license_key_report','
{{#if user.site_roles.administrator}}
  <div class="jc_license-results-matrix">
    <table class="jc_license-results-matrix">
      <thead>
	  <tr>
        <th>Coupons</th>
        <th>License Key</th>
        <th>Order</th>
        <th>Purchaser</th>
        <th>Created</th>
        <th>Cart66 Desc</th>
        <th>Seats Available</th>
        <th>Seats Used</th>
  </tr>
{{#if attributes.suppress_header}}
{{else}}
	  <tr>
        <th>Coupons</th>
        <th>License Key</th>
        <th>Order</th>
        <th>Purchaser</th>
        <th>Created</th>
        <th>Cart66 Desc</th>
        <th>Seats Available</th>
        <th>Seats Used</th>
  </tr>
{{/if}}
      </thead>
      {{#each this.custom}}
         <tr>
           <td>{{this.[0]}}</td>
           <td><a href="' . $url . '/invitation-report?invitation_name={{this.[1]}}" target="invitation-{{this.[1]}}">{{this.[1]}}</a></td>
           <td><a href="' . $url . '/wp-admin/post.php?post={{this.[2]}}&action=edit" target="order-{{this.[2]}}">{{this.[2]}}</a></td>
           <td>{{this.[3]}}</td>
           <td>{{this.[4]}}</td>
           <td>{{this.[5]}}</td>
           <td>{{this.[6]}}</td>
           <td>{{this.[7]}}</td>
         </tr>
      {{/each}}
{{#if attributes.suppress_header}}
{{else}}
      <tfoot>
        <th>Coupons</th>
        <th>License Key</th>
        <th>Order</th>
        <th>Purchaser</th>
        <th>Created</th>
        <th>Cart66 Desc</th>
        <th>Seats Available</th>
        <th>Seats Used</th>
      </tfoot>
{{/if}}
    </table>
  </div>
{{else}}
<br/>
You do not have access to this page.
{{/if}}
');


add_filter('bright_extend_on_generic','jc_license_key_report',1,2);

function jc_license_key_report($coursedata,$attr) {
  $bright = \Bright\Wordpress::getInstance();

  if ($attr['template'] == "jc_license_key_report") 
	  return $bright->callApi('stored_query/run',array('params' => array('name'           => 'jc_license_key_report'),
													   'raw'    => true)); /* return raw JSON to the page */

  else
    return $coursedata;
}


function jc_add_query_vars_filter( $vars ){
  $vars[] = "invitation_name";
  return $vars;
}
add_filter( 'query_vars', 'jc_add_query_vars_filter' );


$bright->addTemplate('jc_seats_available_for','
{{#each this.custom}}
<strong>Seats Used: {{this.[7]}} of {{this.[6]}}</strong>
{{/each}}
');

add_filter('bright_extend_on_generic','jc_seats_available_for',1,2);

function jc_seats_available_for($coursedata,$attr) {
  if ($attr['template'] == "jc_seats_available_for")  {
    $invitation_name = get_query_var( 'invitation_name');
    if (!empty($invitation_name)) {
      $bright = \Bright\Wordpress::getInstance();
      $bright->log('jc_seats_available_for invitation_name', $invitation_name);
      
      return $bright->callApi('stored_query/run',array('params' => array('name'           => 'jc_seats_available_for',
                                                                         'invitation_name' => $invitation_name),
                                                       'raw'    => true)); /* return raw JSON to the page */
    } else
      return Array();
  }
  return $coursedata;
}




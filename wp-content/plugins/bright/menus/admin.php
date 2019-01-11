<?php

function bright_menu_admin() {
  if (!current_user_can('manage_options')) {
    wp_die(bright_message('You do not have sufficient permissions to access this page.'));
  }

  $users = get_users();
?>
  <script type="text/javascript">
    var users = [
<?php  

  for($i = 0; $i < count($users); ++$i) {
    $user = $users[$i];
     echo("{\n");
     echo("  name: '$user->user_email',\n");
     echo("  role: 'unknown',\n"); /* TBD */
    echo("},\n");
  }

?>
    ];
  </script>
<?php  

  global $bright_token;
  $providers = bright_get_course_providers($bright_token);
  $current_provider_id = get_user_option('bright_course_provider_id');
  echo 'Course Provider(s): ';
  for ($i = 0; $i < count($providers); ++$i) {
    $provider = $providers[$i];
    if ($provider->id == $current_provider_id) 
      echo htmlspecialchars($provider->name);
    else {
      echo '<a href="/wp-admin/admin.php?page=bright_options_admin&course_provider_id='.
        htmlspecialchars($provider->id).'">'.
        htmlspecialchars($provider->name).'</a>';
    }
    if ($i < count($providers) - 1)
	  echo " | ";
  }
if (!empty($current_provider_id)) {
  echo " | ";
  echo '<a href="/wp-admin/admin.php?page=bright_options_admin&clear_course_provider_id=true">Clear Selected Course Provider</a>';
}


  ?>
    <iframe src="javascript:''" id="__gwt_historyFrame" tabIndex='-1' style="position:absolute;width:0;height:0;border:0"></iframe>
    <noscript>
      <div style="width: 22em; position: absolute; left: 50%; margin-left: -11em; color: red; background-color: white; border: 1px solid red; padding: 4px; font-family: sans-serif">
        Your web browser must have JavaScript enabled
        in order for this application to display correctly.
      </div>
    </noscript>
    <div id="bright-settings"></div>
<?php
}

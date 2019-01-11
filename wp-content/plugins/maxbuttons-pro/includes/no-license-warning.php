<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
?>

<div class='license_warning'>
  <h3><?php _e('No License Found!', 'maxbuttons-pro'); ?> </h3>


  <p><?php _e('MaxButtons PRO requires a license. Activate or renew your license to save or edit buttons, get updates and new features plus support!.','maxbuttons-pro'); ?></p>

  <p class='enter_license'><a href="/wp-admin/admin.php?page=maxbuttons-license"><?php _e('Click to enter license.', 'maxbuttons-pro'); ?></a></p>

  <p><?php printf(__('Your license is in your purchase email or in the %s Account %s section of our Website.', 'maxbuttons-pro'), "<a href='https://maxbuttons.com/my-account' target='_blank'>", "</a>" ); ?></p>

</div>

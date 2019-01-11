
# Removal of bright-launchbox from div id's and classes.

This includes tracking down the css of of this stuff and remove it. 

#  before_bright_rewrite_embed_code filter

rename before_bright_rewrite_embed_code to bright_before_rewrite_embed_code

# $bright_embedder_templates

remove the global $bright_embedder_templates

# courselist custom

remove pushing the entire course list into the custom variable of the courselist.  What was the point of that anyway?

Here's the block from Bright\Wordpress->expandShortCode()

	  $this->renderCourseList($courseList);
	  /* $customData = $this->extensionPoint('filter','bright_extend_on_courselist',$courseList,$attr); */
	  $customData = $this->extensionPoint('filter','bright_extend_on_courselist',null,$attr);

The old bit is the commented out part

# renaming of login_url filter to bright_login_url

Found here:

    wp-content/plugins/bright/bright.php
    wp-content/plugins/bright/php-connect/wordpress.php
    wp-content/plugins/penman-bright-customizations/members-is-user-logged_out-shortcode.php
    wp-content/plugins/penman-bright-customizations/penman-bright-customizations.php

# removal of global $bright_token

This thing ... its everwhere?  Try

    Bright\Wordpress::getInstance()->accessToken;

# wp-deprecation.php

There's a whole bunch of functions in here.   These need to be hunted down and remove from any of the bright
plugins or the customer extensions.



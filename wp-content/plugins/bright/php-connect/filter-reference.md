# bright_before_rewrite_embed_code [Action]

Called with no arguments, prior to the execution of the bright shortcode expander.

Can be used to capture data from a POST, in the case where this data is to be used in rendering the template

    add_action('before_bright_rewrite_embed_code', 'capture_data_from_post');
    
    function capture_data_from_post() {
      $current_user= bright_get_user();
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $somedata = $_POST['somedata'];
        /* do something with it prior to templates being rendered; like save it to the database */
      }
    }

# bright_can_manage_bright [Filter]

Normally, bright admin pages are accessed via:

    current_user_can('manage_options')

You can replace this by overriding the bright_can_manage_bright filter.  In this example; we replicate the functionality.

    function can_manage_bright($menu,$can_manage) {
      return $can_manage; // do something else here to customize
    }
    add_filter('bright_can_manage_bright','can_manage_bright',10, 2);

# bright_course_id [Filter]

      $courseId = apply_filters('bright_course_id',
                                 $course, /* pass in the one from template shortcode if set */
                                 array('attr' => $attr));
    

With this filter, it is possible to override the course_id used in a bright shortcode. One of the most powerful aspects of this is the ability to derive the course to be displayed using custom business logic.

# bright_curl_error [Filter]

During shortcode expansion, if a authentication key isn't available for the user and a cached CURL error from Bright Server is found, the message to be displayed on the page is the server error.

This filter allows a plugin to intercept and rewrite such an error message by replacing it.

# bright_extend_on_course [Filter]

With this filter, you can modify the raw [unparsed string] customData that will be passed to the Bright javascript template expansion.

      $rawCourseData = apply_filters('bright_extend_on_course',$rawCourseData,$courseGuid,$rawRegistrationData);
    

Note that this filter takes 3 arguments:

*   the current custom data [you must parse, alter, and convert back to JSON string].
*   the course GUID
*   the raw registration data for the course; as returned by the getRegistrationDataForCourse() routine running in "raw" mode.

# bright_extend_on_courselist [Filter]

Normally, the bright template for a courselist will contain a blank section called "custom". You can populate the data for this section by implementing a filter called

    apply_filters('bright_extend_on_courselist',$bright_course_list,$attr);
    

It takes 2 arguments:

*   the bright courselist as returned from the Bright API course interface.
*   The attributes passed to the embed code in an array.

Here's a sample implementation:

    add_filter('bright_extend_on_courselist','my_extend_on_courselist',1,2);
    function my_extend_on_courselist($coursedata,$attr) {
      return '{"somejson": {}}';
    }
    

Any JSON you return will be evaulated and placed in the "custom" context variable passed to the Bright template when it renders.

# bright_extend_on_generic [Filter]

Normally, the bright template for a generic will contain a blank section called "custom". You can populate the data for this section by implementing a filter called

    apply_filters('bright_extend_on_generic',null,$attr);
    

It takes 2 arguments:

*   a null ; which can be replaced with any data that the implemented filter would like to make the custom data of the template.
*   The attributes passed to the bright shortcode in an array [like is passed initially by wordpress in the do_shortcode filter].

Here's a sample implementation:

    add_filter('bright_extend_on_generic','my_extend_on_courselist',1,2);
    function my_extend_on_courselist($data,$attr) {
      /* note; $data may be populated by another filter ... so in this case, you'd want to json_parse() it; and then modify the parsed data, then convert it to json and return it */
      return '{"somejson": {}}'; /* only works for a single filter */
    }
    

Any JSON you return will be evaulated and placed in the "custom" context variable passed to the Bright template when it renders.

# bright_get_wp_current_user [filter]

Normally

    $bright->getCurrentUserFromWebstack()
    

returns the results of wp_get_current_user(). You can override this to change the user Bright is authenticated against. For example:

    /*
     * use a shadow user if no one is logged in.   Can be useful to render a bright template on publicly accessible pages.
     * Use w/ caution.
     */
    
    function change_bright_user($user) {
      if (empty($user->ID))
        $user = get_user_by('email','a.shadow.user@notverysecure.com');
      return $user;
    }
    
    add_filter('bright_get_wp_current_user','change_bright_user',1);
    

# bright_is_user_logged_in [filter]

Normally, a call to $bright->isUserLoggedIn() will return true only if a user is logged in, and generally Bright won't render unless this function is returning true.

Via this filter; Bright can be configured to run even if no user is logged in. For example:

    function set_anon_access($loggedIn) {
      return true;
    }
    
    add_filter('bright_is_user_logged_in','set_anon_access',1);
    

Generally used in combination with [bright_get_wp_current_user][1].

# bright_login_url [filter]

When generated message that say things like "please login", bright uses this filter to derive the login URL. The default is:

     '/wp-login.php'
    

# bright_please_login [filter]

When a bright template is attempted to be rendered on page visible to an anonymous [non logged-in user] user, bright will generate a message along the lines of:

"please login or register to view this content"

With a link to the login page [see also the bright_login_url filter to modify this URL]. To modify this message, use the bright_please_login filter.

# bright_support_email [filter]

When generating some error messages, the error message may include an email for support@aura-software.com. If you want to direct support to another email address, you can replace the support email with this filter.

# bright_templates [filter]

Bright uses the following logic to fetch template text:

1.  the Bright class $embedderTemplates variable.
2.  the deprecated $bright_embedder_templates variable [this will be remove in Bright 9.0].
3.  the API is queried to see if the template is stored there.

Prior to checking the API, the bright_templates filter is passed a merged array of the templates derived by step 1 and 2 above. This lets a plugin developer create a template on the fly, and define it.

Here's the relevant code from the Bright Base class:

    $templates = array_merge($bright_embedder_templates,$this->embedderTemplates);
    
    $bright_embedder_templates = $this->extensionPoint('filter','bright_templates',$templates);

 [1]: http://help.aura-software.com/bright-hooks-reference/#bright_get_wp_current_user-filter

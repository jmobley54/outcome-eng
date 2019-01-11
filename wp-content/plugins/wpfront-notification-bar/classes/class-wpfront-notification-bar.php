<?php

/*
  WPFront Notification Bar Plugin
  Copyright (C) 2013, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront Notification Bar Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once("base/class-wpfront-base.php");
require_once("class-wpfront-notification-bar-options.php");

if (!class_exists('WPFront_Notification_Bar')) {

    /**
     * Main class of WPFront Notification Bar plugin
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2013 WPFront.com
     */
    class WPFront_Notification_Bar extends WPFront_Base {

        //Constants
        const VERSION = '1.7.1';
        const OPTIONS_GROUP_NAME = 'wpfront-notification-bar-options-group';
        const OPTION_NAME = 'wpfront-notification-bar-options';
        const PLUGIN_SLUG = 'wpfront-notification-bar';
        //cookie names
        const COOKIE_LANDINGPAGE = 'wpfront-notification-bar-landingpage';
        //role consts
        const ROLE_NOROLE = 'wpfront-notification-bar-role-_norole_';
        const ROLE_GUEST = 'wpfront-notification-bar-role-_guest_';

        //Variables
        protected $options;
        private $markupLoaded;
        private $scriptLoaded;

        function __construct() {
            parent::__construct(__FILE__, self::PLUGIN_SLUG);

            $this->markupLoaded = FALSE;
        }

        public function init() {
            //for landing page tracking
            if (!isset($_COOKIE[self::COOKIE_LANDINGPAGE])) {
                setcookie(self::COOKIE_LANDINGPAGE, 1);
            }
        }
        
        public function admin_menu() {
            $page_hook_suffix = add_options_page($this->__('WPFront Notification Bar'), $this->__('Notification Bar'), 'manage_options', self::PLUGIN_SLUG, array($this, 'options_page'));

            add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_options_scripts'));
            add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_options_styles'));
        }

        //add scripts
        public function enqueue_scripts() {
            if ($this->enabled() == FALSE)
                return;

            $jsRoot = $this->pluginURLRoot . 'js/';

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery.cookie', $this->pluginURLRoot . 'jquery-plugins/jquery.c.js', array('jquery'), '1.4.0');
            wp_enqueue_script('wpfront-notification-bar', $jsRoot . 'wpfront-notification-bar.js', array('jquery'), self::VERSION);

            $this->scriptLoaded = TRUE;

            add_action('wp_footer', array(&$this, 'write_markup'));
            add_action('shutdown', array(&$this, 'write_markup'));
        }

        //add styles
        public function enqueue_styles() {
            if ($this->enabled() == FALSE)
                return;

            $cssRoot = $this->pluginURLRoot . 'css/';

            wp_enqueue_style('wpfront-notification-bar', $cssRoot . 'wpfront-notification-bar.css', array(), self::VERSION);
        }

        public function admin_init() {
            register_setting(self::OPTIONS_GROUP_NAME, self::OPTION_NAME);
        }

        //options page scripts
        public function enqueue_options_scripts() {
            $this->enqueue_scripts();

            wp_enqueue_script('jquery-ui-datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', array('jquery'), '1.8.16');

            wp_enqueue_script('jquery-ui-timepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.8/jquery.timepicker.min.js', array('jquery'), '1.8.8');

            $jsRoot = $this->pluginURLRoot . 'jquery-plugins/colorpicker/js/';
            wp_enqueue_script('jquery.eyecon.colorpicker', $jsRoot . 'colorpicker.js', array('jquery', 'jquery-ui-datepicker'), self::VERSION);

            $jsRoot = $this->pluginURLRoot . 'jquery-plugins/';
            wp_enqueue_script('json2', $jsRoot . 'json2.min.js', array('jquery'), self::VERSION);

//            $jsRoot = $this->pluginURLRoot . 'js/';
//            wp_enqueue_script('wpfront-notification-bar-options', $jsRoot . 'options.js', array(), self::VERSION);
        }

        //options page styles
        public function enqueue_options_styles() {
            $this->enqueue_styles();

            $styleRoot = $this->pluginURLRoot . 'jquery-plugins/jquery-ui/smoothness/';
            wp_enqueue_style('jquery.ui.smoothness.datepicker', $styleRoot . 'jquery-ui-1.10.4.custom.min.css', array(), self::VERSION);

            wp_enqueue_style('jquery.ui.timepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.8/jquery.timepicker.min.css', array(), '1.8.8');

            $styleRoot = $this->pluginURLRoot . 'jquery-plugins/colorpicker/css/';
            wp_enqueue_style('jquery.eyecon.colorpicker.colorpicker', $styleRoot . 'colorpicker.css', array(), self::VERSION);

            $styleRoot = $this->pluginURLRoot . 'css/';
            wp_enqueue_style('wpfront-notification-bar-options', $styleRoot . 'options.css', array(), self::VERSION);
        }

        public function plugins_loaded() {
            //load plugin options
            $this->options = new WPFront_Notification_Bar_Options(self::OPTION_NAME, self::PLUGIN_SLUG);
        }

        //writes the html and script for the bar
        public function write_markup() {
            if ($this->markupLoaded) {
                return;
            }

            if (!$this->scriptLoaded) {
                return;
            }

            if(WPFront_Static::doing_ajax()) {
                return;
            }
            
            if ($this->enabled()) {
                include($this->pluginDIRRoot . 'templates/notification-bar-template.php');

                echo '<script type="text/javascript">';
                echo 'if(typeof wpfront_notification_bar == "function") ';
                echo 'wpfront_notification_bar(' . json_encode(array(
                    'position' => $this->options->position(),
                    'height' => $this->options->height(),
                    'fixed_position' => $this->options->fixed_position(),
                    'animate_delay' => $this->options->animate_delay(),
                    'close_button' => $this->options->close_button(),
                    'button_action_close_bar' => $this->options->button_action_close_bar(),
                    'auto_close_after' => $this->options->auto_close_after(),
                    'display_after' => $this->options->display_after(),
                    'is_admin_bar_showing' => WPFront_Static::is_admin_bar_showing(),
                    'display_open_button' => $this->options->display_open_button(),
                    'keep_closed' => $this->options->keep_closed(),
                    'keep_closed_for' => $this->options->keep_closed_for(),
                    'position_offset' => $this->options->position_offset(),
                    'display_scroll' => $this->options->display_scroll(),
                    'display_scroll_offset' => $this->options->display_scroll_offset(),
                )) . ');';
                echo '</script>';
            }

            $this->markupLoaded = TRUE;
        }

        protected function get_message_text() {
            $message = $this->options->message();

            if ($this->options->message_process_shortcode()) {
                $message = do_shortcode($message);
            }

            return $message;
        }
        
        protected function get_button_text() {
            $text = $this->options->button_text();

            if ($this->options->message_process_shortcode()) {
                $text = do_shortcode($text);
            }

            return $text;
        }

        protected function get_filter_objects() {
            $objects = array();

            $objects['1.home'] = $this->__('[Page]') . ' ' . $this->__('Home');

            $pages = get_pages();
            foreach ($pages as $page) {
                $objects['1.' . $page->ID] = $this->__('[Page]') . ' ' . $page->post_title;
            }

            $posts = get_posts();
            foreach ($posts as $post) {
                $objects['2.' . $post->ID] = $this->__('[Post]') . ' ' . $post->post_title;
            }

//            $categories = get_categories();
//            foreach ($categories as $category) {
//                $objects['3.' . $category->cat_ID] = $this->__('[Category]') . ' ' . $category->cat_name;
//            }

            return $objects;
        }

        protected function get_role_objects() {
            $objects = array();
            global $wp_roles;

            $roles = $wp_roles->role_names;
            foreach ($roles as $role_name => $role_display_name) {
                $objects[$role_name] = $role_display_name;
            }

            return $objects;
        }

        protected function filter() {
            if (is_admin())
                return TRUE;

            $now = current_time('mysql');
            $now = strtotime($now);
            $now = date('Y-m-d h:i a', $now);
            $now = strtotime($now);

            $start_date = $this->options->start_date();
            if ($start_date != NULL) {
                $start_date = date('Y-m-d', $start_date);
                $start_time = $this->options->start_time();
                if($start_time == NULL) {
                    $start_time = '12:00 am';
                } else {
                    $start_time = date('h:i a', $start_time);
                }
                $start_date = $start_date . ' ' . $start_time;
                $start_date = strtotime($start_date);
                
                if ($start_date > $now)
                    return FALSE;
            }
            
            $end_date = $this->options->end_date();
            if ($end_date != NULL) {
                $end_date = date('Y-m-d', $end_date);
                $end_time = $this->options->end_time();
                if($end_time == NULL) {
                    $end_time = '11:59 pm';
                } else {
                    $end_time = date('h:i a', $end_time);
                }
                
                $end_date = $end_date . ' ' . $end_time;
                $end_date = strtotime($end_date);
                
                if ($end_date < $now)
                    return FALSE;
            }
            
            switch ($this->options->display_roles()) {
                case 1:
                    break;
                case 2:
                    if (!$this->is_user_logged_in())
                        return FALSE;
                    break;
                case 3:
                    if ($this->is_user_logged_in())
                        return FALSE;
                    break;
                case 4:
                    global $current_user;
                    if (empty($current_user->roles)) {
                        $role = self::ROLE_GUEST;
                        if ($this->is_user_logged_in())
                            $role = self::ROLE_NOROLE;
                        if (!in_array($role, $this->options->include_roles()))
                            return FALSE;
                    } else {
                        $display = FALSE;
                        foreach ($current_user->roles as $role) {
                            if (in_array($role, $this->options->include_roles())) {
                                $display = TRUE;
                                break;
                            }
                        }
                        if (!$display)
                            return FALSE;
                    }
                    break;
            }

            switch ($this->options->display_pages()) {
                case 1:
                    return TRUE;
                case 2:
                    return !isset($_COOKIE[self::COOKIE_LANDINGPAGE]);
                case 3:
                case 4:
                    global $post;
                    $ID = FALSE;
                    $type = FALSE;
                    if (is_home()) {
                        $ID = 'home';
                        $type = 1;
                    } elseif (is_singular()) {
                        $post_type = get_post_type();
                        if ($post_type == 'page') {
                            $ID = $post->ID;
                            $type = 1;
                        } elseif ($post_type == 'post') {
                            $ID = $post->ID;
                            $type = 2;
                        }
                    }
                    if ($this->options->display_pages() == 3) {
                        if ($ID !== FALSE && $type !== FALSE) {
                            if ($this->filter_pages_contains($this->options->include_pages(), $type . '.' . $ID) === FALSE)
                                return FALSE;
                            else
                                return TRUE;
                        }
                        return FALSE;
                    }
                    if ($this->options->display_pages() == 4) {
                        if ($ID !== FALSE && $type !== FALSE) {
                            if ($this->filter_pages_contains($this->options->exclude_pages(), $type . '.' . $ID) === FALSE)
                                return TRUE;
                            else
                                return FALSE;
                        }
                        return TRUE;
                    }
            }

            return TRUE;
        }

        protected function is_user_logged_in() {
            $logged_in = is_user_logged_in();

            if ($this->options->wp_emember_integration() && function_exists('wp_emember_is_member_logged_in')) {
                $logged_in = $logged_in || wp_emember_is_member_logged_in();
            }

            return $logged_in;
        }

        public function filter_pages_contains($list, $key) {
            return strpos(',' . $list . ',', ',' . $key . ',');
        }

        protected function enabled() {
            if ($this->options->enabled()) {
                return $this->filter();
            }

            return FALSE;
        }

    }

}

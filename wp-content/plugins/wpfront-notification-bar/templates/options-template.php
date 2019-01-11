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

/**
 * Template for WPFront Notification Bar Options
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2013 WPFront.com
 */
?>

<?php @$this->options_page_header($this->__('WPFront Notification Bar Settings'), WPFront_Notification_Bar::OPTIONS_GROUP_NAME); ?>

<h3><?php echo $this->__('Display'); ?></h3>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php echo $this->options->enabled_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->enabled_name(); ?>" <?php echo $this->options->enabled() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->position_label(); ?>
        </th>
        <td>
            <select name="<?php echo $this->options->position_name(); ?>">
                <option value="1" <?php echo $this->options->position() == '1' ? 'selected' : ''; ?>><?php echo $this->__('Top'); ?></option>
                <option value="2" <?php echo $this->options->position() == '2' ? 'selected' : ''; ?>><?php echo $this->__('Bottom'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->fixed_position_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->fixed_position_name(); ?>" <?php echo $this->options->fixed_position() ? 'checked' : ''; ?> />&#160;<span class="description"><?php echo $this->__('[Sticky Bar, bar will stay at position regardless of scrolling.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_scroll_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->display_scroll_name(); ?>" <?php echo $this->options->display_scroll() ? 'checked' : ''; ?> />&#160;<span class="description"><?php echo $this->__('[Displays the bar on window scroll.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_scroll_offset_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->display_scroll_offset_name(); ?>" value="<?php echo $this->options->display_scroll_offset(); ?>" />&#160;<?php echo $this->__('px'); ?>&#160;<span class="description">[<?php echo $this->__('Number of pixels to be scrolled before the bar appears.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->height_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->height_name(); ?>" value="<?php echo $this->options->height(); ?>" />&#160;<?php echo $this->__('px'); ?>&#160;<span class="description">[<?php echo $this->__('Set 0px to auto fit contents.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->position_offset_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->position_offset_name(); ?>" value="<?php echo $this->options->position_offset(); ?>" />&#160;<?php echo $this->__('px'); ?>&#160;<span class="description">[<?php echo $this->__('(Top bar only) If you find the bar overlapping, try increasing this value. (eg. WordPress 3.8 Twenty Fourteen theme, set 48px)'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_after_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->display_after_name(); ?>" value="<?php echo $this->options->display_after(); ?>" />&#160;<?php echo $this->__('second(s)'); ?>&#160;<span class="description">[<?php echo $this->__('Set 0 second(s) to display immediately. Do not work in "Display on Scroll" mode.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->animate_delay_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->animate_delay_name(); ?>" value="<?php echo $this->options->animate_delay(); ?>" />&#160;<?php echo $this->__('second(s)'); ?>&#160;<span class="description">[<?php echo $this->__('Set 0 second(s) for no animation.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->close_button_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->close_button_name(); ?>" <?php echo $this->options->close_button() ? 'checked' : ''; ?> />&#160;<span class="description"><?php echo $this->__('[Displays a close button at the top right corner of the bar.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->auto_close_after_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->auto_close_after_name(); ?>" value="<?php echo $this->options->auto_close_after(); ?>" />&#160;<?php echo $this->__('second(s)'); ?>&#160;<span class="description">[<?php echo $this->__('Set 0 second(s) to disable auto close. Do not work in "Display on Scroll" mode.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_shadow_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->display_shadow_name(); ?>" <?php echo $this->options->display_shadow() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_open_button_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->display_open_button_name(); ?>" <?php echo $this->options->display_open_button() ? 'checked' : ''; ?> />&#160;<span class="description">[<?php echo $this->__('A reopen button will be displayed after the bar is closed.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->keep_closed_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->keep_closed_name(); ?>" <?php echo $this->options->keep_closed() ? 'checked' : ''; ?> />&#160;<span class="description">[<?php echo $this->__('Once closed, bar will display closed on other pages.'); ?>]</span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->keep_closed_for_label(); ?>
        </th>
        <td>
            <input class="seconds" name="<?php echo $this->options->keep_closed_for_name(); ?>" value="<?php echo $this->options->keep_closed_for(); ?>" />&#160;<?php echo $this->__('day(s)'); ?>&#160;<span class="description">[<?php echo $this->__('Bar will be kept closed for the number of days specified from last closed date.'); ?>]</span>
        </td>
    </tr>
</table>

<h3><?php echo $this->__('Content'); ?></h3>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php echo $this->options->message_label(); ?>
        </th>
        <td>
            <textarea rows="5" cols="75" name="<?php echo $this->options->message_name(); ?>"><?php echo $this->options->message(); ?></textarea>
            <br />
            <span class="description"><?php echo esc_html($this->__('[HTML tags are allowed. e.g. Add <br /> for break.]')); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->message_process_shortcode_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->message_process_shortcode_name(); ?>" <?php echo $this->options->message_process_shortcode() ? 'checked' : ''; ?> />&#160;<span class="description"><?php echo $this->__('[Processes shortcodes in message text.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_button_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->display_button_name(); ?>" <?php echo $this->options->display_button() ? 'checked' : ''; ?> />&#160;<span class="description"><?php echo $this->__('[Displays a button next to the message.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->button_text_label(); ?>
        </th>
        <td>
            <input name="<?php echo $this->options->button_text_name(); ?>" value="<?php echo $this->options->button_text(); ?>" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->button_action_label(); ?>
        </th>
        <td>
            <label>
                <input type="radio" name="<?php echo $this->options->button_action_name(); ?>" value="1" <?php echo $this->options->button_action() == 1 ? 'checked' : ''; ?> />
                <span><?php echo $this->options->button_action_url_label(); ?></span>
            </label>
            <input class="URL" name="<?php echo $this->options->button_action_url_name(); ?>" value="<?php echo $this->options->button_action_url(); ?>" />
            <br />
            <label>
                <input type="checkbox" name="<?php echo $this->options->button_action_new_tab_name(); ?>" <?php echo $this->options->button_action_new_tab() ? 'checked' : ''; ?> />
                <span><?php echo $this->options->button_action_new_tab_label(); ?></span>
            </label>
            <br />
            <label>
                <input type="checkbox" name="<?php echo $this->options->button_action_url_nofollow_name(); ?>" <?php echo $this->options->button_action_url_nofollow() ? 'checked' : ''; ?> />
                <span><?php echo $this->options->button_action_url_nofollow_label(); ?></span>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo $this->options->button_action_name(); ?>" value="2" <?php echo $this->options->button_action() == 2 ? 'checked' : ''; ?> />
                <span><?php echo $this->options->button_action_javascript_label(); ?></span>
            </label>
            <br />
            <textarea rows="5" cols="75" name="<?php echo $this->options->button_action_javascript_name(); ?>"><?php echo $this->options->button_action_javascript(); ?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->button_action_close_bar_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->button_action_close_bar_name(); ?>" <?php echo $this->options->button_action_close_bar() ? 'checked' : ''; ?> />
        </td>
    </tr>
</table>

<h3><?php echo $this->__('Filter'); ?></h3>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php echo $this->options->start_date_label(); ?>
        </th>
        <td>
            <input class="date" name="<?php echo $this->options->start_date_name(); ?>" value="<?php echo $this->options->start_date() == NULL ? '' : date('Y-m-d', $this->options->start_date()); ?>" />
            <input class="time" name="<?php echo $this->options->start_time_name(); ?>" value="<?php echo $this->options->start_time() == NULL ? '' : date('h:i a', $this->options->start_time()); ?>" />
            &#160;
            <span class="description"><?php echo $this->__('[YYYY-MM-DD] [hh:mm ap]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->end_date_label(); ?>
        </th>
        <td>
            <input class="date" name="<?php echo $this->options->end_date_name(); ?>" value="<?php echo $this->options->end_date() == NULL ? '' : date('Y-m-d', $this->options->end_date()); ?>" />
            <input class="time" name="<?php echo $this->options->end_time_name(); ?>" value="<?php echo $this->options->end_time() == NULL ? '' : date('h:i a', $this->options->end_time()); ?>" />
            &#160;
            <span class="description"><?php echo $this->__('[YYYY-MM-DD] [hh:mm ap]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_pages_label(); ?>
        </th>
        <td>
            <label>
                <input type="radio" name="<?php echo $this->options->display_pages_name(); ?>" value="1" <?php echo $this->options->display_pages() == 1 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('All pages.'); ?></span>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo $this->options->display_pages_name(); ?>" value="2" <?php echo $this->options->display_pages() == 2 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('Only in landing page.'); ?></span>&#160;<span class="description"><?php echo $this->__('[The first page they visit on your website.]'); ?></span>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo $this->options->display_pages_name(); ?>" value="3" <?php echo $this->options->display_pages() == 3 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('Include in following pages'); ?></span>
            </label>
            <div class="pages-selection">
                <input type="hidden" name="<?php echo $this->options->include_pages_name(); ?>" value="<?php echo $this->options->include_pages(); ?>" />
                <?php
                $objects = $this->get_filter_objects();
                foreach ($objects as $key => $value) {
                    ?>
                    <div class="page-div">
                        <label>
                            <input type="checkbox" value="<?php echo $key; ?>" <?php echo $this->filter_pages_contains($this->options->include_pages(), $key) === FALSE ? '' : 'checked'; ?> />
                            <?php echo $value; ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </div>
            <label>
                <input type="radio" name="<?php echo $this->options->display_pages_name(); ?>" value="4" <?php echo $this->options->display_pages() == 4 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('Exclude in following pages'); ?></span>
            </label>
            <div class="pages-selection">
                <input type="hidden" name="<?php echo $this->options->exclude_pages_name(); ?>" value="<?php echo $this->options->exclude_pages(); ?>" />
                <?php
                $objects = $this->get_filter_objects();
                foreach ($objects as $key => $value) {
                    ?>
                    <div class="page-div">
                        <label>
                            <input type="checkbox" value="<?php echo $key; ?>" <?php echo $this->filter_pages_contains($this->options->exclude_pages(), $key) === FALSE ? '' : 'checked'; ?> />
                            <?php echo $value; ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->display_roles_label(); ?>
        </th>
        <td>
            <label>
                <input type="radio" name="<?php echo $this->options->display_roles_name(); ?>" value="1" <?php echo $this->options->display_roles() == 1 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('All users.'); ?></span>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo $this->options->display_roles_name(); ?>" value="2" <?php echo $this->options->display_roles() == 2 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('All logged in users.'); ?></span>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo $this->options->display_roles_name(); ?>" value="3" <?php echo $this->options->display_roles() == 3 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('Guest users. [Non-logged in users]'); ?></span>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo $this->options->display_roles_name(); ?>" value="4" <?php echo $this->options->display_roles() == 4 ? 'checked' : ''; ?> />
                <span><?php echo $this->__('For following user roles'); ?></span>&nbsp;<span>[<a target="_blank" href="https://wpfront.com/nbtoure"><?php echo $this->__('Manage Roles'); ?>]</a></span>
            </label>
            <br />
            <div class="roles-selection">
                <input type="hidden" name="<?php echo $this->options->include_roles_name(); ?>" value="<?php echo htmlentities(json_encode($this->options->include_roles())); ?>" />
                <?php
                foreach ($this->get_role_objects() as $key => $value) {
                    ?>
                    <div class="role-div">
                        <label>
                            <input type="checkbox" value="<?php echo $key; ?>" <?php echo in_array($key, $this->options->include_roles()) === FALSE ? '' : 'checked'; ?> />
                            <?php echo $value; ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
                <div class="role-div">
                    <label>
                        <input type="checkbox" value="<?php echo WPFront_Notification_Bar::ROLE_NOROLE; ?>" <?php echo in_array(WPFront_Notification_Bar::ROLE_NOROLE, $this->options->include_roles()) === FALSE ? '' : 'checked'; ?> />
                        <?php echo $this->__('[No Role]'); ?>
                    </label>
                </div>
                <div class="role-div">
                    <label>
                        <input type="checkbox" value="<?php echo WPFront_Notification_Bar::ROLE_GUEST; ?>" <?php echo in_array(WPFront_Notification_Bar::ROLE_GUEST, $this->options->include_roles()) === FALSE ? '' : 'checked'; ?> />
                        <?php echo $this->__('[Guest]'); ?>
                    </label>
                </div>
            </div>
            <label>
                <input type="checkbox" name="<?php echo $this->options->wp_emember_integration_name(); ?>" <?php echo $this->options->wp_emember_integration() ? 'checked' : ''; ?> />
                <span><?php echo $this->__('Enable WP eMember integration.'); ?></span>
            </label>
        </td>
    </tr>
</table>

<h3><?php echo $this->__('Color'); ?></h3>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php echo $this->__('Bar Color'); ?>
        </th>
        <td>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->bar_from_color(); ?>"></div>&#160;<span><?php echo $this->options->bar_from_color(); ?></span>
                <input type="hidden" name="<?php echo $this->options->bar_from_color_name(); ?>" value="<?php echo $this->options->bar_from_color(); ?>" />
            </div>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->bar_to_color(); ?>"></div>&#160;<span><?php echo $this->options->bar_to_color(); ?></span>
                <input type="hidden" name="<?php echo $this->options->bar_to_color_name(); ?>" value="<?php echo $this->options->bar_to_color(); ?>" />
            </div>
            <span class="description"><?php echo $this->__('[Select two different colors to create a gradient.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->message_color_label(); ?>
        </th>
        <td>
            <div class="color-selector" color="<?php echo $this->options->message_color(); ?>"></div>&#160;<span><?php echo $this->options->message_color(); ?></span>
            <input type="hidden" name="<?php echo $this->options->message_color_name(); ?>" value="<?php echo $this->options->message_color(); ?>" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->__('Button Color'); ?>
        </th>
        <td>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->button_from_color(); ?>"></div>&#160;<span><?php echo $this->options->button_from_color(); ?></span>
                <input type="hidden" name="<?php echo $this->options->button_from_color_name(); ?>" value="<?php echo $this->options->button_from_color(); ?>" />
            </div>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->button_to_color(); ?>"></div>&#160;<span><?php echo $this->options->button_to_color(); ?></span>
                <input type="hidden" name="<?php echo $this->options->button_to_color_name(); ?>" value="<?php echo $this->options->button_to_color(); ?>" />
            </div>
            <span class="description"><?php echo $this->__('[Select two different colors to create a gradient.]'); ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->button_text_color_label(); ?>
        </th>
        <td>
            <div class="color-selector" color="<?php echo $this->options->button_text_color(); ?>"></div>&#160;<span><?php echo $this->options->button_text_color(); ?></span>
            <input type="hidden" name="<?php echo $this->options->button_text_color_name(); ?>" value="<?php echo $this->options->button_text_color(); ?>" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->open_button_color_label(); ?>
        </th>
        <td>
            <div class="color-selector" color="<?php echo $this->options->open_button_color(); ?>"></div>&#160;<span><?php echo $this->options->open_button_color(); ?></span>
            <input type="hidden" name="<?php echo $this->options->open_button_color_name(); ?>" value="<?php echo $this->options->open_button_color(); ?>" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->close_button_color_label(); ?>
        </th>
        <td>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->close_button_color(); ?>"></div>&#160;<span><?php echo $this->options->close_button_color(); ?></span>
                <input type="hidden" name="<?php echo $this->options->close_button_color_name(); ?>" value="<?php echo $this->options->close_button_color(); ?>" />
            </div>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->close_button_color_hover(); ?>"></div>&#160;<span><?php echo $this->options->close_button_color_hover(); ?></span>
                <input type="hidden" name="<?php echo $this->options->close_button_color_hover_name(); ?>" value="<?php echo $this->options->close_button_color_hover(); ?>" />
            </div>
            <div class="color-selector-div">
                <div class="color-selector" color="<?php echo $this->options->close_button_color_x(); ?>"></div>&#160;<span><?php echo $this->options->close_button_color_x(); ?></span>
                <input type="hidden" name="<?php echo $this->options->close_button_color_x_name(); ?>" value="<?php echo $this->options->close_button_color_x(); ?>" />
            </div>
            <span class="description"><?php echo $this->__('[Normal, Hover, X]'); ?></span>
        </td>
    </tr>
</table>

<h3><?php echo $this->__('CSS'); ?></h3>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php echo $this->options->custom_css_label(); ?>
        </th>
        <td>
            <textarea name="<?php echo $this->options->custom_css_name(); ?>" rows="10" cols="75"><?php echo $this->options->custom_css(); ?></textarea>
        </td>
    </tr>
</table>

<?php
@$this->options_page_footer('notification-bar-plugin-settings/', 'notification-bar-plugin-faq/', array(array(
                'href' => 'http://wpfront.com/notification-bar-plugin-ideas/',
                'target' => '_blank',
                'text' => $this->__('Plugin Ideas')
        )));
?>

<script type="text/javascript">
    (function($) {
        function setColorPicker(div) {
            div.ColorPicker({
                color: div.attr('color'),
                onShow: function(colpkr) {
                    $(colpkr).fadeIn(500);
                    return false;
                }, onHide: function(colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function(hsb, hex, rgb) {
                    div.css('backgroundColor', '#' + hex);
                    div.next().text('#' + hex).next().val('#' + hex);
                }
            }).css('backgroundColor', div.attr('color'));
        }

        $('#wpfront-notification-bar-options').find(".color-selector").each(function(i, e) {
            setColorPicker($(e));
        });

        $('#wpfront-notification-bar-options .pages-selection input[type="checkbox"]').change(function() {
            var values = [];
            var div = $(this).parent().parent().parent();
            div.find('input:checked').each(function(i, e) {
                values.push($(e).val());
            });
            div.children(":first").val(values.join());
        });
        
        $('#wpfront-notification-bar-options .roles-selection input[type="checkbox"]').change(function() {
            var values = [];
            var div = $(this).parent().parent().parent();
            div.find('input:checked').each(function(i, e) {
                values.push($(e).val());
            });
            div.children(":first").val(JSON.stringify(values));
        });
        
        $('#wpfront-notification-bar-options input.date').datepicker({
            'dateFormat' : 'yy-mm-dd'
        });
        
        $('#wpfront-notification-bar-options input.time').timepicker({
            'timeFormat': 'h:i a'
        });

    })(jQuery);
</script>
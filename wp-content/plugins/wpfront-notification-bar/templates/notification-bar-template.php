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
 * Template for WPFront Notification Bar
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2013 WPFront.com
 */
?>

<style type="text/css">
    #wpfront-notification-bar 
    {
        background: <?php echo $this->options->bar_from_color(); ?>;
        background: -moz-linear-gradient(top, <?php echo $this->options->bar_from_color(); ?> 0%, <?php echo $this->options->bar_to_color(); ?> 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $this->options->bar_from_color(); ?>), color-stop(100%,<?php echo $this->options->bar_to_color(); ?>));
        background: -webkit-linear-gradient(top, <?php echo $this->options->bar_from_color(); ?> 0%,<?php echo $this->options->bar_to_color(); ?> 100%);
        background: -o-linear-gradient(top, <?php echo $this->options->bar_from_color(); ?> 0%,<?php echo $this->options->bar_to_color(); ?> 100%);
        background: -ms-linear-gradient(top, <?php echo $this->options->bar_from_color(); ?> 0%,<?php echo $this->options->bar_to_color(); ?> 100%);
        background: linear-gradient(to bottom, <?php echo $this->options->bar_from_color(); ?> 0%, <?php echo $this->options->bar_to_color(); ?> 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $this->options->bar_from_color(); ?>', endColorstr='<?php echo $this->options->bar_to_color(); ?>',GradientType=0 );
    }

    #wpfront-notification-bar div.wpfront-message
    {
        color: <?php echo $this->options->message_color(); ?>;
    }

    #wpfront-notification-bar a.wpfront-button
    {
        background: <?php echo $this->options->button_from_color(); ?>;
        background: -moz-linear-gradient(top, <?php echo $this->options->button_from_color(); ?> 0%, <?php echo $this->options->button_to_color(); ?> 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $this->options->button_from_color(); ?>), color-stop(100%,<?php echo $this->options->button_to_color(); ?>));
        background: -webkit-linear-gradient(top, <?php echo $this->options->button_from_color(); ?> 0%,<?php echo $this->options->button_to_color(); ?> 100%);
        background: -o-linear-gradient(top, <?php echo $this->options->button_from_color(); ?> 0%,<?php echo $this->options->button_to_color(); ?> 100%);
        background: -ms-linear-gradient(top, <?php echo $this->options->button_from_color(); ?> 0%,<?php echo $this->options->button_to_color(); ?> 100%);
        background: linear-gradient(to bottom, <?php echo $this->options->button_from_color(); ?> 0%, <?php echo $this->options->button_to_color(); ?> 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $this->options->button_from_color(); ?>', endColorstr='<?php echo $this->options->button_to_color(); ?>',GradientType=0 );

        color: <?php echo $this->options->button_text_color(); ?>;
    }

    #wpfront-notification-bar-open-button
    {
        background-color: <?php echo $this->options->open_button_color(); ?>;
    }

    #wpfront-notification-bar  div.wpfront-close 
    {
        border: 1px solid <?php echo $this->options->close_button_color(); ?>;
        background-color: <?php echo $this->options->close_button_color(); ?>;
        color: <?php echo $this->options->close_button_color_x(); ?>;
    }

    #wpfront-notification-bar  div.wpfront-close:hover 
    {
        border: 1px solid <?php echo $this->options->close_button_color_hover(); ?>;
        background-color: <?php echo $this->options->close_button_color_hover(); ?>;
    }
</style>

<?php if ($this->options->display_button() && $this->options->button_action() == 2) { ?>
    <script type="text/javascript">
        function wpfront_notification_bar_button_action_script() {
            try {
    <?php echo $this->options->button_action_javascript(); ?>
            }
            catch (err) {
            }
        }
    </script>
<?php } ?>

<div id="wpfront-notification-bar-spacer"  style="display: none;">
    <div id="wpfront-notification-bar-open-button" class="<?php echo $this->options->position() == 1 ? 'top wpfront-bottom-shadow' : 'bottom wpfront-top-shadow'; ?>"></div>
    <div id="wpfront-notification-bar" class="wpfront-fixed <?php if ($this->options->display_shadow()) echo $this->options->position() == 1 ? 'wpfront-bottom-shadow' : 'wpfront-top-shadow'; ?>">
        <?php if ($this->options->close_button()) { ?>
            <div class="wpfront-close">X</div>
        <?php } ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div class="wpfront-message">
                        <?php echo $this->get_message_text(); ?>
                    </div>
                    <div>
                        <?php 
                            if ($this->options->display_button()) { 
                                $button_text = $this->get_button_text();
                                ?>
                                <?php if ($this->options->button_action() == 1) { ?>
                                    <a class="wpfront-button" href="<?php echo $this->options->button_action_url(); ?>"  target="<?php echo $this->options->button_action_new_tab() ? '_blank' : '_self'; ?>" <?php echo $this->options->button_action_url_nofollow() ? 'rel="nofollow"' : ''; ?>><?php echo $button_text; ?></a>
                                <?php } ?>
                                <?php if ($this->options->button_action() == 2) { ?>
                                    <a class="wpfront-button" onclick="javascript:wpfront_notification_bar_button_action_script();"><?php echo $button_text; ?></a>
                                <?php } ?>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>


<style type="text/css">
<?php echo $this->options->custom_css(); ?>
</style>

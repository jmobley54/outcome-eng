<?php
namespace maxButtons;
defined('ABSPATH') or die('No direct access permitted');


class gutenBerg{

  public static function init()
  {
      add_action( 'enqueue_block_editor_assets', array(maxUtils::namespaceit('gutenBerg'), 'editor_scripts') );
      add_action( 'maxbuttons/ajax/gutenberg_button', array(maxUtils::namespaceit('gutenBerg'), 'generate_button'));
      add_action('init', array(maxUtils::namespaceit('gutenBerg'), 'register_block'));

  }

  public static function editor_scripts()
  {
    $version = MAXBUTTONS_VERSION_NUM;

    wp_register_script(
  		'maxbuttons_gutenberg-js', // Handle.
  		plugins_url( 'blocks.build.js',  __FILE__  ), // Block.build.js: We register the block here. Built with Webpack.
  		array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
  		$version,
  		true // Enqueue the script in the footer.
  	);

    wp_localize_script('maxbuttons_gutenberg-js', 'mb_gutenberg', array(
        'ispro' => (defined('MAXBUTTONS_PRO_ROOT_FILE')) ? true : false,
        'icon_url' => MB()->get_plugin_url() . '/images/mb-32.png',
    ));

    wp_enqueue_script('maxbuttons_gutenberg-js');

  	// Styles.
  	wp_enqueue_style(
  		'maxbuttons_block-editor-css', // Handle.
  		plugins_url( 'blocks.editor.build.css',  __FILE__  ), // Block editor CSS.
  		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
  		$version
  	);

  }

  public static function register_block()
  {
    if (function_exists('register_block_type'))
    {
      register_block_type( 'maxbuttons/maxbuttons-block', array(
         'render_callback' => array(maxUtils::namespaceit('gutenBerg'), 'render_shortcode'),
        ) );
    }
  }

  public static function generate_button($post)
  {
    $id = isset($post['id']) ? $post['id'] : false;
    $text = isset($post['text']) ? $post['text'] : null;
    $text2 = isset($post['text2']) ? $post['text2']: null;
    $url = isset($post['url']) ? $post['url'] : null;
    $linktitle = isset($post['linktitle']) ? $post['linktitle'] : null;
    $window = isset($post['newwindow']) ? $post['newwindow']: null;
    $nofollow = isset($post['nofollow']) ? $post['nofollow'] : null;
    $extraclass = isset($post['extraclass']) ? $post['extraclass'] : null;
    $reset = isset($post['reset']) ? $post['reset'] : false;

    if ($window == 'true')
      $window = 'new';

    $button = MB()->getClass("button");

    if ($reset == 'true')
    {
      $shortcode_args = array('id' => $id);
    }
    else {
      $shortcode_args = array(
          'id' => $id,
          'text' => $text,
          'text2' => $text2,
          'url' => $url,
          'linktitle' => $linktitle,
          'window' => $window,
          'nofollow' => $nofollow,
    //      'extraclass' => $extraclass,
        );
    }

    $the_button = $button->shortcode($shortcode_args);

    $response = array(
        'button' => $the_button,
        'style' => admin_url('admin-ajax.php'). '?action=maxbuttons_front_css&id=' . $id,
        'attributes' => false,
      );

    if ($reset == 'true') // on load new button, put all fields to the buttons values
    {
      $data = $button->get();

      $text = $data['text']['text'];
      $text2 = isset($data['text']['text2'])? $data['text']['text2'] : '';

      $url = $data['basic']['url'];
      $linktitle = $data['basic']['link_title'];
      $window = $data['basic']['new_window'];
      $nofollow= $data['basic']['nofollow'];

      $response['attributes'] = array(
         'id' => $id,
         'text' => $text,
         'text2' => $text2,
         'url' => $url,
         'tooltip' => $linktitle,
         'newwindow' => ($window == 1) ? true : false,
         'relnofollow' => ($nofollow == 1) ? true : false,
      );
    }

    wp_send_json_success($response);
  }

  public static function render_shortcode($atts)
  {
      if (! isset($atts['id'])) // no id, no button
      {
        return;
      }

      $id = $atts['id'];

      $args = array(
          'id' => $atts['id'],
          'text' => isset($atts['text']) ? $atts['text'] : null,
          'text2' => isset($atts['text2']) ? $atts['text2'] : null,
          'url' => isset($atts['url']) ? $atts['url'] : null,
          'linktitle' => isset($atts['tooltip']) ? $atts['tooltip'] : null,
          'window' => isset($atts['newwindow']) && $atts['newwindow'] == 1 ? 'new' : null,
          'nofollow' => isset($atts['relnofollow']) && $atts['relnofollow'] == 1 ? 'true' : null,
      //    'extraclass' => isset($atts['className']) ? $atts['className'] : null,
      );

      $button = MB()->getClass("button");
      $thebutton = $button->shortcode($args);

      return $thebutton;
  }

} // class



gutenBerg::init();

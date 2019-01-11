<?php
namespace OutcomeEngenuity;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Skilljar email  class used to send out welcome emails to the Skilljar product.
 *
 * @extends \WC_Email
 */

class SkilljarWelcomeEmail extends \WC_Email {
  
  /**
   * Set email defaults
   */
  public function __construct() {

    // Unique ID for custom email
    $this->id = 'oe_skilljar_welcome_email';

    // Is a customer email
    $this->customer_email = true;
    
    // Title field in WooCommerce Email settings
    $this->title = __( 'Just Culture Academy Welcome', 'woocommerce' );

    // Description field in WooCommerce email settings
    $this->description = __( 'Welcome email is sent with the login token for the Academy Product Bundle.', 'woocommerce' );

    // Default heading and subject lines in WooCommerce email settings
    $this->subject = apply_filters( 'oe_skilljar_welcome_email_default_subject', __( 'Your Just Culture Academy Login', 'woocommerce' ) );
    $this->heading = apply_filters( 'oe_skilljar_welcome_email_default_heading', __( 'Your Just Culture Academy Login', 'woocommerce' ) );
    
    $this->template_base  = plugin_dir_path( __FILE__ ) . 'emails/';  // Fix the template base lookup for use on admin screen template path display

    $this->template_html  = 'skilljar-welcome-email.html.php';
    $this->template_plain = 'skilljar-welcome-email.txt.php';

    \add_action( 'woocommerce_payment_complete', [ $this, 'trigger' ] );

    // Call parent constructor to load any other defaults not explicity defined here
    parent::__construct();
  }


  /**
   * Prepares email content and triggers the email
   *
   * @param int $order_id
   */
  public function trigger( $order_id ) {

    // Bail if no order ID is present
    if ( ! $order_id )
      return;
    
    // Send welcome email only once and not on every order status change    
    if ( ! get_post_meta( $order_id, '_oe_skilljar_welcome_email', true ) ) {
      
      // setup order object
      $this->object = new \WC_Order( $order_id );
      
      // get order items as array
      $order_items = $this->object->get_items();

      $item_ids = [];

      // collect the product ids in the order
      foreach ( $order_items as $item ) {
        $item_ids[] = $item->get_product_id();
      }

      // determine if any of the ids in the order are not a skilljar product, exit 
      if ( count(array_intersect(\OE_SKILLJAR_ASSESSMENT_IDS, $item_ids)) == 0) {
          return;
      }

      /* Proceed with sending email */
      
      $this->recipient = $this->object->get_billing_email();

      // replace variables in the subject/headings
      $this->find[] = '{order_date}';
      $this->replace[] = date_i18n( \woocommerce_date_format(), strtotime( $this->object->order_date ) );

      $this->find[] = '{order_number}';
      $this->replace[] = $this->object->get_order_number();

      global $oe_skilljar_code;
      $this->skilljar_code = $oe_skilljar_code; 

      if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
        return;
      }

      // All well, send the email
      $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
      
      // add order note about the same
      $this->object->add_order_note( sprintf( __( '%s email sent to the customer.', 'woocommerce' ), $this->title ) );

      // Set order meta to indicate that the welcome email was sent
      update_post_meta( $this->object->id, '_oe_skilljar_welcome_email_sent', 1 );
      
    }
    
  }
  
  /**
   * get_content_html function.
   *
   * @return string
   */
  public function get_content_html() {
    return \wc_get_template_html( $this->template_html, array(
      'order'         => $this->object,
      'email_heading' => $this->get_heading(),
      'sent_to_admin' => false,
      'plain_text'    => false,
      'email'         => $this
    ), '', $this->template_base );
  }


  /**
   * get_content_plain function.
   *
   * @return string
   */
  public function get_content_plain() {
    return \wc_get_template_html( $this->template_plain, array(
      'order'         => $this->object,
      'email_heading'     => $this->get_heading(),
      'sent_to_admin'     => false,
      'plain_text'      => true,
      'email'         => $this
    ), '', $this->template_base );
  }


  /**
   * Initialize settings form fields
   */
  public function init_form_fields() {

    $this->form_fields = array(
      'enabled'    => array(
        'title'   => __( 'Enable/Disable', 'woocommerce' ),
        'type'    => 'checkbox',
        'label'   => 'Enable this email notification',
        'default' => 'yes'
      ),
      'subject'    => array(
        'title'       => __( 'Subject', 'woocommerce' ),
        'type'        => 'text',
        'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
        'placeholder' => '',
        'default'     => ''
      ),
      'heading'    => array(
        'title'       => __( 'Email Heading', 'woocommerce' ),
        'type'        => 'text',
        'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
        'placeholder' => '',
        'default'     => ''
      ),
      'email_type' => array(
        'title'       => __( 'Email type', 'woocommerce' ),
        'type'        => 'select',
        'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
        'default'       => 'html',
        'class'         => 'email_type wc-enhanced-select',
        'options'     => array(
          'plain'     => __( 'Plain text', 'woocommerce' ),
          'html'      => __( 'HTML', 'woocommerce' ),
          'multipart' => __( 'Multipart', 'woocommerce' ),
        )
      )
    );
  }
    
}

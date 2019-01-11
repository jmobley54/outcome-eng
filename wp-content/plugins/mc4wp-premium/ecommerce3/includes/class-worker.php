<?php

class MC4WP_Ecommerce_Worker {

    /**
     * @var MC4WP_Queue
     */
    protected $queue;

    /**
     * @var MC4WP_Ecommerce
     */
    protected $ecommerce;

    /**
     * @var array
     */
    protected $settings;

    /**
     * MC4WP_Ecommerce_Worker constructor.
     *
     * @param array $settings
     * @param MC4WP_Ecommerce $ecommerce
     * @param MC4WP_Queue $queue
     */
    public function __construct( array $settings, MC4WP_Ecommerce $ecommerce, MC4WP_Queue $queue ) {
        $this->settings = $settings;
        $this->ecommerce = $ecommerce;
        $this->queue = $queue;
    }

    /**
     * Hook
     */
    public function hook() {
        add_action( 'mc4wp_ecommerce_process_queue', array( $this, 'work' ) );
    }

    /**
     * Work!
     *
     * TODO: Re-schedule failed jobs in a separate queue maybe?
     */
    public function work() {
        while( ( $job = $this->queue->get() ) ) {

            // ensure job data matches expected format
            if( empty( $job->data['method'] ) || ! method_exists( $this, $job->data['method'] ) ) {
                $this->queue->delete( $job );
                continue;
            }

            // call job method with args
            try {
                $success = call_user_func_array( array( $this, $job->data['method'] ), $job->data['args'] );
            } catch( Error $e ) {
                $message = sprintf( 'E-Commerce: Failed to process background job. %s in %s:%d', $e->getMessage(), $e->getFile(), $e->getLine() );
                $this->get_log()->error( $message );
            }

            // remove job from queue & force save
            $this->queue->delete( $job );
            $this->queue->save();
        }

        // save again to handle deleted jobs properly too
        $this->queue->save();
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function add_order( $id ) {
        try {
            $this->ecommerce->update_order( $id );
        } catch( Exception $e ) {
            if( $e->getCode() === MC4WP_Ecommerce::ERR_NO_ITEMS) {
                $this->get_log()->warning( sprintf( "E-Commerce: Skipping order #%d. %s", $id, $e->getMessage() ) );
            } else if( $e->getCode() === MC4WP_Ecommerce::ERR_NO_EMAIL_ADDRESS) {
                $this->get_log()->warning( sprintf( "E-Commerce: Skipping order #%d. Order has no email address.", $id, $e->getMessage() ) );
            } else {
                $this->get_log()->error( sprintf( "E-Commerce: Error adding order #%d. %s", $id, $e ) );
            }
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully added order #%d.", $id) );
        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete_order( $id ) {
        // do nothing if order is not tracked
        if( ! $this->ecommerce->is_object_tracked( $id ) ) {
            return false;
        }

        try {
            $this->ecommerce->delete_order( $id );
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error deleting order #%d. %s", $id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully deleted order #%d.", $id) );
        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function add_product( $id ) {
        try {
            $this->ecommerce->update_product( $id );
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error updating product #%d. %s", $id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully updated product #%d.", $id) );
        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete_product( $id ) {
        try {
            $this->ecommerce->delete_product( $id );
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error deleting product #%d. %s", $id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully deleted product #%d.", $id) );
        return true;
    }

    /**
     * @param string $cart_id
     * @param WP_User|object|int $customer
     * @param array $cart_contents
     *
     * @return bool
     */
    public function update_cart( $cart_id, $customer, $cart_contents = array() ) {

        // turn $user_id into WP_User object
        if( is_numeric( $customer ) ) {
            $customer = get_userdata( $customer );
        }

        try {
            $this->ecommerce->update_cart( $cart_id, $customer, $cart_contents );
        } catch( Exception $e ) {
            if( $e->getCode() === MC4WP_Ecommerce::ERR_NO_ITEMS ) {
                 $this->get_log()->warning( sprintf( "E-Commerce: Skipping cart #%s. %s", $cart_id, $e->getMessage() ) );
            } else {
                $this->get_log()->error( sprintf( "E-Commerce: Error updating cart #%s. %s", $cart_id, $e ) );
            }
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully updated cart #%s.", $cart_id ) );
    }

    /**
     * @param string $cart_id
     *
     * @return bool
     */
    public function delete_cart( $cart_id ) {
        try {
            $this->ecommerce->delete_cart( $cart_id );
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            // cart was never in MailChimp. Don't log.
            return false;
        }catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error deleting cart #%s. %s", $cart_id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully deleted cart #%s.", $cart_id) );
        return true;
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function update_customer( $user_id ) {
        $user = get_userdata( $user_id );

        // do nothing if user no longer exists by now.
        if( ! $user instanceof WP_User ) {
            return false;
        }

        try {
            $this->ecommerce->update_customer($user);
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            // Customer was not in MailChimp, can happen in case of email address update (since we use that as ID).
            // It's okay
            return true;
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error updating user #%d. %s", $user_id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully updated user #%d.", $user_id ) );
        return true;
    }

    public function update_promo( $post_id ) {
        try {
            $this->ecommerce->update_promo( $post_id );
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error updating promo #%d. %s", $post_id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully updated promo #%d.", $post_id) );
        return true;
    }

    public function delete_promo( $post_id ) {
        try {
            $this->ecommerce->delete_promo( $post_id );
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error deleting promo #%d. %s", $post_id, $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully deleted promo  #%d.", $post_id) );
        return true;
    }

    public function update_subscriber_email( $old_email_address, $new_email_address ) {
        if (empty($this->settings['store']['list_id'])) {
            return true;
        }

        $list_id = $this->settings['store']['list_id'];
        $api = mc4wp_get_api_v3();

        try {
            $data = $api->get_list_member( $list_id, $old_email_address );

            // we can only update email addresses of members with a "subscribed" status in MailChimp
            if( $data->status !== 'subscribed' ) {
                return true;
            }

            $api->update_list_member( $list_id, $old_email_address, array( 'email_address' => $new_email_address ) );
        } catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
            // this means the customer was not on the list; which is okay
            return true;
        } catch( Exception $e ) {
            $this->get_log()->error( sprintf( "E-Commerce: Error updating customer email. %s", $e ) );
            return false;
        }

        $this->get_log()->info( sprintf( "E-Commerce: Successfully updated customer email from %s to %s.", $old_email_address, $new_email_address ) );
        return true;
    }

    /**
     * @return MC4WP_Debug_Log
     */
    private function get_log() {
        return mc4wp('log');
    }

}

<?php
/**
 * license.php
 *
 * @author Justin Greer <justin@justin-greer.com
 * @copyright Justin Greer Interactive, LLC
 * @date 5/8/17
 * @package WP-Nightly
 *
 * @todo Add Addon filter and run the grid from that instead of being hardcoded
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$options = get_option( 'wo_license_information' );
?>

<div class="section group product-license-grid">

    <div class="col span_3_of_6">
        <div class="product">
            <h3 class="product-title">WP OAuth Server</h3>
            <hr/>
			<?php
			$license_key = wo_license_key();
			if ( ! empty( $options['license'] ) && ! empty( $license_key ) && $options['license'] != 'invalid' ): ?>
                <p>
                    License valid
                    until <?php echo $options['expires'] !== 'lifetime' ? date( "F jS, Y", strtotime( $options['expires'] ) ) : '<strong>Lifetime</strong>'; ?>
                </p>
                <table>
                    <tr>
                        <th style="text-align: left;">Status:</th>
                        <td><?php echo ucfirst( $options['license'] ); ?></td>
                    </tr>

					<?php if ( ! empty( $options['customer_name'] ) ): ?>
                        <tr>
                            <th style="text-align: left;">Customer:</th>
                            <td><?php echo $options['customer_name']; ?></td>
                        </tr>
					<?php endif; ?>
                </table>

				<?php if ( $options['license'] == 'invalid' ): ?>
                    <p>
                        <span style="color:red;">INVALID</span>
                    </p>
				<?php endif; ?>

			<?php elseif (! empty( $options['license'] ) && ! empty( $license_key ) && $options['license'] == 'invalid'): ?>
                <p>
                    <span style="color:red;">INVALID LICENSE - Visit <a href="https://wp-oauth.com">https://wp-oauth.com</a> to get a valid license.</span>
                </p>
            <?php endif; ?>

            <form class="license_form" action="" method="post">
                <input type="text" name="wo_license_key" value="<?php echo wo_license_key(); ?>"
                       placeholder="Enter License Key"/>
                <input type="hidden" name="activate_license" value="true"/>
                <input class="button" type="submit" value="Activate / Reactivate"/>

                <div class="clearboth"></div>

				<?php global $license_error;
				if ( ! is_null( $license_error ) ): ?>
                    <p style="color:red;"><?php echo $license_error; ?></p>
				<?php endif; ?>
            </form>
        </div>
    </div>

</div>

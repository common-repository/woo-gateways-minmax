<?php
/**
 * Plugin Name: Gateways MinMax for WooCommerce
 * Description: Set minimum and maximum amount for payment gateways in WooCommerce
 * Version: 1.0
 * Author: hamed reihani
 * Author URI: http://www.shivahost.net
 */
add_action('admin_menu', 'gmfw_gateways_minmax_create_menu');
    function gmfw_gateways_minmax_create_menu() {

    	//create new top-level menu
    	add_menu_page('Woo Gateways MinMax', 'Woo Gateways MinMax', 'manage_options', __FILE__, 'gmfw_gateways_minmax_settings_page'  );

    	//call register settings function
    	add_action( 'admin_init', 'register_gmfw_gateways_minmax_settings' );
    }
    
    function register_gmfw_gateways_minmax_settings() {
    	//register our settings
        register_setting( 'gmfw_gateways_minmax-settings-group', 'gmfw_gateway_min_amount' );
        register_setting( 'gmfw_gateways_minmax-settings-group', 'gmfw_mingateways' );

        register_setting( 'gmfw_gateways_minmax-settings-group', 'gmfw_gateway_max_amount' );
        register_setting( 'gmfw_gateways_minmax-settings-group', 'gmfw_maxgateways' );
    }

    function gmfw_gateways_minmax_settings_page() {
        $active_min_gateways = array();   
        $active_max_gateways = array();
        $gateways = WC()->payment_gateways->payment_gateways();
        $active_min_gateways = get_option('gmfw_mingateways');
        $active_max_gateways = get_option('gmfw_maxgateways');
        $enabled_gateways = [];
        if( $gateways ) {
            foreach( $gateways as $gateway ) {
                    $enabled_gateways[] = $gateway;
            }
        }
        $idenabled_gateways = array_column($enabled_gateways, 'id');
?>
        <div class="wrap">
            <h1>Gateways MinMax for WooCommerce</h1>
            You can see all installed woocommerce payment gateways and you can set min or max for all of them. You must activate any gateway from woocommerce settings to work on your website.
            <form method="post" action="options.php">
                <?php settings_fields( 'gmfw_gateways_minmax-settings-group' ); ?>
                <?php do_settings_sections( 'gmfw_gateways_minmax-settings-group' ); ?>
               
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Minimum Amount</th>
                        <td><input type="text" name="gmfw_gateway_min_amount" value="<?php echo esc_attr( get_option('gmfw_gateway_min_amount') ); ?>" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="padding: 20px; background-color: beige; border: dashed 1px; font-size: 12px;">
                            Selected payment gateways will be disabled for orders less than "Minimum Amount"<br><br>
                            <?php
                                foreach ($idenabled_gateways as $mingateway){
                                    $gateway_title = WC()->payment_gateways->payment_gateways()[$mingateway];
                                    $gateway_title = $gateway_title->title;
                                    if( in_array( $mingateway , $active_min_gateways ) ) {
                                    echo '<label style="margin: 0 20px;"><input type="checkbox" style="margin: 2px 0;" name="gmfw_mingateways[]" checked="yes" value="'.$mingateway.'"> '.$gateway_title.'</label><br>';
                                    } else {                                    
                                    echo '<label style="margin: 0 20px;"><input type="checkbox" style="margin: 2px 0;" name="gmfw_mingateways[]" value="'.$mingateway.'"> '.$gateway_title.'</label><br>';
                                    } 
                                }
                            ?>
                            </div>
                        </td>
                    </tr>
         
                    <tr valign="top">
                        <th scope="row">Maximum Amount</th>
                        <td><input type="text" name="gmfw_gateway_max_amount" value="<?php echo esc_attr( get_option('gmfw_gateway_max_amount') ); ?>" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="padding: 20px; background-color: beige; border: dashed 1px; font-size: 12px;">                        
                            Selected payment gateways will be disabled for orders more than "Maximum Amount"<br><br>

                            <?php
                                foreach ($idenabled_gateways as $maxgateway){
                                    $gateway_title = WC()->payment_gateways->payment_gateways()[$maxgateway];
                                    $gateway_title = $gateway_title->title;
                                    if( in_array( $maxgateway , $active_max_gateways ) ) {
                                    echo '<label style="margin: 0 20px;"><input type="checkbox" style="margin: 2px 0;" name="gmfw_maxgateways[]" checked="yes" value="'.$maxgateway.'"> '.$gateway_title.'</label><br>';
                                    } else {                                    
                                    echo '<label style="margin: 0 20px;"><input type="checkbox" style="margin: 2px 0;" name="gmfw_maxgateways[]" value="'.$maxgateway.'"> '.$gateway_title.'</label><br>';
                                    }                                    
                                }
                            ?>
                            </div>    
                        </td>
                    </tr>                    
                </table>
    
                <?php submit_button(); ?>

            </form>
        </div>
<?php
    } 
    

add_filter( 'woocommerce_available_payment_gateways', 'gmfw_disable_gateway_above_x' );

    function gmfw_disable_gateway_above_x( $available_gateways ) {
        $maximum = get_option('gmfw_gateway_max_amount') ;
        $minimum = get_option('gmfw_gateway_min_amount') ;
        $active_minimum_gateways = get_option('gmfw_mingateways');
        $active_maximum_gateways = get_option('gmfw_maxgateways');
        
        if ( WC()->cart->total > $maximum ) {
            foreach ($active_maximum_gateways as $active_maximum_gateway) {
                unset( $available_gateways[$active_maximum_gateway] );
            }
        }
        if ( WC()->cart->total < $minimum ) {
            foreach ($active_minimum_gateways as $active_minimum_gateway) {
                unset( $available_gateways[$active_minimum_gateway] );
            }
        }
        return $available_gateways;
    }
    
?>
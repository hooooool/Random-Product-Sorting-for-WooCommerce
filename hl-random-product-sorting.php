<?php
/**
 * Plugin Name:       Random Product Sorting for WooCommerce
 * Description:       Add a random product sorting method to WooCommerce.
 * Version:           1.0.0
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            hooooool
 * Author URI:        https://t.me/hooooool
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rpsfw-product-sorting
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 6.8
 */
?>
<?php
    if ( ! defined ( 'ABSPATH' ) ) {
        exit; //Exit if accessed directly.
    }

    if ( in_array ( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        if ( ! function_exists( 'rpsfw_order_setting' ) && ! function_exists( 'rpsfw_product_randomize' ) && ! function_exists( 'rpsfw_load_textdomain' ) ) {

            // Add "Random sorting" setting to product sorting menu
            // Добавляет пункт "Случайная сортировка" в меню сортировки товаров
            function rpsfw_order_setting( $sortby ) {
                $sortby['random_order'] = esc_html__( 'Random sorting', 'rpsfw-product-sorting' );
                    return $sortby;
            }
            add_filter ( 'woocommerce_default_catalog_orderby_options', 'rpsfw_order_setting' );
            add_filter ( 'woocommerce_catalog_orderby', 'rpsfw_order_setting' );

            // Randomize products when setting is used
            // "Время жизни" случайной сортировки на сайте (1 час)
            function rpsfw_product_randomize( $args ) {
                $order_by_setting = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
                if ( 'random_order' == $order_by_setting ) {
                    if ( false === ( $seed = get_transient( 'hl_product_random_seed' ) ) ) {
                        $seed = rand();
                        set_transient( 'hl_product_random_seed', $seed, 3600 );
                    }
                    $args['orderby']    = 'RAND(' . $seed . ')';
                    $args['meta_key']   = '';
                    $args['order']      = '';
                }
                return $args;
            }
            add_filter( 'woocommerce_get_catalog_ordering_args', 'rpsfw_product_randomize' );

            function rpsfw_load_textdomain() {
                load_plugin_textdomain( 'rpsfw-product-sorting', FALSE, basename( dirname(__FILE__) ) . '/languages/' );
            }
            add_action( 'plugins_loaded', 'rpsfw_load_textdomain' );
        }

    } else {

    if ( ! function_exists( 'rpsfw_woocommerce_admin_notice' ) ) {

        // Warn on missing WooCommerce
        // Проверка на наличие WooCommerce
        function rpsfw_woocommerce_admin_notice() { 
            ?>
                <div class="notice error is-dismissible">
                    <p><?php esc_html__( 'WooCommerce plugin must be installed on your site to use the WooCommerce Random Sorting feature.', 'rpsfw-product-sorting' ); ?></p>
                </div>
            <?php 
        }
        add_action( 'admin_notices', 'rpsfw_woocommerce_admin_notice' );
    }
}
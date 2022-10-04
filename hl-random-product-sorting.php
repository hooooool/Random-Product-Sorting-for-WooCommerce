<?php
/**
 * Plugin Name:       WooCommerce Random Sorting
 * Description:       Add a random product sorting method to WooCommerce.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            hooooool
 * Author URI:        https://t.me/hooooool
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hl-random-product-sorting
 * Domain Path:       /languages
 */
?>
<?php
    if ( ! defined ( 'ABSPATH' ) ) {
        exit; //Exit if accessed directly.
    }

    if ( in_array ( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        if ( ! function_exists( 'hl_product_random_order_setting' ) && ! function_exists( 'hl_product_randomize' ) && ! function_exists( 'hl_product_load_text_domain' ) ) {

            // Add "Random sorting" setting to product sorting menu
            // Добавляет пункт "Случайная сортировка" в меню сортировки товаров
            function hl_product_random_order_setting( $sortby ) {
                $sortby['random_order'] = esc_html__( 'Random sorting', 'hl-random-product-sorting' );
                    return $sortby;
            }
            add_filter ( 'woocommerce_default_catalog_orderby_options', 'hl_product_random_order_setting' );
            add_filter ( 'woocommerce_catalog_orderby', 'hl_product_random_order_setting' );

            // Randomize products when setting is used
            // "Время жизни" случайной сортировки на сайте (1 час)
            function hl_product_randomize( $args ) {
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
            add_filter( 'woocommerce_get_catalog_ordering_args', 'hl_product_randomize' );

            function hl_product_load_text_domain() {
                load_plugin_textdomain( 'hl-random-product-sorting', FALSE, basename( dirname(__FILE__) ) . '/languages/' );
            }
            add_action( 'plugins_loaded', 'hl_product_load_text_domain' );
        }

    } else {

    if ( ! function_exists( 'hl_woo_admin_notice' ) ) {

        // Warn on missing WooCommerce
        // Проверка на наличие WooCommerce
        function hl_woo_admin_notice() { 
            ?>
                <div class="notice error is-dismissible">
                    <p><?php _e( 'WooCommerce plugin must be installed on your site to use the WooCommerce Random Sorting feature.', 'hl-random-product-sorting' ); ?></p>
                </div>
            <?php 
        }
        add_action( 'admin_notices', 'hl_woo_admin_notice' );
    }
}
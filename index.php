<?php 
/**
 * Plugin Name: Test wc 
 * Author: Rupom
 * Description: Plugin description
 * Version: 1.0
 */
add_action( 'init', 'custom_orders_page_callback' );
function custom_orders_page_callback(){
    $current_time = current_time('timestamp');  // get current time
    $execution_time = get_option('last_time');  //get option current time 
    $execution_time += 24 * 3600;
    if ($current_time < $execution_time) {
        return; 
    }
    $category_remove_popular = 'popular'; 
    $category_remove_trending = 'trending'; 
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1
    );
    $products_query = new WP_Query($args);
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product_id = get_the_ID();
            wp_remove_object_terms($product_id, $category_remove_popular, 'product_cat');
            wp_remove_object_terms($product_id, $category_remove_trending, 'product_cat');
        }
        wp_reset_postdata();
    }
    // get last 45 days order for popular products
    $days_ago_45 = date('Y-m-d', $current_time - (45 * 24 * 3600));
    $popular_orders = wc_get_orders(array(
        'date_created' => '>' . $days_ago_45,
    ));
    if ($popular_orders) {
        foreach ($popular_orders as $order) {
            foreach ($order->get_items() as $item) { 
                $product_id = $item->get_product_id();
                wp_set_object_terms($product_id, 'popular', 'product_cat',true); 
            }
        }
    }
    // get last 10 days order for trending products
    $days_ago_10 = date('Y-m-d', $current_time - (10 * 24 * 3600));
    $trending_orders = wc_get_orders(array(
        'date_created' => '>' . $days_ago_10,
    ));
    if ($trending_orders) {
        foreach ($trending_orders as $order) {
            foreach ($order->get_items() as $item) { 
                $product_id = $item->get_product_id();
                wp_set_object_terms($product_id, 'trending', 'product_cat',true); 
            }
        }
    }
    update_option('last_time', $current_time);
}
?>
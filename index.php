<?php 
/**
 * Plugin Name: Test wc 
 * Author: Rupom
 * Description: Plugin description
 * Version: 1.0
 */
add_action( 'init', 'custom_orders_page_callback' );
function custom_orders_page_callback(){
    $transient_data = get_transient('last_time');
    // $execution_time = get_option('last_time');
    // $execution_time += 24 * 3600;
    // $current_time = current_time('timestamp');
    // if ($current_time < $execution_time) {
    //     return; 
    // }
    // delete_transient('last_time');
    var_dump($transient_data);
    if($transient_data !== false ){
        return;
    }
    $category_to_remove = 'popular'; 
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => 'popular'
            )
        )
    );
    $products_query = new WP_Query($args);
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product_id = get_the_ID();
            wp_remove_object_terms($product_id, $category_to_remove, 'product_cat');
        }
        wp_reset_postdata();
    }
    // get last 10 days order products
    $days_ago = date('Y-m-d', strtotime('-10 days'));
    $orders = wc_get_orders(array(
        'date_created' => '>' . $days_ago,
    ));
    if ($orders) {
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item_id => $item) { 
                $product_id = $item->get_product_id();
                wp_set_object_terms($product_id, 'popular', 'product_cat',true); 
            }
        }
    }
    set_transient('last_time', array('time' => 'value'), 24 * 3600);
    // update_option('last_time', $current_time);
}

?>
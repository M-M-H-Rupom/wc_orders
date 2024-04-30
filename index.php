<?php 
/**
 * Plugin Name: Test wc 
 * Author: Rupom
 * Description: Plugin description
 * Version: 1.0
 */
add_action( 'admin_menu', function(){
    add_menu_page('wc', 'WC', 'manage_options', 'wc', 'custom_orders_page_callback');
} );

function custom_orders_page_callback(){
    $days_ago = date('Y-m-d', strtotime('-10 days'));
    $orders = wc_get_orders(array(
        'date_created' => '>' . $days_ago,
    ));
    echo '<div>';
    echo '<h2>Custom Orders</h2>';
    if ($orders) {
        echo '<table>';
        echo '<tbody>';
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item_id => $item) {
                $product_name = $item->get_name(); 
                $product_qty = $item->get_quantity();
                $product_price = $item->get_total(); 
                $product_id = $item->get_product_id();
                $result = wp_set_object_terms($product_id, 'popular', 'product_cat'); 
                // $result = wp_remove_object_terms($product_id, 'music', 'product_cat');
                // if (is_wp_error($result)) {
                //     echo 'Error';
                // } else {
                //     echo 'Category changed successfully!';
                // }
                $product = $item->get_product();
                $product_categories = $product->get_category_ids();
                $category_names = array();
                foreach ($product_categories as $category_id) {
                    $category = get_term_by('id', $category_id, 'product_cat');
                    if ($category) {
                        $category_names[] = $category->name;
                    }
                }
                echo "<tr><td>$product_name</td><td>$product_qty</td><td>$product_price</td><td>" . implode(', ', $category_names) . "</td></tr>";
            }
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'No orders found';
    }
    echo '</div>';
}

?>

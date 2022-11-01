<?php
//add_action("pewc_start_groups", "get_category_filters");
function get_category_filters() {
	echo "List of Categories";
}

//add_filter( 'woocommerce_is_purchasable', 'flava_is_purchasable', 10, 2 );
function flava_is_purchasable( $purchasable, $product ){
    if( ($product->get_id() == 3554) && ($product->get_price() >= 0) ) {
        $purchasable = true;
	}
	
    return $purchasable;
}

add_action('wp_ajax_flava_ajax_empty_cart', 'flava_ajax_empty_cart');
add_action('wp_ajax_nopriv_flava_ajax_empty_cart', 'flava_ajax_empty_cart');
function flava_ajax_empty_cart() {
	WC()->cart->empty_cart();
	
	echo wp_send_json( array("msg" => "Emptied") );
	wp_die();
}

add_action('wp_ajax_fd_ajax_add_to_cart', 'flava_add_items_to_cart');
add_action('wp_ajax_nopriv_fd_ajax_add_to_cart', 'flava_add_items_to_cart');
function flava_add_items_to_cart() {
	$cart_items = json_decode(stripslashes($_POST['cart_items']));
	$items = '';
	foreach( $cart_items as $item_id => $cart_item ) {
		$product_id = $cart_item->product_id;
		$quantity = $cart_item->quantity;
		$cart_item_data = $cart_item->extras;
		//$items .= $product_id.' - '.$quantity.' - '.$cart_item_data.'<br>';
		
		/*$product_cart_id = WC()->cart->generate_cart_id( $product_id );
		$cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
		if ( $cart_item_key ) {
			WC()->cart->remove_cart_item( $cart_item_key );
		}*/
		
		$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
		$product_status = get_post_status($product_id);

		// Remember to add $cart_item_data to WC->cart->add_to_cart
		if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, 0, $cart_item_data) && 'publish' === $product_status) {

			do_action('woocommerce_ajax_added_to_cart', $product_id);

			if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
				wc_add_to_cart_message(array($product_id => $quantity), true);
			}

			/*WC_AJAX :: get_refreshed_fragments();*/
		} else {

			$data = array(
				'error' => true,
				'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

			//echo wp_send_json($data);
		}
	}
	
	echo wp_send_json( array( "success" => true ) );
	//echo wp_send_json( array( "items" => $items ) );
    wp_die();
}

add_action( 'woocommerce_cart_calculate_fees', 'flava_wc_add_cart_fees_by_product_meta' );
if ( ! function_exists( 'flava_wc_add_cart_fees_by_product_meta' ) ) {
    /**
     * flava_wc_add_cart_fees_by_product_meta.
     */
    function flava_wc_add_cart_fees_by_product_meta( $cart ) {
		$amount = 0;
        foreach ( $cart->get_cart() as $cart_item_key => $values ) {
            if ( $values['_extra_cost'] > 0 ) {
                $amount += $values['_extra_cost'];
                //$name      = 'Options Total:';
                //$taxable   = true;
                //$tax_class = '';
                //$cart->add_fee( $name, $amount, $taxable, $tax_class );
            }
        }
		$name      = 'Options Total:';
		$taxable   = true;
		$tax_class = '';
		
		if($amount > 0) {
        	$cart->add_fee( $name, $amount, $taxable, $tax_class );
		}
    }
}

//add_action( 'woocommerce_before_calculate_totals', 'flava_update_custom_price', 1, 1 );
function flava_update_custom_price( $cart_object ) {
    foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {       
        // Version 2.x
        //$value['data']->price = $value['_custom_options']['custom_price'];

        // Version 3.x / 4.x
        $value['data']->set_price($value['_custom_options']['custom_price']);
    }
}

add_filter('woocommerce_add_cart_item_data', 'flava_add_item_data', 1, 10);
function flava_add_item_data($cart_item_data, $product_id) {
    global $woocommerce;
    $new_value = array();
    //$new_value['_extras'] = $_POST['extras'];
	//$new_value['_extra_cost'] = $_POST['extra_cost'];
	$cart_items = json_decode(stripslashes($_POST['cart_items']));
	$items = '';
	foreach( $cart_items as $item_id => $cart_item ) {
		if($item_id == $product_id) {
			if( $cart_item->_child_products ) {
				$new_value['_child_products'] = $cart_item->_child_products;
			}
			$new_value['_extras'] = $cart_item->extras;
			$new_value['_extra_cost'] = $cart_item->extra_cost;
			break;
		}
	}

    if(empty($cart_item_data)) {
        return $new_value;
    } else {
        return array_merge($cart_item_data, $new_value);
    }
}

add_filter('woocommerce_get_cart_item_from_session', 'flava_get_cart_items_from_session', 1, 3 );
function flava_get_cart_items_from_session($item, $values, $key) {
    if (array_key_exists( '_extras', $values ) ) {
        $item['_extras'] = $values['_extras'];
    }
    if (array_key_exists( '_extra_cost', $values ) ) {
        $item['_extra_cost'] = $values['_extra_cost'];
    }
	if (array_key_exists( '_child_products', $values ) ) {
        $item['_child_products'] = $values['_child_products'];
    }

    return $item;
}

add_filter('woocommerce_cart_item_name', 'flava_add_user_custom_session', 1, 3);
function flava_add_user_custom_session($product_name, $values, $cart_item_key ) {
	if( $values['_child_products'] ) {
		//$return_string = $product_name . "<br />" . $values['_child_products'];
		$return_string = "<h5>".$product_name . "</h5>" . $values['_child_products'];
		return $return_string;
	}
	
    //$return_string = $product_name . "<br />" . $values['_extras'];
    $return_string = "<h5>".$product_name . "</h5>" . $values['_extras'];
    return $return_string;
}

add_action('woocommerce_add_order_item_meta', 'flava_add_values_to_order_item_meta', 1, 2);
function flava_add_values_to_order_item_meta($item_id, $values) {
    global $woocommerce, $wpdb;

	if( $values['_child_products'] ) {
		wc_add_order_item_meta($item_id, '_child_products', $values['_child_products']);
	}
    wc_add_order_item_meta($item_id, 'meal_extras', $values['_extras']);
    //wc_add_order_item_meta($item_id,'customer_image',$values['_custom_options']['another_example_field']);
    //wc_add_order_item_meta($item_id,'_hidden_field',$values['_custom_options']['hidden_info']);
}

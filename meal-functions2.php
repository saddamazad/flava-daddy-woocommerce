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

add_action('wp_ajax_remove_item_from_woo_cart', 'flava_remove_item_from_cart');
add_action('wp_ajax_nopriv_remove_item_from_woo_cart', 'flava_remove_item_from_cart');
function flava_remove_item_from_cart() {
	$cart_item_key = trim($_POST['item_key']);
	WC()->cart->remove_cart_item( $cart_item_key );
	
	echo wp_send_json( array("msg" => "success") );
	wp_die();
}

add_action('wp_ajax_flava_ajax_store_order_type', 'flava_ajax_store_order_type');
add_action('wp_ajax_nopriv_flava_ajax_store_order_type', 'flava_ajax_store_order_type');
function flava_ajax_store_order_type() {
	$order_type = $_POST['order_type'];
	//WC()->session->set('order_type', $order_type );
	$_SESSION['order_type'] = $order_type;
	
	echo wp_send_json( array("msg" => "success") );
	wp_die();
}

add_action('wp_ajax_flava_ajax_add_to_cart', 'flava_add_items_to_cart');
add_action('wp_ajax_nopriv_flava_ajax_add_to_cart', 'flava_add_items_to_cart');
function flava_add_items_to_cart() {
	$cart_items = json_decode(stripslashes($_POST['cart_items']));	
	//WC()->cart->empty_cart(); // This will remove gift cards from the cart, not useful.
	
	foreach( $cart_items as $item_id => $cart_item ) {
		$product_id = $cart_item->product_id;
		$quantity = $cart_item->quantity;
		$item_type = $cart_item->type;
		$cart_item_meta = array();
		
		if( $item_type == "onetime" ) {
			$cart_item_extras = $cart_item->extras;
			$cart_item_extra_cost = ($cart_item->extra_cost / $quantity);
			$cart_item_unit_price = ($cart_item->price / $quantity);

			$cart_item_meta = array(
								'_extras' => $cart_item_extras,
								'_extra_cost' => $cart_item_extra_cost,
								'_base_price' => $cart_item_unit_price
							);
		}
		
		/*if( $item_type == "subscription" ) {
			$args = array(
				'featured' => true,
				'limit' => 5
			);
			$products = wc_get_products( $args );
			if( count($products) > 0 ) {
				$ml_counter = 0;
				$child_products = '';
				$cp_qty = 1;
				foreach($products as $product) {
					$ml_counter++;					
					$cpdt_id = $product->get_id();
					$meal_title = get_the_title($cpdt_id);
					$meals_plan_num = get_post_meta($product_id, 'number_of_meals_plan', true);
					
					if( $meals_plan_num == 6 ) {
						//$child_products .= $meal_title." - x".$cp_qty."<br>";
						if( $ml_counter < 2 ) {
							$cp_qty = 2;
						} else {
							$cp_qty = 1;
						}
						
						//if( $ml_counter == 6 ) {
							//$ml_counter = 0;
						//}
					}
					if( $meals_plan_num == 8 ) {
						if( $ml_counter < 4 ) {
							$cp_qty = 2;
						} else {
							$cp_qty = 1;
						}
					}
					if( $meals_plan_num == 10 ) {
						$cp_qty = 2;
					}
					if( $meals_plan_num == 15 ) {
						$cp_qty = 3;
					}
					if( $meals_plan_num == 20 ) {
						$cp_qty = 4;
					}
					if( $meals_plan_num == 30 ) {
						$cp_qty = 6;
					}
					
					$child_products .= $meal_title." - x".$cp_qty."\n";
				}
				$cart_item_meta = array( '_child_products' => $child_products );
			}
		}*/
		
		/*$product = wc_get_product( $product_id );
		$cart_item_hash = wc_get_cart_item_data_hash( $product );*/		
		
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            //if($cart_item['product_id'] == $product_id) {
			if($cart_item_key == $item_id) {
                WC()->cart->remove_cart_item( $cart_item_key );
				break;
            }
			
			if( ($cart_item['product_id'] == $product_id) && ($item_type == "subscription") ) {
				WC()->cart->remove_cart_item( $cart_item_key );
				break;
			}
        }
		
		$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
		$product_status = get_post_status($product_id);

		// Remember to add $cart_item_data to WC->cart->add_to_cart
		if ($passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_meta ) && 'publish' === $product_status) {
			
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
    wp_die();
}

//add_action( 'woocommerce_cart_calculate_fees', 'flava_wc_add_cart_fees_by_product_meta' );
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

add_action( 'woocommerce_before_calculate_totals', 'flava_update_custom_price', 1, 1 );
function flava_update_custom_price( $cart_object ) {
    foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {
		if( isset($value['_base_price']) ) {
			$value['data']->set_price($value['_base_price']+$value['_extra_cost']);
		}
    }
}

//add_filter('woocommerce_cart_product_subtotal', 'flava_woocommerce_cart_product_subtotal', 10, 4); 
function flava_woocommerce_cart_product_subtotal( $product_subtotal, $product, $quantity, $cart_object ) {
	foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {
		$product_id = $product->get_id();
		if( $product_id == $value['product_id'] ) {
			$additional_cost = $value['_extra_cost'];

			if( $additional_cost ) {
				$line_total = (float)$product->get_price() * $quantity;
				$line_total += $additional_cost * $quantity;
				$product_subtotal = number_format((float)$line_total, 2, '.', '');
				break;
			}
		}
	}
	
	return wc_price($product_subtotal);
}

// Display the default product price (instead of the calculated one)
add_filter( 'woocommerce_cart_item_price', 'flava_filter_woocommerce_cart_item_price', 10, 3 );
function flava_filter_woocommerce_cart_item_price( $product_price, $cart_item, $cart_item_key  ) {
    if( isset($cart_item['_base_price']) ) {
        $product_price = wc_price( wc_get_price_to_display( $cart_item['data'], array('price' => $cart_item['_base_price']) ) );
    }
    return $product_price;
}




/* This hook is NOT needed for the One time order page, because we're adding the cart item's with meta data via AJAX */

//add_filter('woocommerce_add_cart_item_data', 'flava_add_item_data', 1, 10);
function flava_add_item_data($cart_item_data, $product_id) {
    //global $woocommerce;
    //$new_value = array();
    
    /*$product = wc_get_product( $product_id );
	if( ! $product->is_type('subscription') && ! $product->is_type('variable-subscription') ) {
		// Force individual cart items
		$unique_cart_item_key = md5( microtime() . rand() );
		$cart_item_data['unique_key'] = $unique_cart_item_key;
	}*/
	
	/*$cart_items = json_decode(stripslashes($_POST['cart_items']));
	$items = '';
	foreach( $cart_items as $item_key => $cart_item ) {
		//if($item_id == $product_id) {
		if( $cart_item->product_id == $product_id ) {
			if( $cart_item->_child_products ) {
				//$new_value['_child_products'] = $cart_item->_child_products;
				$cart_item_data['_child_products'] = $cart_item->_child_products;
			}
			$cart_item_data['_extras'] = $cart_item->extras;
			//$cart_item_data['_extras'] = $item_key;
			//$new_value['_extra_cost'] = $cart_item->extra_cost;
			$cart_item_data['_extra_cost'] = ($cart_item->extra_cost / $cart_item->quantity);
			
			$cart_item_data['_base_price'] = ($cart_item->price / $cart_item->quantity);
			$cart_item_data['_line_total'] = $cart_item->line_total;
			$cart_item_data['_quantity'] = $cart_item->quantity;
			//$new_value['_quantity'] = $cart_item->quantity;
			break;
		}
	}*/

    /*if(empty($cart_item_data)) {
        return $new_value;
    } else {
        return array_merge($cart_item_data, $new_value);
    }*/
	return $cart_item_data;
}




// get meta data from the session to show on cart/checkout pages
add_filter('woocommerce_get_cart_item_from_session', 'flava_get_cart_items_from_session', 1, 3 );
function flava_get_cart_items_from_session($item, $values, $key) {
    if (array_key_exists( '_extras', $values ) ) {
        $item['_extras'] = $values['_extras'];
    }
    if (array_key_exists( '_extra_cost', $values ) ) {
        $item['_extra_cost'] = $values['_extra_cost'];
    }
	
	if (array_key_exists( '_base_price', $values ) ) {
        $item['_base_price'] = $values['_base_price'];
    }
	if (array_key_exists( '_line_total', $values ) ) {
        $item['_line_total'] = $values['_line_total'];
    }
	if (array_key_exists( '_quantity', $values ) ) {
        $item['_quantity'] = $values['_quantity'];
    }
	
	if (array_key_exists( '_child_products', $values ) ) {
        $item['_child_products'] = $values['_child_products'];
    }

    return $item;
}

// show the meta data within the cart line item
add_filter('woocommerce_cart_item_name', 'flava_add_user_custom_session', 1, 3);
function flava_add_user_custom_session($product_name, $values, $cart_item_key ) {
	if( $values['_child_products'] ) {
		$return_string = "<h5>".$product_name . "</h5>" . nl2br($values['_child_products']);
		return $return_string;
	}
	
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
}


// Get Ajax request and saving to WC session
add_action( 'wp_ajax_insulated_bag_fee', 'get_insulated_bag_fee' );
add_action( 'wp_ajax_nopriv_insulated_bag_fee', 'get_insulated_bag_fee' );
function get_insulated_bag_fee() {
    if ( isset($_POST['insulated_bag_fee']) ) {
        WC()->session->set('insulated_bag_fee', $_POST['insulated_bag_fee'] );
    } else {
		if( WC()->session->get('insulated_bag_fee') ) {
			WC()->session->__unset('insulated_bag_fee');
		}
	}
    die();
}

// Add a custom fee
add_action( 'woocommerce_cart_calculate_fees', 'custom_insulated_bag_fee', 20, 1 );
function custom_insulated_bag_fee( $cart ) {
    // Only on checkout
    if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! is_checkout() ) {
        return;
	}

    //$percent = 3;

    if( WC()->session->get('insulated_bag_fee') ) {
		$bag_fee = intval(WC()->session->get('insulated_bag_fee'));
        $cart->add_fee( __( 'Insulated bag with ice', 'woocommerce'), $bag_fee );
	}
}

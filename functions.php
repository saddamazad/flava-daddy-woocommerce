<?php

/**
 * astra-child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package astra-child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );


function child_enqueue_styles() {	
	wp_enqueue_style( 'slick-style', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1' );
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
	wp_enqueue_script( 'slick-js',  '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js');
	
	wp_enqueue_script( 'meals-js',  get_stylesheet_directory_uri() . '/meals.js');
	
	//https://www.zip-codes.com/canadian/city.asp?city=ottawa&province=on
	//https://regexr.com/
	//Regex: <li><a href="\/canadian\/postal-code.asp\?postalcode=(\w+\+\w+)">(\w+\ \w+)<\/a><\/li>

	//https://www.phpliveregex.com/#tab-preg-replace
	//Replace: $2,
	$flava_meal_obj = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'post_codes' => 'K1A 0A1,K1A 0A2,K1A 0A3,K1A 0A4,K1A 0A5,K1A 0A6,K1A 0A8,K1A 0A9,K1A 0B1,K1A 0B2,K1A 0B4,K1A 0B6,K1A 0B7,K1A 0B8,K1A 0B9,K1A 0C1,K1A 0C2,K1A 0C4,K1A 0C5,K1A 0C6,K1A 0C7,K1A 0C8,K1A 0E1,K1A 0E2,K1A 0E4,K1A 0E6,K1A 0E8,K1A 0E9,K1A 0G1,K1A 0G2,K1A 0G5,K1A 0G6,K1A 0G7,K1A 0G8,K1A 0G9,K1A 0H2,K1A 0H5,K1A 0H7,K1A 0H8,K1A 0H9,K1A 0J1,K1A 0J4,K1A 0J6,K1A 0K1,K1A 0K2,K1A 0K3,K1A 0K5,K1A 0K7,K1A 0K9,K1A 0L1,K1A 0L2,K1A 0L3,K1A 0L4,K1A 0L5,K1A 0L8,K1A 0L9,K1A 0M1,K1A 0M2,K1A 0N4,K1A 0N5,K1A 0N7,K1A 0N8,K1A 0P4,K1A 0P5,K1A 0P7,K1A 0P8,K1A 0P9,K1A 0R1,K1A 0R2,K1A 0R3,K1A 0R5,K1A 0R6,K1A 0R7,K1A 0S4,K1A 0S6,K1A 0T1,K1A 0T2,K1A 0T4,K1A 0T6,K1A 0T7,K1A 0T8,K1A 0W6,K1A 0W8,K1A 0W9,K1A 0X1,K1A 0X6,K1A 0X8,K1A 0Y3,K1A 0Y6,K1A 0Y7,K1A 0Y8,K1A 0Y9,K1A 0Z2,K1A 0Z4,K1A 1A1,K1A 1A2,K1A 1A3,K1A 1A8,K1A 1A9,K1A 1B1,K1A 1B4,K1A 1B5,K1A 1B7,K1A 1B9,K1A 1C1,K1A 1E1,K1A 1E3,K1A 1E4,K1A 1G1,K1A 1G4,K1A 1G5,K1A 1G7,K1A 1G9,K1A 1H5,K1A 1J2,K1A 1J3,K1A 1J4,K1A 1J5,K1A 1J6,K1A 1J7,K1A 1J8,K1A 1J9,K1A 1K1,K1A 1K2,K1A 1K3,K1A 1K6,K1A 1K9,K1A 1L1,K1A 1L5,K1A 1L6,K1A 1L7,K1A 1L8,K1A 1L9,K1A 1M2,K1A 1M3,K1A 1M5,K1A 1M6,K1A 1M8,K1A 9Z5,K1A 9Z6,K1A 9Z7,K1A 9Z8,K1B 0A9,K1B 0B1,K1B 1A4,K1B 1A6,K1B 1A7,K1B 1A8,K1B 1A9,K1B 1B2,K1B 1C6,K1B 1E3,K1B 3M4,K1B 3M5,K1B 3M6,K1B 3N2,K1B 3N7,K1B 3N8,K1B 3R1,K1B 3R2,K1B 3R3,K1B 3S1,K1B 3S2,K1B 3S3,K1B 3S4,K1B 3S6,K1B 3S7,K1B 3S9,K1B 3T1,K1B 3T2,K1B 3T3,K1B 3T4,K1B 3T5,K1B 3T7,K1B 3V1,K1B 3V2,K1B 3V3,K1B 3V6,K1B 3V7,K1B 3V8,K1B 3V9,K1B 3W1,K1B 3W2,K1B 3W3,K1B 3W4,K1B 3W9,K1B 4E4,K1B 4E5,K1B 4H2,K1B 4H3,K1B 4H4,K1B 4H5,K1B 4H6,K1B 4H7,K1B 4H8,K1B 4H9,K1B 4J1,K1B 4J2,K1B 4J3,K1B 4J4,K1B 4J5,K1B 4J6,K1B 4J7,K1B 4J8,K1B 4J9,K1B 4K1,K1B 4K2,K1B 4K9,K1B 4L1,K1B 4L2,K1B 4L5,K1B 4L6,K1B 4L7,K1B 4L8,K1B 4L9,K1B 4N4,K1B 4S4,K1B 4S5,K1B 4S6,K1B 4S8,K1B 4S9,K1B 4T3,K1B 4T7,K1B 4T8,K1B 4T9,K1B 4V1,K1B 4V2,K1B 4V8,K1B 4W5,K1B 4W8,K1B 4X3,K1B 4Z4,K1B 5A4,K1B 5B4,K1B 5B5,K1B 5B6,K1B 5B7,K1B 5K2,K1B 5K9,K1B 5L1,K1B 5L3,K1B 5L6,K1B 5L8,K1B 5M1,K1B 5M6,K1B 5M7,K1B 5M8,K1B 5N1,K1B 5N2,K1B 5N3,K1B 5N6,K1B 5N7,K1B 5P6,K1B 5R1,K1B 5R4,K1B 5R6,K1G 0A1,K1G 0A2,K1G 0A3,K1G 0A4,K1G 0A5,K1G 0A6,K1G 0A7,K1G 0A8,K1G 0A9,K1G 0B1,K1G 0B2,K1G 0B3,K1G 0B4,K1G 0B5,K1G 0B6,K1G 0B7,K1G 0B8,K1G 0B9,K1G 0C1,K1G 0C2,K1G 0C3,K1G 0C4,K1G 0C5,K1G 0C6,K1G 0C7,K1G 0C9,K1G 0E1,K1G 0E2,K1G 0E3,K1G 0E4,K1G 0E5,K1G 0E6,K1G 0E7,K1G 0E8,K1G 0E9,K1G 0G1,K1G 0G2,K1G 0G3,K1G 0G4,K1G 0G5,K1G 0G6,K1G 0G7,K1G 0G8,K1G 0G9,K1G 0H1,K1G 0H2,K1G 0H3,K1G 0H4,K1G 0H5,K1G 0H6,K1G 0H7,K1G 0H8,K1G 0H9,K1G 0J1,K1G 0J2,K1G 0J3,K1G 0J4,K1G 0J5,K1G 0J6,K1G 0J7,K1G 0J8,K1G 0J9,K1G 0K1,K1G 0K2,K1G 0K3,K1G 0K4,K1G 0K5,K1G 0K6,K1G 0K7,K1G 0K8,K1G 0K9,K1G 0L1,K1G 0L2,K1G 0L3,K1G 0L4,K1G 0L5,K1G 0L6,K1G 0L7,K1G 0L8,K1G 0L9,K1G 0M1,K1G 0M2,K1G 0M3,K1G 0M4,K1G 0M5,K1G 0M6,K1G 0M7,K1G 0M8,K1G 0M9,K1G 0N1,K1G 0N3,K1G 0N4,K1G 0N5,K1G 0N6,K1G 0N7,K1G 0N8,K1G 0N9,K1G 0P1,K1G 0P2,K1G 0P3,K1G 0P4,K1G 0P5,K1G 0P6,K1G 0P7,K1G 0P8,K1G 0P9,K1G 0R1,K1G 0R2,K1G 0R3,K1G 0R4,K1G 0R5,K1G 0R6,K1G 0R7,K1G 0R8,K1G 0R9,K1G 0S1,K1G 0S2,K1G 0S3,K1G 0S4,K1G 0S5,K1G 0S6,K1G 0S7,K1G 0S8,K1G 0S9,K1G 0T1,K1G 0T2,K1G 0T3,K1G 0T4,K1G 0T5,K1G 0T6,K1G 0T7,K1G 0T8,K1G 0T9,K1G 0V1,K1G 0V2,K1G 0V3,K1G 0V4,K1G 0V5,K1G 0V6,K1G 0V7,K1G 0V8,K1G 0V9,K1G 0W1,K1G 0W2',
		);
	if( isset($_GET["subs_id"]) || $_GET["subs_id"] != "" ) {
		$product_id = $_GET["subs_id"];
		if( get_post_type( $product_id ) == 'product' ) {
			if( get_post_meta($product_id, 'number_of_meals_plan', true) ) {
				$meals_req = get_post_meta($product_id, 'number_of_meals_plan', true);
				$flava_meal_obj['meals_req'] = $meals_req;
			}
		}
	}
	wp_localize_script( 'jquery', 'flava_meals_object', $flava_meal_obj);
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

// include meal functionalities
require("meal-functions.php");

add_action('wp_head', 'get_custom_script');
function get_custom_script(){
	?>
    	<script>
		jQuery( document ).ready(function() {
			 jQuery('.testimonial_slider').slick({
			  slidesToScroll: 1,
			  slidesToShow: 3,
			  centerPadding: '160px',
			  arrows: false,
			  //fade: true,
			  infinite: true,
			  centerMode: true,
			  focusOnSelect: true,
			  responsive: [
			  	{
				  breakpoint: 1024,
				  settings: {
					centerPadding: '180px',
					slidesToShow: 2
				  }
				},
				{
				  breakpoint: 768,
				  settings: {
					centerPadding: '120px',
					slidesToShow: 1
				  }
				},
				{
				  breakpoint: 480,
				  settings: {
					centerPadding: '80px',
					slidesToShow: 1
				  }
				}
			  ]
			});
		});
		</script>
    <?php
	
}


add_shortcode( 'display_testimonials', 'get_testimonial_slider_shortcode_init' );
function get_testimonial_slider_shortcode_init() {	
	$args_post = array(
		'post_type' => 'testimonials',
		'posts_per_page' => '-1'	
	);
				
	$query_post = new WP_Query( $args_post );	
	
	$output = '';
    if ( $query_post->have_posts() ):  
		$output .='<div class="testimonial_slider">';   
			global $post;       
			 while ( $query_post->have_posts() ) : $query_post->the_post(); 
			 	 $output .='<div class="testimonial_item">';
				 $output .= '<img class="testimonial_quote" src="/wp-content/uploads/2022/10/quote-color.jpg" alt="" />';			 
				 $output .='<div class="entry_content">'.get_the_content().'</div>';
				 $output .='<h5>@ '.get_the_title().'</h5>';
				 if(has_post_thumbnail()){	
                 $feature_img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
				 $output .='<img class="feature_thumb" src="'.$feature_img[0].'" alt="'.get_the_title().'" />';
                 }
				 $output .='</div>';
			 endwhile;
		$output .='</div><!--section_portfolio_post-->';
       endif;
	wp_reset_query();
	return $output;					 

}// End

add_shortcode('login_register_btn', 'get_login_register_btn_shortcode');
function get_login_register_btn_shortcode(){
	ob_start();
		echo '<ul class="register_login_btn">';
			echo '<li class="btn_signup"><a href="/register/">SIGN UP</a></li>';
			 if (is_user_logged_in()) : 
				echo '<li class="btn_login"><a href="'.wp_logout_url(get_permalink()).'">Logout</a></li>';
			 else : 
				echo '<li class="btn_login"><a href="/login/">Login</a></li>';
			 endif;
		echo '</ul>';
	return ob_get_clean();
}

add_shortcode("get_delivery_options", "render_delivery_options");
function render_delivery_options() {
	ob_start();
	?>
	<form class="order_delivery_form" action="" method="post">
		<div class="input-group d-flex">
			<div class="col-6"><label class="input-label">Choose order type</label></div>
			<div class="col-6">
				<div class="radio_inline">
				<span>            
					<input type="radio" id="order_type1" name="order_type" value="Delivery">
					<label for="order_type1">Delivery</label>
				</span>
				<span>            
					<input type="radio" id="order_type2" name="order_type" value="Pickup" checked>
					<label for="order_type2">Pickup</label>
				</span>
				</div>
			</div>
		</div>
		<div class="input-group">
			<label for="postal_code" class="input-label">Enter your postal code</label>
			<div class="d-flex">
				<input type="text" name="postal_code" id="postal_code" placeholder="Enter your postal code" />
				<button type="button" id="postal-btn">Submit</button>
			</div>
			<div class="postcode-msg"></div>
		</div>
		<div class="input-group">
			<label class="input-label" style="font-size:14px">Delivery Option:</label>
			<div class="radio_list checked-opt">
				<input type="radio" id="pickup_time1" name="pickup_time" value="Pickup time: Sunday 6-8pm" checked />
				<label for="pickup_time1">Pickup time: Sunday 6-8pm</label>
			</div>
			<div class="radio_list">
				<input type="radio" id="pickup_time2" name="pickup_time" value="Delivery Sunday 6-10pm" />
				<label for="pickup_time2">Delivery Sunday 6-10pm</label>
			</div>
			<div class="radio_list">
				<input type="radio" id="pickup_time3" name="pickup_time" value="Delivery Monday AM 8am-12pm">
				<label for="pickup_time3">Delivery Monday AM 8am-12pm</label>
			</div>
			<div class="radio_list">
				<input type="radio" id="pickup_time4" name="pickup_time" value="Delivery Monday PM 12pm-6pm">
				<label for="pickup_time4">Delivery Monday PM 12pm-6pm</label>
			</div>
		</div>
		<div class="address">
		<p style="margin-bottom:0;">Minimum order for delivery is 6 meals.  No minimum for pickup.</p>
		<p><strong>Pickup Location: 101 Schneider Road, Kanata, ON K2K 1Y3</strong></p>
		</div>
		<!--<input type="submit" name="submit" id="delivery-type-submit" value="Next" />-->
		<a href="/select-meal-plans/" class="button" id="delivery-type-submit">Next</a>
	</form>
	<?php
	return ob_get_clean();
}

add_shortcode("get_meals_by_cat", "get_meals_by_cat_callback");
function get_meals_by_cat_callback($atts) {
	extract(shortcode_atts(array(
		'cat' => '',
	), $atts));
	
	ob_start();
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'product_cat'    => $cat,
	);
	
	if( !$cat ) {
		$args['tax_query'] = array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'term_id',
									'terms'    => array( 49 ), //excluding subscription products
									'operator' => 'NOT IN'
								)
							);
	}

	$loop = new WP_Query( $args );

	if( $loop->have_posts() ) {		 
		echo '<div class="cat_product_grid" data-category="'.$cat.'">';
		$term = get_term_by('slug', $cat, 'product_cat');
		echo '<h3>'.$term->name.'</h3>';
		echo '<div class="cat-products-wrap-row">';
		while ( $loop->have_posts() ) : $loop->the_post();
			global $product;
		
			$price = $product->get_price();
			if( is_page(3870) && $price ) {
				$price = $price - 0.50;
			}

			echo '<div class="meal-product" data-product-id="'.$product->get_id().'" data-product-price="'.$price.'">';
			if( has_post_thumbnail( $product->get_id() ) ) {
				$img_atts = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
				echo '<div class="image_wrap"><img src="'.$img_atts[0].'" class="meal-image" alt="'.get_the_title().'" /></div>';
			} else {
				echo '<div class="image_wrap"><img src="'.home_url("/wp-content/uploads/woocommerce-placeholder-300x300.png").'" class="meal-image" alt="'.get_the_title().'" /></div>';
			}
			echo '<div class="product_extra_content">';
			echo '<h4 class="cat-product-title">'.get_the_title().'</h4>';
			echo '<div class="meal-meta"></div>';
			?>
			<div class="meal-extra">
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" id="extra_meat_<?php echo $product->get_id(); ?>" class="extra-opts" value="Extra Meat" autocomplete="off" data-ext-cost="4.00" /> <label for="extra_meat_<?php echo $product->get_id(); ?>">Extra Meat</label>
				</div>
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" class="extra-opts" id="extra_carbs_<?php echo $product->get_id(); ?>" value="Extra Carbs" autocomplete="off" data-ext-cost="2.00" /> <label for="extra_carbs_<?php echo $product->get_id(); ?>">Extra Carbs</label>
				</div>
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" class="extra-opts" id="double_veg_<?php echo $product->get_id(); ?>" value="Double Veg" autocomplete="off" data-ext-cost="2.00" /> <label for="double_veg_<?php echo $product->get_id(); ?>">Double Veg</label>
				</div>
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" class="extra-opts" id="no_carb_<?php echo $product->get_id(); ?>" value="No Carb/Extra Veg" autocomplete="off" data-ext-cost="0.00" /> <label for="no_carb_<?php echo $product->get_id(); ?>">No Carb/Extra Veg</label>
				</div>
			</div>
			<div class="meal-button">
				<input type="number" name="meal_<?php echo $product->get_id(); ?>_quantity" value="0" min="0" autocomplete="off" class="meal-quantity" style="max-width: 70px;" />
				<button type="button" class="meal-add-btn"><i class="fas fa-shopping-cart"></i> Add to cart</button>
			</div>
			<div class="meal-price" style="padding-top: 20px; font-weight: 700;">
				<?php //echo $product->get_price_html(); ?>
				<?php echo '$'.$price; ?>
			</div>
			<?php
			echo '</div></div>';
		endwhile;
		echo '</div></div>';
	}
	wp_reset_query();	
	
	return ob_get_clean();
}

add_shortcode("get_meals_cart", "render_meals_cart");
function render_meals_cart() {
	ob_start();
	?>
	<div id="flash-cart">
		<div class="cart-header">
			<h3>Your Cart</h3>
			<span class="close-cart">x</span>
		</div>
		<div class="cart-content-wrap">
			<div class="cart-items-wrap">
				<div class="cart-no-item">No item</div>
			</div>
			<div class="cart-totals">
				<div class="cart-subtotal-line d-flex">
					<span>Subtotal</span>
					<strong class="cart-subtotal">$0.00</strong>
				</div>
				<div class="cart-extra-total-line d-flex" style="display: none;">
					<span>Options Total</span>
					<strong class="cart-extras">$0.00</strong>
				</div>
				<div class="cart-total-line d-flex">
					<span>Total</span>
					<strong class="cart-total">$0.00</strong>
				</div>
				<input type="hidden" name="items_in_cart" id="items-in-cart" value="" autocomplete="off" />
				<input type="hidden" name="meals_count" id="meals-count" value="0" autocomplete="off" />
			</div>
			<?php if( isset($_GET["subs_id"]) && $_GET["subs_id"] > 0 ) { ?>
				<div class="subs-cart-totals">
					<div class="subs-cart-total-line d-flex">
						<span>Total</span>
						<?php //$sale = get_post_meta( get_the_ID(), '_sale_price', true); ?>
						<strong class="subs-cart-total">$<?php echo get_post_meta( $_GET["subs_id"], '_regular_price', true); ?></strong>
					</div>
				</div>
				<form method="POST" action="" id="meal-subscription-form">
					<input type="hidden" name="subscription_id" id="subscription-id" value="<?php echo $_GET["subs_id"]; ?>" autocomplete="off" />
					<!-- on button click validate the number of meals requried -->
					<input type="hidden" name="subs_cart_items" id="subs-cart-items" value="" autocomplete="off" />
					<button type="button" id="proceed-wksubs-cart" style="width: 100%;">Proceed</button>
				</form>
			<?php } else { ?>
				<button type="button" id="proceed-cart">Proceed</button>
			<?php } ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode("get_cart_notes", "render_cart_notes");
function render_cart_notes() {
	ob_start();
	?>
    <div class="cart_note">
    	<h5>Note</h5>
        <p>The order minimum is <strong>5 meals <a class="min-meals-hl">(<span class="meals-left-count">5</span> Meals Left)</a></strong></p>
        <!--<p>One-time orders – minimum is  <strong>6 Meals <a class="min-meals-hl">(<span class="meals-left-count">6</span> Meals Left)</a></strong></p>-->
		<?php if( is_page(3580) ) { ?>
        <p>There is no minimum order <strong>for pickup orders</strong></p>
		<?php } ?>
    </div>
	<?php
	return ob_get_clean();
}

add_action( 'woocommerce_review_order_before_payment', 'add_custom_checkout_radio_options', 20 );
function add_custom_checkout_radio_options() {
	/*$chosen = WC()->session->get('radio_chosen');
	$chosen = empty( $chosen ) ? WC()->checkout->get_value('delivery_for_not_home') : $chosen;
	$chosen = empty( $chosen ) ? 'no_option' : $chosen;*/
	
    // Add a custom checkbox field
    woocommerce_form_field( 'delivery_for_not_home', array(
        'type'  => 'radio',
        'label' => __(' If I am not home at time of delivery, please leave the package:'),
		'options' => array(
						'At the Front Door' => 'At the Front Door',
						'At the Back Door' => 'At the Back Door',
						'At the Concierge' => 'At the Concierge',
						'At my Unit' => 'At my Unit'
					),
        'class' => array( 'form-row-wide' ),
		'required' => false,
    ), '' );
	//'default' => $chosen
	
	woocommerce_form_field( 'order_type', array(
        'type'  => 'text',
        'label' => __('Order Type'),
        'class' => array( 'wc-hidden-field' ),
		'required' => false,
    ), '' );
	
	woocommerce_form_field( 'delivery_option', array(
        'type'  => 'text',
        'label' => __('Delivery Option'),
        'class' => array( 'wc-hidden-field' ),
		'required' => false,
    ), '' );
}

// Remove "(optional)" label on "Installement checkbox" field
add_filter( 'woocommerce_form_field' , 'flava_remove_order_comments_optional_fields_label', 10, 4 );
function flava_remove_order_comments_optional_fields_label( $field, $key, $args, $value ) {
    // Only on checkout page for Order notes field
    if( 'delivery_for_not_home' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
    return $field;
}

//add_action('woocommerce_checkout_process', 'flava_custom_checkout_field_process');
function flava_custom_checkout_field_process() {
    // Check if set, if its not set add an error.
    if ( ! $_POST['delivery_for_not_home'] )
        wc_add_notice( __( 'Please select an option for if you are not home at the time of delivery.' ), 'error' );
}

// Update the order meta with field value
add_action( 'woocommerce_checkout_update_order_meta', 'flava_custom_checkout_field_update_order_meta', 10, 1 );
function flava_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['delivery_for_not_home'] ) ) {
        update_post_meta( $order_id, '_delivery_for_not_home', sanitize_text_field( $_POST['delivery_for_not_home'] ) );
    }
	
	if ( ! empty( $_POST['order_type'] ) ) {
        update_post_meta( $order_id, '_order_type', sanitize_text_field( $_POST['order_type'] ) );
    }
	
	if ( ! empty( $_POST['delivery_option'] ) ) {
        update_post_meta( $order_id, '_delivery_option', sanitize_text_field( $_POST['delivery_option'] ) );
    }
}

// Display the custom-field in orders view
add_action( 'woocommerce_order_details_after_customer_details', 'display_flava_custom_field_in_orde_details', 10, 1 );
function display_flava_custom_field_in_orde_details( $order ) {
    $home_delivery_opts = get_post_meta( $order->get_id(), '_delivery_for_not_home',  true );
	$order_type = get_post_meta( $order->get_id(), '_order_type',  true );
	$delivery_option = get_post_meta( $order->get_id(), '_delivery_option',  true );

    if ( ! empty( $home_delivery_opts ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--customer-details shop_table customer_details">
            <tbody>
				<tr>
					<th>If I am not home at time of delivery, please leave the package:</th>
					<td><?php echo $home_delivery_opts; ?></td>
				</tr>
			</tbody>
        </table>
    <?php
	}
	if ( ! empty( $order_type ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--customer-details shop_table customer_details">
            <tbody>
				<tr>
                	<th>Order Type:</th>
                	<td><?php echo $order_type; ?></td>
            	</tr>
			</tbody>
        </table>
    <?php
	}
	if ( ! empty( $delivery_option ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--customer-details shop_table customer_details">
            <tbody>
				<tr>
                	<th>Delivery Option:</th>
                	<td><?php echo $delivery_option; ?></td>
            	</tr>
			</tbody>
        </table>
    <?php
	}
}

//add_filter( 'woocommerce_add_to_cart_validation', 'remove_cart_item_before_add_to_cart', 20, 3 );

add_action("template_redirect", 'flava_empty_cart_before_add_to_cart');
function flava_empty_cart_before_add_to_cart() {
	if( is_page(3580) ) {
		if( ! WC()->cart->is_empty() ) {
			WC()->cart->empty_cart();
		}
	}
	
	if( is_page(3870) && !$_GET["action"] == "elementor" ) {
		if( !isset($_GET["subs_id"]) || $_GET["subs_id"] == "" ) {
			if( strpos($_SERVER['REQUEST_URI'], 'elementor') === false ) {
				wp_redirect( get_permalink(3742) );
				exit;
			}
		}
	}
}

add_filter( 'body_class', 'custom_class' );
function custom_class( $classes ) {
	if ( is_page(3870) ) {
        $classes[] = 'weekly-meals-order-tmp';
    }
	return $classes;
}

add_filter( 'wp_mail_from_name', function( $name ) {
	return 'Flava Daddy';
});

add_action("template_redirect", "add_subscription_items_to_cart");
function add_subscription_items_to_cart() {
	if( isset($_POST["subscription_id"]) ) {
		$product_id = $_POST["subscription_id"];
		$cart_items = json_decode(stripslashes($_POST["subs_cart_items"]), true);
		//print_r( json_decode(stripslashes($_POST["subs_cart_items"]), true) );
		$item_meta = '';
		foreach($cart_items as $cart_item) {
			$item_meta .= $cart_item['title']." - x".$cart_item['quantity']."<br>";
		}
		//WC()->cart->add_to_cart( 14, 1, 0, array(), array( 'misha_custom_price' => 1000 ) );
		WC()->cart->add_to_cart( $product_id, 1, 0, array(), array( '_child_products' => $item_meta ) );
		
		wp_redirect("/cart/");
		exit;
	}
}

// remove product link from cart items
add_filter('woocommerce_cart_item_permalink','__return_false');

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
	if( isset($_GET["gfur_activation"]) ) {
		wp_enqueue_style( 'fontawesome-style', 'https://flavadaddy.com/wp-content/plugins/elementor/assets/lib/font-awesome/css/solid.min.css', array(), '5.15.3' );
	}
	wp_enqueue_script( 'slick-js',  '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js');
	
	if( is_page(3580) || is_page(3742) || is_cart() ) {
		wp_enqueue_script( 'md5-js',  get_stylesheet_directory_uri() . '/md5.min.js');
	}
	wp_enqueue_script( 'meals-js',  get_stylesheet_directory_uri() . '/meals.js');
	
	//https://www.zip-codes.com/canadian/city.asp?city=ottawa&province=on
	//https://regexr.com/
	//Regex: <li><a href="\/canadian\/postal-code.asp\?postalcode=(\w+\+\w+)">(\w+\ \w+)<\/a><\/li>

	//https://www.phpliveregex.com/#tab-preg-replace
	//Replace: $2,
	$flava_meal_obj = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'post_codes' => 'K2A,K2B,K2H,K2L,K2M,K2S,K2R,K2J,K2G,K2C,K2E,K1V,K1Z,K1Y,K1S,K1R,K2P,K1H,K1N,K1M,K1L,K1T,K1J,K4P,K4M,K1X,K1C,K1G,K1B,K2V,K2K,K2T,K2W,K4A,K1K,K0A,K4B,J8R,J8V,J8P,J8T,K4C,K1A,K1B,K1G,K1J,K1T,K1V,K1W,K1X,K4P,K2K,K2L,K2M,K2S,K2T,K2V,K2W,K4B,K1A,K1C,K1E,K1W,K4A,K1K,K1L,K1M,K2S,K2V,K7C',
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
			
			jQuery(".change-delivery-time").on("click", function() {
				jQuery(".delivery-change-form").toggle();
			});
			
			<?php if( !isset($_SESSION['order_type']) ) { ?>
				// clear all the stored data from the localstorage
				localStorage.removeItem("cart_items");
				localStorage.removeItem("cart_subtotal");
				localStorage.removeItem("cart_total");
				localStorage.removeItem("cart_extras");

				localStorage.removeItem("order_type");
				localStorage.removeItem("pickup_time");
				localStorage.removeItem("postal_code");
			<?php } else { ?>
			jQuery('.elementor-icon-list-item a[datafooter-item="onetime"]').attr("href", "/one-time-order/");
			jQuery('.elementor-icon-list-item a[datafooter-item="weeklysubs"]').attr("href", "/weekly-subscription/");
			<?php } ?>
		});
		</script>
		<style>
    <?php
		if( ! get_option('top_bar_enable') || get_option('top_bar_enable') != 1 ) {
			echo 'body:not(.elementor-editor-active) #highlight-bar { display: none; }';
		}
	
		if( sizeof(WC()->cart->get_cart()) == 0 ) {
			echo '#wpmenucartli { visibility: hidden; }';
			echo '.elementor-widget-icon .wpmenucart-contents.empty-wpmenucart-visible { display: none; }';
		}
		
		if( isset($_SESSION['order_type']) ) {
			echo '.register_login_btn li.btn_signup { visibility: hidden; }';
		}
	
		global $post;
		$product_cats_ids = wc_get_product_term_ids( $post->ID, 'product_cat' );
		if( in_array(32, $product_cats_ids) ) {
			echo '.elementor-heading-title { font-size: 50px !important; }';
			echo 'h2.product_title { font-size: 40px !important; }';
			echo '.elementor .elementor-hidden-desktop.elementor-widget-woocommerce-product-price { display: block !important; }';
			echo '.woocommerce .elementor-widget-woocommerce-product-price .price { font-family: "Inter", Sans-serif !important; }';
		}
	?>
			.woocommerce-cart .flava-return-btn { display: inline-block; }
			.subscription_details .button.change_address { display: none; }
		</style>
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
			echo '<li class="btn_signup"><a href="/select-meal-plans/">Order Now</a></li>';
			 if (is_user_logged_in()) : 
				echo '<li class="btn_login"><a href="'.wp_logout_url(get_permalink()).'">Logout</a></li>';
			 else : 
				echo '<li class="btn_login"><a href="/login/">Login</a></li>';
			 endif;
		echo '</ul>';
	return ob_get_clean();
}

add_shortcode('get_top_bar_text', 'flava_get_top_bar_text');
function flava_get_top_bar_text() {
	ob_start();
	if( get_option('top_bar_msg') ) {
		echo htmlspecialchars_decode( get_option('top_bar_msg') );
	}
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
					<input type="radio" id="order_type1" name="order_type" value="Delivery" checked="checked" />
					<label for="order_type1">Delivery</label>
				</span>
				<span>            
					<input type="radio" id="order_type2" name="order_type" value="Pickup" />
					<label for="order_type2">Pickup</label>
				</span>
				</div>
			</div>
		</div>
		<div class="input-group">
			<label class="input-label" style="font-size:14px">Delivery Option:</label>
			<div class="delivery-opts-group">
				<div class="radio_list pickup-opt" style="display: none;">
					<input type="radio" id="pickup_time1" name="pickup_time" value="Pickup time: Sunday 6-7pm" />
					<label for="pickup_time1">Pickup time: Sunday 6-7pm</label>
				</div>
				<div class="radio_list checked-opt delivery-opt">
					<input type="radio" id="pickup_time2" name="pickup_time" value="Delivery: Sunday 5-10pm" checked />
					<label for="pickup_time2">Delivery Sunday 5-10pm</label>
				</div>
				<div class="radio_list delivery-opt">
					<input type="radio" id="pickup_time3" name="pickup_time" value="Delivery: Monday AM 8am-12pm">
					<label for="pickup_time3">Delivery Monday AM 8am-12pm</label>
				</div>
				<!--<div class="radio_list delivery-opt">
					<input type="radio" id="pickup_time4" name="pickup_time" value="Delivery: Monday PM 12pm-6pm">
					<label for="pickup_time4">Delivery Monday PM 12pm-6pm</label>
				</div>-->
			</div>
		</div>
		<div class="input-group postal-code-group">
			<label for="postal_code" class="input-label">Enter your postal code</label>
			<div class="d-flex">
				<input type="text" name="postal_code" id="postal_code" placeholder="Enter your postal code" />
				<button type="button" id="postal-btn">Submit</button>
			</div>
			<div class="postcode-msg"></div>
		</div>		
		<div class="address">
			<p style="margin-bottom:0;">Minimum order for delivery is 6 meals.<br>Minimum order for pickup – none.</p>
			<p><strong>Pickup Location: 101 Schneider Road, Kanata, ON K2K 1Y3</strong></p>
			<?php if( isset($_GET["order_type"]) && $_GET["order_type"] != "" ) { ?>
			<input type="hidden" id="meal-plan-type" name="meal_plan_type" value="<?php echo $_GET["order_type"]; ?>" />
			<?php } ?>
		</div>
		<!--<input type="submit" name="submit" id="delivery-type-submit" value="Next" />-->
		<a href="" class="button" id="delivery-type-submit">Next</a>
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
		echo '<div class="cat_product_grid cat-'.$cat.'-products" data-category="'.$cat.'">';
		$term = get_term_by('slug', $cat, 'product_cat');
		echo '<h3>'.$term->name.'</h3>';
		echo '<div class="cat-products-wrap-row">';
		while ( $loop->have_posts() ) : $loop->the_post();
			global $product;
		
			$price = $product->get_price();
			if( is_page(3870) && $price ) {
				$price = $price - 0.50;
			}
			
			$lbx_link_start = '';
			$lbx_link_end = '';
			$the_content = apply_filters('the_content', get_the_content());
			if( !empty($the_content) ) {
				$lbx_link_start .= '<a href="#meal-desc-'.$product->get_id().'" class="fancybox-inline">';
				$lbx_link_end .= '</a>';
			}
			
			echo '<div class="meal-product" data-product-id="'.$product->get_id().'" data-product-price="'.$price.'">';
			if( has_post_thumbnail( $product->get_id() ) ) {
				$img_atts = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
				echo '<div class="image_wrap">'.$lbx_link_start.'<img src="'.$img_atts[0].'" class="meal-image" alt="'.get_the_title().'" />'.$lbx_link_end.'</div>';
			} else {
				echo '<div class="image_wrap">'.$lbx_link_start.'<img src="'.home_url("/wp-content/uploads/woocommerce-placeholder-300x300.png").'" class="meal-image" alt="'.get_the_title().'" />'.$lbx_link_end.'</div>';
			}
			echo '<div class="product_extra_content">';
			echo '<h4 class="cat-product-title">'.$lbx_link_start.get_the_title().$lbx_link_end.'</h4>';
			
			if( !empty($the_content) ) {
				$lbx_img = '<img src="'.home_url("/wp-content/uploads/woocommerce-placeholder-300x300.png").'" class="lbx-meal-image" alt="'.get_the_title().'" />';
				if( isset($img_atts[0]) ) {
					$lbx_img = '<img src="'.$img_atts[0].'" class="lbx-meal-image" alt="'.get_the_title().'" />';
				}
				echo '<div style="display :none;"><div id="meal-desc-'.$product->get_id().'" class="meal-ingredients-info"><h4 class="lbx-product-title">'.get_the_title().'</h4>'.$lbx_img.$the_content.'</div></div>';
			}
			
			$meal_meta = '';
			if( get_post_meta($product->get_id(), 'meal_cal', true) ) {
				$meal_meta .= '<span class="meal-cal-meta"><img src="'.get_stylesheet_directory_uri().'/images/cal.png" alt="Cal" />'.get_post_meta($product->get_id(), 'meal_cal', true).'Cal <a class="nutrition-info"><i class="fa fa-info-circle"></i></a></span>';
			}
			if( get_post_meta($product->get_id(), 'meal_gluten', true) ) {
				$meal_meta .= '<span class="meal-gluten-meta"><img src="'.get_stylesheet_directory_uri().'/images/gluten.png" alt="gluten" />Gluten '.get_post_meta($product->get_id(), 'meal_gluten', true).' <a class="nutrition-info"><i class="fa fa-info-circle"></i></a></span>';
			}
			if( get_post_meta($product->get_id(), 'meal_serve', true) ) {
				$meal_meta .= '<span class="meal-serve-meta"><img src="'.get_stylesheet_directory_uri().'/images/serve.png" alt="serve" />'.get_post_meta($product->get_id(), 'meal_serve', true).'-Serve <a class="nutrition-info"><i class="fa fa-info-circle"></i></a></span>';
			}
			echo '<div class="meal-meta">'.$meal_meta.'</div>';
			?>
			<div class="meal-extra">
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" id="extra_meat_<?php echo $product->get_id(); ?>" class="extra-opts extra-meat" value="Extra Meat" autocomplete="off" data-ext-cost="4.00" /> <label for="extra_meat_<?php echo $product->get_id(); ?>">Extra Meat ($4)</label>
				</div>
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" class="extra-opts extra-carbs" id="extra_carbs_<?php echo $product->get_id(); ?>" value="Extra Carbs" autocomplete="off" data-ext-cost="2.00" /> <label for="extra_carbs_<?php echo $product->get_id(); ?>">Extra Carbs ($2)</label>
				</div>
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" class="extra-opts double-veg" id="double_veg_<?php echo $product->get_id(); ?>" value="Double Veg" autocomplete="off" data-ext-cost="2.00" /> <label for="double_veg_<?php echo $product->get_id(); ?>">Double Veg ($2)</label>
				</div>
				<div class="extra-meal-opt">
					<input type="checkbox" name="extra_meal_<?php echo $product->get_id(); ?>_opts[]" class="extra-opts no-carb-veg" id="no_carb_<?php echo $product->get_id(); ?>" value="No Carb/Extra Veg" autocomplete="off" data-ext-cost="0.00" /> <label for="no_carb_<?php echo $product->get_id(); ?>">No Carb/Extra Veg</label>
				</div>
			</div>
			<div class="meal-button">
				<input type="number" name="meal_<?php echo $product->get_id(); ?>_quantity" value="1" min="1" autocomplete="off" class="meal-quantity" style="max-width: 70px;" />
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
	<div id="cart-overlay"></div>
	<div id="flash-cart">
		<div class="flash-cart-contents-wrap">
			<div id="flash-cart-toggle">
				<i class="fa fa-angle-left"></i>
			</div>
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
				<?php //if( WC()->session->get('order_type') && WC()->session->get('order_type') == "Delivery" ) { ?>
				<p class="order-minlimit-info" style="display: none;">The order minimum is <strong>$88 <a class="min-meals-hl" style="display:none;">($<span class="meals-left-count">88</span> Left)</a></strong></p>
				<?php //} ?>
				<button type="button" id="proceed-cart">Proceed to Shopping Cart</button>
			</div>
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

add_shortcode("get_menu_this_week", "get_menu_this_week_callback");
function get_menu_this_week_callback() {
	$args = array(
		'featured' => true,
		'limit' => 6
	);
	$products = wc_get_products( $args );
	
	ob_start();
	if( count($products) > 0 ) {
		echo '<div class="menu-this-week">';
		foreach($products as $product) {
			$product_id = $product->get_id();
			$product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
			$prod_link = "/select-meal-plans/";
			if( in_array(32, $product_cats_ids) ) {
				// dessert
				//$prod_link = get_permalink( $product_id );
			}
			echo '<div class="featured-meal-wrap">';
			echo '<div class="ft-meal-image"><a href="'.$prod_link.'">'.$product->get_image().'</a></div>';
			echo '<h4 class="ft-meal-title">'.get_the_title($product_id).'</h4>';
			echo '</div>';
		}
		echo '</div>';
	}
	return ob_get_clean();
}

add_action( 'woocommerce_review_order_before_payment', 'add_custom_checkout_radio_options', 5 );
function add_custom_checkout_radio_options() {
	$chosen = '';
	if( WC()->session->get('insulated_bag_fee') ) {
		$chosen = 1;
	}
	
	$req = false;
	/*if( WC()->session->get('order_type') && WC()->session->get('order_type') == "Delivery" ) {
		$req = true;
	}*/
	if( isset($_SESSION['order_type']) && $_SESSION['order_type'] == "Delivery" ) {
		$req = true;
	}

    // Add a custom radio button field
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
		'autocomplete' => 'off',
		'required' => $req,
    ), '' );
	
	woocommerce_form_field( 'insulated_bag_fee', array(
        'type'  => 'checkbox',
        'label' => __(' Insulated Bag with Ice $9 <i class="fa fa-info-circle" data-title="Please leave bag out for pickup in order to receive a credit on your account for the insulated bag with ice."></i>'),
        'class' => array( 'form-row-wide' ),
    ), $chosen );
	
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
    // Only on checkout page for specific fields
    if( 'delivery_for_not_home' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
    if( 'insulated_bag_fee' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
	if( 'order_comments' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
    return $field;
}

add_action('woocommerce_checkout_process', 'flava_custom_checkout_field_process');
function flava_custom_checkout_field_process() {
	//if( WC()->session->get('order_type') && WC()->session->get('order_type') == "Delivery" ) {
	if( isset($_SESSION['order_type']) && $_SESSION['order_type'] == "Delivery" ) {
		// Check if set, if its not set add an error.
		if ( ! $_POST['delivery_for_not_home'] ) {
			wc_add_notice( __( 'Please select an option for if you are not home at the time of delivery.' ), 'error' );
		}
	}
}

// Update the order meta with field value
add_action( 'woocommerce_checkout_update_order_meta', 'flava_custom_checkout_field_update_order_meta', 10, 1 );
function flava_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['delivery_for_not_home'] ) ) {
        update_post_meta( $order_id, '_delivery_for_not_home', sanitize_text_field( $_POST['delivery_for_not_home'] ) );
    }
	
	if ( isset( $_POST['insulated_bag_fee'] ) ) {
        update_post_meta( $order_id, '_insulated_bag_fee', sanitize_text_field( $_POST['insulated_bag_fee'] ) );
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
	$insulated_bag_fee = get_post_meta( $order->get_id(), '_insulated_bag_fee',  true );

    if ( ! empty( $home_delivery_opts ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--delivery-details shop_table delivery_details">
            <tbody>
				<tr>
					<td>If I am not home at time of delivery, please leave the package: <?php echo $home_delivery_opts; ?></td>
				</tr>
			</tbody>
        </table>
    <?php
	}
	if ( ! empty( $order_type ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--ordertype-details shop_table ordertype_details">
            <tbody>
				<?php if( $insulated_bag_fee ) { ?>
				<tr>
                	<td>Insulated Bag with Ice $9</td>
            	</tr>
				<?php } ?>
				<tr>
                	<td>Order Type: <?php echo $order_type; ?></td>
            	</tr>
				<?php
				if ( ! empty( $delivery_option ) ) {
					$delivery_cng = '';
					$delivery_cng_form = '';
					
					if( is_wc_endpoint_url( 'view-subscription' ) || is_wc_endpoint_url( 'view-order' ) ) {
						if( $order_type == "Delivery" ) {
							$delivery_cng = '<a href="javascript:void(0);" class="change-delivery-time">Change Time</a>';
							//$delivery_times = array("Delivery: Sunday 5-10pm","Delivery: Monday AM 8am-12pm","Delivery: Monday PM 12pm-6pm");
							$delivery_times = array("Delivery: Sunday 5-10pm","Delivery: Monday AM 8am-12pm");
							$delivery_opts = '';
							
							foreach($delivery_times as $delivery_time) {
								$delivery_select = '';
								if( $delivery_option == $delivery_time ) {
									$delivery_select = 'selected="selected"';
								}
								$delivery_opts .= '<option value="'.$delivery_time.'" '.$delivery_select.'>'.$delivery_time.'</option>';
							}
							
							$delivery_cng_form = '<form action="" method="post" class="delivery-change-form" style="display:none;">
								<select name="delivery_opts">'.$delivery_opts.'</select>
								<input type="hidden" name="upd_order" value="'.$order->get_id().'" />
								<button type="submit" name="delivery_change_btn">Change</button>
							</form>';
						}
					}
				?>
				<tr>
                	<td><?php echo $delivery_option.' '.$delivery_cng; ?></td>
            	</tr>
				<?php
					if( $delivery_cng_form ) {
						echo '<tr>
								<td>'.$delivery_cng_form.'</td>
							</tr>';
					}
				}
				?>
			</tbody>
        </table>
    <?php
	}
}

function flava_display_order_data_in_admin( $order ) {
	if( get_post_meta( $order->id, '_delivery_option', true ) ) {
		echo '<p style="margin-bottom: 0;">&nbsp;</p>';
		echo '<p style="margin-top: 0; font-size: 16px; font-weight: 600;">' . get_post_meta( $order->id, '_delivery_option', true ) . '</p>';
	}
	
	if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
		echo '<p style="margin-top: 0;">If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
	}
}
add_action('woocommerce_admin_order_data_after_order_details', 'flava_display_order_data_in_admin');

//add_action('woocommerce_new_order', 'flava_woocommerce_new_order', 10, 2);
function flava_woocommerce_new_order( $order_id, $order ) {
	// remove fee from session after order placed
	if( null !== WC()->session->get('insulated_bag_fee') ) {
		WC()->session->__unset('insulated_bag_fee');
	}
}

//add_filter( 'woocommerce_add_to_cart_validation', 'remove_cart_item_before_add_to_cart', 20, 3 );

add_action("template_redirect", 'flava_empty_cart_before_add_to_cart');
function flava_empty_cart_before_add_to_cart() {
	// Only on one-time or weekly subscription pages
	if( is_page(3580) || is_page(3742) ) {
		// todo
	}
	
	if( is_page(3870) && !$_GET["action"] == "elementor" ) {
		if( !isset($_GET["subs_id"]) || $_GET["subs_id"] == "" ) {
			if( strpos($_SERVER['REQUEST_URI'], 'elementor') === false ) {
				wp_redirect( get_permalink(3742) );
				exit;
			}
		}
	}
	
	// redirect if on the Register page
	if( is_page(3281) ) {
		if( is_user_logged_in() ) {
			wp_redirect( home_url() );
			exit;
		}
	}
	
	if( is_singular( 'product' ) ) {
		global $post;
		
		$product_cats_ids = wc_get_product_term_ids( $post->ID, 'product_cat' );
		
		if( ! get_post_meta($post->ID, '_gift_card', true) && !in_array(32, $product_cats_ids) ) {
			wp_redirect( home_url(), 301 );
			exit;
		}
	}
	
	// only on the customer’s account page
	if( is_account_page() ) {
		if( ! is_user_logged_in() ) {
			wp_redirect( home_url('/login/') );
			exit;
		}
	}
	
	// only on the order received OR order thank-you page
	if( is_order_received_page() ) {
		if( null !== WC()->session->get('insulated_bag_fee') ) {
			WC()->session->__unset('insulated_bag_fee');
		}
		
		if( isset($_SESSION['order_type']) ) {
			unset($_SESSION['order_type']);
		}
	}
	
	// only on One-Time Order or Weekly Subscription or Order Delivery pages
	if( is_page(3580) || is_page(3742) || is_page(3462) ) {
		$option = get_option('sold_out_enable');
		$value  = ( empty( $option ) ) ? 0 : $option;
		if( $value ) {
			// redirect to the sold out page
			wp_redirect( get_permalink(4599) );
			exit;
		}
	}
	
	if( is_product_category() || is_product_tag() || is_search() ) {
		wp_redirect( home_url() );
		exit;
	}
	
	if( is_checkout() ) {
		if( isset($_SESSION['order_type']) && $_SESSION['order_type'] == "Delivery" ) {
			$cart_sub_total = WC()->cart->subtotal_ex_tax;
			if( $cart_sub_total < 88 ) {
				wp_redirect( home_url("/cart/?errmsg=less") );
				exit;
			}
		}
	}
	
	if( isset($_POST["delivery_change_btn"]) ) {
		$order_id = $_POST["upd_order"];
		$delivery_time = $_POST["delivery_opts"];
		
		update_post_meta( $order_id, '_delivery_option', $delivery_time );
	}
	
	if( !isset($_SESSION['order_type']) ) {
		/*WC()->cart->empty_cart();*/
        //WC()->session->destroy_session();
	}
}

add_filter( 'body_class', 'custom_class' );
function custom_class( $classes ) {
	if ( is_page(3870) ) {
        $classes[] = 'weekly-meals-order-tmp';
    }
	if ( isset($_GET["gfur_activation"]) ) {
        $classes[] = 'gf-user-activation-template';
    }
	return $classes;
}

add_filter( 'wp_mail_from_name', function( $name ) {
	return 'Flava Daddy';
});

add_action("template_redirect", "add_subscription_items_to_cart");
function add_subscription_items_to_cart() {
	/*if( isset($_POST["subscription_id"]) ) {
		$product_id = $_POST["subscription_id"];
		$cart_items = json_decode(stripslashes($_POST["subs_cart_items"]), true);
		$item_meta = '';
		foreach($cart_items as $cart_item) {
			$item_meta .= $cart_item['title']." - x".$cart_item['quantity']."<br>";
		}
		WC()->cart->add_to_cart( $product_id, 1, 0, array(), array( '_child_products' => $item_meta ) );
		
		wp_redirect("/cart/");
		exit;
	}*/
	
	if( isset($_GET["add_subs_items"]) ) {
		$cart_items = explode(",", $_GET["add_subs_items"]);
		foreach($cart_items as $product_id) {
			WC()->cart->add_to_cart( $product_id, 1 );
		}
		
		wp_redirect("/cart/");
		exit;
	}
}

add_filter('woocommerce_cart_item_permalink','__return_false');

/**
 * Hide shipping rates when free shipping is available.
 * Updated to support WooCommerce 2.6 Shipping Zones.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function flava_hide_shipping_when_free_is_available( $rates ) {
	$chosen_method = array();
	
	if( isset($_SESSION['order_type']) ) {
	//if( WC()->session->get('order_type') ) {
		//$order_type = WC()->session->get('order_type');
		$order_type = $_SESSION['order_type'];
		//echo '?? dfsl-'.$order_type.'-dsf';
		
		$free_shipping_applied = false;
		
		$applied_coupons = WC()->cart->get_applied_coupons();
		if( sizeof($applied_coupons) > 0 ) {
			foreach($applied_coupons as $coupon_code) {
				$coupon = new WC_Coupon( $coupon_code );
				
				// check if this couple allows free shipping
				if( $coupon->get_free_shipping() ) {
					$free_shipping_applied = true;
					break;
				}
			}
		}
		
		foreach( $rates as $rate_id => $rate ) {
			if( $free_shipping_applied ) {
				if( ($order_type == 'Delivery') && ('free_shipping' === $rate->method_id) ) {
					$chosen_method[ $rate_id ] = $rate;
					break;
				}
			} else {
				if( ($order_type == 'Delivery') && ('flat_rate' === $rate->method_id) ) {
					$chosen_method[ $rate_id ] = $rate;
					break;
				} elseif( ($order_type == 'Pickup') && ('local_pickup' === $rate->method_id) ) {
					$chosen_method[ $rate_id ] = $rate;
					break;
				}
			}
			
			/*if(strpos($rate_id, 'flat_rate') !== false) {
				unset($rates[$rate_id]);
			}*/
		}
	}
	
	return ! empty( $chosen_method ) ? $chosen_method : $rates;
	//return $rates;
}
add_filter( 'woocommerce_package_rates', 'flava_hide_shipping_when_free_is_available', 100 );

add_action( 'after_setup_theme', function() {
    WC_Cache_Helper::get_transient_version( 'shipping', true );
});

add_filter('gettext', 'translate_reply');
add_filter('ngettext', 'translate_reply');
function translate_reply($translated) {
	if( ! is_admin() && $translated == "Suspend" ) {
        $translated = str_ireplace('Suspend', 'Pause', $translated);
    }
	if( $translated == "Initial Shipment" ) {
		$translated = str_ireplace('Initial Shipment', 'Shipping', $translated);
	}
	
	return $translated;
}

function flava_custom_admin_head() {
	?>
	<style type="text/css">
		#order_line_items .item .display_meta tr th, .wc-order-bulk-actions .button.refund-items, #menu-posts-product .wp-submenu li:nth-last-child(3) {
			display: none;
		}
		.meal-type-table tbody tr:nth-child(2n) {
			background: #f2f2f2;
		}
		.date-selection-row, .orders-list-table {
			padding: 20px;
		}
		
		.post-type-shop_order .tablenav.top .actions, .post-type-shop_order .tablenav.bottom, .post-type-shop_order td#cb, .post-type-shop_order .check-column, .post-type-shop_order #subscription_relationship, .post-type-shop_order .column-subscription_relationship, .post-type-shop_order .order-preview, .post-type-shop_order #posts-filter .search-box, .post-type-shop_order .tablenav.top br.clear, .post-type-shop_subscription .search-box, .post-type-shop_subscription .tablenav .actions:not(.bulkactions) {
			display: none;
		}
		.post-type-shop_order .tablenav.top {
			position: relative;
			height: auto;
		}
		.post-type-shop_order .tablenav.top .tablenav-pages {
			margin-top: -35px;
		}
		.post-type-shop_order .manage-column.sortable a {
			color: #000;
		}
		.post-type-shop_order .manage-column.sortable a, .post-type-shop_order .manage-column.sortable a span {
			cursor: text;
		}
		.post-type-shop_order th.desc:hover span.sorting-indicator {
			visibility: hidden;
		}
		.filter-orders-type {
			margin-bottom: 10px;
			font-size: 14px;
		}
		.filter-orders-type a {
			text-decoration: none;
			border: 1px solid #acacac;
			padding: 3px 15px;
			display: inline-block;
			line-height: 25px;
		}
		.filter-orders-type a.active-sec {
			background: #2271b1;
			color: #fff;
		}
		.woocommerce_page_woo-cart-abandonment-recovery #addedit_template tr:nth-child(6), .woocommerce_page_woo-cart-abandonment-recovery #addedit_template tr:nth-last-child(2), .woocommerce_page_woo-cart-abandonment-recovery #addedit_template tr:last-child {
			display: none;
		}
	</style>
	<?php
		$screen = get_current_screen();
		if ( ("product_page_orders-delivery-for-the-week" === $screen->id) || ("product_page_orders-meal-for-the-week" === $screen->id) ) {
	?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.reporting-date').datetimepicker({
					dateFormat : 'yy-mm-dd',
					controlType: 'select',
					oneLine: true,
					timeFormat: 'hh:mm tt',
					hour: 17
				});
				
				jQuery(".filter-orders-type a").on("click", function(e) {
					e.preventDefault();
					jQuery(".filter-orders-type a").removeClass("active-sec");
					jQuery(this).addClass("active-sec");
					var delivery_type = jQuery(this).attr("data-order-type");
					if( delivery_type == "all" ) {
						jQuery('tr[data-delivery-type="Delivery"]').show();
						jQuery('tr[data-delivery-type="Pickup"]').show();
						var pdf_report_download_url = jQuery(".pdf-download-btn").attr("href");
						var csv_report_download_url = jQuery(".csv-download-btn").attr("href");
						if( pdf_report_download_url.includes("transportation") && csv_report_download_url.includes("transportation") ) {
							var pdf_report_url_arr = pdf_report_download_url.split("&transportation");
							var new_pdf_report_url = pdf_report_url_arr[0];
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							
							var csv_report_url_arr = csv_report_download_url.split("&transportation");
							var new_csv_report_url = csv_report_url_arr[0];
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						} else {
							// no changes
						}
					}
					if( delivery_type == "delivery" ) {
						jQuery('tr[data-delivery-type="Delivery"]').show();
						jQuery('tr[data-delivery-type="Pickup"]').hide();
						var pdf_report_download_url = jQuery(".pdf-download-btn").attr("href");
						var csv_report_download_url = jQuery(".csv-download-btn").attr("href");
						if( pdf_report_download_url.includes("transportation") && csv_report_download_url.includes("transportation") ) {
							var pdf_report_url_arr = pdf_report_download_url.split("transportation=");
							pdf_report_url_arr[1] = "delivery";
							var new_pdf_report_url = pdf_report_url_arr.join("transportation=");
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							
							var csv_report_url_arr = csv_report_download_url.split("transportation=");
							csv_report_url_arr[1] = "delivery";
							var new_csv_report_url = csv_report_url_arr.join("transportation=");
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						} else {
							var new_pdf_report_url = pdf_report_download_url+"&transportation=delivery";
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							var new_csv_report_url = csv_report_download_url+"&transportation=delivery";
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						}
					}
					if( delivery_type == "pickup" ) {
						jQuery('tr[data-delivery-type="Pickup"]').show();
						jQuery('tr[data-delivery-type="Delivery"]').hide();
						var pdf_report_download_url = jQuery(".pdf-download-btn").attr("href");
						var csv_report_download_url = jQuery(".csv-download-btn").attr("href");
						if( pdf_report_download_url.includes("transportation") && csv_report_download_url.includes("transportation") ) {
							var pdf_report_url_arr = pdf_report_download_url.split("transportation=");
							pdf_report_url_arr[1] = "pickup";
							var new_pdf_report_url = pdf_report_url_arr.join("transportation=");
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							
							var csv_report_url_arr = csv_report_download_url.split("transportation=");
							csv_report_url_arr[1] = "pickup";
							var new_csv_report_url = csv_report_url_arr.join("transportation=");
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						} else {
							var new_pdf_report_url = pdf_report_download_url+"&transportation=pickup";
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							var new_csv_report_url = csv_report_download_url+"&transportation=pickup";
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						}
					}
				});
			});
		</script>
	<?php
	}
	
	//if( "product_page_orders-delivery-for-the-week" == $screen->id ) {
		//if( isset($_GET["single_order"]) && isset($_GET["vmode"]) && $_GET["vmode"] == "print" ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(".flava-print-order").on("click", function() {
						window.frames["print_frame"].document.body.innerHTML = document.getElementById("order-print").innerHTML;
						window.frames["print_frame"].window.focus();
						window.frames["print_frame"].window.print();
					});
				});
			</script>
			<?php
		//}
	//}
	
	if( ($screen->id == "shop_order") || ($screen->id == "shop_subscription") ) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				/*if( jQuery("#order_line_items").length ) {
					jQuery(".item").each(function() {
						var item_name = jQuery(this).find(".wc-order-item-name").text();
						if( item_name.includes("Meals Plan") ) {
							jQuery('<button type="button" class="subs-customize button-primary">Customize</button>').appendTo( jQuery(this).find(".name").find(".display_meta").find("td") );
						}
					});
				}*/
				
				jQuery("body").on("click", ".subs-customize", function() {
					jQuery(this).closest(".name").find(".edit").show();
					jQuery(this).closest(".name").find(".edit").find( "input[value=_child_products]" ).hide();
					jQuery(this).closest(".name").find(".edit").find( ".remove_order_item_meta" ).hide();
					jQuery(this).closest(".name").find(".edit").find( ".add_order_item_meta" ).hide();
					jQuery(this).closest(".name").find(".edit").find( "textarea" ).css("min-height", "150px");
				});
				
				jQuery("#woocommerce-order-items").on("click", "a.edit-order-item", function() {
					if( jQuery(this).parents('.item').length ) {
						var item_title = jQuery(this).closest(".item").find(".name").attr("data-sort-value");
						if( item_title.includes("Meals Plan") ) {
							jQuery(this).closest(".item").find(".name").find(".edit").find( "input[value=_child_products]" ).hide();
							jQuery(this).closest(".item").find(".name").find(".edit").find( ".remove_order_item_meta" ).hide();
							jQuery(this).closest(".item").find(".name").find(".edit").find( ".add_order_item_meta" ).hide();
							jQuery(this).closest(".item").find(".name").find(".edit").find( "textarea" ).css("min-height", "130px");
						}
					}
				});
			});
		</script>
		<?php
	}
	
}
add_action('admin_head', 'flava_custom_admin_head');

/**
 * Disable messages about the mobile apps in WooCommerce emails.
 * https://wordpress.org/support/topic/remove-process-your-orders-on-the-go-get-the-app/
 */
function flava_disable_mobile_messaging( $mailer ) {
    remove_action( 'woocommerce_email_footer', array( $mailer->emails['WC_Email_New_Order'], 'mobile_messaging' ), 9 );
}
add_action('woocommerce_email', 'flava_disable_mobile_messaging');

function flava_remove_order_details( $order, $sent_to_admin, $plain_text, $email ){
    $mailer = WC()->mailer(); // get the instance of the WC_Emails class
    remove_action( 'woocommerce_email_order_details', array( $mailer, 'order_details' ), 10, 4 );
}
//add_action('woocommerce_email_order_details', 'flava_remove_order_details', 5, 4);

function flava_send_welcome_email_to_new_user($user_id) {
	$wc = new WC_Emails();
	$wc->customer_new_account($user_id);
}
add_action('user_register', 'flava_send_welcome_email_to_new_user');

//https://stackoverflow.com/questions/51894493/use-different-different-shipping-prices-based-on-cart-subtotal-in-woocommerce
add_filter('woocommerce_package_rates', 'flava_shipping_cost_based_on_price', 10, 2);
function flava_shipping_cost_based_on_price( $rates, $package ) {
    if( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return $rates;
	}

    // HERE define the differents costs
    $cost1 = 10; // From 85
    $cost2 = 5; // Above 140
	
    $cost3 = 500; // Cost for our specific product ID

    $min_subtotal = 85;
    $max_subtotal   = 500;


    // HERE DEFINE the specific product ID to be charged at 500
    $targeted_product_id = 37;

    // The cart subtotal
    $subtotal = WC()->cart->get_cart_contents_total();
	//$subtotal = WC()->cart->get_subtotal();
	
    // Loop through the shipping taxes array
    foreach( $rates as $rate_key => $rate ) {
        $has_taxes = false;

        // If subtotal is above 500 we enable free shipping only
        /*if( 'free_shipping' !== $rate->method_id && $subtotal >= $max_subtotal ) {
            unset($rates[$rate_key]);
        }*/
		
        // Targetting "flat rate" only for subtotal between 85-139.99
        if( 'flat_rate' === $rate->method_id ) {
            // Get the initial cost
            $initial_cost = $new_cost = $rates[$rate_key]->cost;

            // Calculate new cost
            if( $subtotal >= 140 ) { // From $140 and above
                $new_cost = $cost2;
            }
            elseif( ($subtotal >= 85) && ($subtotal < 140) ) { // Between $85 - $139.99
                $new_cost = $cost1;
            }
			
			$shipping_zone = wc_get_shipping_zone( $package );
			
			// Get the zone ID
			$zone_id = $shipping_zone->get_id();
			
			// check if it is in the SECONDARY Delivery Zone and add an additional $10
			if( $zone_id == 3 ) {
				$new_cost += 10;
			}
			
            // Set the new cost
			$rates[$rate_key]->cost = $new_cost;

            // Taxes rate cost (if enabled)
            $taxes = [];
            // Loop through the shipping taxes array (as they can be many)
            foreach ($rates[$rate_key]->taxes as $key => $tax){
                if( $rates[$rate_key]->taxes[$key] > 0 ) {
                    // Get the initial tax cost
                    $initial_tax_cost = $new_tax_cost = $rates[$rate_key]->taxes[$key];
                    // Get the tax rate conversion
                    $tax_rate = $initial_tax_cost / $initial_cost;
                    // Set the new tax cost
                    $taxes[$key] = $new_cost * $tax_rate;
                    $has_taxes = true; // Enabling tax
                }
            }
			
            if( $has_taxes ) {
                $rates[$rate_key]->taxes = $taxes;
			}
        }
    }
    return $rates;
}

function flava_is_valid_shipping() {
    $selected_shipping = WC()->session->get('chosen_shipping_methods');
    if( in_array(false, $selected_shipping) ) {
        wc_add_notice( __( 'Your Shipping is not valid.' ), 'error' );
    }
}
//add_action( 'woocommerce_checkout_process', 'flava_is_valid_shipping' );

add_action( 'woocommerce_after_checkout_form', 'flava_disable_shipping_local_pickup' );
function flava_disable_shipping_local_pickup( $available_gateways ) {
    
   // Part 1: Hide shipping based on the static choice @ Cart
   // Note: "#customer_details .col-2" strictly depends on your theme
 
   $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
   $chosen_shipping = $chosen_methods[0];
   if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {
   ?>
	<script type="text/javascript">
		//jQuery('#customer_details .col-2').fadeOut();
	</script>
   <?php  
   } 
 
   // Part 2: Hide shipping based on the dynamic choice @ Checkout
   // Note: "#customer_details .col-2" strictly depends on your theme
 
   ?>
	<script type="text/javascript">
		jQuery('form.checkout').on('change','input[name^="shipping_method"]',function() {
			var val = jQuery( this ).val();
			if (val.match("^local_pickup")) {
				jQuery("#ship-to-different-address").fadeOut();
				jQuery(".shipping_address").fadeOut();
				jQuery("#delivery_for_not_home_field").hide();
			} else {
				jQuery('#ship-to-different-address').fadeIn();
				jQuery('#ship-to-different-address-checkbox').prop('checked', false);
				jQuery("#delivery_for_not_home_field").show();
			}
		});
	</script>
   <?php
}

add_action('admin_menu', 'flava_register_weekly_report_submenu_page'); 
function flava_register_weekly_report_submenu_page() {
	add_submenu_page( 
		'edit.php?post_type=product',
        'Orders for the week',
        'View Weekly Orders',
        'manage_options',
        'orders-delivery-for-the-week',
        'orders_delivery_for_the_week_callback',
    );
    add_submenu_page( 
		'edit.php?post_type=product',
        'Orders for the week',
        'Orders Meal Type',
        'manage_options',
        'orders-meal-for-the-week',
        'orders_meal_for_the_week_callback',
    );
}

function orders_meal_for_the_week_callback() {
	$time_start = '17:00:00';
	$time_end   = '17:00:00';
	$prev_friday = date( 'Y-m-d', strtotime( 'previous friday' ) );
	$from_date = $prev_friday;
	$to_date = date("Y-m-d");
	$report_gen = false;
	$pdf_gen = false;
	$show_from_time = "05:00 pm";
	$show_to_time = "05:00 pm";

	$orders_list = '<div class="orders-list-table">';
	
	if( isset($_POST["order_date_range"]) || ( isset($_GET["sdate"]) && isset($_GET["edate"]) ) ) {
		if( isset($_POST["order_date_range"]) ) {
			$from_date_arr = explode(" ", $_POST["from_date"]);
			$from_date = $from_date_arr[0];
			$from_time = $from_date_arr[1];
			$time_start = $from_time.":00";
			$show_from_time = $from_time.' '.$from_date_arr[2];
			
			$to_date_arr = explode(" ", $_POST["to_date"]);
			$to_date = $to_date_arr[0];
			$to_time = $to_date_arr[1];
			$time_end = $to_time.":00";
			$show_to_time = $to_time.' '.$to_date_arr[2];
			
			$from_timestamp = strtotime($from_date);
			$show_from_date = date("M d, Y ", $from_timestamp);
			$to_timestamp = strtotime($to_date);
			$show_to_date = date("M d, Y ", $to_timestamp);
		}
		if( isset($_GET["sdate"]) && isset($_GET["edate"]) ) {
			$from_date_arr = explode(" ", $_GET["sdate"]);
			$from_date = $from_date_arr[0];
			$from_time = $from_date_arr[1];
			$time_start = $from_time.":00";
			$show_from_time = $from_time.' '.$from_date_arr[2];
			
			$to_date_arr = explode(" ", $_GET["edate"]);
			$to_date = $to_date_arr[0];
			$to_time = $to_date_arr[1];
			$time_end = $to_time.":00";
			$show_to_time = $to_time.' '.$to_date_arr[2];

			if( isset($_GET["or_ac"]) && $_GET["or_ac"] == "download" ) {
				$report_gen = true;
			}
			if( isset($_GET["or_dl_ac"]) && $_GET["or_dl_ac"] == "pdf" ) {
				$pdf_gen = true;
			}
			
			$from_timestamp = strtotime($from_date);
			$show_from_date = date("M d, Y ", $from_timestamp);
			$to_timestamp = strtotime($to_date);
			$show_to_date = date("M d, Y ", $to_timestamp);
		}		
	} else {
		$show_from_date = date("M d, Y ", strtotime( 'previous friday' ));
		$show_to_date = date("M d, Y ");
	}

	$q_time_start = date("H:i:s", strtotime($time_start));
	$q_time_end = date("H:i:s", strtotime($time_end));
	
	$start_date = date( $from_date.' '.$q_time_start );
	$end_date = date( $to_date.' '.$q_time_end );
	// 'status' => array( 'wc-processing','wc-completed','wc-on-hold' )
	$args = array(
		'orderby'       => 'id',
		'order'         => 'DESC',
		'posts_per_page' => -1,
		'status'        => array( 'wc-processing' ),
		'date_created'  => $start_date.'...'.$end_date
	);

	$orders = wc_get_orders( $args );

	if ( ! empty ( $orders ) ) {
		if( $report_gen ) {
			$data_rows = array();
		}

		$orders_list .= '<div style="display: flex; justify-content: space-between; align-items: center;"><div style="font-size: 14px;"><strong>From:</strong> '.$show_from_date.$show_from_time.' &nbsp;&nbsp;&nbsp; <strong>To:</strong> '.$show_to_date.$show_to_time.'</div>';
		//$orders_list .= '<div style="text-align: right; margin-bottom: 15px;"><a href="/wp-admin/edit.php?post_type=product&page=orders-for-the-week&or_ac=download&sdate='.date( "Y-m-d", strtotime($start_date) ).'&edate='.date( "Y-m-d", strtotime($end_date) ).'" class="button button-secondary button-large">Download CSV</a></div>';
		$orders_list .= '<div style="text-align: right; margin-bottom: 15px;"><a href="/wp-admin/edit.php?post_type=product&page=orders-meal-for-the-week&or_dl_ac=pdf&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large">Download PDF</a></div>';
		$orders_list .= '</div>';
		$orders_list .= '<br>';
		$orders_list .= '<table class="wp-list-table widefat fixed striped meal-type-table">';
		$orders_list .= '<thead>
								<tr>
									<td style="font-size: 15px;"><strong>Meal</strong></td>
									<td style="font-size: 15px;"><strong>Quantity</strong></td>
								</tr>
							</thead>';
		
		if( $pdf_gen ) {
			// create new PDF document
			$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('FlavaDaddy Team');
			$pdf->SetTitle('FlavaDaddy Team');
			$pdf->SetSubject('Order Info');
			$pdf->SetKeywords('Order, PDF, FlavaDaddy, Fully Prepared Meals');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
			// remove default header/footer
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set font
			$pdf->SetFont('helvetica', '', 10);
			
			// add a page
			$pdf->AddPage();
		}
		
		$meal_items = array();
		//$user_subs = '';
		foreach( $orders as $order ) {
			foreach($order->get_items() as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$product_name = $item->get_name();
				$quantity = $item->get_quantity();
				$item_key = $product_id;
				//$item_key = $product_name;
				
				/*if( $item->get_meta('_child_products', true) ) {
					$item_key_enc = md5($item_key);
					if( ! array_key_exists($item_key_enc, $meal_items) ) {
						$meal_items[$item_key_enc] = $product_name."||".$quantity;
					} else {
						$item_qty = explode("||", $meal_items[$item_key_enc])[1];
						$new_qty = (int) $item_qty + $quantity;
						$meal_items[$item_key_enc] = $product_name."||".$new_qty;
					}
					$child_products = $item->get_meta('_child_products', true);
					$child_products_arr = preg_split("/\r\n|\n|\r/", $child_products);
					foreach($child_products_arr as $child_product) {
						$cp_info_arr = explode(" X ", $child_product);
						if( count($cp_info_arr) > 1 ) {
							$cp_name = $cp_info_arr[0];
							$cp_qty = $cp_info_arr[1];
							//echo $cp_name.$cp_qty."??<br>";
							$item_key = html_entity_decode($cp_name);
							
							$item_key_enc = md5($item_key);
							if( ! array_key_exists($item_key_enc, $meal_items) ) {
								$meal_items[$item_key_enc] = $cp_name."||".$cp_qty;
							} else {
								$item_qty = explode("||", $meal_items[$item_key_enc])[1];
								$new_qty = (int) $item_qty + $cp_qty;
								$meal_items[$item_key_enc] = $cp_name."||".$new_qty;
							}
						}
					}
					
				/*} else {*/
				
					if( $item->get_meta('meal_extras', true) ) {
						$item_key .= $item->get_meta('meal_extras', true);
						$product_name .= " (".preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras', true)).")";
					}

					$item_key_enc = md5($item_key);
					if( ! array_key_exists($item_key_enc, $meal_items) ) {
						$meal_items[$item_key_enc] = $product_name."||".$quantity;
					} else {
						$item_qty = explode("||", $meal_items[$item_key_enc])[1];
						$new_qty = (int) $item_qty + $quantity;
						$meal_items[$item_key_enc] = $product_name."||".$new_qty;
					}
					
				/*}*/
				
				/*$product = wc_get_product( $product_id );
				if( $product->is_type( 'subscription' ) ) {
					
				}*/
			}
			/*Chili Lime Chicken Poke Bowl - x2
			Protein Ganache Rice Krispy (single) - x2
			Spicy Beef Noodz - x1
			Chimichurri Steak &#038; Potatoes - x1
			Teriyaki Turkey Bowl - x1
			Texas Smokey BBQ Chicken - x1*/
		}
		
		$pdf_table_rows = '';
		
		sort($meal_items);
		foreach($meal_items as $meal_item) {
			$meal_item_arr = explode("||", $meal_item);
			$orders_list .= '<tr>
								<td style="font-size: 15px;">'.$meal_item_arr[0].'</td>
								<td style="font-size: 15px;"><strong>'.$meal_item_arr[1].'</strong></td>
							</tr>';
			
			if( $pdf_gen ) {
				$pdf_table_rows .= '<tr>
										<td>'.$meal_item_arr[0].'</td>
										<td><strong>'.$meal_item_arr[1].'</strong></td>
									</tr>';
			}
			
			if( $report_gen ) {
				$row = array($meal_item_arr[0], $meal_item_arr[1]);
				$data_rows[] = $row;
			}
		}

		if( $pdf_gen ) {
			$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
						<tr>
							<td style="text-align: center;">
								<img src="'.home_url('/wp-content/uploads/2022/10/logo.png').'" />
								<br><br>
							</td>
						</tr>
						<tr>
							<td>
								<table border="1" cellpadding="10" cellspacing="0" style="width:100%;">
									<thead>
										<tr>
											<td style="font-size: 15px;"><strong>Meal</strong></td>
											<td style="font-size: 15px;"><strong>Quantity</strong></td>
										</tr>
									</thead>
									<tbody>
									'.$pdf_table_rows.'
									</tbody>
								</table>
							</td>
						</tr>
					</table>';
			
			$pdf->writeHTML($html, true, false, true, false, '');
			
			// reset pointer to the last page
			$pdf->lastPage();

			$upload_dir = wp_upload_dir();
			$pdf_dir = $upload_dir['basedir'].'/order-pdfs';
			$pdf_dir_url = $upload_dir['baseurl'].'/order-pdfs';
			if( ! file_exists( $pdf_dir ) ) {
				wp_mkdir_p( $pdf_dir );
			}

			// delete the current PDF if already exist
			if( file_exists($pdf_dir.'/Orders-meal-type-'.time().'.pdf') ) {
				unlink($pdf_dir.'/Orders-meal-type-'.time().'.pdf');
			}

			ob_end_clean();

			//Close and output PDF document
			$pdf->Output($pdf_dir.'/Orders-meal-type-'.time().'.pdf', 'D');
		}
		
		if( $report_gen ) {
			$domain = $_SERVER['SERVER_NAME'];
			$filename = 'orders-meals' . $domain . '-' . time() . '.csv';

			$header_columns = array('Meal','Quantity');
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename='.$filename);

			ob_end_clean();

			$fh = fopen( 'php://output', 'w' );

			fputcsv( $fh, $header_columns );

			foreach ( $data_rows as $data_row ) {
				fputcsv( $fh, $data_row );
			}

			exit();
		}

		$orders_list .= '</table>';
	}
	?>
	<div class="date-selection-row" style="padding-bottom: 0;">
		<h1 style="margin-bottom: 0;">Orders of this week</h1>
		<form action="" method="POST" style="margin-top: 20px;">
			<input type="text" name="from_date" class="reporting-date" placeholder="Start Date" value="<?php //echo $from_date; ?>" />
			<input type="text" name="to_date" class="reporting-date" placeholder="End Date" value="<?php //echo $to_date; ?>" />
			<br><br>
			<input type="submit" name="order_date_range" class="button button-primary button-large" value="Submit" />
		</form>
	</div>
	<?php
	$orders_list .= '</div>';
	echo $orders_list;
}

function orders_delivery_for_the_week_callback() {
	if( isset($_GET["single_order"]) && $_GET["single_order"] != "" ) {
		if( isset($_GET["vmode"]) && $_GET["vmode"] == "print" ) {
			$order_id = $_GET["single_order"];
			$order = wc_get_order($order_id);
			echo '<div id="order-print" style="display: inline-block; margin: 10px 0 0 0;"><div style="width: 384px; height: 576px; background: #ffffff; border: 1px solid #e6e6e6; padding: 15px 10px; font-size: 14px; line-height: 20px;">';
			$items_list = '';
			$plan_type = 'One Time';
			foreach ($order->get_items() as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$product = wc_get_product( $product_id );
				if ( $product->is_type( 'subscription' ) ) {
					$plan_type = 'Subscription';
				}
				
				$item_ext = '';
				if( $item->get_meta('meal_extras') ) {
					$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
					$item_ext = ' <span>('.$item_extra_info.')</span>';
				}
				$item_name_qty = '   '.$item->get_name().' x '.$item->get_quantity().strip_tags($item_ext);
				$item_name = $item->get_name();
				if( strlen($item_name_qty) > 52 ) {
					$total_chars = strlen($item_name_qty);
					$del_chars = $total_chars - 52;
					$del_chars_with_dots = $del_chars + 3; // 3 dots
					$item_name = substr($item->get_name(), 0, -$del_chars_with_dots)."...";
				}
				$items_list .= ' &nbsp; '.$item_name.'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
				if( $product->is_type( 'subscription' ) && $item->get_meta('_child_products', true) ) {
					$items_list .= '<span style="display: inline-block; margin-left: 15px; font-size: 98%;">'.nl2br( $item->get_meta('_child_products', true) ).'</span><br>';
				}
			}
			echo '<span>Plan Type: '.$plan_type.'</span><br>';
			echo '<span>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</span><br>';
			echo '<span>Name: '.$order->get_billing_first_name().' '.$order->get_billing_last_name().'</span><br><br>';
			echo '<span><strong>Address:</strong> <br>'.$order->get_formatted_shipping_address().'</span><br><br>';
			echo '<span><strong>Items:</strong> </span><br>';
			/*foreach ($order->get_items() as $item_id => $item ) {
				$item_ext = '';
				if( $item->get_meta('meal_extras') ) {
					$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
					$item_ext = ' <span>('.$item_extra_info.')</span>';
				}
				echo ' &nbsp; '.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
			}*/
			echo $items_list;
			if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
				echo '<br><span>Insulated bag: Yes</span><br>';
			}
			echo '</div></div><span class="dashicons dashicons-printer flava-print-order" style="margin: 15px 0 0 10px; cursor: pointer; font-size: 30px;"></span>';
			echo '<br><iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>';
		} else {
			$order_id = $_GET["single_order"];
			echo '<h2>Order #'.$order_id.' <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&single_order='.$order_id.'&dl_pdf=1" style="text-decoration: none;"><span class="dashicons dashicons-pdf" style="margin-left: 10px;"></span></a></h2>';
			echo '<hr />';

			$order = wc_get_order($order_id);

			echo '<p style="font-size: 15px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';
			//echo '<p style="font-size: 15px;"><strong>Phone:</strong> '.$order->get_billing_phone().'</p>';

			$order_items = '<p style="font-size: 15px;"><strong>Items:</strong><br>';
			foreach ($order->get_items() as $item_id => $item ) {
				$item_ext = '';
				if( $item->get_meta('meal_extras') ) {
					$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
					$item_ext = ' <span style="color: #c6731c;">('.$item_extra_info.')</span>';
				}
				$order_items .= ' &nbsp; '.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
			}
			echo $order_items.'</p>';

			//echo "<p>&nbsp;</p>";
			echo '<p style="font-size: 15px;"><strong>Time:</strong><br>';
			echo get_post_meta( $order->get_id(), '_delivery_option',  true )."</p>";

			//echo "<p>&nbsp;</p>";
			echo '<p style="font-size: 15px;"><strong>Address:</strong><br>';
			echo $order->get_formatted_shipping_address()."</p>";

			if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
				echo '<p style="font-size: 15px;">Insulated Bag with Ice: <strong>Yes</strong></p>';
			}

			if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
				echo '<p style="font-size: 15px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
			}

			//$customer_note = $order->get_customer_note();
			if( $order->get_customer_note() ) {
				echo "<strong>Note:</strong> ".$order->get_customer_note();
			}
		}
	} else {
		$time_start = '17:00:00';
		$time_end   = '17:00:00';
		$prev_friday = date( 'Y-m-d', strtotime( 'previous friday' ) );
		$from_date = $prev_friday;
		$to_date = date("Y-m-d");
		$report_gen = false;
		$xcel_gen = false;
		$pdf_gen = false;
		$show_from_time = "05:00 pm";
		$show_to_time = "05:00 pm";
		$ord_type = false;
		$trns_type = false;

		$orders_list = '<div class="orders-list-table">';

		if( isset($_GET["transportation"]) && $_GET["transportation"] != "" ) {
			$ord_type = $_GET["transportation"];
		}
		
		if( isset($_POST["order_date_range"]) || ( isset($_GET["sdate"]) && isset($_GET["edate"]) ) ) {
			if( isset($_POST["order_date_range"]) ) {
				$from_date_arr = explode(" ", $_POST["from_date"]);
				$from_date = $from_date_arr[0];
				$from_time = $from_date_arr[1];
				$time_start = $from_time.":00";
				$show_from_time = $from_time.' '.$from_date_arr[2];

				$to_date_arr = explode(" ", $_POST["to_date"]);
				$to_date = $to_date_arr[0];
				$to_time = $to_date_arr[1];
				$time_end = $to_time.":00";
				$show_to_time = $to_time.' '.$to_date_arr[2];

				$from_timestamp = strtotime($from_date);
				$show_from_date = date("M d, Y ", $from_timestamp);
				$to_timestamp = strtotime($to_date);
				$show_to_date = date("M d, Y ", $to_timestamp);
			}
			if( isset($_GET["sdate"]) && isset($_GET["edate"]) ) {
				$from_date_arr = explode(" ", $_GET["sdate"]);
				$from_date = $from_date_arr[0];
				$from_time = $from_date_arr[1];
				$time_start = $from_time.":00";
				$show_from_time = $from_time.' '.$from_date_arr[2];

				$to_date_arr = explode(" ", $_GET["edate"]);
				$to_date = $to_date_arr[0];
				$to_time = $to_date_arr[1];
				$time_end = $to_time.":00";
				$show_to_time = $to_time.' '.$to_date_arr[2];

				if( isset($_GET["or_ac"]) && $_GET["or_ac"] == "download" ) {
					$report_gen = true;
				}
				/*if( isset($_GET["wtdo"]) && $_GET["wtdo"] == "exportdel" ) {
					$xcel_gen = true;
				}*/
				if( isset($_GET["or_dl_ac"]) && $_GET["or_dl_ac"] == "pdf" ) {
					$pdf_gen = true;
				}

				$from_timestamp = strtotime($from_date);
				$show_from_date = date("M d, Y ", $from_timestamp);
				$to_timestamp = strtotime($to_date);
				$show_to_date = date("M d, Y ", $to_timestamp);
			}		
		} else {
			$show_from_date = date("M d, Y ", strtotime( 'previous friday' ));
			$show_to_date = date("M d, Y ");
		}
		
		if( isset($_GET["wtdo"]) && $_GET["wtdo"] == "exportdel" ) {
			$xcel_gen = true;
			$trns_type = "delivery";
		}

		$q_time_start = date("H:i:s", strtotime($time_start));
		$q_time_end = date("H:i:s", strtotime($time_end));

		$start_date = date( $from_date.' '.$q_time_start );
		$end_date = date( $to_date.' '.$q_time_end );
		$args = array(
			'orderby'       => 'id',
			'order'         => 'DESC',
			'posts_per_page' => -1,
			'status'        => array( 'wc-processing' ),
			'date_created'  => $start_date.'...'.$end_date
		);

		$orders = wc_get_orders( $args );

		if ( ! empty ( $orders ) ) {
			if( $report_gen || $xcel_gen ) {
				$data_rows = array();
			}
			
			if( isset($_GET["vmode"]) && $_GET["vmode"] == "print" ) {
				echo '<div id="order-print" style="display: inline-block; margin: 10px 0 0 0;">';
				foreach ( $orders as $order ) {
					//if( 'delivery' == strtolower(get_post_meta( $order->get_id(), '_order_type',  true )) ) {
						echo '<div style="width: 384px; height: 576px; background: #ffffff; border: 1px solid #e6e6e6; padding: 15px 10px; font-size: 14px; line-height: 20px;">';
						$items_list = '';
						$plan_type = 'One Time';
						foreach ($order->get_items() as $item_id => $item ) {
							$product_id = $item->get_product_id();
							$product = wc_get_product( $product_id );
							if ( $product->is_type( 'subscription' ) ) {
								$plan_type = 'Subscription';
							}

							$item_ext = '';
							if( $item->get_meta('meal_extras') ) {
								$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
								$item_ext = ' <span>('.$item_extra_info.')</span>';
							}
							$item_name_qty = '   '.$item->get_name().' x '.$item->get_quantity().strip_tags($item_ext);
							$item_name = $item->get_name();
							if( strlen($item_name_qty) > 52 ) {
								$total_chars = strlen($item_name_qty);
								$del_chars = $total_chars - 52;
								$del_chars_with_dots = $del_chars + 3; // 3 dots
								$item_name = substr($item->get_name(), 0, -$del_chars_with_dots)."...";
							}
							$items_list .= ' &nbsp; '.$item_name.'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
							if( $product->is_type( 'subscription' ) && $item->get_meta('_child_products', true) ) {
								$items_list .= '<span style="display: inline-block; margin-left: 15px; font-size: 98%;">'.nl2br( $item->get_meta('_child_products', true) ).'</span><br>';
							}
						}
						echo '<span>Plan Type: '.$plan_type.'</span><br>';
						echo '<span>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</span><br>';
						echo '<span>Name: '.$order->get_billing_first_name().' '.$order->get_billing_last_name().'</span><br><br>';
						echo '<span><strong>Address:</strong> <br>'.$order->get_formatted_shipping_address().'</span><br><br>';
						echo '<span><strong>Items:</strong> </span><br>';
						/*foreach ($order->get_items() as $item_id => $item ) {
							$item_ext = '';
							if( $item->get_meta('meal_extras') ) {
								$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
								$item_ext = ' <span>('.$item_extra_info.')</span>';
							}
							echo ' &nbsp; '.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
						}*/

						echo $items_list;
						if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
							echo '<br><span>Insulated bag: Yes</span><br>';
						}

						echo '</div>';
					/*} else {
						continue;
					}*/
				}
				echo '</div><span class="dashicons dashicons-printer flava-print-order" style="margin: 15px 0 0 10px; cursor: pointer; font-size: 30px;"></span>';
				echo '<br><iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>';
			} else {

				$orders_list .= '<div style="display: flex; justify-content: space-between; align-items: center;"><div style="font-size: 14px;"><strong>From:</strong> '.$show_from_date.$show_from_time.' &nbsp;&nbsp;&nbsp; <strong>To:</strong> '.$show_to_date.$show_to_time.'</div>';
				$orders_list .= '<div style="text-align: right; margin-bottom: 5px;"><a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&vmode=print&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large csv-export-btn">Print Deliveries</a> <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&wtdo=exportdel&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large csv-export-btn">Export Deliveries</a> <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&or_ac=download&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large csv-download-btn">Download CSV</a> <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&or_dl_ac=pdf&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large pdf-download-btn">Download PDF</a></div></div>';

				$orders_list .= '<div class="filter-orders-type"><a href="#" class="active-sec" data-order-type="all">All</a> <a href="#" data-order-type="delivery">Delivery</a> <a href="#" data-order-type="pickup">Pickup</a></div>';

				$orders_list .= '<table class="wp-list-table widefat fixed striped table-view-list posts">';
				$orders_list .= '<thead>
										<tr>
											<td><strong>Order</strong></td>
											<td><strong>Date</strong></td>
											<td width="300px"><strong>Items</strong></td>
											<td><strong>Insulated Bag</strong></td>
											<td><strong>Status</strong></td>
											<td><strong>Type</strong></td>
											<td><strong>Time</strong></td>
											<td><strong>Address</strong></td>
											<td><strong>Total</strong></td>
										</tr>
									</thead>';

				if( $pdf_gen ) {
					// create new PDF document
					$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

					// set document information
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('FlavaDaddy Team');
					$pdf->SetTitle('FlavaDaddy Team');
					$pdf->SetSubject('Order Info');
					$pdf->SetKeywords('Order, PDF, FlavaDaddy, Fully Prepared Meals');

					// set default header data
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);

					// set header and footer fonts
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					// remove default header/footer
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);

					// set default monospaced font
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

					// set margins
					$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

					// set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

					// set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

					// set font
					$pdf->SetFont('helvetica', '', 10);
				}

				foreach ( $orders as $order ) {
					$order_date = date( "M d, Y", strtotime( $order->get_date_created() ) );

					$insulated_bag = 'No';
					if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
						$insulated_bag = 'Yes';
					}

					$order_items = '';
					foreach ($order->get_items() as $item_id => $item ) {
						$item_ext = '';
						if( $item->get_meta('meal_extras') ) {
							$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
							$item_ext = ' <span style="color: #c6731c;">('.$item_extra_info.')</span>';
						}
						$order_items .= ''.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
					}

					$orders_list .= '<tr data-delivery-type="'.get_post_meta( $order->get_id(), '_order_type',  true ).'">
											<td><a href="/wp-admin/post.php?post='.$order->get_id().'&action=edit" target="_blank"><strong>#'.$order->get_id().' '.$order->get_billing_first_name().'</strong></a><br><a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&single_order='.$order->get_id().'" target="_blank">View</a> | <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&single_order='.$order->get_id().'&vmode=print" target="_blank">Print</a></td>
											<td>'.$order_date.'</td>
											<td>'.$order_items.'</td>
											<td>'.$insulated_bag.'</td>
											<td class="order_status"><span class="order-status status-processing">'.ucwords($order->get_status()).'</span></td>
											<td>'.get_post_meta( $order->get_id(), '_order_type',  true ).'</td>
											<td>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</td>
											<td>'.$order->get_formatted_shipping_address().'</td>
											<td>$'.$order->get_total().'</td>
										</tr>';

					// to be removed
					//echo "+".$ord_type."-".strtolower(get_post_meta( $order->get_id(), '_order_type',  true ))."+ &nbsp;&nbsp;";

					if( $pdf_gen ) {					
						if( ! $ord_type ) {
							// add a page
							$pdf->AddPage();

							$pdf_body = '<h2>Order #'.$order->get_id().'</h2>';
							//$order = wc_get_order($order_id);

							$pdf_body .= '<p style="font-size: 14px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';

							$pdf_body .= '<p style="font-size: 14px;"><strong>Items:</strong><br>';
							foreach ($order->get_items() as $order_item_id => $order_item ) {
								$pdf_item_ext = '';
								if( $order_item->get_meta('meal_extras') ) {
									$order_item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $order_item->get_meta('meal_extras'));
									$pdf_item_ext = ' <span style="color: #c6731c;">('.$order_item_extra_info.')</span>';
								}
								$pdf_body .= ' &nbsp; '.$order_item->get_name().'<strong> x '.$order_item->get_quantity().'</strong>'.$pdf_item_ext.'<br>';
							}
							$pdf_body .= '</p>';

							$pdf_body .= '<p style="font-size: 14px;"><strong>Time:</strong><br>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</p>';

							$pdf_body .= '<p style="font-size: 14px;"><strong>Address:</strong><br>'.$order->get_formatted_shipping_address().'</p>';

							if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
								$pdf_body .= '<p style="font-size: 14px;">Insulated Bag with Ice: <strong>Yes</strong></p>';
							}

							if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
								$pdf_body .= '<p style="font-size: 14px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
							}

							if( $order->get_customer_note() ) {
								$pdf_body .= '<p style="font-size: 14px;"><strong>Note:</strong> '. $order->get_customer_note() . '</p>';
							}

							$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
										<tr>
											<td style="text-align: center;">
												<img src="'.home_url('/wp-content/uploads/2022/10/logo.png').'" />
												<br><br>
											</td>
										</tr>
										<tr>
											<td>
												'.$pdf_body.'
											</td>
										</tr>
									</table>';

							$pdf->writeHTML($html, true, false, true, false, '');						
						} else {
							if( $ord_type == strtolower(get_post_meta( $order->get_id(), '_order_type',  true )) ) {
								// add a page
								$pdf->AddPage();

								$pdf_body = '<h2>Order #'.$order->get_id().'</h2>';
								//$order = wc_get_order($order_id);

								$pdf_body .= '<p style="font-size: 14px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Items:</strong><br>';
								foreach ($order->get_items() as $order_item_id => $order_item ) {
									$pdf_item_ext = '';
									if( $order_item->get_meta('meal_extras') ) {
										$order_item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $order_item->get_meta('meal_extras'));
										$pdf_item_ext = ' <span style="color: #c6731c;">('.$order_item_extra_info.')</span>';
									}
									$pdf_body .= ' &nbsp; '.$order_item->get_name().'<strong> x '.$order_item->get_quantity().'</strong>'.$pdf_item_ext.'<br>';
								}
								$pdf_body .= '</p>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Time:</strong><br>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</p>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Address:</strong><br>'.$order->get_formatted_shipping_address().'</p>';

								if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
									$pdf_body .= '<p style="font-size: 14px;">Insulated Bag with Ice: <strong>Yes</strong></p>';
								}

								if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
									$pdf_body .= '<p style="font-size: 14px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
								}

								if( $order->get_customer_note() ) {
									$pdf_body .= '<p style="font-size: 14px;"><strong>Note:</strong> '. $order->get_customer_note() . '</p>';
								}

								$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
											<tr>
												<td style="text-align: center;">
													<img src="'.home_url('/wp-content/uploads/2022/10/logo.png').'" />
													<br><br>
												</td>
											</tr>
											<tr>
												<td>
													'.$pdf_body.'
												</td>
											</tr>
										</table>';

								$pdf->writeHTML($html, true, false, true, false, '');
							}
						}
					}

					if( $report_gen ) {
						$row = array(
							'#'.$order->get_id(),
							$order->get_billing_first_name(),
							$order_date,
							wp_strip_all_tags(str_replace("<br>", "\n", $order_items)),
							ucwords($order->get_status()),
							get_post_meta( $order->get_id(), '_order_type',  true ),
							get_post_meta( $order->get_id(), '_delivery_option',  true ),
							str_replace("<br/>","\n",$order->get_formatted_shipping_address()),
							'$'.$order->get_total()
						);
						$data_rows[] = $row;
					}

					if( $xcel_gen ) {
						//if( $trns_type == strtolower(get_post_meta( $order->get_id(), '_order_type',  true )) ) {
							$row = array(
								$order->get_billing_first_name().' '.$order->get_billing_last_name(),
								str_replace("<br/>","\n",$order->get_formatted_shipping_address()),
								$order->get_shipping_address_1()."\n".$order->get_shipping_address_2(),
								$order->get_shipping_city(),
								$order->get_shipping_state(),
								$order->get_shipping_postcode(),
								$order->get_billing_phone(),
								$order->get_customer_note(),
								get_post_meta( $order->get_id(), '_order_type',  true ),
								get_post_meta( $order->get_id(), '_delivery_option',  true )
							);
							$data_rows[] = $row;
						//}
					}
				}

				if( $pdf_gen ) {
					// reset pointer to the last page
					$pdf->lastPage();

					$upload_dir = wp_upload_dir();
					$pdf_dir = $upload_dir['basedir'].'/order-pdfs';
					$pdf_dir_url = $upload_dir['baseurl'].'/order-pdfs';
					if( ! file_exists( $pdf_dir ) ) {
						wp_mkdir_p( $pdf_dir );
					}

					// delete the current PDF if already exist
					if( file_exists($pdf_dir.'/Orders-'.time().'.pdf') ) {
						unlink($pdf_dir.'/Orders-'.time().'.pdf');
					}

					ob_end_clean();

					//Close and output PDF document
					$pdf->Output($pdf_dir.'/Orders-'.time().'.pdf', 'D');
				}

				if( $report_gen ) {
					$domain = $_SERVER['SERVER_NAME'];
					$filename = 'orders-' . $domain . '-' . time() . '.csv';

					$header_columns = array('Order','Name','Date','Items','Status','Type','Time','Address','Total');
					header('Content-Type: application/csv');
					header('Content-Disposition: attachment; filename='.$filename);

					ob_end_clean();

					$fh = fopen( 'php://output', 'w' );

					fputcsv( $fh, $header_columns );

					foreach ( $data_rows as $data_row ) {
						fputcsv( $fh, $data_row );
					}

					exit();
				}

				if( $xcel_gen ) {
					//$domain = $_SERVER['SERVER_NAME'];
					$filename = 'Customer Input Template Complete.csv';

					$header_columns = array('Full Name','Address','Apt or Suite # and BUZZ #','City','Province','Postal Code','Phone #','Notes','Delivery Method','Time');
					header('Content-Type: application/csv');
					header('Content-Disposition: attachment; filename='.$filename);

					ob_end_clean();

					$fh = fopen( 'php://output', 'w' );

					fputcsv( $fh, $header_columns );

					foreach ( $data_rows as $data_row ) {
						fputcsv( $fh, $data_row );
					}

					exit();
				}

				$orders_list .= '</table>';
				
			}
		}
		
		if( !isset($_GET["vmode"]) ) {
		?>
		<div class="date-selection-row" style="padding-bottom: 0;">
			<h1 style="margin-bottom: 0;">Orders of this week</h1>
			<form action="" method="POST" style="margin-top: 20px;">
				<input type="text" name="from_date" class="reporting-date" placeholder="Start Date" value="<?php //echo $from_date; ?>" />
				<input type="text" name="to_date" class="reporting-date" placeholder="End Date" value="<?php //echo $to_date; ?>" />
				<br><br>
				<input type="submit" name="order_date_range" class="button button-primary button-large" value="Submit" />
			</form>
		</div>
		<?php
		}
		
		$orders_list .= '</div>';
		echo $orders_list;
	}
}

// Include the main TCPDF library
require_once('tcpdf/tcpdf.php');

/**
  * Extend TCPDF to work with custom header
  */
class MC_TCPDF extends TCPDF {
	//Page header
	public function Header() {
		// Logo
		$image_file = home_url('/wp-content/uploads/2022/10/logo.png'); //K_PATH_IMAGES.'logo_example.jpg'
		$this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 20);
		// Line break
		$this->Ln();
		// Title
		$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}
}

function flava_generate_order_pdf($order_id, $download = false) {
	if( isset($_GET["single_order"]) && isset($_GET["dl_pdf"]) && $_GET["dl_pdf"] == "1" ) {
		$order_id = $_GET["single_order"];
		
		// create new PDF document
		$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('FlavaDaddy Team');
		$pdf->SetTitle('FlavaDaddy Team');
		$pdf->SetSubject('Order Info');
		$pdf->SetKeywords('Order, PDF, FlavaDaddy, Fully Prepared Meals');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set font
		$pdf->SetFont('helvetica', '', 10);

		// add a page
		$pdf->AddPage();

		$pdf_body = '<h2>Order #'.$order_id.'</h2>';
		$order = wc_get_order($order_id);

		$pdf_body .= '<p style="font-size: 14px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';

		$pdf_body .= '<p style="font-size: 14px;"><strong>Items:</strong><br>';
		foreach ($order->get_items() as $item_id => $item ) {
			$item_ext = '';
			if( $item->get_meta('meal_extras') ) {
				$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
				$item_ext = ' <span style="color: #c6731c;">('.$item_extra_info.')</span>';
			}
			$pdf_body .= ' &nbsp; '.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
		}
		$pdf_body .= '</p>';

		$pdf_body .= '<p style="font-size: 14px;"><strong>Time:</strong><br>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</p>';

		$pdf_body .= '<p style="font-size: 14px;"><strong>Address:</strong><br>'.$order->get_formatted_shipping_address().'</p>';

		if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
			$pdf_body .= '<p style="font-size: 14px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
		}

		//$customer_note = $order->get_customer_note();
		if( $order->get_customer_note() ) {
			$pdf_body .= '<p style="font-size: 14px;"><strong>Note:</strong> '. $order->get_customer_note() . '</p>';
		}

		$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
					<tr>
						<td style="text-align: center;">
							<img src="'.home_url('/wp-content/uploads/2022/10/logo.png').'" />
							<br><br>
						</td>
					</tr>
					<tr>
						<td>
							'.$pdf_body.'
						</td>
					</tr>
				</table>';

		$pdf->writeHTML($html, true, false, true, false, '');

		// reset pointer to the last page
		$pdf->lastPage();

		$upload_dir = wp_upload_dir();
		$pdf_dir = $upload_dir['basedir'].'/order-pdfs';
		$pdf_dir_url = $upload_dir['baseurl'].'/order-pdfs';
		if( ! file_exists( $pdf_dir ) ) {
			wp_mkdir_p( $pdf_dir );
		}

		// delete the current PDF if already exist
		if( file_exists($pdf_dir.'/Order-Details-'.$order_id.'.pdf') ) {
			unlink($pdf_dir.'/Order-Details-'.$order_id.'.pdf');
		}

		ob_end_clean();
		
		//Close and output PDF document
		$pdf->Output($pdf_dir.'/Order-Details-'.$order_id.'.pdf', 'D');
	}
}
add_action("admin_init", "flava_generate_order_pdf");

function flava_admin_scripts( $hook_suffix ) {
	$screen = get_current_screen();
	
    if ( $hook_suffix === $screen->id ) {
		wp_register_script( "jquery-ui-timepicker", '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-datepicker' ) );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		
		wp_register_style( 'jquery-ui-timepicker-style', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css' );
		wp_enqueue_style( 'jquery-ui-timepicker-style' );
		
		wp_enqueue_script( 'jquery-ui-datepicker' );
		
		wp_register_style( 'jquery-ui-datepicker-style', '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui-datepicker-style' );
    }
}
add_action( 'admin_enqueue_scripts', 'flava_admin_scripts' );

function flava_disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
	
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'flava_disable_shipping_calc_on_cart', 99 );

function flava_general_section() {  
    add_settings_section(  
        'flava_options_section',
        'Top bar options',
        'flava_section_options_callback',
        'general'
    );
    
	add_settings_field(
        'top_bar_enable',
        'Top bar enable',
        'flava_topbar_enable_callback',
        'general',
        'flava_options_section',
		array( 
			'label_for' => 'top_bar_enable'
		)
    );
	
    add_settings_field(
        'top_bar_msg',
        'Top bar text',
        'flava_textbox_callback',
        'general',
        'flava_options_section'
    );
	
	add_settings_field(
        'sold_out_enable',
        'Sold out enable',
        'flava_sold_out_enable_callback',
        'general',
        'flava_options_section'
    );
    
    register_setting('general','top_bar_msg', 'esc_attr');
    register_setting('general','top_bar_enable', 'esc_attr');
	register_setting('general','sold_out_enable', 'esc_attr');
}
add_action('admin_init', 'flava_general_section');

function flava_section_options_callback() {
    //echo '<p>A little message on editing info</p>';  
}

function flava_textbox_callback( $args ) {
    $option = get_option('top_bar_msg');
	$content = isset( $option ) ?  htmlspecialchars_decode($option) : false;
    wp_editor( $content, 'top_bar_msg', array( 
        'textarea_name' => 'top_bar_msg',
        'media_buttons' => false,
		'textarea_rows' => 3
    ) );
}

function flava_topbar_enable_callback( $args ) {
    $checked = '';
    $option = get_option('top_bar_enable');
    $value   = ( empty( $option ) ) ? 0 : $option;
    if($value) { $checked = ' checked="checked" '; }
        $html  = '';
        $html .= '<input id="top_bar_enable" name="top_bar_enable" type="checkbox" value="1" ' . $checked . '/>';
        $html .= '<label for="top_bar_enable">Check to enable top bar.</label>';

        echo $html;
}

function flava_sold_out_enable_callback( $args ) {
    $checked = '';
    $option = get_option('sold_out_enable');
    $value   = ( empty( $option ) ) ? 0 : $option;
    if($value) { $checked = ' checked="checked" '; }
        $html  = '';
        $html .= '<input id="sold_out_enable" name="sold_out_enable" type="checkbox" value="1" ' . $checked . '/>';
        $html .= '<label for="sold_out_enable">Check to enable the sold out page in case you\'re sold out and don\'t want to take any more orders for the week.</label>';

        echo $html;
}

add_filter('woocommerce_thankyou_order_received_text', 'flava_custom_order_thanks_msg');
function flava_custom_order_thanks_msg ( $thank_you_msg ) {
	$thank_you_msg = 'Your order has been completed. Thank You! We\'ve just emailed you a receipt. You may need to check your junk mail.';
	return $thank_you_msg;
}

add_filter('wpmenucart_menu_item_a_content', 'flava_custom_cart_content_items', 10, 4);
function flava_custom_cart_content_items($menu_item_a_content, $menu_item_icon, $cart_contents, $item_data) {
	$menu_item_count = ($item_data['cart_contents_count']) ? $item_data['cart_contents_count'] : 0;
	$menu_item_a_content .= '<span class="cartcontents items-counter">'.$menu_item_count.'</span>';
	return $menu_item_a_content;
}

add_filter('wpmenucart_emptytitle', function($start_shopping) {
	return '';
});

add_filter('wpmenucart_fulltitle', function($viewing_cart) {
	return '';
});

add_filter('wpmenucart_emptyurl', function($page_url) {
	return wc_get_cart_url();
});

function flava_woocommerce_order_status_completed( $order_id ) {
    $today = date("Y-m-d");
	$follow_up_date = date('Y-m-d', strtotime($today. ' + 3 days'));
	update_post_meta( $order_id, '_follow_up_email_date', $follow_up_date );
}
add_action( 'woocommerce_order_status_completed', 'flava_woocommerce_order_status_completed', 10, 1 );

/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
add_action( 'wp', 'flava_setup_schedule' );
function flava_setup_schedule() {
	if ( ! wp_next_scheduled( 'flava_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'flava_daily_event');
	}
}

/**
 * On the scheduled action hook, run a function.
 */
add_action( 'flava_daily_event', 'flava_do_this_daily' );
function flava_do_this_daily() {
	// do something everyday
	flava_generate_daily_email();
}

function flava_generate_daily_email() {
	$scheduled_orders = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_follow_up_email_date',
		'meta_value'  => date("Y-m-d"),
		'post_type'   => 'shop_order',
		'post_status' => array('wc-completed'),
		'fields'      => 'ids', // Return ids
	) );
	
	if( count($scheduled_orders) > 0 ) {
		foreach($scheduled_orders as $order_id) {
			$order = new WC_Order( $order_id );
			$customer_email = '';
			//$customer_name = '';
			$first_order = false;
			if( $order->get_user() ) {
				$customer = $order->get_user();
				$customer_email = $customer->user_email;
				//$customer_name = $customer->display_name;
				$customer_id = $customer->ID;
				
				if( !flava_customer_has_bought( $customer_id ) ) {
					$first_order = true;
				}
			} else {
				$customer_email = $order->get_billing_email();
				//$customer_name = $order->get_billing_first_name();
				
				if( !flava_customer_has_bought( $customer_email ) ) {
					$first_order = true;
				}
			}
			
			$subject = "Can You Give Us a Review?";
			
			/*$message = '<p>Hi '.$customer_name.',</p>';
			$message .= '<p>Thank you for your order.</p>';
			$message .= '<p>If you are happy with our service, please take a minute to review it on Google</p>';
			$message .= '<p>– The FlavaDaddy Team</p>';*/
			
			if( $first_order ) {
				// load the mailer class
				$mailer = WC()->mailer();

				$content = flava_get_custom_email_html( $order, $subject, $mailer );
				$headers = "Content-Type: text/html\r\n";

				//send the email through wordpress
				$mailer->send( $customer_email, $subject, $content, $headers );
			}
		}
	}
}

function flava_get_custom_email_html( $order, $heading = false, $mailer ) {

	$template = 'emails/customer-follow-up-email.php';

	return wc_get_template_html( $template, array(
		'order'         => $order,
		'email_heading' => $heading,
		'sent_to_admin' => false,
		'plain_text'    => false,
		'email'         => $mailer
	) );

}

function flava_custom_retry_rule( $rule, $retry_number, $order_id ) {
	// https://flavadaddy.com/wp-admin/post.php?post=5448&action=edit
	if( 5448 == $order_id ) {
		$subscription = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'renewal' ) );

		if ( ! empty( $subscription ) && 'week' === $subscription->billing_period ) {

			$existing_rule_raw = $rule->get_raw_data();

			if ( ! empty( $existing_rule_raw['retry_after_interval'] ) ) {
				$existing_rule_raw['retry_after_interval'] = WEEK_IN_SECONDS;
				$rule = new EG_Retry_Rule( $rule->get_rule_raw() );
			}
		}
	}

    return $rule;
}
//add_filter( 'wcs_get_retry_rule', 'flava_custom_retry_rule', 10, 3 );

add_action("woocommerce_before_cart", "flava_insufficient_amount_error");
function flava_insufficient_amount_error() {
	if( isset($_GET["errmsg"]) ) {
		echo '<div style="color: #f00; text-align: center; margin-bottom: 20px; font-weight: 500;">The order minimum is $88</div>';
	}
}

add_action("woocommerce_before_cart", "flava_show_return_buttons");
add_action("woocommerce_before_checkout_form", "flava_show_return_buttons");
function flava_show_return_buttons() {
	/*$has_subs = false;
	$is_reg = false;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$product_id = $cart_item['product_id'];
		$product = wc_get_product( $product_id );
		if( $product->is_type( 'subscription' ) ) {
			$has_subs = true;
			//break;
		}
		
		if( $product->is_type('simple') ) {
			$is_reg = true;
			//break;
		}
	}*/
	?>
	<div class="return-btns-wrap">
		<?php //if( $is_reg ) { ?>
		<a href="/one-time-order/" class="flava-return-btn">Return to One Time Meals</a>
		<?php //} ?>
		<?php //if( $has_subs ) { ?>
		<a href="/weekly-subscription/" class="flava-return-btn">Return to Subscription Meals</a>
		<?php //} ?>
	</div>
	<?php
}

function flava_customer_has_bought( $value = 0 ) {
    if ( ! is_user_logged_in() && $value === 0 ) {
        return false;
    }

    global $wpdb;
    
    // Based on user ID (registered users)
    if ( is_numeric( $value) ) { 
        $meta_key   = '_customer_user';
        $meta_value = $value == 0 ? (int) get_current_user_id() : (int) $value;
    } 
    // Based on billing email (Guest users)
    else { 
        $meta_key   = '_billing_email';
        $meta_value = sanitize_email( $value );
    }
    
    //$paid_order_statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );

    $customer_orders = $wpdb->get_var( $wpdb->prepare("
        SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts AS p
        INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
        WHERE p.post_status IN ( 'wc-completed','wc-processing' )
        AND p.post_type LIKE 'shop_order'
        AND pm.meta_key = '%s'
        AND pm.meta_value = %s
        LIMIT 2
    ", $meta_key, $meta_value ) );

    // Return a boolean value based on orders count
    //return $customer_orders > 0;
    //return count($customer_orders) > 0 ? true : false;
	return count($customer_orders) > 1 ? true : false;
}

//add_action("woocommerce_order_item_meta_start", "show_subscription_item_meta", 10, 4);
function show_subscription_item_meta( $item_id, $item, $order, $plain_text ) {
	$product = $item->get_product();
	if( $product->is_type( 'subscription' ) ) {
		echo '<br><span style="display: inline-block; margin-left: 8px; font-size: 95%;">'.nl2br(wc_get_order_item_meta( $item_id , '_child_products', true )).'</span>';
	}
}

//add_action("woocommerce_new_order_item", "flava_auto_assign_meals_for_the_week", 10, 3);
function flava_auto_assign_meals_for_the_week($item_id, $item, $order_id) {
	if( is_admin() && current_user_can( 'manage_options' ) ) {
		$product_id = $item->get_product_id();
		$li_product = wc_get_product( $product_id );
		if( $li_product->is_type( 'subscription' ) ) {
			$item_meta = "";

			$args = array(
				'featured' => true,
				'limit' => 5,
				'exclude_category' => 'dessert'
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

					$child_products .= $meal_title." X ".$cp_qty."\n";
				}

				$item_meta = $child_products;
				wc_add_order_item_meta($item_id, '_child_products', $item_meta);
			}
		}
	}
}

//add_filter('woocommerce_product_data_store_cpt_get_products_query', 'flava_exclude_cat_query', 10, 2);
function flava_exclude_cat_query($query, $query_vars) {
    if( !empty($query_vars['exclude_category']) ) {
        $query['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $query_vars['exclude_category'],
            'operator' => 'NOT IN',
        );
    }
    return $query;
}

add_action("init", "flava_update_order_specific_meta");
function flava_update_order_specific_meta() {
 	if( isset($_GET["ord_id"]) && $_GET["ord_id"] != "" ) {
 		$order_id = $_GET["ord_id"];
// 		//update_post_meta( $order_id, '_delivery_for_not_home', "At the Front Door" );
 		//update_post_meta( $order_id, '_delivery_option', "Delivery: Monday AM 8am-12pm" );
		//update_post_meta( $order_id, '_order_type', "Delivery" );
		
// 		//update_post_meta( $order_id, '_delivery_option', "Pickup time: Sunday 6-7pm" );
 	}
	
	if( ! session_id() ) {
		session_start();
	}
	
// 	/*$cart_item_data_key = '';
// 	$cart_item_data = array(
// 								'_extras' => 'Extra Carbs: $2',
// 								'_extra_cost' => '2',
// 								'_base_price' => '15'
// 							);
// 	foreach ( $cart_item_data as $key => $value ) {
// 		if ( is_array( $value ) || is_object( $value ) ) {
// 			$value = http_build_query( $value );
// 		}
// 		$cart_item_data_key .= trim( $key ) . trim( $value );
// 	}
// 	echo $cart_item_data_key;*/
}

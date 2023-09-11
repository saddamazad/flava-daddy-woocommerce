jQuery(document).ready(function() {
	jQuery("#delivery-type-submit").css("opacity", 0);
		
	jQuery("#postal-btn").on("click", function() {
		var order_type = jQuery('input[name="order_type"]:checked').val();
		localStorage.setItem("order_type", order_type);

		var pickup_time = jQuery('input[name="pickup_time"]:checked').val();
		localStorage.setItem("pickup_time", pickup_time);
		
		var postal_code = jQuery("#postal_code").val();
		if( postal_code ) {
			var service_post_codes = flava_meals_object.post_codes;
			var zone_no = postal_code.substring(0,3).toLowerCase();
			var zones_arr = service_post_codes.toLowerCase().split(",");
			//if( service_post_codes.includes(postal_code) ) {
			if( (postal_code.length > 5) && zones_arr.includes(zone_no) ) {
				jQuery(".postal-error").remove();
				localStorage.setItem("postal_code", postal_code);
				
				jQuery.ajax({
					type : "post",
					dataType: "json",
					url : flava_meals_object.ajaxurl,
					data : {action : 'flava_ajax_store_order_type', order_type: order_type},
					beforeSend: function() {
						//jQuery(".cart_container").html('Loading...').show();
					}, 
					success: function(data) {
						var meal_type = jQuery("#meal-plan-type").val();
						if(meal_type == "onetime") {
							window.location = "/one-time-order/";
						}
						if(meal_type == "subscription") {
							window.location = "/weekly-subscription/";
						}
					}
				});
			} else {
				jQuery(".postcode-msg").html('<div class="postal-error">Sorry, we don\'t deliver to this location!</div>');
				jQuery("#delivery-type-submit").attr("href", "").css("opacity", 0);
			}
			jQuery("#postal_code").css("border-color", "#dddddd");
		} else {
			jQuery("#postal_code").css("border-color", "#f00");
		}
	});
	
	jQuery(".radio_inline span input").on("click", function() {
		var order_type = jQuery(this).val();
		
		if(order_type == "Pickup") {
			localStorage.setItem("order_type", order_type);

			jQuery.ajax({
				type : "post",
				dataType: "json",
				url : flava_meals_object.ajaxurl,
				data : {action : 'flava_ajax_store_order_type', order_type: order_type},
				beforeSend: function() {
					//jQuery(".cart_container").html('Loading...').show();
				}, 
				success: function(data) {
				}
			});

			var meal_type = jQuery("#meal-plan-type").val();
			if(meal_type == "onetime") {
				jQuery("#delivery-type-submit").attr("href", "/one-time-order/");
			}
			if(meal_type == "subscription") {
				jQuery("#delivery-type-submit").attr("href", "/weekly-subscription/");
			}
			jQuery("#delivery-type-submit").css("opacity", 1);			
			
			jQuery(".radio_list input").removeAttr('checked');
			jQuery(".radio_list.pickup-opt input").click();
			jQuery(".radio_list.pickup-opt").show();
			localStorage.setItem("pickup_time", "Pickup time: Sunday 6-7pm");
			// remove postal code if there is any
			if( localStorage.getItem("postal_code") ) {
				localStorage.removeItem("postal_code");
			}
			
			jQuery(".radio_list").removeClass("checked-opt");
			jQuery(".radio_list.pickup-opt").addClass("checked-opt");
			jQuery(".radio_list.delivery-opt").hide();
			jQuery(".postal-code-group").hide();
			
			jQuery("#pickup-map").show();
			jQuery("#delivery-map").hide();
		}
		if(order_type == "Delivery") {
			jQuery(".radio_list.pickup-opt input").removeAttr('checked');
			jQuery(".radio_list.pickup-opt").removeClass("checked-opt").hide();
			jQuery(".radio_list.delivery-opt").show();
			jQuery(".delivery-opts-group .radio_list.delivery-opt:first input").click();
			jQuery(".delivery-opts-group .radio_list.delivery-opt:first").addClass("checked-opt");
			jQuery(".postal-code-group").show();
			jQuery("#postal_code").val('');
			jQuery("#delivery-type-submit").attr("href", "").css("opacity", 0);
			
			jQuery("#delivery-map").show();
			jQuery("#pickup-map").hide();
		}
	});
	
	jQuery("#cat-filters .elementor-icon-list-item a").on("click", function(e) {
		e.preventDefault();
		var category = jQuery(this).attr("data-cat");
		jQuery("#cat-filters .elementor-icon-list-item a").attr("data-active", 0);
		jQuery(this).attr("data-active", 1);
		if(category == "all") {
			jQuery(".cat_product_grid").show();
		} else {
			jQuery(".cat_product_grid").hide();
			jQuery('.cat_product_grid[data-category='+category+']').show();
		}
	});

	if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Delivery" ) {
		jQuery(".order-minlimit-info").show();
	}
	
	// If one-time order page
	if( jQuery("body").hasClass("page-id-3580") ) {
		if( ! localStorage.getItem("order_type") ) {
			window.location.href = "/order-delivery/?order_type=onetime";
		}
		
		if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Pickup" ) {
			//jQuery(".cart_note").hide();
		}
	}
	
	if( jQuery("body").hasClass("weekly-meals-order-tmp") ) {
		if( ! localStorage.getItem("order_type") ) {
			window.location.href = "/order-delivery/?order_type=subscription";
		}		
	}
	
	// If weekly subscription page
	if( jQuery("body").hasClass("page-id-3742") ) {
		if( ! localStorage.getItem("order_type") ) {
			window.location.href = "/order-delivery/?order_type=subscription";
		}

		/*localStorage.removeItem("cart_items");
		localStorage.removeItem("cart_subtotal");
		localStorage.removeItem("cart_total");
		localStorage.removeItem("cart_extras");*/
	}
	
	if( jQuery("body").hasClass("woocommerce-order-received") ) {
		// clear all the stored data from the localstorage
		localStorage.removeItem("cart_items");
		localStorage.removeItem("cart_subtotal");
		localStorage.removeItem("cart_total");
		localStorage.removeItem("cart_extras");
		
		localStorage.removeItem("order_type");
		localStorage.removeItem("pickup_time");
		localStorage.removeItem("postal_code");
	}
	
	if( jQuery("body").hasClass("woocommerce-cart") ) {
		if( jQuery(".woocommerce-cart-form").length ) {
			jQuery(".shop_table.cart tbody .cart_item").each(function() {
				jQuery(this).find(".product-quantity .qty").attr("min", 1);
			});
		}
	}
	
	// if one-time order OR weekly subscription OR cart page
	if( jQuery("body").hasClass("page-id-3580") || jQuery("body").hasClass("page-id-3742") || jQuery("body").hasClass("woocommerce-cart") ) {
		var min_meals = 6;
		var min_amount = 88;
		
		var items_in_storage = JSON.parse(localStorage.getItem("cart_items"));
		var item_keys_arr = [];
		var stored_cart_total = 0;
		var stored_cart_subtotal = 0;
		var stored_cart_extra_total = 0;
		var stored_meals_count = 0;
		jQuery.each( items_in_storage, function( line_item_key, line_item ) {
			var plan_txt = '';
			if( line_item.plan ) {
				plan_txt = ' - '+line_item.plan;
			}
			
			jQuery(".cart-items-wrap").prepend('<div class="cart-line-item" data-product-id="'+line_item.product_id+'" data-cart-item-key="'+line_item_key+'" data-price="'+line_item.line_total+'" data-item-type="'+line_item.type+'"><div class="line-item-image"><img src="'+line_item.image+'" alt="'+line_item.title+'" /></div><div class="line-item-title"><h4>'+line_item.title+plan_txt+' <small>($'+(line_item.price/line_item.quantity).toFixed(2)+')</small></h4><div class="line-item-qty">Count: <span class="item-qty">'+line_item.quantity+'</span></div><div class="line-item-extras">'+line_item.extras+'</div></div><span class="remove-line-item">x</span></div>');
			item_keys_arr.push(line_item_key);
			
			stored_cart_total += parseFloat(line_item.line_total);
			stored_cart_subtotal += parseFloat(line_item.price);
			if( line_item.extra_cost ) {
				stored_cart_extra_total += parseFloat(line_item.extra_cost);
			}
			
			if( line_item.type == "onetime" ) {
				stored_meals_count += parseInt(line_item.quantity);
			} else if(line_item.type == "subscription") {
				var stored_plan_meals = line_item.plan.split(" ")[0];
				stored_meals_count += parseInt(stored_plan_meals);
			}
			
			jQuery(".cart-no-item").hide();
		});
		item_keys_arr.join();
		jQuery("#items-in-cart").val(item_keys_arr);

		if(item_keys_arr.length > 2) {
			jQuery(".cart-items-wrap").addClass("too-many-items");
		}
		
		jQuery("#flash-cart .cart-subtotal").html('$'+stored_cart_subtotal.toFixed(2));
		jQuery(".cart-total").html('$'+stored_cart_total.toFixed(2));

		if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Delivery" ) {
			var stored_meals_left = min_meals - stored_meals_count;
			/*if(stored_meals_left > 0) {
				jQuery(".order-minlimit-info").show();
				jQuery(".meals-left-count").text(stored_meals_left);
			} else {
				jQuery(".order-minlimit-info").hide();
			}*/
			var stored_amount_left = min_amount - stored_cart_total;
			if(stored_amount_left > 0) {
				jQuery(".order-minlimit-info").show();
				//jQuery(".meals-left-count").text(stored_amount_left.toFixed(2));
			} else {
				jQuery(".order-minlimit-info").hide();
			}
		}

		jQuery("#meals-count").val(stored_meals_count);
		jQuery(".min-meals-error").remove();

		if( stored_cart_extra_total > 0 ) {
			jQuery(".cart-extras").html('$'+stored_cart_extra_total.toFixed(2));
			jQuery(".cart-extra-total-line").show();
		}
	}
	
	if( jQuery(".wc_gc_giftcard_delivery").length ) {
		jQuery(".wc_gc_giftcard_delivery label").text("Date Your Gift Card Will be Sent");
	}
	
	jQuery(".meal-add-btn").on("click", function(e) {
		var product_id = parseInt(jQuery(this).parents(".meal-product").attr("data-product-id"));
		var image = jQuery(this).parents(".meal-product").find(".meal-image").attr("src");
		var quantity = jQuery(this).siblings(".meal-quantity").val();
		var title = jQuery(this).parents(".meal-product").find(".cat-product-title").text();
		var price = parseFloat(jQuery(this).parents(".meal-product").attr("data-product-price"));
		var extras = [];
		var extra_cost = 0;
		jQuery('input[name="extra_meal_'+product_id+'_opts[]"]:checked').each(function() {
			var ext_base_cost = parseFloat(jQuery(this).attr("data-ext-cost"));
			var ext_cost = ext_base_cost*quantity;
			extras.push( jQuery(this).val()+": $"+ext_base_cost );
			extra_cost += ext_cost;
		});
		var ext_string = extras.join(", ");
		var line_total = (price*quantity) + extra_cost;
		
		if(quantity > 0) {
			jQuery(this).siblings(".meal-quantity").css("border-color", "#dddddd");
			if( ! localStorage.getItem("cart_items") ) {
				var cart_items = {};
				localStorage.setItem("cart_items", "");
			} else {
				var cart_items = JSON.parse(localStorage.getItem("cart_items"));
			}
			
			var cart_line_item = {
				product_id: product_id,
				quantity: quantity,
				plan: '',
				title: title,
				image: image,
				price: price*quantity,
				extras: ext_string,
				extra_cost: extra_cost,
				line_total: line_total.toFixed(2),
				type: 'onetime'
			};
			
			//var cart_item_id_string = product_id + ext_string;
			
			// https://woocommerce.github.io/code-reference/files/woocommerce-includes-class-wc-cart.html#source-view.976
			// matching the cart id generation process with woocommerce
			var cart_item_meta_data = '_extras'+ext_string+'_extra_cost'+(extra_cost/quantity)+'_base_price'+price;
			var cart_item_id_string = product_id+"_"+cart_item_meta_data;
			const cart_item_key = md5(cart_item_id_string);
			
			cart_items[cart_item_key] = cart_line_item;
			localStorage.setItem("cart_items", JSON.stringify(cart_items));
			
			if( jQuery("body").hasClass("weekly-meals-order-tmp") ) {
				jQuery("#subs-cart-items").attr('value', localStorage.getItem("cart_items"));
			}
						
			jQuery(".cart-no-item").hide();			
			var li_price = 0;
			var li_ext_cost = 0;
			
			if( localStorage.getItem("cart_items") ) {
				var ses_cart = JSON.parse(localStorage.getItem("cart_items"));
				if( ses_cart[cart_item_key] ) {
					var li_quantity = ses_cart[cart_item_key]["quantity"];
					li_price = ses_cart[cart_item_key]["price"];
					li_ext_cost = ses_cart[cart_item_key]["extra_cost"];
					
					var items_in_cart = jQuery("#items-in-cart").val();
					if( items_in_cart ) {
						var items_arr = items_in_cart.split(',');
					} else {
						var items_arr = [];
					}
					if( jQuery.inArray(String(cart_item_key), items_arr) !== -1 ) {
						jQuery(".cart-line-item[data-cart-item-key="+cart_item_key+"]").find(".item-qty").text(li_quantity);
						jQuery(".cart-line-item[data-cart-item-key="+cart_item_key+"]").find(".line-item-extras").text(ext_string);
					} else {
						jQuery(".cart-items-wrap").prepend('<div class="cart-line-item" data-product-id="'+product_id+'" data-cart-item-key="'+cart_item_key+'" data-item-type="onetime"><div class="line-item-image"><img src="'+image+'" alt="'+title+'" /></div><div class="line-item-title"><h4>'+title+' <small>($'+(li_price/li_quantity).toFixed(2)+')</small></h4><div class="line-item-qty">Count: <span class="item-qty">'+li_quantity+'</span></div><div class="line-item-extras">'+ext_string+'</div></div><span class="remove-line-item">x</span></div>');
						//items_arr.push(String(product_id));
						items_arr.push(String(cart_item_key));
						items_arr.join();
						jQuery("#items-in-cart").val(items_arr);
						
						if(items_arr.length > 2) {
							jQuery(".cart-items-wrap").addClass("too-many-items");
						}
					}
				}
								
				var cart_total = 0;
				var cart_subtotal = 0;
				var cart_extra_total = 0;
				var meals_count = 0;
				jQuery.each(ses_cart, function (i) {
					var cart_line_item_type = ses_cart[i].type;
					var cart_line_item_plan = ses_cart[i].plan;
					
					jQuery.each(ses_cart[i], function (key, val) {
						if(key == "line_total") {
							cart_total += parseFloat(val);
						}
						if(key == "price") {
							cart_subtotal += parseFloat(val);
						}
						if(key == "extra_cost") {
							cart_extra_total += parseFloat(val);
						}
						if(key == "quantity") {
							// don't count the subscription products/items
							if(cart_line_item_type == "onetime") {
								meals_count += parseInt(val);
							} else if(cart_line_item_type == "subscription") {
								var plan_meals = cart_line_item_plan.split(" ")[0];
								meals_count += parseInt(plan_meals);
							}
						}
					});
				});
				if( cart_extra_total > 0 ) {
					localStorage.setItem("cart_extras", cart_extra_total.toFixed(2));
					jQuery(".cart-extras").html('$'+cart_extra_total.toFixed(2));
					jQuery(".cart-extra-total-line").show();
				} else {
					localStorage.removeItem("cart_extras");
					jQuery(".cart-extra-total-line").hide();
				}
			}
			
			localStorage.setItem("cart_subtotal", cart_subtotal.toFixed(2));
			jQuery("#flash-cart .cart-subtotal").html('$'+cart_subtotal.toFixed(2));
			
			localStorage.setItem("cart_total", cart_total.toFixed(2));
			jQuery(".cart-total").html('$'+cart_total.toFixed(2));
			
			if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Delivery" ) {
				var meals_left = min_meals - meals_count;
				/*if(meals_left > 0) {
					jQuery(".order-minlimit-info").show();
					jQuery(".meals-left-count").text(meals_left);
				} else {
					jQuery(".order-minlimit-info").hide();
				}*/
				var amount_left = min_amount - cart_total;
				if(amount_left > 0) {
					jQuery(".order-minlimit-info").show();
					//jQuery(".meals-left-count").text(amount_left.toFixed(2));
				} else {
					jQuery(".order-minlimit-info").hide();
				}
			}

			jQuery("#meals-count").val(meals_count);
			jQuery(".min-meals-error").remove();
			
			jQuery("#cart-overlay").fadeIn(200);
			jQuery("#flash-cart").addClass("revealed");
			
			// reset quantiy field after item added to the cart
			jQuery(this).parents(".meal-product").find(".meal-quantity").val(1);
			// reset the checkboxes after item added to the cart
			jQuery('input[name="extra_meal_'+product_id+'_opts[]"]:checked').each(function() {
				jQuery(this).click();
			});
		} else {
			jQuery(this).siblings(".meal-quantity").css("border-color", "#f00");
		}
	});
	
	jQuery("#proceed-cart").on("click", function(e) {
		var session_cart = localStorage.getItem("cart_items");
		var process_cart = true;
		
		if( localStorage.getItem("order_type") ) {
			if( localStorage.getItem("order_type") == "Delivery" ) {
				/*var meals_count = jQuery("#meals-count").val();
				if(meals_count < min_meals) {
					jQuery(".min-meals-error").remove();
					jQuery('<div class="min-meals-error">You have to order minimum 6 meals.</div>').insertBefore( jQuery(this) );
					process_cart = false;
				}*/
				var total_amount_txt = jQuery(".cart-total").text();
				var cart_total_amount = parseFloat( total_amount_txt.slice(1) );
				if(cart_total_amount < min_amount) {
					jQuery(".min-meals-error").remove();
					jQuery('<div class="min-meals-error">You have to order minimum $'+min_amount+'.</div>').insertBefore( jQuery(this) );
					process_cart = false;
				}
			}
		}
		
		if( process_cart ) {
			jQuery.ajax({
				type : "post",
				dataType: "json",
				url : flava_meals_object.ajaxurl,
				data : {action : 'flava_ajax_add_to_cart', cart_items: session_cart},
				beforeSend: function() {
					//jQuery(".cart_container").html('Loading...').show();
				}, 
				success: function(data) {
					window.location.href = "/cart/";
					//console.log(data.msg);
				}
			});
		}
	});
	
	jQuery("#proceed-wksubs-cart").on("click", function(e) {
		var req_items = parseInt(flava_meals_object.meals_req);
		var meals_added = jQuery("#meals-count").val();
		if( meals_added != req_items ) {
			jQuery(".req-items-msg").remove();
			jQuery('<div class="req-items-msg">You have to select '+req_items+' meals</div>').insertBefore( jQuery(this) );
		} else {
			setTimeout(function() { 
				jQuery("#meal-subscription-form").submit();
			}, 500);
		}
	});
	
	jQuery(".extra-opts").on("click", function(e) {		
		var prod_id = parseInt(jQuery(this).closest(".meal-product").attr("data-product-id"));
		
		if( jQuery(this).hasClass("no-carb-veg") ) {
			if( (jQuery("#extra_carbs_"+prod_id).prop('checked') == true) || (jQuery("#double_veg_"+prod_id).prop('checked') == true) ) {
				e.preventDefault();
				return;
			}
		}
		
		if( jQuery(this).hasClass("extra-carbs") || jQuery(this).hasClass("double-veg") ) {
			if( jQuery("#no_carb_"+prod_id).prop('checked') == true ) {
				e.preventDefault();
				return;
			}
		}
	});
		
	jQuery("#flash-cart").on("click", ".remove-line-item", function(e) {
		var product_id = parseInt(jQuery(this).closest(".cart-line-item").attr("data-product-id"));
		var cart_item_key = jQuery(this).closest(".cart-line-item").attr("data-cart-item-key");
		var cart_item_type = jQuery(this).closest(".cart-line-item").attr("data-item-type");
		
		var sess_cart = JSON.parse(localStorage.getItem("cart_items"));
		var cart_index = cart_item_key;
		
		var adj_quantity = parseFloat(sess_cart[cart_index]['quantity']);
		if(cart_item_type == "subscription") {
			var adj_plan_quantity = sess_cart[cart_index]['plan'];
			adj_quantity = parseInt( adj_plan_quantity.split(" ")[0] );
		}
		
		var adj_price = parseFloat(sess_cart[cart_index]['price']);
		var adj_extra_cost = parseFloat(sess_cart[cart_index]['extra_cost']);
		delete sess_cart[cart_index];
		localStorage.setItem("cart_items", JSON.stringify(sess_cart));
		
		if( jQuery("body").hasClass("weekly-meals-order-tmp") ) {
			jQuery("#subs-cart-items").attr('value', localStorage.getItem("cart_items"));
		}

		var sess_subtotal = parseFloat(localStorage.getItem("cart_subtotal"));
		sess_subtotal = (sess_subtotal - adj_price);
		localStorage.setItem("cart_subtotal", sess_subtotal.toFixed(2));
		jQuery("#flash-cart .cart-subtotal").html('$'+sess_subtotal.toFixed(2));

		var sess_extras_total = parseFloat(localStorage.getItem("cart_extras"));
		if( cart_item_type == "onetime" ) {
			if( sess_extras_total > 0 && adj_extra_cost > 0 ) {
				sess_extras_total = (sess_extras_total - adj_extra_cost);
				localStorage.setItem("cart_extras", sess_extras_total.toFixed(2));
				jQuery(".cart-extras").html('$'+sess_extras_total.toFixed(2));			
				if(sess_extras_total < 1) {
					jQuery(".cart-extra-total-line").hide();
				}
			}
		}
		
		var sess_total = parseFloat(localStorage.getItem("cart_total"));
		if( cart_item_type == "onetime" && adj_extra_cost > 0 ) {
			sess_total = Math.abs( sess_total - (adj_price + adj_extra_cost) );
		} else {
			sess_total = Math.abs( sess_total - adj_price );
		}
		localStorage.setItem("cart_total", sess_total.toFixed(2));
		jQuery(".cart-total").html('$'+sess_total.toFixed(2));
		if( sess_total < 1 ) {
			jQuery(".cart-no-item").show();
		}

		var items_in_cart = jQuery("#items-in-cart").val();
		var items_arr = items_in_cart.split(',');
		var item_index = items_arr.indexOf(String(cart_item_key));
		items_arr.splice(item_index, 1);
		items_arr.join();
		jQuery("#items-in-cart").val(items_arr);
		
		if(items_arr.length < 3) {
			jQuery(".cart-items-wrap").removeClass("too-many-items");
		}
		
		if(cart_item_type == "onetime") {
			jQuery('.meal-product[data-product-id='+product_id+'] .meal-quantity').val(1);
		}
		
		//var item_quantity = parseInt(jQuery(this).closest(".cart-line-item").find(".item-qty").text());
		var meals_count = jQuery("#meals-count").val();
		
		if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Delivery" ) {
			//if(cart_item_type == "onetime") {
				meals_count = meals_count - adj_quantity;
				var meals_left = min_meals - meals_count;
				/*if(meals_left > 0) {
					jQuery(".order-minlimit-info").show();
					jQuery(".meals-left-count").text(meals_left);
				} else {
					jQuery(".order-minlimit-info").hide();
				}*/
				
				var pr_total_amount = parseFloat(localStorage.getItem("cart_total"));
				if( adj_extra_cost > 0 ) {
					pr_total_amount = Math.abs( pr_total_amount - (adj_price + adj_extra_cost) );
				} else {
					pr_total_amount = Math.abs( pr_total_amount - adj_price );
				}
				var amount_left = min_amount - pr_total_amount;
				if(amount_left > 0) {
					jQuery(".order-minlimit-info").show();
					//jQuery(".meals-left-count").text(amount_left.toFixed(2));
				} else {
					jQuery(".order-minlimit-info").hide();
				}
			//}
		}
		
		jQuery("#meals-count").val(meals_count);
		jQuery(this).closest(".cart-line-item").remove();
		
		jQuery.ajax({
			type: 'POST',
			url: flava_meals_object.ajaxurl,
			data: {
				'action': 'remove_item_from_woo_cart',
				'item_key': cart_item_key,
			},
			success: function (data) {
				//jQuery('body').trigger('update_checkout');
			},
		});
		
	});
	
	if( jQuery("body").hasClass("woocommerce-checkout") ) {
		if( localStorage.getItem("order_type") ) {
			var order_type = localStorage.getItem("order_type");
			jQuery("#order_type").val(order_type);
			
			if( order_type == "Pickup" ) {
				// hide the package leave delivery options
				jQuery("#delivery_for_not_home_field").hide();
			}
		}
		if( localStorage.getItem("order_type") ) {
			var delivery_option = localStorage.getItem("pickup_time");
			jQuery("#delivery_option").val(delivery_option);
		}
		if( localStorage.getItem("postal_code") ) {
			var postal_code = localStorage.getItem("postal_code").toUpperCase();
			jQuery("#billing_postcode").val(postal_code);
			jQuery("#billing_postcode").attr("readonly", "readonly");
			jQuery("#shipping_postcode").val(postal_code);
			jQuery("#shipping_postcode").attr("readonly", "readonly");
		}
	}
	
	jQuery(".add-wk-meal-btn").on("click", function() {
		var subs_product_id = jQuery(this).closest(".meal-subs-wrap").find(".number-of-meals-plan").val();
		var sp_title = jQuery(this).closest(".elementor-section").find(".elementor-image-box-title").text();
		var sp_image = jQuery(this).closest(".elementor-section").find(".elementor-widget-image img").attr("src");
		var subs_plan = jQuery(this).closest(".meal-subs-wrap").find(".number-of-meals-plan").find(":selected").text();
		var subs_price = jQuery(this).closest(".meal-subs-wrap").find(".number-of-meals-plan").find(":selected").attr('data-price');
		
		var items_in_cart = jQuery("#items-in-cart").val();
		if( items_in_cart ) {
			var items_arr = items_in_cart.split(',');
		} else {
			var items_arr = [];
		}

		if( ! localStorage.getItem("cart_items") ) {
			var cart_items = {};
			localStorage.setItem("cart_items", "");
		} else {
			var cart_items = JSON.parse(localStorage.getItem("cart_items"));
		}

		var cart_line_item = {
			product_id: subs_product_id,
			quantity: 1,
			plan: subs_plan,
			title: sp_title,
			image: sp_image,
			price: subs_price,
			extras: '',
			extra_cost: 0,
			line_total: subs_price,
			type: 'subscription'
		};

		// matching the cart id generation process with woocommerce
		/*var cart_item_meta_data = '_extras'+ext_string+'_extra_cost'+(extra_cost/quantity)+'_base_price'+price;
		var cart_item_id_string = product_id+"_"+cart_item_meta_data;*/
		const cart_item_key = md5(subs_product_id);

		cart_items[cart_item_key] = cart_line_item;
		localStorage.setItem("cart_items", JSON.stringify(cart_items));
		
		if( jQuery.inArray(cart_item_key, items_arr) !== -1 ) {
			// todo
		} else {
			jQuery(".cart-items-wrap").prepend('<div class="cart-line-item" data-product-id="'+subs_product_id+'" data-cart-item-key="'+cart_item_key+'" data-price="'+subs_price+'" data-item-type="subscription"><div class="line-item-image"><img src="'+sp_image+'" alt="'+sp_title+'" /></div><div class="line-item-title"><h4>'+sp_title+' - '+subs_plan+' <small>($'+subs_price+')</small></h4></div><span class="remove-line-item">x</span></div>');
			//items_arr.push(subs_product_id);
			items_arr.push(cart_item_key);
			items_arr.join();
			jQuery("#items-in-cart").val(items_arr);
			jQuery(".cart-no-item").hide();

			if(items_arr.length > 2) {
				jQuery(".cart-items-wrap").addClass("too-many-items");
			}
			
			
			
			var meals_count = parseInt(jQuery("#meals-count").val());
			var plan_meals = subs_plan.split(" ")[0];
			meals_count += parseInt(plan_meals);
			
			if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Delivery" ) {
				var meals_left = min_meals - meals_count;
				if(meals_left > 0) {
					jQuery(".order-minlimit-info").show();
					jQuery(".meals-left-count").text(meals_left);
				} else {
					jQuery(".order-minlimit-info").hide();
				}
			}

			jQuery("#meals-count").val(meals_count);
			jQuery(".min-meals-error").remove();
			
			
			
			//var subs_sub_total = jQuery(".cart-subtotal").text();
			//subs_sub_total = parseFloat(subs_sub_total.replace("$", ""));
			var subs_sub_total = 0;
			if( localStorage.getItem("cart_subtotal") ) {
				subs_sub_total = parseFloat(localStorage.getItem("cart_subtotal"));
			}
			subs_sub_total += parseFloat(subs_price);
			jQuery("#flash-cart .cart-subtotal").text('$'+subs_sub_total.toFixed(2));			
			localStorage.setItem("cart_subtotal", subs_sub_total.toFixed(2));
			
			var cart_extras_cost = 0;
			if( localStorage.getItem("cart_extras") ) {
				cart_extras_cost += parseFloat(localStorage.getItem("cart_extras"));
			}
			
			var subs_cart_total = parseFloat(subs_sub_total+cart_extras_cost).toFixed(2);
			jQuery(".cart-total").text('$'+subs_cart_total);
			localStorage.setItem("cart_total", subs_cart_total);
		}
		jQuery("#cart-overlay").fadeIn(200);
		jQuery("#flash-cart").addClass("revealed");
	});
	
	jQuery("#flash-cart").on("click", ".remove-subs-line-item", function() {
		var product_id = parseInt(jQuery(this).closest(".cart-line-item").attr("data-product-id"));
		var cart_item_key = parseInt(jQuery(this).closest(".cart-line-item").attr("data-cart-item-key"));
		var subs_price = parseFloat(jQuery(this).closest(".cart-line-item").attr('data-price'));
		var items_in_cart = jQuery("#items-in-cart").val();
		var items_arr = items_in_cart.split(',');
		//var item_index = items_arr.indexOf(product_id);
		var item_index = items_arr.indexOf(cart_item_key);
		items_arr.splice(item_index, 1);
		items_arr.join();
		jQuery("#items-in-cart").val(items_arr);

		var stored_cart = JSON.parse(localStorage.getItem("cart_items"));
		delete stored_cart[cart_item_key];
		localStorage.setItem("cart_items", JSON.stringify(stored_cart));		
		
		if(items_arr.length < 3) {
			jQuery(".cart-items-wrap").removeClass("too-many-items");
		}
		
		if(items_arr.length < 1) {
			jQuery(".cart-no-item").show();
		}
		
		//var subs_sub_total = jQuery(".cart-subtotal").text();
		//subs_sub_total = parseFloat(subs_sub_total.replace("$", ""));
		var subs_sub_total = 0;
		if( localStorage.getItem("cart_subtotal") ) {
			subs_sub_total = parseFloat(localStorage.getItem("cart_subtotal"));
		}
		subs_sub_total -= parseFloat(subs_price);
		jQuery("#flash-cart .cart-subtotal").text('$'+subs_sub_total.toFixed(2));
		localStorage.setItem("cart_subtotal", subs_sub_total.toFixed(2));
		
		var cart_extras_cost = 0;
		if( localStorage.getItem("cart_extras") ) {
			cart_extras_cost += parseFloat(localStorage.getItem("cart_extras"));
		}
		//var adj_extra_cost = parseFloat(stored_cart[cart_item_key]['extra_cost']);

		var subs_cart_total = parseFloat(subs_sub_total+cart_extras_cost).toFixed(2);
		jQuery(".cart-total").text('$'+subs_cart_total);
		localStorage.setItem("cart_total", subs_cart_total);
		
		jQuery(this).closest(".cart-line-item").remove();
	});
	
	jQuery(".number-of-meals-plan").on("change", function() {
		var subs_price = jQuery(this).find(":selected").attr('data-price');
		jQuery(this).closest(".meal-subs-wrap").find(".woocommerce-Price-amount bdi").html('<span class="woocommerce-Price-currencySymbol">$</span>'+subs_price);
		
		jQuery(this).closest(".weekly-plan-section").find(".meal-mobile-price").remove();
		jQuery(this).closest(".weekly-plan-section").find(".elementor-image-box-title").after('<div class="meal-mobile-price">$'+subs_price+'</div>');
	});
	
	jQuery("#proceed-subs-cart").on("click", function() {
		var items_in_cart = jQuery("#items-in-cart").val();
		if( items_in_cart ) {
			var curr_page = window.location.href;
			window.location.href = curr_page+"?add_subs_items="+items_in_cart;
		}
	});
	
	jQuery(".close-cart").on("click", function() {
		jQuery("#flash-cart").removeClass("revealed");
		jQuery("#cart-overlay").fadeOut(200);
	});
	
	jQuery(".radio_list").on("click", function() {
		jQuery(".radio_list").removeClass("checked-opt");
		jQuery(this).addClass("checked-opt");
	});
	
	jQuery('form.checkout').on('change', '#insulated_bag_fee', function(e) {
		var fee = jQuery(this).prop('checked') === true ? 9 : '';
		jQuery.ajax({
			type: 'POST',
			url: wc_checkout_params.ajax_url,
			data: {
				'action': 'insulated_bag_fee',
				'insulated_bag_fee': fee,
			},
			success: function (result) {
				jQuery('body').trigger('update_checkout');
			},
		});
	});
	
	if( jQuery("body").hasClass("woocommerce-checkout") && localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Pickup" ) {
		jQuery("#ship-to-different-address").fadeOut();
		jQuery(".shipping_address").fadeOut();
	}
	
	if( jQuery("body").hasClass("page-id-3742") ) {
		jQuery(".weekly-plan-section").each(function() {
			var opt1_price = jQuery(this).find(".number-of-meals-plan option:first").attr("data-price");
			jQuery(this).find(".elementor-image-box-title").after('<div class="meal-mobile-price">$'+opt1_price+'</div>');
		});
	}

	jQuery("#cart-overlay").on("click", function(e) {
		jQuery("#flash-cart").removeClass("revealed");
		jQuery(this).fadeOut(200);
		e.stopPropagation();
	});
	
	jQuery(".meal-product .image_wrap").on("click", function() {
		jQuery(this).closest(".meal-product").find(".fancybox-inline").click();
	});
	
	jQuery("#insulated_bag_fee_field i").on("click", function(e) {
		e.preventDefault();
		jQuery(this).toggleClass("actv");
	});
	
	jQuery(".weekly-subs-title i").on("click", function() {
		jQuery(this).toggleClass("actv");
	});
	
	jQuery("#flash-cart-toggle").on("click", function() {
		jQuery("#flash-cart").toggleClass("revealed");
		jQuery("#cart-overlay").fadeToggle(200);
	});
	
	jQuery(".product-remove .remove").on("click", function() {
		var item_key_string = jQuery(this).attr("href");
		var item_key_part = item_key_string.split("&")[0];
		var the_item_key = item_key_part.split("=")[1];
		//var sess_cart_items = JSON.parse(localStorage.getItem("cart_items"));
		jQuery('#flash-cart .cart-line-item[data-cart-item-key="'+the_item_key+'"] .remove-line-item').trigger('click');
	});
	
	setTimeout(function() {
		if( jQuery("#home-banner").length ) {
			var device_mode = jQuery("body").attr("data-elementor-device-mode");
			if( device_mode == "mobile" ) {
				jQuery("#home-banner .elementor-html5-video").attr("src", "/wp-content/uploads/2022/12/Banner-1-Mobile.mp4");
			}
		}
		if( jQuery("#home-smoky-sect").length ) {
			var device_mode = jQuery("body").attr("data-elementor-device-mode");
			if( device_mode == "mobile" ) {
				jQuery("#home-smoky-sect .elementor-html5-video").attr("src", "/wp-content/uploads/2022/12/Banner-2-Mobile.mp4");
			}
		}
		if( jQuery("#wpmenucartli").length ) {
			var device_mode = jQuery("body").attr("data-elementor-device-mode");
			if( device_mode == "mobile" ) {
				jQuery(".wpmenucart-contents").clone().insertBefore(".elementor-location-header .elementor-widget-icon .elementor-icon");
			}
		}
	}, 500);	
});

jQuery(document).on('updated_checkout', function() {
	if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Pickup" ) {
		if( jQuery('input.shipping_method[value*="flat_rate"]').length ) {
			//jQuery('input.shipping_method[value*="flat_rate"]').closest("li").hide();
			//jQuery('input.shipping_method[value*="local_pickup"]').hide();
		}
		
		jQuery("#ship-to-different-address").fadeOut();
		jQuery(".shipping_address").fadeOut();
	}
	if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Delivery" ) {
		if( jQuery('input.shipping_method[value*="local_pickup"]').length ) {
			//jQuery('input.shipping_method[value*="local_pickup"]').closest("li").hide();
			//jQuery('input.shipping_method[value*="flat_rate"]').hide();
		}
		
		jQuery("#ship-to-different-address").fadeIn();
		jQuery("#delivery_for_not_home_field").show();
	}
	if( jQuery(".woocommerce-no-shipping-available-html").length ) {
		jQuery("#ship-to-different-address").fadeOut();
		jQuery(".shipping_address").fadeOut();
		jQuery("#delivery_for_not_home_field").hide();
	}
	if( jQuery("#shipping_method li").length == 1 && jQuery('input.shipping_method[value*="local_pickup"]').length ) {
		if( jQuery("#ship-to-different-address-checkbox").prop('checked') === false ) {
			jQuery("#ship-to-different-address").fadeOut();
			jQuery(".shipping_address").fadeOut();
			jQuery("#delivery_for_not_home_field").hide();
		}
	}
	/*if( localStorage.getItem("postal_code") ) {
		var postal_code = localStorage.getItem("postal_code").toUpperCase();
		if( jQuery("#ship-to-different-address-checkbox").prop('checked') === true ) {
			//jQuery("#billing_postcode").val(postal_code);
			jQuery("#billing_postcode").removeAttr("readonly");
		} else {		
			jQuery("#billing_postcode").val(postal_code);
			jQuery("#billing_postcode").attr("readonly", "readonly");
		}
		jQuery("#shipping_postcode").val(postal_code);
		jQuery("#shipping_postcode").attr("readonly", "readonly");
	}*/
});

jQuery(document).on('update_checkout', function() {
	if( localStorage.getItem("postal_code") ) {
		var postal_code = localStorage.getItem("postal_code").toUpperCase();
		if( jQuery("#ship-to-different-address-checkbox").prop('checked') === true ) {
			//jQuery("#billing_postcode").val(postal_code);
			jQuery("#billing_postcode").removeAttr("readonly");
		} else {		
			jQuery("#billing_postcode").val(postal_code);
			jQuery("#billing_postcode").attr("readonly", "readonly");
		}
		jQuery("#shipping_postcode").val(postal_code);
		jQuery("#shipping_postcode").attr("readonly", "readonly");
	}
});

// on trigger 'updated_wc_div'
jQuery(document).on('updated_cart_totals', function() {
	var cart_total = 0;
	var cart_subtotal = 0;
	var cart_extra_total = 0;
	jQuery(".shop_table.cart tbody .cart_item").each(function() {
		var item_key_string = jQuery(this).find(".remove").attr("href");
		var item_key_part = item_key_string.split("&")[0];
		var the_item_key = item_key_part.split("=")[1];
		var sess_cart_items = JSON.parse(localStorage.getItem("cart_items"));
		var item_new_qty = parseInt(jQuery('input[name="cart['+the_item_key+'][qty]"]').val());
				
		var current_item_price_total = parseFloat(sess_cart_items[the_item_key].price);
		var current_item_quantity = parseInt(sess_cart_items[the_item_key].quantity);
		var current_item_unit_price = current_item_price_total / current_item_quantity;
		var item_new_price = current_item_unit_price * item_new_qty;
		sess_cart_items[the_item_key].price = item_new_price;
		
		sess_cart_items[the_item_key].quantity = item_new_qty;

		var current_item_extra_cost_total = parseFloat(sess_cart_items[the_item_key].extra_cost);
		var current_item_unit_extra_cost = current_item_extra_cost_total / current_item_quantity;		
		var item_new_extra_cost = current_item_unit_extra_cost * item_new_qty;
		sess_cart_items[the_item_key].extra_cost = item_new_extra_cost;
		
		sess_cart_items[the_item_key].line_total = item_new_price + item_new_extra_cost;
		localStorage.setItem("cart_items", JSON.stringify(sess_cart_items));
		
		cart_subtotal += item_new_price;
		cart_extra_total += item_new_extra_cost;
	});
	
	localStorage.setItem("cart_subtotal", cart_subtotal.toFixed(2));
	localStorage.setItem("cart_extras", cart_extra_total.toFixed(2));
	cart_total = cart_subtotal + cart_extra_total;
	localStorage.setItem("cart_total", cart_total.toFixed(2));
	
	//console.log("Remove triggered update total");
});

// ToDo:
// on WC cart item restore/undo removal, add item to the localStorage

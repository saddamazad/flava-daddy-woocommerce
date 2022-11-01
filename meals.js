jQuery(document).ready(function() {
	jQuery("#delivery-type-submit").css("opacity", 0);
		
	jQuery("#postal-btn").on("click", function() {
		var order_type = jQuery('input[name="order_type"]:checked').val();
		localStorage.setItem("order_type", order_type);
		
		var postal_code = jQuery("#postal_code").val();
		if( postal_code ) {
			var service_post_codes = flava_meals_object.post_codes;
			if( service_post_codes.includes(postal_code) ) {
				jQuery(".postal-error").remove();
				localStorage.setItem("postal_code", postal_code);
				jQuery("#delivery-type-submit").css("opacity", 1);
			} else {
				jQuery(".postcode-msg").html('<div class="postal-error">Sorry, we don\'t deliver to this location!</div>');
				jQuery("#delivery-type-submit").css("opacity", 0);
			}
			jQuery("#postal_code").css("border-color", "#dddddd");
		} else {
			jQuery("#postal_code").css("border-color", "#f00");
		}
		
		var pickup_time = jQuery('input[name="pickup_time"]:checked').val();
		localStorage.setItem("pickup_time", pickup_time);		
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

	if( jQuery("body").hasClass("page-id-3580") ) {
		if( ! localStorage.getItem("order_type") ) {
			window.location.href = "/order-delivery/";
		}
		localStorage.removeItem("cart_items");
		localStorage.removeItem("cart_subtotal");
		localStorage.removeItem("cart_total");
		localStorage.removeItem("cart_extras");
		
		var min_meals = 5;
		if( localStorage.getItem("order_type") && localStorage.getItem("order_type") == "Pickup" ) {
			//jQuery(".cart_note").hide();
		}
	}
	
	if( jQuery("body").hasClass("weekly-meals-order-tmp") ) {
		localStorage.removeItem("cart_items");
		localStorage.removeItem("cart_subtotal");
		localStorage.removeItem("cart_total");
		localStorage.removeItem("cart_extras");
	}
	
	/*if( ! localStorage.getItem("cart_items") ) {
		var cart_items = {};
		localStorage.setItem("cart_items", "");
	} else {
		var cart_items = JSON.parse(localStorage.getItem("cart_items"));
	}*/

	
	jQuery(".meal-add-btn").on("click", function(e) {
		var product_id = parseInt(jQuery(this).parents(".meal-product").attr("data-product-id"));
		var image = jQuery(this).parents(".meal-product").find(".meal-image").attr("src");
		var quantity = jQuery(this).siblings(".meal-quantity").val();
		var title = jQuery(this).parents(".meal-product").find(".cat-product-title").text();
		var price = parseFloat(jQuery(this).parents(".meal-product").attr("data-product-price"));
		var extras = [];
		var extra_cost = 0;
		jQuery('input[name="extra_meal_'+product_id+'_opts[]"]:checked').each(function() {
			var ext_cost = parseFloat(jQuery(this).attr("data-ext-cost"));
			extras.push( jQuery(this).val()+": $"+ext_cost );
			extra_cost += ext_cost;
		});
		var ext_string = extras.join(",");
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
				title: title,
				price: price*quantity,
				extras: ext_string,
				extra_cost: extra_cost,
				line_total: line_total.toFixed(2)
			};
			
			const cart_index = product_id;
			cart_items[cart_index] = cart_line_item;
			localStorage.setItem("cart_items", JSON.stringify(cart_items));
			
			if( jQuery("body").hasClass("weekly-meals-order-tmp") ) {
				jQuery("#subs-cart-items").attr('value', localStorage.getItem("cart_items"));
			}
						
			jQuery(".cart-no-item").hide();			
			var li_price = 0;
			var li_ext_cost = 0;
			
			if( localStorage.getItem("cart_items") ) {
				var ses_cart = JSON.parse(localStorage.getItem("cart_items"));
				if( ses_cart[product_id] ) {
					var li_quantity = ses_cart[product_id]["quantity"];
					li_price = ses_cart[product_id]["price"];
					li_ext_cost = ses_cart[product_id]["extra_cost"];
					
					var items_in_cart = jQuery("#items-in-cart").val();
					if( items_in_cart ) {
						var items_arr = items_in_cart.split(',');
					} else {
						var items_arr = [];
					}
					if( jQuery.inArray(String(product_id), items_arr) !== -1 ) {
						jQuery(".cart-line-item[data-product-id="+product_id+"]").find(".item-qty").text(li_quantity);
					} else {
						jQuery(".cart-items-wrap").prepend('<div class="cart-line-item" data-product-id="'+product_id+'"><div class="line-item-image"><img src="'+image+'" alt="'+title+'" /></div><div class="line-item-title"><h4>'+title+'</h4><div class="line-item-qty">Count: <span class="item-qty">'+li_quantity+'</span></div></div><span class="remove-line-item">x</span></div>');
						items_arr.push(String(product_id));
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
							meals_count += parseInt(val);
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
			jQuery(".cart-subtotal").html('$'+cart_subtotal.toFixed(2));
			
			localStorage.setItem("cart_total", cart_total.toFixed(2));
			jQuery(".cart-total").html('$'+cart_total.toFixed(2));
			
			var meals_left = min_meals - meals_count;
			if(meals_left > 0) {
				jQuery(".min-meals-hl").show();
				jQuery(".meals-left-count").text(meals_left);
			} else {
				jQuery(".min-meals-hl").hide();
			}
			jQuery("#meals-count").val(meals_count);
			jQuery(".min-meals-error").remove();
			
			jQuery("#flash-cart").addClass("revealed");
		} else {
			jQuery(this).siblings(".meal-quantity").css("border-color", "#f00");
		}
	});
	
	jQuery("#proceed-cart").on("click", function(e) {
		var session_cart = localStorage.getItem("cart_items");
		var process_cart = true;
		
		if( localStorage.getItem("order_type") ) {
			if( localStorage.getItem("order_type") == "Delivery" ) {
				var meals_count = jQuery("#meals-count").val();
				if(meals_count < min_meals) {
					jQuery(".min-meals-error").remove();
					jQuery('<div class="min-meals-error">You have to order minimum 6 meals.</div>').insertBefore( jQuery(this) );
					process_cart = false;
				}
			}
		}
		
		if( process_cart ) {
			jQuery.ajax({
				type : "post",
				dataType: "json",
				url : flava_meals_object.ajaxurl,
				data : {action : 'fd_ajax_add_to_cart', cart_items: session_cart},
				beforeSend: function() {
					//jQuery(".cart_container").html('Loading...').show();
				}, 
				success: function(data) {
					window.location.href = "/cart/";
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
		var quantity = jQuery(this).closest(".meal-product").find(".meal-quantity").val();
		if( quantity > 0 ) {
			jQuery(this).closest(".meal-product").find(".meal-add-btn").click();
		}
	});
	
	/*jQuery(".meal-quantity").on("change", function(e) {
		var quantity = jQuery(this).val();
		if( quantity > 0 ) {
			jQuery(this).closest(".meal-product").find(".meal-add-btn").click();
		}
	});*/
	
	jQuery("#flash-cart").on("click", ".remove-line-item", function(e) {
		var product_id = parseInt(jQuery(this).closest(".cart-line-item").attr("data-product-id"));
		
		var sess_cart = JSON.parse(localStorage.getItem("cart_items"));
		var cart_index = product_id;
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
		jQuery(".cart-subtotal").html('$'+sess_subtotal.toFixed(2));

		var sess_extras_total = parseFloat(localStorage.getItem("cart_extras"));
		if( sess_extras_total > 0 && adj_extra_cost > 0 ) {
			sess_extras_total = (sess_extras_total - adj_extra_cost);
			localStorage.setItem("cart_extras", sess_extras_total.toFixed(2));
			jQuery(".cart-extras").html('$'+sess_extras_total.toFixed(2));			
			if(sess_extras_total < 1) {
				jQuery(".cart-extra-total-line").hide();
			}
		}
		
		var sess_total = parseFloat(localStorage.getItem("cart_total"));
		if( adj_extra_cost > 0 ) {
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
		var item_index = items_arr.indexOf(String(product_id));
		items_arr.splice(item_index, 1);
		items_arr.join();
		jQuery("#items-in-cart").val(items_arr);
		
		if(items_arr.length < 3) {
			jQuery(".cart-items-wrap").removeClass("too-many-items");
		}
		jQuery('.meal-product[data-product-id='+product_id+'] .meal-quantity').val(0);
		
		var item_quantity = parseInt(jQuery(this).closest(".cart-line-item").find(".item-qty").text());
		var meals_count = jQuery("#meals-count").val();
		meals_count = meals_count - item_quantity;
		var meals_left = min_meals - meals_count;
		if(meals_left > 0) {
			jQuery(".min-meals-hl").show();
			jQuery(".meals-left-count").text(meals_left);
		} else {
			jQuery(".min-meals-hl").hide();
		}
		jQuery("#meals-count").val(meals_count);
		jQuery(this).closest(".cart-line-item").remove();
	});
	
	if( jQuery("body").hasClass("woocommerce-checkout") ) {
		if( localStorage.getItem("order_type") ) {
			var order_type = localStorage.getItem("order_type");
			jQuery("#order_type").val(order_type);
		}
		if( localStorage.getItem("order_type") ) {
			var delivery_option = localStorage.getItem("pickup_time");
			jQuery("#delivery_option").val(delivery_option);
		}
	}
	
	jQuery(".add-wk-meal-btn").on("click", function() {
		var subs_product_id = jQuery(".number-of-meals-plan").val();
		window.location.href = "/weekly-meals-order/?subs_id="+subs_product_id;
	});
	
	jQuery(".close-cart").on("click", function() {
		jQuery("#flash-cart").removeClass("revealed");
	});
	
	jQuery(".radio_list").on("click", function() {
		jQuery(".radio_list").removeClass("checked-opt");
		jQuery(this).addClass("checked-opt");
	});
});

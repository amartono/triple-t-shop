<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues the theme stylesheet on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$src    = 'style' . $suffix . '.css';

		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( $src ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
		wp_style_add_data(
			'twentytwentyfive-style',
			'path',
			get_parent_theme_file_path( $src )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

if ( ! function_exists( 'twentytwentyfive_product_search_filter' ) ) :
	/**
	 * Enables partial-match product search by searching post_title with SQL LIKE.
	 * Matches search terms anywhere in the product name (front, middle, back).
	 *
	 * @param string $sql   Existing search SQL.
	 * @param WP_Query $query The current query object.
	 * @return string Modified search SQL.
	 */
	function twentytwentyfive_product_search_filter( $sql, $query ) {
		global $wpdb;
		if ( ! is_admin() && ( $query->is_search() || ! empty( $_GET['ttt_search'] ) || ! empty( $_GET['s'] ) ) ) {
			$search = $query->get( 's' );
			if ( empty( $search ) && ! empty( $_GET['ttt_search'] ) ) {
				$search = sanitize_text_field( $_GET['ttt_search'] );
			}
			if ( ! empty( $search ) ) {
				$like = '%' . $wpdb->esc_like( $search ) . '%';
				$sql = $wpdb->prepare(
					" AND {$wpdb->posts}.post_title LIKE %s",
					$like
				);
			}
		}
		return $sql;
	}
endif;
add_filter( 'posts_search', 'twentytwentyfive_product_search_filter', 10, 2 );

if ( ! function_exists( 'twentytwentyfive_product_query_search' ) ) :
	/**
	 * Sets the search query var from ttt_search URL parameter on the main query.
	 * Used by the shop page search bar to filter products.
	 *
	 * @param WP_Query $query The main query object.
	 */
	function twentytwentyfive_product_query_search( $query ) {
		if ( ! is_admin() && $query->is_main_query() && ! empty( $_GET['ttt_search'] ) ) {
			$search = sanitize_text_field( $_GET['ttt_search'] );
			$query->set( 's', $search );
		}
	}
endif;
add_action( 'pre_get_posts', 'twentytwentyfive_product_query_search' );

if ( ! function_exists( 'twentytwentyfive_enqueue_shop_search' ) ) :
	/**
	 * Enqueues the shop-search.js script on WooCommerce shop, category, and tag pages.
	 * Provides AJAX autocomplete search functionality on the product archive.
	 */
	function twentytwentyfive_enqueue_shop_search() {
		if ( is_shop() || is_product_category() || is_product_tag() ) {
			wp_enqueue_script( 'twentytwentyfive-shop-search', get_template_directory_uri() . '/assets/js/shop-search.js', array(), '1.0', true );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_shop_search' );

// Chatbot JS
add_action( 'wp_enqueue_scripts', function () {
	/**
	 * Enqueues the AI chatbot JavaScript (chatbot.js) on all frontend pages.
	 * Creates a floating chat button (bottom-right) powered by Groq AI API.
	 * Handles product questions, add-to-cart, and shop navigation.
	 */
	wp_enqueue_script( 'ttt-chatbot', get_template_directory_uri() . '/assets/js/chatbot.js', array(), '1.0', true );
});

// Custom shop template via shortcode
add_shortcode( 'ttt_product_grid', function () {
	/**
	 * [ttt_product_grid] shortcode — Renders the custom product grid on the shop page.
	 * Displays products in a 3-column responsive flex grid with images, titles,
	 * prices, and Add to Cart buttons. Uses the main WordPress query for pagination.
	 * Supports search filtering via the ttt_search URL parameter.
	 *
	 * @return string HTML output of the product grid.
	 */
	global $wp_query;
	$out = '';

	if ($wp_query->have_posts()) {
		$out .= '<div class="ttt-products" style="display:flex;flex-wrap:wrap;gap:8px;margin:0;padding:0;">';
		while ($wp_query->have_posts()) { $wp_query->the_post();
			$p = wc_get_product(get_the_ID());
			$img = get_the_post_thumbnail_url(get_the_ID(), 'full');
			$cart_url = esc_url( add_query_arg('add-to-cart', get_the_ID(), home_url('/?page_id=7')) );
			$permalink = esc_url( get_permalink() );
			$title = esc_html( get_the_title() );
			$price = $p->get_price_html();
			$out .= '<div class="product-card" style="flex:1 1 calc(33.333% - 8px);min-width:240px;max-width:calc(33.333% - 8px);box-sizing:border-box;">';
			$out .= '<a href="'.$permalink.'" style="display:block;text-decoration:none;">';
			if ($img) $out .= '<img src="'.esc_url($img).'" alt="'.$title.'" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:16px;display:block;" loading="lazy">';
			$out .= '<div style="text-align:center;color:#3D0C02;font-size:1rem;font-weight:600;margin:6px 0 2px 0;">'.$title.'</div>';
			$out .= '</a>';
			$out .= '<div style="text-align:center;color:#C6742E;font-size:.95rem;font-weight:700;margin:0 0 6px 0;">'.$price.'</div>';
			$out .= '<a href="'.$cart_url.'" style="display:block;width:fit-content;margin:0 auto;padding:8px 20px;background:#C6742E;color:#fff;border-radius:50px;font-size:.85rem;font-weight:600;text-decoration:none;">Add to cart</a>';
			$out .= '</div>';
		}
		$out .= '</div>';
		if ($wp_query->max_num_pages > 1) {
			$out .= '<div style="text-align:center;margin-top:24px;display:flex;flex-direction:row;justify-content:center;gap:8px;flex-wrap:nowrap;">' . paginate_links(array('current' => max(1, get_query_var('paged')), 'total' => $wp_query->max_num_pages, 'prev_text' => '&laquo;', 'next_text' => '&raquo;')) . '</div>';
		}
		wp_reset_postdata();
	} else {
		$out .= '<p style="text-align:center;color:#3D0C02;">No products found.</p>';
	}
	return $out;
});

// Set products per page for the main query
add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( is_post_type_archive( 'product' ) || is_shop() ) ) {
		$query->set( 'posts_per_page', 9 );
	}
});

// Strip add-to-cart from URL after redirect to prevent re-adding on refresh
add_action( 'template_redirect', function () {
	if ( is_cart() && ! empty( $_GET['add-to-cart'] ) ) {
		wp_safe_redirect( remove_query_arg( 'add-to-cart' ) );
		exit;
	}
}, 5 );
add_filter( 'body_class', function ( $classes ) {
	if ( is_user_logged_in() ) {
		$classes[] = 'ttt-logged-in';
	}
	return $classes;
});

/* ========== 2FA Email OTP Login ========== */
if ( ! function_exists( 'twentytwentyfive_otp_generate' ) ) :
	/**
	 * Generates a cryptographically secure 6-digit OTP code for 2FA login.
	 * Uses random_int() for security and zero-pads to 6 digits.
	 *
	 * @return string 6-digit verification code.
	 */
	function twentytwentyfive_otp_generate() {
		return str_pad( random_int( 0, 999999 ), 6, '0', STR_PAD_LEFT );
	}
endif;

if ( ! function_exists( 'twentytwentyfive_otp_send' ) ) :
	/**
	 * Sends the OTP verification code to the user via wp_mail() (Gmail SMTP).
	 * Called during the 2FA login flow after password verification.
	 *
	 * @param string $email User's email address.
	 * @param string $code  6-digit OTP code.
	 * @param WP_User $user The user object.
	 */
	function twentytwentyfive_otp_send( $email, $code, $user ) {
		$site = get_bloginfo( 'name' );
		$subject = "[$site] Your Verification Code: $code";
		$message  = "Hello,\n\n";
		$message .= "Your verification code for $site is:\n\n";
		$message .= "    $code\n\n";
		$message .= "This code expires in 15 minutes. If you did not request this, please ignore this email.\n\n";
		$message .= "— $site";
		wp_mail( $email, $subject, $message );
	}
endif;

// Intercept login — if credentials are correct, redirect to OTP page
add_filter( 'authenticate', function ( $user, $username, $password ) {
	if ( $user instanceof WP_Error || ! $user instanceof WP_User ) {
		return $user;
	}

	// Bypass OTP for administrators
	if ( user_can( $user, 'administrator' ) ) {
		return $user;
	}

	// If OTP was just verified, let them through
	$verified = get_transient( 'ttt_otp_verified_' . $user->ID );
	if ( $verified ) {
		delete_transient( 'ttt_otp_verified_' . $user->ID );
		return $user;
	}

	// Generate OTP and redirect to verification page
	$pending = get_transient( 'ttt_otp_pending_' . $user->ID );
	if ( ! $pending ) {
		$code = twentytwentyfive_otp_generate();
		set_transient( 'ttt_otp_pending_' . $user->ID, $code, 15 * MINUTE_IN_SECONDS );
		twentytwentyfive_otp_send( $user->user_email, $code, $user );
	}

	// Store user session so OTP page knows who this is
	set_transient( 'ttt_otp_user_' . $user->ID, $user->ID, 15 * MINUTE_IN_SECONDS );

	// Redirect to OTP page
	$redirect = get_permalink( 83 );
	if ( ! empty( $_GET['redirect_to'] ) ) {
		$redirect = add_query_arg( 'redirect_to', urlencode( $_GET['redirect_to'] ), $redirect );
	}
	wp_redirect( $redirect );
	exit;
}, 999, 3 );

// Handle OTP verification on page 83
add_action( 'template_redirect', function () {
	if ( ! is_page( 83 ) ) return;

	global $wpdb;
	$user_id = 0;

	if ( isset( $_POST['ttt_otp_code'] ) && ! empty( $_POST['ttt_otp_code'] ) ) {
		// Find user with pending OTP
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value IS NOT NULL",
				'%_transient_ttt_otp_user_%'
			)
		);
		foreach ( $transients as $t ) {
			$uid = (int) str_replace( '_transient_ttt_otp_user_', '', $t );
			if ( $uid && get_transient( 'ttt_otp_user_' . $uid ) ) {
				$user_id = $uid;
				break;
			}
		}

		if ( $user_id ) {
			$pending = get_transient( 'ttt_otp_pending_' . $user_id );
			$submitted = sanitize_text_field( $_POST['ttt_otp_code'] );

			if ( $pending && hash_equals( $pending, $submitted ) ) {
				delete_transient( 'ttt_otp_pending_' . $user_id );
				delete_transient( 'ttt_otp_user_' . $user_id );
				set_transient( 'ttt_otp_verified_' . $user_id, '1', 60 );

				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id, true );

				$redirect = ! empty( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : home_url();
				wp_safe_redirect( $redirect );
				exit;
			} else {
				// Invalid code — show error
				add_action( 'wp_footer', function () {
					echo '<script>document.getElementById("ttt-otp-error").style.display="block";</script>';
				});
			}
		}
	}

	// If no valid OTP session, redirect to login
	$has_session = $wpdb->get_var(
		"SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '%_transient_ttt_otp_user_%' AND option_value IS NOT NULL"
	);
	if ( ! $has_session && empty( $_POST['ttt_otp_code'] ) ) {
		wp_safe_redirect( home_url( '/?page_id=9' ) );
		exit;
	}
});

// Redirect to login if checkout while not logged in
add_action( 'template_redirect', function () {
	/**
	 * Redirects non-logged-in users from checkout to the login page.
	 * Adds a redirect_to parameter so users return to checkout after login.
	 * Enforces the "must login to checkout" policy.
	 */
	if ( is_checkout() && ! is_user_logged_in() ) {
		wp_safe_redirect( add_query_arg( 'redirect_to', wc_get_checkout_url(), wc_get_page_permalink( 'myaccount' ) ) );
		exit;
	}
}, 5 );

// Redirect back to checkout after login
add_filter( 'woocommerce_login_redirect', function ( $redirect, $user ) {
	if ( ! empty( $_GET['redirect_to'] ) ) {
		return esc_url_raw( $_GET['redirect_to'] );
	}
	return $redirect;
}, 10, 2 );

// Login form — use "Email address" label
add_filter( 'gettext', function ( $translation, $text, $domain ) {
	if ( $domain === 'woocommerce' ) {
		if ( $text === 'Username or email address' || $text === 'Username or email' ) {
			return 'Email address';
		}
	}
	return $translation;
}, 10, 3 );

// Registration password validation
add_filter( 'woocommerce_registration_errors', function ( $errors, $username, $email ) {
	if ( ! empty( $_POST['password'] ) && strlen( $_POST['password'] ) < 6 ) {
		return new WP_Error( 'registration-error', 'Password must be at least 6 characters.' );
	}
	return $errors;
}, 10, 3 );

// Separate login/register pages — hide irrelevant form based on page
add_action( 'wp_head', function () {
	$page_id = get_queried_object_id();
	if ( $page_id == 9 ) {
		// Login page — hide registration form and duplicate headings
		echo '<style>
			.woocommerce-account .u-column2.col-2,
			.woocommerce-account .woocommerce-form-register,
			.woocommerce-MyAccount-content .woocommerce-form-register { display: none !important; }
			.u-column1.col-1 { width: 100% !important; }
			body.page-id-9 h1.wp-block-post-title,
			body.page-id-9 .u-column1 h2,
			body.page-id-9 .u-column2 h2,
			body.page-id-9 .woocommerce-MyAccount-content h2 { display: none !important; }
			body.page-id-9 .u-column1::before {
				content: "Login";
				display: block;
				font-size: 1.75rem;
				font-weight: 700;
				color: #3D0C02;
				text-align: center;
				margin-bottom: 1.5rem;
			}
		</style>';
	}
	if ( $page_id == 80 ) {
		// Register page — hide login form, show registration
		echo '<style>
			.woocommerce-account .u-column1.col-1,
			.woocommerce-account .woocommerce-form-login:not(#customer_login .woocommerce-form-login),
			.woocommerce-MyAccount-content .woocommerce-form-login { display: none !important; }
			.u-column2.col-2 { width: 100% !important; }
			.woocommerce-account .woocommerce-form-register { display: block !important; }
			body.page-id-80 h1.wp-block-post-title,
			body.page-id-80 .u-column1 h2,
			body.page-id-80 .u-column2 h2,
			body.page-id-80 .woocommerce-MyAccount-content h2 { display: none !important; }
			body.page-id-80 .u-column2::before {
				content: "Create Account";
				display: block;
				font-size: 1.75rem;
				font-weight: 700;
				color: #3D0C02;
				text-align: center;
				margin-bottom: 1.5rem;
			}
		</style>';
		echo '<script>document.addEventListener("DOMContentLoaded",function(){var r=document.querySelector(".woocommerce-form-register");if(r)r.style.display="block";var l=document.querySelector(".woocommerce-form-login");if(l&&!document.querySelector("#customer_login"))l.style.display="none"});</script>';
	}
});

// Redirect Register page to show registration
add_action( 'template_redirect', function () {
	if ( is_page( 80 ) && empty( $_GET['action'] ) ) {
		// Set flag for the shortcode
		add_filter( 'woocommerce_my_account_get_endpoint_url', '__return_empty_string', 100 );
	}
});

// Replace empty cart icon with SadFace image
add_filter( 'render_block', function ( $content, $block ) {
	if ( isset( $block['blockName'] ) && $block['blockName'] === 'woocommerce/empty-cart-block' ) {
		$content = str_replace( 'with-empty-cart-icon', '', $content );
	}

	if ( isset( $block['blockName'] ) && $block['blockName'] === 'woocommerce/product-new' ) {
		// Force all product images to same height
		$content = preg_replace(
			'/<img\s/',
			'<img style="height:260px;width:100%;object-fit:cover;" ',
			$content
		);
		// Strip all but first 4 product cards
		$count = 0;
		$content = preg_replace_callback(
			'/<li class="wc-block-grid__product".*?<\/li>/s',
			function ( $m ) use ( &$count ) {
				$count++;
				return $count <= 4 ? $m[0] : '';
			},
			$content
		);
	}

	return $content;
}, 10, 2 );

// Header nav position fix
add_action('wp_head', function(){
    /**
	 * Dashboard layout CSS — Applied via wp_head when user is logged in on
	 * the My Account page. Creates a flexbox sidebar layout with:
	 * - 200px fixed nav sidebar pinned to the left edge (negative margin).
	 * - Flexible content area with white card background and rounded corners.
	 * - Hides the stray login form wrapper (u-columns#customer_login).
	 * - Makes order table text smaller for better fit.
	 */
    if (is_user_logged_in() && (is_page(9) || is_account_page())) {
        echo '<style>
.woocommerce-account .woocommerce { max-width: 100% !important; transform: translateX(-200px); }
.xnav2 { display: flex !important; flex-direction: row !important; align-items: flex-start !important; width: 100% !important; }
.xnav2s { flex: 0 0 200px !important; margin-left: -200px; margin-right: 24px; }
.xnav2s .woocommerce-MyAccount-navigation { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 10px rgba(61,12,2,.06); width: 100% !important; float: none !important; height: 100%; }
.xnav2m { flex: 1 !important; min-width: 0; margin-left: 200px; margin-right: -200px; background: #fff; border-radius: 12px; padding: 2rem 2.5rem; width: auto !important; overflow-wrap: break-word !important; word-break: break-word !important; }
.woocommerce-orders-table { font-size: 0.85rem !important; }
.woocommerce-orders-table td, .woocommerce-orders-table th { padding: 0.5rem 0.75rem !important; white-space: nowrap !important; }
.woocommerce-orders-table__cell-order-actions .button { font-size: 0.8rem !important; padding: 4px 12px !important; }
body.ttt-logged-in.page-id-9 .u-columns#customer_login { display: none !important; }
.xnav2 { position: relative !important; }
</style>';
    }
});

// Hide register/login link on page content when logged in
add_filter( 'the_content', function( $content ) {
    if ( is_page( 9 ) && is_user_logged_in() ) {
        $content = preg_replace( '/<p[^>]*>Don.*?have an account\?.*?<\\/p>/i', '', $content );
    }
    if ( is_page( 80 ) && is_user_logged_in() ) {
        $content = preg_replace( '/<p[^>]*>Already have an account\?.*?<\\/p>/i', '', $content );
    }
    return $content;
}, 99 );

// Hide WooCommerce notices on cart page
add_action('wp_head', function(){
    if (is_cart()) {
        echo '<style>body.woocommerce-cart .woocommerce-message,body.woocommerce-cart .woocommerce-info,body.woocommerce-cart .woocommerce-error,body.woocommerce-cart .woocommerce-notices-wrapper{display:none!important}</style>';
    }
});

// Header nav position lock
add_action('wp_head', function(){
    /**
	 * Header navigation position lock — Positions the main nav (Shop/Cart/Account)
	 * absolutely at right:80px within the header row. This prevents the nav from
	 * shifting between pages when the mini-cart or other header elements change size.
	 */
    echo '<style>
header .wp-block-group.alignwide { position: relative !important; }
header .wp-block-navigation { position: absolute !important; right: 80px !important; top: 50% !important; transform: translateY(-50%) !important; }
.home .wp-site-blocks > * + * { margin-block-start: 0 !important; }
</style>';
});

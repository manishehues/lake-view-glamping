<?php
/**
 * Plugin Name: WP Hotel Booking Stripe Payment
 * Plugin URI: http://thimpress.com/
 * Description: Stripe payment gateway for WP Hotel Booking
 * Author: ThimPress
 * Version: 1.7.7
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * Author URI: http://thimpress.com
 * Tags: wphb
 */

define( 'TP_HB_STRIPE_DIR', plugin_dir_path( __FILE__ ) );
define( 'TP_HB_STRIPE_URI', plugins_url( '', __FILE__ ) );
//define( 'TP_HB_STRIPE_VER', '1.7.6' );

if ( ! class_exists( 'WP_Hotel_Booking_Payment_Stripe' ) ) {
	/**
	 * Class WP_Hotel_Booking_Payment_Stripe
	 */
	class WP_Hotel_Booking_Payment_Stripe {

		/**
		 * @var bool
		 */
		public $is_hotel_active = false;

		/**
		 * @var string
		 */
		public $slug = 'stripe';

		/**
		 * WP_Hotel_Booking_Payment_Stripe constructor.
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'is_hotel_active' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Check hotel booking activated.
		 */
		public function is_hotel_active() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( ( class_exists( 'TP_Hotel_Booking' ) && is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) ) || ( is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && class_exists( 'WP_Hotel_Booking' ) ) ) {
				$this->is_hotel_active = true;
			}

			if ( ! $this->is_hotel_active ) {
				add_action( 'admin_notices', array( $this, 'add_notices' ) );
			} else {
				// add payment
				add_filter( 'hb_payment_gateways', array( $this, 'add_payment_classes' ) );
				if ( $this->is_hotel_active ) {
					require_once TP_HB_STRIPE_DIR . '/inc/class-hb-payment-gateway-stripe.php';
				}
			}

			$this->load_text_domain();
		}

		/**
		 * Load text domain.
		 */
		public function load_text_domain() {
			$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-stripe-' . get_locale() . '.mo';
			$plugin_file = TP_HB_STRIPE_DIR . '/languages/wp-hotel-booking-stripe-' . get_locale() . '.mo';
			if ( file_exists( $default ) ) {
				$file = $default;
			} else {
				$file = $plugin_file;
			}
			if ( $file ) {
				load_textdomain( 'wp-hotel-booking-stripe', $file );
			}
		}

		/**
         * Filter callback add payments.
         *
		 * @param $payments
		 *
		 * @return mixed
		 */
		public function add_payment_classes( $payments ) {
			if ( array_key_exists( $this->slug, $payments ) ) {
				return $payments;
			}

			$payments[ $this->slug ] = new HB_Payment_Gateway_Stripe();

			return $payments;
		}

		/**
		 * Notices missing WP Hotel Booking plugin.
		 */
		public function add_notices() {?>
            <div class="error">
                <p><?php _e( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Stripe Payment</strong> add-on.' ); ?></p>
            </div>
			<?php
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			if ( ! class_exists( 'WPHB_Settings' ) ) {
				return;
			}
			// stripe and checkout assets
			wp_register_script( 'tp-hotel-booking-stripe-js', 'https://checkout.stripe.com/checkout.js', array() );
			wp_register_script( 'tp-hotel-booking-stripe-checkout-js', TP_HB_STRIPE_URI . '/assets/js/checkout.js', array() );
			wp_register_script( 'tp-hotel-booking-stripe-custom-checkout-js', TP_HB_STRIPE_URI . '/assets/js/custom.js', array('custom_stripe', 'jquery') );

			$setting = WPHB_Settings::instance()->get( 'stripe' );

			if ( ! empty( $setting['enable'] ) && $setting['enable'] == 'on' ) {
				// stripe
				$test_publish_key = $setting['test_publish_key'];
				$live_publish_key = $setting['live_publish_key'];
				$publish_key = $setting['test_mode'] == 'on' ? $test_publish_key : $live_publish_key;

				wp_enqueue_script( 'custom_stripe', 'https://js.stripe.com/v3/', '', '3.0', true );
				wp_enqueue_script( 'tp-hotel-booking-stripe-js' );
				wp_enqueue_script( 'tp-hotel-booking-stripe-checkout-js' );
				wp_enqueue_script( 'tp-hotel-booking-stripe-custom-checkout-js' );

				$data = array(
					'publish_key' => $publish_key,
					'button_verify' => esc_html__( 'Checkout', 'wp-hotel-booking-stripe' ),
					'error_verify' => esc_html__( 'Unable to process this payment, please try again or use alternative method.', 'wp-hotel-booking-stripe' )
				);

				wp_localize_script( 'tp-hotel-booking-stripe-js', 'hotel_booking_stripe_params', $data );
			}
		}
	}
}

new WP_Hotel_Booking_Payment_Stripe();

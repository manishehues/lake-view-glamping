<?php
/**
 * HB_WPML_Support
 *
 * @author   ThimPress
 * @package  WP-Hotel-Booking/Stripe/Classes
 * @version  1.7.3
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'HB_Payment_Gateway_Stripe' ) ) {
	/**
	 * Class HB_Payment_Gateway_Stripe
	 */
	class HB_Payment_Gateway_Stripe extends WPHB_Payment_Gateway_Base {

		/**
		 * @var array
		 */
		protected $_settings = array();

		/**
		 * @var string
		 */
		public $slug = 'stripe';

		/**
		 * @var string
		 */
		protected $_api_endpoint = '';

		/**
		 * protected strip secret
		 */
		protected $_stripe_secret = null;

		/**
		 * @var mixed|null
		 */
		protected $_stripe_publish = null;

		/**
		 * @var null
		 */
		protected $_stripe = null;

		/**
		 * Request Token
		 *
		 * @var string
		 */
		protected $source = null;
		
		// /**
		//  * @var null
		//  */
		// protected $intent_id = null;

		/**
		 * HB_Payment_Gateway_Stripe constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->_title       = __( 'Stripe', 'wp-hotel-booking-stripe' );
			$this->_description = __( 'Pay with Stripe', 'wp-hotel-booking-stripe' );
			$this->_settings    = maybe_unserialize( WPHB_Settings::instance()->get( 'stripe' ) );

			$debug = ( ! isset( $this->_settings['test_mode'] ) || $this->_settings['test_mode'] === 'on' ) ? true : false;
			if ( ! isset( $this->_settings['test_secret_key'] ) || ! $this->_settings['test_secret_key'] ) {
				$this->_settings['test_secret_key'] = 'sk_test_NRayUQ1DIth4X091iEH9qzaq';
			}

			if ( ! isset( $this->_settings['test_publish_key'] ) || ! $this->_settings['test_publish_key'] ) {
				$this->_settings['test_publish_key'] = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';
			}

			if ( ! isset( $this->_settings['live_secret_key'] ) || ! $this->_settings['live_secret_key'] ) {
				$this->_settings['live_secret_key'] = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';
			}

			if ( ! isset( $this->_settings['live_publish_key'] ) || ! $this->_settings['live_publish_key'] ) {
				$this->_settings['live_publish_key'] = 'pk_live_n5AVJxHj8XSFV4HsPIaiFgo3';
			}

			$this->_stripe_secret  = $debug ? $this->_settings['test_secret_key'] : $this->_settings['live_secret_key'];
			$this->_stripe_publish = ( ! isset( $this->_settings['test_mode'] ) || $this->_settings['test_mode'] === 'on' ) ? $this->_settings['test_publish_key'] : $this->_settings['live_publish_key'];

			add_action( 'hotel_booking_before_checkout_form', array( $this, 'htb_stripe_publish_key' ) );

			add_action('wp_ajax_verify_intent_after_checkout', array($this,'verify_intent_after_checkout'));

			add_action('wp_ajax_nopriv_verify_intent_after_checkout',array($this,'verify_intent_after_checkout'));

			$this->_api_endpoint = 'https://api.stripe.com/v1';
			$this->init();
		}

		/**
		 * Init.
		 */
		public function init() {
			add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
		}

		/**
		 * Admin settings.
		 */
		public function admin_settings() {
			include_once TP_HB_STRIPE_DIR . '/inc/views/strip-settings.php';
		}

		/**
		 * @return bool
		 */
		public function is_enable() {
			return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
		}

		/**
		 * @param null $booking_id
		 *
		 * @return array
		 */
		public function process_checkout( $booking_id = null ) {

			$cart    = WPHB_Cart::instance();

			$booking = WPHB_Booking::instance( $booking_id );

			$advance_pay = (float) $cart->get_advance_payment();
			
			$customer_id = $this->add_customer( $booking_id );

			$order = str_replace('#','',$booking->get_booking_number()) ;

			$request = array(
				'source' =>$this->source,
				'amount'      => round( $advance_pay * 100 ),
				'currency'    => hb_get_currency(),
				'customer'    => $customer_id,
				'description' => sprintf(
					__( '%s - Order %s ', 'wp-hotel-booking-stripe' ),
					bloginfo('title'),
					$order
				),
				'metadata'=>array(
					'customer_name'=>hb_get_customer_fullname($booking_id,true),
					'customer_email'=>$booking->customer_email,
					'order_id'=> $order,
				)
			);

			$response = $this->stripe_request( $request , 'payment_intents');

			$pi = isset( $response->id ) ? $response->id : '';

			if ( ! empty( $pi ) ) {

				$intent = $this->confim_payment_stripe( $pi );
				
				$intent_id = $intent->id;

				if ( is_wp_error( $intent ) ) {
					$return = array(
						'result'  => 'error',
						'message' => sprintf( __( '%s. Please try again', 'wp-hotel-booking-stripe' ), $intent->get_error_message() )
					);
				} else {
					if ( 'requires_action'=== $intent->status ) {

						$nonce = wp_create_nonce( 'hb_stripe_confirm_pi' );

						$sca_verification_url = admin_url('admin-ajax.php?action=verify_intent_after_checkout&order='.$order.'&nonce='.$nonce.'&indent_id='.$intent_id.'&advance_pay='.$advance_pay.'');
						
						$redirect = sprintf( '#confirm-pi-%s:%s', $intent->client_secret, rawurlencode($sca_verification_url));

						$return = array(
							'result'   => 'success',
							'redirect' => $redirect,
						);

					} elseif ('succeeded' === $intent->status ) {
						if ( (float) $advance_pay == (float) $booking->total() ) {
							$booking->update_status( 'completed' );
						} else {
							$booking->update_status( 'processing' );
						}
						$cart->empty_cart();
						$return = array(
							'result'  => 'success',
							'redirect' => hb_get_thank_you_url( $booking_id, $booking->booking_key )
						);
					}
				}
			}

			return $return;
		}
		public function verify_intent_after_checkout() {

			if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'hb_stripe_confirm_pi' ) ) {
				$result = array(
					'result'   => 'fail',
		 			'message'  => esc_html__( 'Error: Verify nonce error!', 'wp-hotel-booking-stripe' ),
				);

			 	wp_send_json( $result );
			}
			$cart    = WPHB_Cart::instance();

			if ( isset( $_GET['advance_pay'] )){
				$advance_pay = $_GET['advance_pay'];
			}

			if ( isset( $_GET['order'] ) && absint( $_GET['order'] ) ) {
				$booking_id = $_GET['order'];
				$booking = WPHB_Booking::instance($booking_id);
			}

			if ( isset( $_GET['indent_id'] )){
				$intent_id = $_GET['indent_id'];
			}
			
			$intent = $this->stripe_request( array(), 'payment_intents/' . $intent_id, 'GET' );
			
			if ( ! $intent ) {
			 	$result = array(
					'result'   => 'fail',
			 		'message'  => esc_html__( 'Error: Can\'t get Intent!', 'wp-hotel-booking-stripe' ),
				);

				wp_send_json( $result );
			}

			clean_post_cache( $booking_id );

			if ( isset( $intent->object ) && 'payment_intent' === $intent->object && isset( $intent->status ) && 'succeeded' === $intent->status ) {

				if ( (float) $advance_pay == (float) $booking->total() ) {
					$booking->update_status( 'completed' );
				} else {
					$booking->update_status( 'processing' );
				}
				$cart->empty_cart();
				$result = array(
					'result'  => 'success',
					'redirect' => hb_get_thank_you_url( $booking_id , $booking->booking_key ),
				);
				
			} else {
				 $message = isset( $intent->last_payment_error->message ) ? $intent->last_payment_error->message : esc_html__( 'Unable to process this payment, please try again or use alternative method.', 'wp-hotel-booking-stripe' );

				 $result = array(
				 	'result'   => 'fail',
					'message'  => $message,
				);
			}
			wp_send_json( $result );
		}

		public function confim_payment_stripe( $pi ) {
			$confim = $this->stripe_request(
				array(
					'source' => $this->source
				),
				'payment_intents/' . $pi . '/confirm'
			);

			return $confim;
		}

		/**
		 * @param null $booking_id
		 * @param null $customer
		 *
		 * @return mixed|null|string|WP_Error
		 */
		public function add_customer( $booking_id = null, $customer = null ) {

			$booking = WPHB_Booking::instance( $booking_id );

			$user_id = $booking->user_id;
			$cus_id  = null;

			// no such customer issue

			//			if ( $user_id ) {
			//				$cus_id = get_user_meta( $user_id, 'tp-hotel-booking-stripe-id', true );
			//			} else {
			//				global $wpdb;
			//				$sql    = $wpdb->prepare( "
			//                    SELECT pm.meta_value FROM $wpdb->postmeta AS pm
			//                        INNER JOIN $wpdb->postmeta AS pm2 ON pm2.post_id = pm.post_id
			//                    WHERE
			//                        pm.meta_key = %s
			//                        AND pm.meta_value = %s
			//                        LIMIT 1
			//                ", 'tp-hotel-booking-stripe-id', $booking->customer_email );
			//				$cus_id = $wpdb->get_var( $sql );
			//			}

			if ( ! $cus_id ) {
				// create customer
				try {
					$convert = array(
						'type' =>'card',
						'token'      => sanitize_text_field( $_POST['id'] ),
					);
					$test_convert = $this->stripe_request( $convert, 'sources' );

					$this->source = $test_convert->id;

					$params   = array(
						'description' => sprintf( '%s', $booking->customer_email ),
						'source'      => $this->source // token get by stripe.js
					);
					$response = $this->stripe_request( $params, 'customers' );
					$cus_id   = $response->id;
					if ( $user_id ) {
						update_user_meta( $user_id, 'tp-hotel-booking-stripe-id', $cus_id );
					} else {
						update_post_meta( $booking_id, 'tp-hotel-booking-stripe-id', $cus_id );
					}
					add_post_meta( $customer, 'tp-hotel-booking-stripe-id', $cus_id );

					return $cus_id;
				}
				catch ( Exception $e ) {
					return new WP_Error( 'tp-hotel-booking-stripe-error', sprintf( '%s', $e->getMessage() ) );
				}
			}

			return $cus_id;
		}

		/**
		 * @param        $request
		 * @param string $api
		 *
		 * @return array|mixed|object|WP_Error
		 */
		public function stripe_request( $request, $api = 'charges' ) {
			$response = wp_remote_post( $this->_api_endpoint . '/' . $api, array(
				'method'     => 'POST',
				'headers'    => array(
					'Authorization' => 'Basic ' . base64_encode( $this->_stripe_secret . ':' )
				),
				'body'       => $request,
				'timeout'    => 70,
				'sslverify'  => false,
				'user-agent' => 'WP Hotel Booking ' . WPHB_VERSION
			) );

			if ( is_wp_error( $response ) ) {
				return new WP_Error( 'stripe_error', __( 'There was a problem connecting to the payment gateway.', 'wp-hotel-booking-stripe' ) );
			}
			if ( empty( $response['body'] ) ) {
				return new WP_Error( 'stripe_error', __( 'Empty response.', 'wp-hotel-booking-stripe' ) );
			}

			$parsed_response = json_decode( $response['body'] );
			// Handle response
			if ( ! empty( $parsed_response->error ) ) {
				return new WP_Error( 'stripe_error', $parsed_response->error->message );
			} elseif ( empty( $parsed_response->id ) ) {
				return new WP_Error( 'stripe_error', __( 'Invalid response.', 'wp-hotel-booking-stripe' ) );
			}

			return $parsed_response;
		}

		/**
		 * Global js.
		 */
		public function htb_stripe_publish_key() {
			//echo '<input type="hidden" name="htb_stripe_secret" value="'.$this->_stripe_secret.'">';
			echo '<input type="hidden" name="htb_stripe_publish" value="' . $this->_stripe_publish . '">';
		}

		/**
		 * Form payment.
		 */
		public function form() {
			_e( 'Pay with Credit card', 'wp-hotel-booking-stripe' );
		}
	}
}


add_filter( 'hb_payment_gateways', 'hotel_booking_payment_stripe' );

if ( ! function_exists( 'hotel_booking_payment_stripe' ) ) {
	/**
	 * @param $payments
	 *
	 * @return mixed
	 */
	function hotel_booking_payment_stripe( $payments ) {
		if ( array_key_exists( 'stripe', $payments ) ) {
			return $payments;
		}

		$payments['stripe'] = new HB_Payment_Gateway_Stripe();

		return $payments;
	}
}
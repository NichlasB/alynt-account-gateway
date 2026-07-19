<?php
/**
 * WooCommerce test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'wc_get_orders' ) ) {
	function wc_get_orders( $args = array() ) {
		$GLOBALS['alynt_ag_test_wc_get_orders_args'][] = $args;

		return isset( $GLOBALS['alynt_ag_test_wc_orders'] )
			? $GLOBALS['alynt_ag_test_wc_orders']
			: array();
	}
}

if ( ! function_exists( 'wc_get_customer_available_downloads' ) ) {
	function wc_get_customer_available_downloads( $user_id ) {
		$GLOBALS['alynt_ag_test_wc_available_download_calls'][] = absint( $user_id );

		return isset( $GLOBALS['alynt_ag_test_wc_available_downloads'] )
			? $GLOBALS['alynt_ag_test_wc_available_downloads']
			: array();
	}
}

if ( ! class_exists( 'WC_Payment_Tokens' ) ) {
	class WC_Payment_Tokens {

		/**
		 * Return configured payment-token fixtures.
		 *
		 * @param int $user_id WordPress user ID.
		 * @return array<int,object>
		 */
		public static function get_customer_tokens( $user_id ) {
			$GLOBALS['alynt_ag_test_wc_payment_token_calls'][] = absint( $user_id );

			return isset( $GLOBALS['alynt_ag_test_wc_payment_tokens'] )
				? $GLOBALS['alynt_ag_test_wc_payment_tokens']
				: array();
		}
	}
}

if ( ! function_exists( 'wc_get_account_formatted_address' ) ) {
	function wc_get_account_formatted_address( $type = 'billing', $user_id = 0 ) {
		$key = sanitize_key( $type ) . ':' . absint( $user_id );
		$GLOBALS['alynt_ag_test_wc_formatted_address_calls'][] = array(
			'type'    => sanitize_key( $type ),
			'user_id' => absint( $user_id ),
		);

		return isset( $GLOBALS['alynt_ag_test_wc_formatted_addresses'][ $key ] )
			? $GLOBALS['alynt_ag_test_wc_formatted_addresses'][ $key ]
			: '';
	}
}

if ( ! function_exists( 'wc_get_order_status_name' ) ) {
	function wc_get_order_status_name( $status ) {
		$statuses = isset( $GLOBALS['alynt_ag_test_wc_order_statuses'] )
			? $GLOBALS['alynt_ag_test_wc_order_statuses']
			: array();

		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : ucfirst( (string) $status );
	}
}

if ( ! function_exists( 'wc_format_datetime' ) ) {
	function wc_format_datetime( $date, $format = '' ) {
		$GLOBALS['alynt_ag_test_wc_format_datetime_calls'][] = array(
			'date'   => $date,
			'format' => $format,
		);

		return date_i18n( $format, $date->getTimestamp() );
	}
}

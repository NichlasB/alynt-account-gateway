<?php
/**
 * WooCommerce customer data.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalizes WooCommerce customer data for dashboard renderers.
 */
class ALYNT_AG_WooCommerce_Customer_Data extends ALYNT_AG_Service_Collaborator {

	/**
	 * Return a small normalized list of a customer's recent orders.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum orders to return.
	 * @return array<int,array<string,mixed>>
	 */
	public function recent_orders( $user_id, $limit = 3 ) {
		$user_id = absint( $user_id );
		$limit   = max( 1, min( 5, absint( $limit ) ) );

		if ( ! $user_id || ! function_exists( 'wc_get_orders' ) ) {
			return array();
		}

		$orders = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'limit'       => $limit,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'return'      => 'objects',
			)
		);

		if ( ! is_array( $orders ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $orders as $order ) {
			if (
				! is_object( $order )
				|| ! method_exists( $order, 'get_id' )
				|| ! method_exists( $order, 'get_order_number' )
				|| ! method_exists( $order, 'get_status' )
			) {
				continue;
			}

			$order_id = absint( $order->get_id() );
			if ( ! $order_id ) {
				continue;
			}

			$status         = sanitize_key( $order->get_status() );
			$date           = method_exists( $order, 'get_date_created' ) ? $order->get_date_created() : null;
			$total          = method_exists( $order, 'get_formatted_order_total' )
				? html_entity_decode( wp_strip_all_tags( $order->get_formatted_order_total() ), ENT_QUOTES, 'UTF-8' )
				: '';
			$formatted_date = '';
			if ( is_object( $date ) && method_exists( $date, 'getTimestamp' ) ) {
				$formatted_date = function_exists( 'wc_format_datetime' )
					? wc_format_datetime( $date, get_option( 'date_format', 'F j, Y' ) )
					: date_i18n( get_option( 'date_format', 'F j, Y' ), $date->getTimestamp() );
			}

			$normalized[] = array(
				'id'     => $order_id,
				'number' => sanitize_text_field( $order->get_order_number() ),
				'status' => function_exists( 'wc_get_order_status_name' )
					? sanitize_text_field( wc_get_order_status_name( $status ) )
					: sanitize_text_field( ucfirst( str_replace( '-', ' ', $status ) ) ),
				'date'   => sanitize_text_field( $formatted_date ),
				'total'  => sanitize_text_field( $total ),
			);
		}

		return $normalized;
	}

	/**
	 * Return normalized available downloads for a customer.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum downloads to return.
	 * @return array<int,array<string,mixed>>
	 */
	public function available_downloads( $user_id, $limit = 3 ) {
		$user_id = absint( $user_id );
		$limit   = max( 1, min( 5, absint( $limit ) ) );

		if ( ! $user_id || ! function_exists( 'wc_get_customer_available_downloads' ) ) {
			return array();
		}

		$downloads = wc_get_customer_available_downloads( $user_id );
		if ( ! is_array( $downloads ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $downloads as $download ) {
			if ( ! is_array( $download ) ) {
				continue;
			}

			$url          = isset( $download['download_url'] ) ? esc_url_raw( $download['download_url'] ) : '';
			$name         = isset( $download['download_name'] ) ? sanitize_text_field( $download['download_name'] ) : '';
			$product_name = isset( $download['product_name'] ) ? sanitize_text_field( $download['product_name'] ) : '';

			if ( '' === $url || ( '' === $name && '' === $product_name ) ) {
				continue;
			}

			$timestamp = ! empty( $download['access_expires'] )
				? strtotime( (string) $download['access_expires'] )
				: false;
			$expires   = $timestamp
				? date_i18n( get_option( 'date_format', 'F j, Y' ), $timestamp )
				: '';

			$normalized[] = array(
				'name'         => '' !== $name ? $name : $product_name,
				'product_name' => $product_name,
				'url'          => $url,
				'remaining'    => isset( $download['downloads_remaining'] ) && is_numeric( $download['downloads_remaining'] )
					? max( 0, (int) $download['downloads_remaining'] )
					: null,
				'expires'      => sanitize_text_field( $expires ),
			);

			if ( count( $normalized ) >= $limit ) {
				break;
			}
		}

		return $normalized;
	}

	/**
	 * Return normalized account-summary data for a customer.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<string,mixed>
	 */
	public function account_details( $user_id ) {
		$user_id = absint( $user_id );
		$user    = $user_id ? get_userdata( $user_id ) : false;

		if ( ! $user instanceof WP_User ) {
			return array();
		}

		$first_name = sanitize_text_field( get_user_meta( $user_id, 'first_name', true ) );
		$last_name  = sanitize_text_field( get_user_meta( $user_id, 'last_name', true ) );
		$email      = sanitize_email( $user->user_email );
		$registered = isset( $user->user_registered ) ? strtotime( (string) $user->user_registered ) : false;

		return array(
			'name'         => trim( $first_name . ' ' . $last_name ),
			'email'        => $email,
			'member_since' => $registered
				? sanitize_text_field( date_i18n( get_option( 'date_format', 'F j, Y' ), $registered ) )
				: '',
			'is_complete'  => '' !== $first_name && '' !== $last_name && '' !== $email,
		);
	}

	/**
	 * Return normalized saved payment-method display data.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum payment methods to return.
	 * @return array<int,array<string,mixed>>
	 */
	public function saved_payment_methods( $user_id, $limit = 3 ) {
		$user_id = absint( $user_id );
		$limit   = max( 1, min( 5, absint( $limit ) ) );

		if ( ! $user_id || ! class_exists( 'WC_Payment_Tokens' ) || ! method_exists( 'WC_Payment_Tokens', 'get_customer_tokens' ) ) {
			return array();
		}

		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id );
		if ( ! is_array( $tokens ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $tokens as $token ) {
			if ( ! is_object( $token ) || ! method_exists( $token, 'get_display_name' ) ) {
				continue;
			}

			$display_name = html_entity_decode(
				wp_strip_all_tags( (string) $token->get_display_name() ),
				ENT_QUOTES,
				'UTF-8'
			);
			$display_name = sanitize_text_field( $display_name );

			if ( '' === $display_name ) {
				continue;
			}

			$normalized[] = array(
				'display_name' => $display_name,
				'is_default'   => method_exists( $token, 'is_default' ) && (bool) $token->is_default(),
			);

			if ( count( $normalized ) >= $limit ) {
				break;
			}
		}

		return $normalized;
	}

	/**
	 * Return normalized billing and shipping address lines.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<string,array<int,string>>
	 */
	public function saved_addresses( $user_id ) {
		$addresses = array(
			'billing'  => array(),
			'shipping' => array(),
		);
		$user_id   = absint( $user_id );

		if ( ! $user_id || ! function_exists( 'wc_get_account_formatted_address' ) ) {
			return $addresses;
		}

		foreach ( array_keys( $addresses ) as $type ) {
			$formatted = wc_get_account_formatted_address( $type, $user_id );
			if ( ! is_string( $formatted ) || '' === trim( $formatted ) ) {
				continue;
			}

			$with_lines = preg_replace( '#<br\s*/?>#i', "\n", $formatted );
			$plain      = html_entity_decode( wp_strip_all_tags( (string) $with_lines ), ENT_QUOTES, 'UTF-8' );
			$plain      = str_replace( "\xc2\xa0", ' ', $plain );
			$lines      = preg_split( '/\r\n|\r|\n/', $plain );

			if ( ! is_array( $lines ) ) {
				continue;
			}

			$addresses[ $type ] = array_values(
				array_filter(
					array_map( 'sanitize_text_field', $lines ),
					static function ( $line ) {
						return '' !== $line;
					}
				)
			);
		}

		return $addresses;
	}
}

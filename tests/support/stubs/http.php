<?php
/**
 * Mail and HTTP test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'wp_mail' ) ) {
	function wp_mail( $to, $subject, $message, $headers = array() ) {
		$GLOBALS['alynt_ag_test_mail'][] = array(
			'to'      => $to,
			'subject' => $subject,
			'message' => $message,
			'headers' => $headers,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_remote_post' ) ) {
	function wp_remote_post( $url, $args = array() ) {
		$GLOBALS['alynt_ag_test_remote_posts'][] = array(
			'url'  => $url,
			'args' => $args,
		);

		if ( isset( $GLOBALS['alynt_ag_test_remote_post_response'] ) ) {
			return $GLOBALS['alynt_ag_test_remote_post_response'];
		}

		return array(
			'body'     => '{"success":true}',
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
		);
	}
}

if ( ! function_exists( 'wp_safe_remote_post' ) ) {
	function wp_safe_remote_post( $url, $args = array() ) {
		$GLOBALS['alynt_ag_test_safe_remote_posts'][] = array(
			'url'  => $url,
			'args' => $args,
		);

		return wp_remote_post( $url, $args );
	}
}

if ( ! function_exists( 'wp_remote_get' ) ) {
	function wp_remote_get( $url, $args = array() ) {
		$GLOBALS['alynt_ag_test_remote_gets'][] = array(
			'url'  => $url,
			'args' => $args,
		);

		if ( isset( $GLOBALS['alynt_ag_test_remote_get_response'] ) ) {
			return $GLOBALS['alynt_ag_test_remote_get_response'];
		}

		return array( 'body' => '{"status":"safe"}' );
	}
}

if ( ! function_exists( 'disabled' ) ) {
	function disabled( $disabled, $current = true, $display = true ) {
		$result = $disabled === $current ? ' disabled="disabled"' : '';

		if ( $display ) {
			echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Test stub mirrors WordPress core.
		}

		return $result;
	}
}

if ( ! function_exists( 'wp_remote_retrieve_body' ) ) {
	function wp_remote_retrieve_body( $response ) {
		return isset( $response['body'] ) ? $response['body'] : '';
	}
}

if ( ! function_exists( 'wp_remote_retrieve_response_code' ) ) {
	function wp_remote_retrieve_response_code( $response ) {
		return isset( $response['response']['code'] ) ? (int) $response['response']['code'] : 0;
	}
}

if ( ! function_exists( 'wp_remote_retrieve_response_message' ) ) {
	function wp_remote_retrieve_response_message( $response ) {
		return isset( $response['response']['message'] ) ? $response['response']['message'] : '';
	}
}

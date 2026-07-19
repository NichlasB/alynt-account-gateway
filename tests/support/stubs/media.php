<?php
/**
 * WordPress media test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {
		$key = (int) $attachment_id . ':' . (string) $size;
		if ( isset( $GLOBALS['alynt_ag_test_attachment_urls'][ $key ] ) ) {
			return $GLOBALS['alynt_ag_test_attachment_urls'][ $key ];
		}

		return '';
	}
}

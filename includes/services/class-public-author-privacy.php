<?php
/**
 * Public author privacy service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keeps frontend comment and review author names privacy-safe.
 */
class ALYNT_AG_Public_Author_Privacy {

	/**
	 * Register public author privacy hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'get_comment_author', array( $this, 'filter_comment_author' ), 10, 3 );
		add_filter( 'preprocess_comment', array( $this, 'filter_preprocess_comment' ), 10, 1 );
		add_filter( 'wp_insert_comment_data', array( $this, 'filter_insert_comment_data' ), 10, 2 );
	}

	/**
	 * Filter frontend comment/review author display names.
	 *
	 * @param string      $author     Current author name.
	 * @param int         $comment_id Comment ID.
	 * @param object|null $comment    Comment object.
	 * @return string
	 */
	public function filter_comment_author( $author, $comment_id = 0, $comment = null ) {
		unset( $comment_id );

		if ( $this->is_admin_context() ) {
			return $author;
		}

		$user_id = $this->comment_user_id( $comment );
		if ( ! $user_id ) {
			return $author;
		}

		$formatted = $this->formatted_public_name_for_user( $user_id );

		return '' !== $formatted ? $formatted : $author;
	}

	/**
	 * Make newly submitted logged-in comment/review author names privacy-safe.
	 *
	 * @param array<string,mixed> $comment_data Comment data.
	 * @return array<string,mixed>
	 */
	public function filter_preprocess_comment( $comment_data ) {
		if ( ! is_array( $comment_data ) ) {
			return $comment_data;
		}

		$user_id = $this->comment_user_id( $comment_data );
		if ( ! $user_id ) {
			return $comment_data;
		}

		$formatted = $this->formatted_public_name_for_user( $user_id );
		if ( '' !== $formatted ) {
			$comment_data['comment_author'] = $formatted;
		}

		return $comment_data;
	}

	/**
	 * Keep programmatic comment/review inserts privacy-safe when a user ID exists.
	 *
	 * @param array<string,mixed> $data        Prepared comment row.
	 * @param array<string,mixed> $commentarr  Raw comment array.
	 * @return array<string,mixed>
	 */
	public function filter_insert_comment_data( $data, $commentarr ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		$user_id = $this->comment_user_id( $commentarr );
		if ( ! $user_id ) {
			$user_id = $this->comment_user_id( $data );
		}

		if ( ! $user_id ) {
			return $data;
		}

		$formatted = $this->formatted_public_name_for_user( $user_id );
		if ( '' !== $formatted ) {
			$data['comment_author'] = $formatted;
		}

		return $data;
	}

	/**
	 * Return a public-safe name for a user according to settings.
	 *
	 * @param int $user_id User ID.
	 * @return string
	 */
	public function formatted_public_name_for_user( $user_id ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$format   = isset( $settings['public_author_name_format'] ) ? sanitize_key( $settings['public_author_name_format'] ) : 'first_last_initial';

		if ( 'wordpress_default' === $format ) {
			return '';
		}

		if ( 'customer' === $format ) {
			return __( 'Customer', 'alynt-account-gateway' );
		}

		$first_name = sanitize_text_field( get_user_meta( $user_id, 'first_name', true ) );
		$last_name  = sanitize_text_field( get_user_meta( $user_id, 'last_name', true ) );

		if ( '' === $first_name ) {
			return 'first_name' === $format ? '' : __( 'Customer', 'alynt-account-gateway' );
		}

		if ( 'first_name' === $format ) {
			return $first_name;
		}

		$initial = $this->last_initial( $last_name );

		return '' !== $initial ? sprintf( '%1$s %2$s.', $first_name, $initial ) : $first_name;
	}

	/**
	 * Extract a user ID from a comment-like value.
	 *
	 * @param mixed $comment Comment object or array.
	 * @return int
	 */
	private function comment_user_id( $comment ) {
		if ( is_object( $comment ) ) {
			$comment_vars = get_object_vars( $comment );
			if ( isset( $comment_vars['user_id'] ) ) {
				return absint( $comment_vars['user_id'] );
			}

			if ( isset( $comment_vars['user_ID'] ) ) {
				return absint( $comment_vars['user_ID'] );
			}
		}

		if ( is_array( $comment ) ) {
			if ( isset( $comment['user_id'] ) ) {
				return absint( $comment['user_id'] );
			}

			if ( isset( $comment['user_ID'] ) ) {
				return absint( $comment['user_ID'] );
			}
		}

		return 0;
	}

	/**
	 * Return the first character of the last name.
	 *
	 * @param string $last_name Last name.
	 * @return string
	 */
	private function last_initial( $last_name ) {
		$last_name = trim( $last_name );
		if ( '' === $last_name ) {
			return '';
		}

		if ( function_exists( 'mb_substr' ) ) {
			return mb_strtoupper( mb_substr( $last_name, 0, 1 ) );
		}

		return strtoupper( substr( $last_name, 0, 1 ) );
	}

	/**
	 * Return whether this is an admin request where raw names should remain visible.
	 *
	 * @return bool
	 */
	private function is_admin_context() {
		return function_exists( 'is_admin' ) && is_admin();
	}
}

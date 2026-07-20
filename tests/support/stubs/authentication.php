<?php
/**
 * Authentication and user test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'check_admin_referer' ) ) {
	function check_admin_referer( $action = -1, $query_arg = false ) {
		$GLOBALS['alynt_ag_test_admin_referer_checks'][] = array(
			'action'    => $action,
			'query_arg' => $query_arg,
		);

		if ( isset( $GLOBALS['alynt_ag_test_admin_nonce_valid'] ) && ! $GLOBALS['alynt_ag_test_admin_nonce_valid'] ) {
			wp_die( 'Invalid admin nonce.' );
		}

		return true;
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action = -1 ) {
		unset( $action );

		if ( isset( $GLOBALS['alynt_ag_test_nonce_valid'] ) ) {
			return $GLOBALS['alynt_ag_test_nonce_valid'] ? 1 : false;
		}

		return 1;
	}
}

if ( ! function_exists( 'is_ssl' ) ) {
	function is_ssl() {
		return false;
	}
}

if ( ! function_exists( 'wp_signon' ) ) {
	function wp_signon( $credentials = array(), $secure_cookie = '' ) {
		$GLOBALS['alynt_ag_test_signons'][] = array(
			'credentials'   => $credentials,
			'secure_cookie' => $secure_cookie,
		);

		$user = new WP_User( $credentials['user_login'] ?? 'customer@example.test' );

		if ( isset( $GLOBALS['alynt_ag_test_signon_roles'] ) && is_array( $GLOBALS['alynt_ag_test_signon_roles'] ) ) {
			$user->roles = $GLOBALS['alynt_ag_test_signon_roles'];
		}

		return $user;
	}
}

if ( ! function_exists( 'email_exists' ) ) {
	function email_exists( $email ) {
		if ( isset( $GLOBALS['alynt_ag_test_existing_emails'] ) && in_array( $email, $GLOBALS['alynt_ag_test_existing_emails'], true ) ) {
			return 123;
		}

		return false;
	}
}

if ( ! function_exists( 'retrieve_password' ) ) {
	function retrieve_password( $user_login = null ) {
		$GLOBALS['alynt_ag_test_retrieve_passwords'][] = $user_login;

		if ( isset( $GLOBALS['alynt_ag_test_retrieve_password_result'] ) ) {
			return $GLOBALS['alynt_ag_test_retrieve_password_result'];
		}

		return true;
	}
}

if ( ! function_exists( 'username_exists' ) ) {
	function username_exists( $username ) {
		return in_array( $username, array( '@User_Damon_Paulo', 'User_Damon_Paulo' ), true ) ? 1 : false;
	}
}

if ( ! function_exists( 'wp_create_user' ) ) {
	function wp_create_user( $username, $password, $email ) {
		$user_id = count( $GLOBALS['alynt_ag_test_created_users'] ) + 456;

		$GLOBALS['alynt_ag_test_created_users'][] = array(
			'ID'       => $user_id,
			'username' => $username,
			'password' => $password,
			'email'    => $email,
		);

		return $user_id;
	}
}

if ( ! function_exists( 'wp_update_user' ) ) {
	function wp_update_user( $userdata ) {
		$GLOBALS['alynt_ag_test_user_updates'][] = $userdata;

		if ( isset( $GLOBALS['alynt_ag_test_user_update_result'] ) ) {
			return $GLOBALS['alynt_ag_test_user_update_result'];
		}

		return isset( $userdata['ID'] ) ? (int) $userdata['ID'] : 0;
	}
}

if ( ! function_exists( 'wp_delete_user' ) ) {
	function wp_delete_user( $user_id ) {
		$GLOBALS['alynt_ag_test_deleted_users'][] = (int) $user_id;

		return isset( $GLOBALS['alynt_ag_test_user_delete_result'] ) ? $GLOBALS['alynt_ag_test_user_delete_result'] : true;
	}
}

if ( ! class_exists( 'WP_User' ) ) {
	class WP_User {
		public $ID;
		public $user_login;
		public $user_email;
		public $display_name;
		public $roles;
		public $user_registered;

		public function __construct( $login = 'damon@example.test' ) {
			$this->ID         = 123;
			$this->user_login = $login;
			$this->user_email = $login;
			$this->display_name = 'Damon Paulo';
			$this->roles = array( 'customer' );
			$this->user_registered = '2026-07-03 10:00:00';
		}
	}
}

if ( ! function_exists( 'get_user_meta' ) ) {
	function get_user_meta( $user_id, $key = '', $single = false ) {
		unset( $user_id, $single );

		if ( isset( $GLOBALS['alynt_ag_test_user_meta'][ $key ] ) ) {
			return $GLOBALS['alynt_ag_test_user_meta'][ $key ];
		}

		$values = array(
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
		);

		return $values[ $key ] ?? '';
	}
}

if ( ! function_exists( 'delete_user_meta' ) ) {
	function delete_user_meta( $user_id, $meta_key ) {
		$GLOBALS['alynt_ag_test_deleted_user_meta'][] = array(
			'user_id'  => $user_id,
			'meta_key' => $meta_key,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_get_current_user' ) ) {
	function wp_get_current_user() {
		return new WP_User( 'damon@example.test' );
	}
}

if ( ! function_exists( 'get_userdata' ) ) {
	function get_userdata( $user_id ) {
		if ( ! $user_id ) {
			return false;
		}

		$user = new WP_User( 'customer@example.test' );
		$user->ID = (int) $user_id;

		return $user;
	}
}

if ( ! function_exists( 'check_password_reset_key' ) ) {
	function check_password_reset_key( $key, $login ) {
		if ( 'bad-key' === $key || '' === $login ) {
			return new WP_Error( 'invalid_key', 'Invalid key.' );
		}

		return new WP_User( $login );
	}
}

if ( ! function_exists( 'reset_password' ) ) {
	function reset_password( $user, $new_pass ) {
		$GLOBALS['alynt_ag_test_reset_password'] = array(
			'user_login' => $user->user_login,
			'password'   => $new_pass,
		);
	}
}

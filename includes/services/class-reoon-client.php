<?php
/**
 * Reoon Email Verifier client placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps Reoon verification statuses.
 */
class ALYNT_AG_Reoon_Client {

	/**
	 * Return whether a Reoon status should be blocked by default.
	 *
	 * @param string $status Reoon status.
	 * @return bool
	 */
	public function is_blocked_status( $status ) {
		return in_array( $status, array( 'invalid', 'disabled', 'disposable', 'spamtrap' ), true );
	}

	/**
	 * Return whether a Reoon status should be allowed but flagged.
	 *
	 * @param string $status Reoon status.
	 * @return bool
	 */
	public function is_flagged_status( $status ) {
		return in_array( $status, array( 'catch_all', 'role_account', 'unknown', 'inbox_full' ), true );
	}
}

<?php
/**
 * Same-site return destination helpers.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates and normalizes destinations used after authentication.
 */
class ALYNT_AG_Return_Destination {

	/**
	 * Return a validated same-site destination as a relative path and query.
	 *
	 * @param string              $destination Candidate destination.
	 * @param array<string,mixed> $settings    Settings.
	 * @return string
	 */
	public function relative_path( $destination, $settings ) {
		$destination = is_scalar( $destination ) ? trim( esc_url_raw( wp_unslash( (string) $destination ) ) ) : '';
		if (
			'' === $destination
			|| 1 === preg_match( '/[\r\n]/', $destination )
			|| 0 === strpos( $destination, '//' )
		) {
			return '';
		}

		$parts = wp_parse_url( $destination );
		if ( ! is_array( $parts ) ) {
			return '';
		}

		$home_parts = wp_parse_url( home_url( '/' ) );
		if ( ! is_array( $home_parts ) ) {
			return '';
		}

		$has_authority = isset( $parts['host'] ) || isset( $parts['scheme'] ) || 0 === strpos( $destination, '//' );
		if ( $has_authority && ! $this->same_site( $parts, $home_parts ) ) {
			return '';
		}

		if ( ! $has_authority && '/' !== substr( $destination, 0, 1 ) ) {
			return '';
		}

		$path = isset( $parts['path'] ) && is_string( $parts['path'] ) ? $parts['path'] : '/';
		$path = $this->relative_to_home_path( $path, $home_parts );

		if ( $this->is_auth_surface( $path, $settings ) ) {
			return '';
		}

		$query = isset( $parts['query'] ) && is_string( $parts['query'] ) ? $parts['query'] : '';

		return $path . ( '' !== $query ? '?' . $query : '' );
	}

	/**
	 * Return a validated same-site absolute URL.
	 *
	 * @param string              $destination Candidate destination.
	 * @param array<string,mixed> $settings    Settings.
	 * @param string              $fallback    Fallback URL.
	 * @return string
	 */
	public function absolute_url( $destination, $settings, $fallback = '' ) {
		$relative = $this->relative_path( $destination, $settings );

		return $relative ? home_url( $relative ) : $fallback;
	}

	/**
	 * Convert a stored relative path into a validated absolute URL.
	 *
	 * @param string              $relative Relative path and optional query.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function from_stored_path( $relative, $settings ) {
		$relative = $this->relative_path( $relative, $settings );

		return $relative ? home_url( $relative ) : '';
	}

	/**
	 * Determine whether URL parts point to the same site as home_url().
	 *
	 * @param array<string,mixed> $candidate Candidate URL parts.
	 * @param array<string,mixed> $home      Home URL parts.
	 * @return bool
	 */
	private function same_site( $candidate, $home ) {
		$candidate_host   = isset( $candidate['host'] ) ? strtolower( (string) $candidate['host'] ) : '';
		$home_host        = isset( $home['host'] ) ? strtolower( (string) $home['host'] ) : '';
		$candidate_scheme = isset( $candidate['scheme'] ) ? strtolower( (string) $candidate['scheme'] ) : '';
		$home_scheme      = isset( $home['scheme'] ) ? strtolower( (string) $home['scheme'] ) : '';

		if (
			'' === $candidate_host
			|| '' === $home_host
			|| $candidate_host !== $home_host
			|| ! in_array( $candidate_scheme, array( 'http', 'https' ), true )
			|| $candidate_scheme !== $home_scheme
			|| isset( $candidate['user'] )
			|| isset( $candidate['pass'] )
		) {
			return false;
		}

		$candidate_port = isset( $candidate['port'] ) ? (int) $candidate['port'] : $this->default_port( $candidate['scheme'] ?? '' );
		$home_port      = isset( $home['port'] ) ? (int) $home['port'] : $this->default_port( $home['scheme'] ?? '' );

		return $candidate_port === $home_port;
	}

	/**
	 * Return the default port for a URL scheme.
	 *
	 * @param string $scheme URL scheme.
	 * @return int
	 */
	private function default_port( $scheme ) {
		return 'http' === strtolower( (string) $scheme ) ? 80 : 443;
	}

	/**
	 * Normalize a path relative to the WordPress home path.
	 *
	 * @param string              $path       Candidate path.
	 * @param array<string,mixed> $home_parts Home URL parts.
	 * @return string
	 */
	private function relative_to_home_path( $path, $home_parts ) {
		$path      = '/' . ltrim( (string) $path, '/' );
		$home_path = isset( $home_parts['path'] ) ? untrailingslashit( '/' . ltrim( (string) $home_parts['path'], '/' ) ) : '';

		if ( $home_path && '/' !== $home_path && ( $path === $home_path || 0 === strpos( $path, $home_path . '/' ) ) ) {
			$path = substr( $path, strlen( $home_path ) );
		}

		return '/' . ltrim( $path, '/' );
	}

	/**
	 * Determine whether a path points back to an authentication surface.
	 *
	 * @param string              $path     Candidate path.
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function is_auth_surface( $path, $settings ) {
		$path          = $this->normalize_path( $path );
		$login_path    = $this->normalize_path( $settings['login_path'] ?? '/login/' );
		$account_base  = $this->normalize_path( $settings['account_action_base'] ?? '/account' );
		$wp_login_path = $this->normalize_path( '/wp-login.php' );

		return in_array( $path, array( $login_path, $account_base, $wp_login_path ), true );
	}

	/**
	 * Normalize a path for comparison.
	 *
	 * @param string $path URL path.
	 * @return string
	 */
	private function normalize_path( $path ) {
		$path = untrailingslashit( '/' . ltrim( (string) $path, '/' ) );

		return '' === $path ? '/' : $path;
	}
}

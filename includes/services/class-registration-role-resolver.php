<?php
/**
 * Registration role resolver.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves the safe WordPress role assigned to completed registrations.
 */
class ALYNT_AG_Registration_Role_Resolver {

	const DEFAULT_ROLE  = 'customer';
	const FALLBACK_ROLE = 'subscriber';

	/**
	 * Return roles this plugin may assign through public registration.
	 *
	 * @return array<int,string>
	 */
	public static function allowed_roles() {
		return array( self::DEFAULT_ROLE, self::FALLBACK_ROLE );
	}

	/**
	 * Return admin select options for the registration role setting.
	 *
	 * @return array<string,string>
	 */
	public static function role_options() {
		return array(
			self::DEFAULT_ROLE  => __( 'Customer', 'alynt-account-gateway' ),
			self::FALLBACK_ROLE => __( 'Subscriber', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Sanitize the stored setting value.
	 *
	 * @param mixed $value Candidate role.
	 * @return string
	 */
	public static function sanitize_setting_value( $value ) {
		$role = sanitize_key( wp_unslash( $value ) );

		if ( self::is_allowed_setting_value( $role ) ) {
			return $role;
		}

		return self::default_setting_value();
	}

	/**
	 * Resolve the role that should actually be assigned during user creation.
	 *
	 * @param array<string,mixed> $settings Plugin settings.
	 * @return string Safe assignable role, or empty string when none is available.
	 */
	public static function resolve( $settings ) {
		$configured = isset( $settings['registration_user_role'] )
			? sanitize_key( wp_unslash( $settings['registration_user_role'] ) )
			: self::DEFAULT_ROLE;

		$candidates = array_values(
			array_unique(
				array_filter(
					array(
						$configured,
						self::DEFAULT_ROLE,
						self::FALLBACK_ROLE,
					)
				)
			)
		);

		foreach ( $candidates as $role ) {
			if ( self::is_safe_assignable_role( $role ) ) {
				return $role;
			}
		}

		return '';
	}

	/**
	 * Return the safest default that can be stored right now.
	 *
	 * @return string
	 */
	private static function default_setting_value() {
		if ( self::is_allowed_setting_value( self::DEFAULT_ROLE ) ) {
			return self::DEFAULT_ROLE;
		}

		return self::FALLBACK_ROLE;
	}

	/**
	 * Return whether a value may be saved in the setting.
	 *
	 * Missing allow-listed roles may be stored so WooCommerce can be enabled
	 * later, but existing roles with elevated capabilities are rejected.
	 *
	 * @param string $role Role key.
	 * @return bool
	 */
	private static function is_allowed_setting_value( $role ) {
		if ( ! in_array( $role, self::allowed_roles(), true ) ) {
			return false;
		}

		if ( ! function_exists( 'get_role' ) ) {
			return true;
		}

		$wp_role = get_role( $role );
		if ( ! $wp_role ) {
			return true;
		}

		return ! self::role_has_unsafe_capabilities( $wp_role );
	}

	/**
	 * Return whether a role exists and can be assigned by public registration.
	 *
	 * @param string $role Role key.
	 * @return bool
	 */
	private static function is_safe_assignable_role( $role ) {
		if ( ! in_array( $role, self::allowed_roles(), true ) ) {
			return false;
		}

		if ( ! function_exists( 'get_role' ) ) {
			return true;
		}

		$wp_role = get_role( $role );
		if ( ! $wp_role ) {
			return false;
		}

		return ! self::role_has_unsafe_capabilities( $wp_role );
	}

	/**
	 * Return whether a role carries capabilities unsuitable for self-registration.
	 *
	 * @param object $role Role object.
	 * @return bool
	 */
	private static function role_has_unsafe_capabilities( $role ) {
		$capabilities = isset( $role->capabilities ) && is_array( $role->capabilities ) ? $role->capabilities : array();
		$unsafe       = array(
			'activate_plugins',
			'create_users',
			'delete_plugins',
			'delete_users',
			'edit_files',
			'edit_plugins',
			'edit_posts',
			'edit_theme_options',
			'edit_themes',
			'edit_users',
			'export',
			'import',
			'install_plugins',
			'install_themes',
			'list_users',
			'manage_options',
			'manage_woocommerce',
			'moderate_comments',
			'promote_users',
			'publish_posts',
			'remove_users',
			'switch_themes',
			'unfiltered_html',
			'update_core',
			'update_plugins',
			'update_themes',
			'upload_files',
		);

		foreach ( $unsafe as $capability ) {
			if ( ! empty( $capabilities[ $capability ] ) ) {
				return true;
			}
		}

		return false;
	}
}

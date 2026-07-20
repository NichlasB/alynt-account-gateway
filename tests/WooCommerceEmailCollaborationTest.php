<?php
/**
 * WooCommerce and email collaboration tests.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Records facade delegation calls.
 */
class ALYNT_AG_Test_Woo_Email_Collaborator_Spy {

	/**
	 * Recorded calls.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $calls = array();

	/**
	 * Handle a collaborator call.
	 *
	 * @param string           $name      Method name.
	 * @param array<int,mixed> $arguments Method arguments.
	 * @return string
	 */
	public function __call( $name, $arguments ) {
		$this->calls[] = array(
			'method'    => $name,
			'arguments' => $arguments,
		);

		return $name . '-result';
	}
}

/**
 * Locks public facade contracts and collaborator boundaries.
 */
class WooCommerceEmailCollaborationTest extends PHPUnit\Framework\TestCase {

	/**
	 * Confirm WooCommerce retains its established public API.
	 */
	public function test_woocommerce_facade_retains_public_api() {
		$expected = array(
			'__construct',
			'standard_endpoint_labels',
			'endpoint_labels',
			'account_menu_items',
			'standard_account_menu_items',
			'hidden_account_menu_items',
			'is_account_menu_item_visible',
			'visible_account_menu_items',
			'account_menu_links',
			'register',
			'detect',
			'takeover_enabled',
			'maybe_handle_account_form_post',
			'endpoint_from_path',
			'render_endpoint',
			'recent_orders',
			'available_downloads',
			'account_details',
			'saved_payment_methods',
			'saved_addresses',
			'order_url',
			'address_url',
			'endpoint_url',
		);

		$this->assertSame( $expected, $this->public_method_names( ALYNT_AG_WooCommerce_Integration::class ) );
	}

	/**
	 * Confirm email templates retain their established public API.
	 */
	public function test_email_facade_retains_public_api() {
		$expected = array(
			'__construct',
			'register',
			'templates',
			'preview_tokens',
			'token_reference',
			'render',
			'send',
			'filter_retrieve_password_notification_email',
			'filter_retrieve_password_title',
			'filter_retrieve_password_message',
			'filter_send_password_change_email',
			'filter_password_change_email',
			'filter_send_email_change_email',
			'filter_email_change_email',
			'filter_new_user_email_content',
			'filter_pre_wp_mail_for_profile_email_change',
			'replace_tokens',
			'build_reset_url',
			'html_headers',
		);

		$this->assertSame( $expected, $this->public_method_names( ALYNT_AG_Email_Template_Service::class ) );
	}

	/**
	 * Preserve no-argument construction and optional injection seams.
	 */
	public function test_facade_constructors_accept_only_optional_collaborators() {
		foreach ( array( ALYNT_AG_WooCommerce_Integration::class, ALYNT_AG_Email_Template_Service::class ) as $class_name ) {
			$parameters = ( new ReflectionMethod( $class_name, '__construct' ) )->getParameters();

			$this->assertCount( 1, $parameters );
			$this->assertSame( 'collaborators', $parameters[0]->getName() );
			$this->assertTrue( $parameters[0]->isOptional() );
			$this->assertSame( array(), $parameters[0]->getDefaultValue() );
		}
	}

	/**
	 * Confirm each WooCommerce concern delegates to its collaborator.
	 */
	public function test_woocommerce_facade_delegates_each_concern() {
		$spies = $this->spies( array( 'navigation', 'routing', 'renderer', 'data' ) );
		$service = new ALYNT_AG_WooCommerce_Integration( $spies );

		$this->assertSame( 'account_menu_links-result', $service->account_menu_links( array() ) );
		$this->assertSame( 'endpoint_from_path-result', $service->endpoint_from_path( '/my-account/', array() ) );
		$this->assertSame( 'render_endpoint-result', $service->render_endpoint( 'orders' ) );
		$this->assertSame( 'recent_orders-result', $service->recent_orders( 7 ) );

		$this->assertSame( 'account_menu_links', $spies['navigation']->calls[0]['method'] );
		$this->assertSame( 'endpoint_from_path', $spies['routing']->calls[0]['method'] );
		$this->assertSame( 'render_endpoint', $spies['renderer']->calls[0]['method'] );
		$this->assertSame( 'recent_orders', $spies['data']->calls[0]['method'] );
	}

	/**
	 * Confirm each email concern delegates to its collaborator.
	 */
	public function test_email_facade_delegates_each_concern() {
		$spies = $this->spies( array( 'tokens', 'renderer', 'sender', 'filters' ) );
		$service = new ALYNT_AG_Email_Template_Service( $spies );

		$this->assertSame( 'templates-result', $service->templates() );
		$this->assertSame( 'render-result', $service->render( 'password_reset', array(), array() ) );
		$this->assertSame( 'send-result', $service->send( 'password_reset', 'person@example.test', array(), array() ) );
		$this->assertSame( 'filter_retrieve_password_title-result', $service->filter_retrieve_password_title( 'Title', 'login', new WP_User() ) );

		$this->assertSame( 'templates', $spies['tokens']->calls[0]['method'] );
		$this->assertSame( 'render', $spies['renderer']->calls[0]['method'] );
		$this->assertSame( 'send', $spies['sender']->calls[0]['method'] );
		$this->assertSame( 'filter_retrieve_password_title', $spies['filters']->calls[0]['method'] );
	}

	/**
	 * WooCommerce collaborators must honor facade overrides.
	 */
	public function test_woocommerce_collaborator_forwards_override_sensitive_calls() {
		$service = new class() extends ALYNT_AG_WooCommerce_Integration {
			public function account_menu_items() {
				return array( 'custom-endpoint' => 'Custom Endpoint' );
			}
		};

		$this->assertSame( 'Custom Endpoint', $service->endpoint_labels()['custom-endpoint'] );
	}

	/**
	 * Email collaborators must honor facade overrides.
	 */
	public function test_email_collaborator_forwards_override_sensitive_calls() {
		$service = new class() extends ALYNT_AG_Email_Template_Service {
			public function preview_tokens() {
				return array( 'first_name' => 'Override' );
			}
		};

		$this->assertSame( 'Hello Override', $service->replace_tokens( 'Hello {{first_name}}', array() ) );
	}

	/**
	 * Preserve hook names, priorities, accepted arguments, and facade callbacks.
	 */
	public function test_facades_register_established_hooks() {
		$GLOBALS['alynt_ag_test_filters'] = array();
		$GLOBALS['alynt_ag_test_actions'] = array();

		$woocommerce = new ALYNT_AG_WooCommerce_Integration();
		$email       = new ALYNT_AG_Email_Template_Service();
		$woocommerce->register();
		$email->register();

		$hooks = array_map(
			static function ( $filter ) {
				return array(
					$filter['hook'],
					$filter['callback'][1],
					$filter['priority'],
					$filter['accepted_args'],
				);
			},
			array_merge( $GLOBALS['alynt_ag_test_actions'], $GLOBALS['alynt_ag_test_filters'] )
		);

		$this->assertContains( array( 'plugins_loaded', 'detect', 20, 1 ), $hooks );
		$this->assertContains( array( 'template_redirect', 'maybe_handle_account_form_post', 0, 1 ), $hooks );
		$this->assertContains( array( 'retrieve_password_notification_email', 'filter_retrieve_password_notification_email', 10, 4 ), $hooks );
		$this->assertContains( array( 'pre_wp_mail', 'filter_pre_wp_mail_for_profile_email_change', 10, 2 ), $hooks );
	}

	/**
	 * Keep extracted concerns and facades within structural thresholds.
	 */
	public function test_service_files_stay_within_structure_thresholds() {
		$files = array(
			'class-woocommerce-navigation.php',
			'class-woocommerce-routing.php',
			'class-woocommerce-endpoint-renderer.php',
			'class-woocommerce-customer-data.php',
			'class-woocommerce-integration.php',
			'class-email-tokens.php',
			'class-email-renderer.php',
			'class-email-sender.php',
			'class-email-wordpress-filters.php',
			'class-email-template-service.php',
		);

		foreach ( $files as $file ) {
			$this->assertLessThanOrEqual( 300, count( file( ALYNT_AG_PLUGIN_DIR . 'includes/services/' . $file ) ), $file );
		}
	}

	/**
	 * Production and test loaders must define collaborators before facades.
	 */
	public function test_loaders_keep_collaborators_before_facades() {
		foreach ( array( 'includes/class-loader.php', 'tests/support/load-plugin.php' ) as $file ) {
			$contents = file_get_contents( ALYNT_AG_PLUGIN_DIR . $file );

			$this->assertIsString( $contents );
			$this->assertLessThan( strpos( $contents, 'class-woocommerce-integration.php' ), strpos( $contents, 'class-woocommerce-navigation.php' ) );
			$this->assertLessThan( strpos( $contents, 'class-email-template-service.php' ), strpos( $contents, 'class-email-tokens.php' ) );
		}
	}

	/**
	 * Return public method names in declaration order.
	 *
	 * @param string $class_name Class name.
	 * @return array<int,string>
	 */
	private function public_method_names( $class_name ) {
		return array_map(
			static function ( $method ) {
				return $method->getName();
			},
			( new ReflectionClass( $class_name ) )->getMethods( ReflectionMethod::IS_PUBLIC )
		);
	}

	/**
	 * Build named collaborator spies.
	 *
	 * @param array<int,string> $keys Collaborator keys.
	 * @return array<string,ALYNT_AG_Test_Woo_Email_Collaborator_Spy>
	 */
	private function spies( $keys ) {
		$spies = array();
		foreach ( $keys as $key ) {
			$spies[ $key ] = new ALYNT_AG_Test_Woo_Email_Collaborator_Spy();
		}

		return $spies;
	}
}

<?php
/**
 * Test database fixture.
 *
 * @package Alynt_Account_Gateway
 */

class ALYNT_AG_Test_WPDB {
	public $prefix = 'wp_';
	public $options = 'wp_options';
	public $insert_id = 1;
	public $last_error = '';

	public function insert( $table, $data, $format = array() ) {
		$GLOBALS['alynt_ag_test_db_inserts'][] = array(
			'table'  => $table,
			'data'   => $data,
			'format' => $format,
		);

		return true;
	}

	public function update( $table, $data, $where, $format = array(), $where_format = array() ) {
		$GLOBALS['alynt_ag_test_db_updates'][] = array(
			'table'        => $table,
			'data'         => $data,
			'where'        => $where,
			'format'       => $format,
			'where_format' => $where_format,
		);

		return true;
	}

	public function delete( $table, $where, $where_format = array() ) {
		$GLOBALS['alynt_ag_test_db_deletes'][] = array(
			'table'        => $table,
			'where'        => $where,
			'where_format' => $where_format,
		);

		return 1;
	}

	public function get_results( $query ) {
		foreach ( $GLOBALS['alynt_ag_test_db_results'] as $table => $rows ) {
			if ( false !== strpos( $query, (string) $table ) ) {
				return $rows;
			}
		}

		return array();
	}

	public function get_row( $query ) {
		$GLOBALS['alynt_ag_test_db_queries'][] = $query;

		if ( empty( $GLOBALS['alynt_ag_test_db_rows'] ) ) {
			return null;
		}

		return array_shift( $GLOBALS['alynt_ag_test_db_rows'] );
	}

	public function prepare( $query, ...$args ) {
		foreach ( $args as $arg ) {
			$replacement = is_int( $arg ) ? (string) $arg : "'" . addslashes( (string) $arg ) . "'";
			$query = preg_replace( '/%[sd]/', $replacement, $query, 1 );
		}

		return $query;
	}

	public function query( $query ) {
		$GLOBALS['alynt_ag_test_db_queries'][] = $query;

		if ( isset( $GLOBALS['alynt_ag_test_db_query_result'] ) ) {
			return $GLOBALS['alynt_ag_test_db_query_result'];
		}

		return true;
	}

	public function get_var( $query ) {
		$GLOBALS['alynt_ag_test_db_queries'][] = $query;

		if ( preg_match( "/SHOW TABLES LIKE '([^']+)'/", $query, $matches ) ) {
			return stripslashes( $matches[1] );
		}

		return $GLOBALS['alynt_ag_test_db_var'] ?? null;
	}

	public function esc_like( $text ) {
		return addcslashes( (string) $text, '_%\\' );
	}

	public function get_charset_collate() {
		return 'DEFAULT CHARSET=utf8mb4';
	}
}

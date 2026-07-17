<?php
/**
 * Off-canvas menu walker.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Walker_Nav_Menu' ) && ! class_exists( 'ALYNT_AG_Offcanvas_Menu_Walker' ) ) {
	/**
	 * Adds accessible submenu toggles to the dashboard off-canvas menu.
	 */
	class ALYNT_AG_Offcanvas_Menu_Walker extends Walker_Nav_Menu {

		/**
		 * Submenu IDs keyed by depth.
		 *
		 * @var array<int,string>
		 */
		private $submenu_ids = array();

		/**
		 * Starts the submenu list.
		 *
		 * @param string $output Used to append additional content.
		 * @param int    $depth  Depth of menu item.
		 * @param object $args   Menu arguments.
		 * @return void
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent     = str_repeat( "\t", $depth );
			$submenu_id = isset( $this->submenu_ids[ $depth ] ) ? $this->submenu_ids[ $depth ] : '';
			$id_attr    = $submenu_id ? ' id="' . esc_attr( $submenu_id ) . '"' : '';

			$output .= "\n$indent<ul$id_attr class=\"sub-menu agw-offcanvas__submenu\" hidden>\n";
		}

		/**
		 * Starts a menu item.
		 *
		 * @param string  $output Used to append additional content.
		 * @param WP_Post $item   Menu item data object.
		 * @param int     $depth  Depth of menu item.
		 * @param object  $args   Menu arguments.
		 * @param int     $id     Current item ID.
		 * @return void
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
			$has_children = in_array( 'menu-item-has-children', $classes, true ) || ( is_object( $args ) && ! empty( $args->has_children ) );
			$classes[]    = 'agw-offcanvas__menu-item';

			if ( $has_children ) {
				$classes[] = 'agw-offcanvas__menu-item--has-children';
			}

			$class_names = implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) );
			$output     .= '<li class="' . esc_attr( $class_names ) . '">';
			$output     .= '<div class="agw-offcanvas__menu-row">';
			$output     .= $this->menu_item_link( $item, $args, $depth );

			if ( $has_children ) {
				$output .= $this->submenu_toggle( $item, $depth );
			}

			$output .= '</div>';
		}

		/**
		 * Ends a menu item.
		 *
		 * @param string  $output Used to append additional content.
		 * @param WP_Post $item   Menu item data object.
		 * @param int     $depth  Depth of menu item.
		 * @param object  $args   Menu arguments.
		 * @return void
		 */
		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			unset( $this->submenu_ids[ $depth ] );
			$output .= "</li>\n";
		}

		/**
		 * Build a menu item link.
		 *
		 * @param WP_Post $item  Menu item data object.
		 * @param object  $args  Menu arguments.
		 * @param int     $depth Depth of menu item.
		 * @return string
		 */
		private function menu_item_link( $item, $args, $depth ) {
			$atts = array(
				'href' => ! empty( $item->url ) ? $item->url : '',
			);

			if ( ! empty( $item->target ) ) {
				$atts['target'] = $item->target;
			}

			if ( ! empty( $item->xfn ) ) {
				$atts['rel'] = $item->xfn;
			}

			if ( ! empty( $item->attr_title ) ) {
				$atts['title'] = $item->attr_title;
			}

			$attributes = '';

			foreach ( $atts as $attr => $value ) {
				if ( '' === $value ) {
					continue;
				}

				$value       = 'href' === $attr ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			return '<a' . $attributes . '>' . esc_html( $title ) . '</a>';
		}

		/**
		 * Build the submenu toggle for a parent menu item.
		 *
		 * @param WP_Post $item  Menu item data object.
		 * @param int     $depth Depth of menu item.
		 * @return string
		 */
		private function submenu_toggle( $item, $depth ) {
			$submenu_id = 'agw-offcanvas-submenu-' . absint( $item->ID );

			$this->submenu_ids[ $depth ] = $submenu_id;

			$clean_title  = wp_strip_all_tags( $item->title );
			$expand_label = sprintf(
				/* translators: %s: menu item title. */
				__( 'Expand submenu for %s', 'alynt-account-gateway' ),
				$clean_title
			);
			$collapse_label = sprintf(
				/* translators: %s: menu item title. */
				__( 'Collapse submenu for %s', 'alynt-account-gateway' ),
				$clean_title
			);

			return '<button type="button" class="agw-offcanvas__submenu-toggle" aria-expanded="false" aria-controls="' . esc_attr( $submenu_id ) . '" aria-label="' . esc_attr( $expand_label ) . '" data-agw-submenu-toggle data-agw-expand-label="' . esc_attr( $expand_label ) . '" data-agw-collapse-label="' . esc_attr( $collapse_label ) . '"><span class="agw-offcanvas__submenu-toggle-icon" aria-hidden="true"></span></button>';
		}
	}
}

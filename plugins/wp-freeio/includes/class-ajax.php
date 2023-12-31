<?php
/**
 * Handles Ajax endpoints
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Ajax {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ) );
		add_action( 'template_redirect', array( __CLASS__, 'do_wpfi_ajax' ), 0 );
	}

	public static function define_ajax() {
		// phpcs:disable
		if ( ! empty( $_GET['wpfi-ajax'] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
			}
			$GLOBALS['wpdb']->hide_errors();
		}
		// phpcs:enable
	}

	public static function get_endpoint( $request = '%%endpoint%%' ) {
		return esc_url_raw( apply_filters( 'wp_freeio_ajax_get_endpoint', add_query_arg( 'wpfi-ajax', $request, home_url( '/' ) ), $request ) );
	}


	private static function wpfi_ajax_headers() {
		if ( ! headers_sent() ) {
			send_origin_headers();
			send_nosniff_header();
			
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
				define( 'DONOTCACHEOBJECT', true );
			}
			if ( ! defined( 'DONOTCACHEDB' ) ) {
				define( 'DONOTCACHEDB', true );
			}
			nocache_headers();

			header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			header( 'X-Robots-Tag: noindex' );
			status_header( 200 );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "wpfi_ajax_headers cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}

	public static function do_wpfi_ajax() {

		global $wp_query;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['wpfi-ajax'] ) ) {
			$wp_query->set( 'wpfi-ajax', sanitize_text_field( wp_unslash( $_GET['wpfi-ajax'] ) ) );
		}

		$action = $wp_query->get( 'wpfi-ajax' );

		if ( $action ) {
			self::wpfi_ajax_headers();
			$action = sanitize_text_field( $action );
			do_action( 'wpfi_ajax_' . $action );
			wp_die();
		}

	}

}

WP_Freeio_Ajax::init();

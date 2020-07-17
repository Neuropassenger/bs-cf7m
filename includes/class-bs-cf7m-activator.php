<?php

/**
 * Fired during plugin activation
 *
 * @link       https://neuropassenger.ru
 * @since      1.0.0
 *
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/includes
 * @author     Oleg Sokolov <turgenoid@gmail.com>
 */
class Bs_Cf7m_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Database
		global $wpdb;
		$table_name = $wpdb->prefix . 'bs_cf7m_requests';
		$charset_collate = $wpdb->get_charset_collate();

		$sql_query = "CREATE TABLE {$table_name} (
			id int NOT NULL AUTO_INCREMENT,
			form_id int NOT NULL,
			time bigint NOT	 NULL,
			PRIMARY KEY (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_query );

		update_option( 'bs_cf7m_last_time', time() );

		add_filter( 'cron_schedules', function( $schedules ) {
			return Bs_Cf7m_Admin::cron_interval( $schedules );
		} );

		$schedule_check_forms_timestamp = wp_next_scheduled( 'bs_cf7m_check_forms' );
		wp_unschedule_event( $schedule_check_forms_timestamp, 'bs_cf7m_check_forms' );

		$exist_interval = intval( get_option( 'bs_cf7m_interval' ) ?: 24 );
		wp_schedule_event( time() + $exist_interval * HOUR_IN_SECONDS, 'bs_cf7m_interval', 'bs_cf7m_check_forms' );

	}

}

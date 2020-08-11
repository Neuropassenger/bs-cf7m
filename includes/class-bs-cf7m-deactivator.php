<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://neuropassenger.ru
 * @since      1.0.0
 *
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Bs_Cf7m
 * @subpackage Bs_Cf7m/includes
 * @author     Oleg Sokolov <turgenoid@gmail.com>
 */
class Bs_Cf7m_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$schedule_check_forms_timestamp = wp_next_scheduled( 'bs_cf7m_check_forms' );
		wp_unschedule_event( $schedule_check_forms_timestamp, 'bs_cf7m_check_forms' );
        //wp_clear_scheduled_hook('bs_cf7m_check_forms');
	}

}

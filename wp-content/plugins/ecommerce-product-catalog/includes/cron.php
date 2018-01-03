<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Cron
 *
 * @version        2.7.1
 * @package        ecommerce-product-catalog/includes
 * @author        Norbert Dreszer
 */

/**
 * IC_EPC_Cron Class
 *
 * This class handles scheduled events
 *
 */
class IC_EPC_Cron {

	/**
	 * Get things going
	 *
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'wp', array( $this, 'schedule_events' ) );
	}

	/**
	 * Registers new cron schedules
	 *
	 *
	 * @param array $schedules
	 * @return array
	 */
	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules[ 'weekly' ] = array(
			'interval'	 => 604800,
			'display'	 => __( 'Once Weekly', 'ecommerce-product-catalog' )
		);

		return $schedules;
	}

	/**
	 * Schedules our events
	 *
	 * @access public
	 * @return void
	 */
	public function schedule_events() {
		$this->weekly_events();
		$this->daily_events();
	}

	/**
	 * Schedule weekly events
	 *
	 * @access private
	 * @return void
	 */
	private function weekly_events() {
		if ( !wp_next_scheduled( 'ic_epc_weekly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp', true ), 'weekly', 'ic_epc_weekly_scheduled_events' );
		}
	}

	/**
	 * Schedule daily events
	 *
	 * @access private
	 * @return void
	 */
	private function daily_events() {
		if ( !wp_next_scheduled( 'ic_epc_daily_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp', true ), 'daily', 'ic_epc_daily_scheduled_events' );
		}
	}

}

$ic_epc_cron = new IC_EPC_Cron;

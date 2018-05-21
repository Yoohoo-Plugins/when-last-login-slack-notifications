<?php
/**
 * Plugin Name: When Last Login - Slack Notifications
 * Plugin URI: https://yoohooplugins.com/plugins/when-last-login-slack-notifications
 * Description: Adds functionality to WordPress to show when a user last logged in. Sends a notification via Slack when this is done.
 * Version: 1.1
 * Author: Yoohoo Plugins
 * Author URI: https://www.whenlastlogin.com
 * Text Domain: when-last-login-slack-notifications
 */

class WhenLastLoginSlackNotifications{

	public function __construct(){

		add_action( 'wll_logged_in_action', array( $this, 'wll_sn_logged_in' ), 10, 1 );
		add_action( 'admin_head', array( $this, 'wll_sn_admin_head' ) );
		add_action( 'admin_menu', array ( $this, 'wll_sn_settings_submenu') );

		add_filter( 'wll_settings_page_tabs', array( $this, 'wll_sn_settings_tab' ) );
		add_filter( 'wll_settings_page_content', array( $this, 'wll_sn_settings_content' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'wll_sn_admin_scripts' ) );
	}

	public function wll_sn_admin_scripts(){

		if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'when-last-login-settings' ) && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'slack-notifications' ) ){

			wp_enqueue_script( 'wll-sn-admin-script', plugins_url( '/js/admin.js', __FILE__ ), array( 'jquery' ) );

		}

	}

	public function wll_sn_logged_in( $array ){

		if( isset( $array['user'] ) ){

			$user = $array['user'];
			
			$user_id = $user->ID;

			$user_display_name = $user->data->display_name;

			$settings = get_option( 'wll_sn_settings' );

			$user_role_matches = false;
			$send_notification = false;

			if( $settings && $settings != "" && isset( $settings['webhook'] ) ){

				if( isset( $settings['user_role'] ) ){

					if( $settings['user_role'] == 'all' ){
						$user_role_matches = true;
					} else {
						foreach( $user->roles as $role ){
							if( $role == $settings['user_role'] ){
								$user_role_matches = true;
							}
						}

					}

				} else {
					$user_role_matches = true;
				}

				$wll_current_time = current_time( 'timestamp' );

				if( $user_role_matches ){

					$start_time_h = isset( $settings['start_time_h'] ) ? $settings['start_time_h'] : "";
					$start_time_m = isset( $settings['start_time_m'] ) ? $settings['start_time_m'] : "";
					$end_time_h = isset( $settings['end_time_h'] ) ? $settings['end_time_h'] : "";
					$end_time_m = isset( $settings['end_time_m'] ) ? $settings['end_time_m'] : "";

					$day_of_week = date( 'w' );

					if( $start_time_h == "" || $start_time_m == "" || $end_time_h == "" || $end_time_m = "" ){

						$send_notification = true;
						
					} else {

						$date_start = strtotime( date( 'Y-m-d' ).' '.$start_time_h.':'.$start_time_m.':00' );
						$date_end = strtotime( date( 'Y-m-d' ).' '.$end_time_h.':'.$end_time_m.':00' );

						if( $settings['timeslot'] == 'every_day' ){
							$send_notification = true;
						} else if( $settings['timeslot'] == 'weekdays' ){

							if( $day_of_week !== 0 && $day_of_week !== 6 ){
								//Its a week day
								if( $wll_current_time > $date_start && $wll_current_time < $date_end ){
									$send_notification = true;
								}
							}

						} else if( $settings['timeslot'] == 'weekends' ){

							if( $day_of_week == 0 || $day_of_week == 6 ){
								//Its a weekend
								if( $wll_current_time > $date_start && $wll_current_time < $date_end ){
									$send_notification = true;
								}
							}

						} else if( $settings['timeslot'] == 'both' ){
							if( $wll_current_time > $date_start && $wll_current_time < $date_end ){
								$send_notification = true;
							}
						}

					}					

				}

				if( $send_notification ){

					$wll_site_url = get_option( 'siteurl' );

					$text = sprintf( __('%s has logged in to %s at %s', 'when-last-login-slack-notifications' ), $user_display_name, $wll_site_url, date( 'Y-m-d H:i:s', $wll_current_time ) );
					$payload = array(
			            'text'        	=> $text,
			            'username'		=> __( 'When Last Login - Slack Notification', 'when-last-login-slack-notifications' )
			        );

			        $output  = 'payload=' . json_encode( $payload );

			        $response = wp_remote_post( $settings['webhook'], array( 'body' => $output ) );

			    }
		        
		    }

	    }

	}

	public function wll_sn_settings_submenu() {
		add_submenu_page( 'when-last-login-settings', __( 'Slack Notifications', 'when-last-login-slack-notifications' ), __( 'Slack Notifications', 'when-last-login-slack-notifications' ), 'manage_options', '?page=when-last-login-settings&tab=slack-notifications' );
	}

	public function wll_sn_settings_tab( $array ){

		$array['slack-notifications'] = array(
			'title' => __( 'Slack Notifications', 'when-last-login-slack-notifications' ),
			'icon' => ''
		);

		return $array;
	}

	public function wll_sn_settings_content( $content ){

		$content['slack-notifications'] = plugin_dir_path( __FILE__ ).'/when-last-login-slack-notifications-settings.php';

		return $content;
	}

	public function wll_sn_admin_head(){

		if( isset( $_POST['wll_sn_save_settings'] ) ){

			if( isset( $_POST['wll_sn_webhook_url'] ) ){

				$wll_sn_settings = array( 
					'webhook' => sanitize_text_field( $_POST['wll_sn_webhook_url'] ),
					'user_role' => sanitize_text_field( $_POST['wll_sn_notify_specific_user_role'] ),
					'timeslot' => sanitize_text_field( $_POST['wll_sn_notify_timeslot'] ),
					'start_time_h' => (int) $_POST['wll_sn_notify_start_time_hours'],
					'start_time_m' => (int) $_POST['wll_sn_notify_start_time_minutes'],
					'end_time_h' => (int) $_POST['wll_sn_notify_end_time_hours'],
					'end_time_m' => (int) $_POST['wll_sn_notify_end_time_minutes']
				);

				$updated = update_option( 'wll_sn_settings', $wll_sn_settings );

				if( $updated ){

					echo "<div class='updated'><p>".__('Settings successfully updated', 'when-last-login-slack-notifications')."</p></div>";
				}

			}

		}

	}

	public static function wll_sn_time_array(){

		$h = array();
		
		for( $i = 0; $i <= 23; $i++){
			$h[] = sprintf('%02d', $i );
		}
		
		$m = array();	
		
		for( $i = 0; $i <= 59; $i++){
			$m[] = sprintf('%02d', $i );
		}
		
		$t = array(
			'hours' 	=> $h,
			'minutes' 	=> $m
		);

		return $t;

	}

}

new WhenLastLoginSlackNotifications();
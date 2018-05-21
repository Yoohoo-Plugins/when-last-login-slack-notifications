<?php $wll_sn_settings = get_option( 'wll_sn_settings' ); ?>

<tr>
	<th><?php _e('Slack Webhook URL', 'when-last-login-slack-notifications'); ?></th>
	<td><input type='text' name='wll_sn_webhook_url' style='width: 250px;' value='<?php echo isset( $wll_sn_settings['webhook'] ) ? $wll_sn_settings['webhook'] : ""; ?>' />
	<small class='description'>
		<p>Go to <a href='https://my.slack.com/services/new/incoming-webhook' target='_BLANK'>https://my.slack.com/services/new/incoming-webhook</a></p>
		<p>Create a new webhook. </p>
		<p>Set a channel to receive the notifications </p>
		<p>Copy the URL for the webhook</p>
	</small>
	</td>
</tr>

<tr>
	<th><?php _e('Only notify of a specific user role', 'when-last-login-slack-notifications'); ?></th>
	<td>
		<select name='wll_sn_notify_specific_user_role'>
		<?php 
			if( isset( $wll_sn_settings['user_role'] ) ){
				$selected = $wll_sn_settings['user_role'];
			} else {
				$selected = '';
			}
		?>
			<option value='all' <?php selected( 'all', $selected ); ?>><?php _e('All User Roles', 'when-last-login-slack-notifications'); ?></option>
			<?php wp_dropdown_roles( $selected ); ?>
		</select>
	</td>
</tr>
<?php $time_array = WhenLastLoginSlackNotifications::wll_sn_time_array(); ?>
<tr>
	<th><?php _e('Only notify during a specific time frame', 'when-last-login-slack-notifications'); ?></th>
	<td>
		<select name='wll_sn_notify_timeslot' id='wll_sn_notify_timeslot'>
			<option value='every_day' <?php selected( $wll_sn_settings['timeslot'], 'every_day' ); ?> ><?php _e('Every Day', 'when-last-login-slack-notifications'); ?></option>
			<option value='weekdays' <?php selected( $wll_sn_settings['timeslot'], 'weekdays' ); ?> ><?php _e('Weekdays', 'when-last-login-slack-notifications'); ?></option>
			<option value='weekends' <?php selected( $wll_sn_settings['timeslot'], 'weekends' ); ?> ><?php _e('Weekends', 'when-last-login-slack-notifications'); ?></option>
			<option value='both' <?php selected( $wll_sn_settings['timeslot'], 'both' ); ?> ><?php _e('Weekdays and Weekends', 'when-last-login-slack-notifications'); ?></option>
		</select> <span class='wll_times_dropdowns'> <?php _e('between', 'when-last-login-slack-notifications'); ?>
			<select name='wll_sn_notify_start_time_hours'>
				<?php
					if( is_array( $time_array ) ){
						foreach( $time_array['hours'] as $t ){
							echo "<option value='$t' ".selected( $wll_sn_settings['start_time_h'], $t, false ).">$t</option>";
						} 
					}
				?> 
			</select>
			<select name='wll_sn_notify_start_time_minutes'>
				<?php
					if( is_array( $time_array ) ){
						foreach( $time_array['minutes'] as $t ){
							echo "<option value='$t' ".selected( $wll_sn_settings['start_time_m'], $t, false ).">$t</option>";
						} 
					}
				?> 
			</select> <?php _e('and', 'when-last-login-slack-notifications'); ?>
			<select name='wll_sn_notify_end_time_hours'>
				<?php
					if( is_array( $time_array ) ){
						foreach( $time_array['hours'] as $t ){
							echo "<option value='$t' ".selected( $wll_sn_settings['end_time_h'], $t, false ).">$t</option>";
						} 
					}
				?> 
			</select>
			<select name='wll_sn_notify_end_time_minutes'>
				<?php
					if( is_array( $time_array ) ){
						foreach( $time_array['minutes'] as $t ){
							echo "<option value='$t' ".selected( $wll_sn_settings['end_time_m'], $t, false ).">$t</option>";
						} 
					}
				?> 
			</select> <small><?php _e('Please ensure you follow a 24 hour clock when selecting times', 'when-last-login-slack-notifications'); ?></small>
		</span>
	</td>
</tr>
<tr>
    <th><input type="submit" name="wll_sn_save_settings"  class="button-primary" value="<?php _e('Save Settings', 'when-last-login'); ?>" /></th>
    <td></td>
</tr>
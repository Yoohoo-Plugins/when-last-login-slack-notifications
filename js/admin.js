jQuery(document).ready(function(){

	jQuery("#wll_sn_notify_timeslot").on("change", function(){

		var timeslot_val = jQuery(this).val();

		if( timeslot_val == 'every_day' ){
			jQuery('.wll_times_dropdowns').hide();
		} else {
			jQuery('.wll_times_dropdowns').show();
		}

	});

	var current_timeslot = jQuery("#wll_sn_notify_timeslot").val();

	if( current_timeslot == 'every_day' ){
		jQuery('.wll_times_dropdowns').hide();
	} else {
		jQuery('.wll_times_dropdowns').show();
	}

});
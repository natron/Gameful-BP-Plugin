<?php

	function gameful_donation_message(){

		global $bp;
				
		$donator = $bp->loggedin_user->fullname;
		$recipients = apply_filters( 'bp_messages_recipients', explode( ' ', $_POST['donateTo'] ) );
		$donation = $_POST['donation'];
		$subject = $donator .' donated ' . $donation . ' ' . gf_pluralize('treat',$donation) . ' to you';
		$content = $_POST['donateWhy'];

		// Send the message
		if ( strlen($content) > 0 
		      && $thread_id = messages_new_message( array(  'recipients' => $recipients, 
                  												   	          'subject' => $subject, 
									                      					      'content' => $content ) ) ) {

		} else {
			 _e( 'There was an error sending that message, please try again' | $thread_id, 'buddypress' );
		}
		
	}
	
	add_action('gameful_donation_message', 'gameful_donation_message');
?>

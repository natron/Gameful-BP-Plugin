<?php

/* Define a constant that can be checked to see if the component is installed or not. */
define ( 'BP_GAMEFUL_IS_INSTALLED', 1 );

/* Define a constant that will hold the current version number of the component */
define ( 'BP_GAMEFUL_VERSION', '0.1' );

/* Define a constant for the Gameful Mayor's id */
define ('BP_GAMEFUL_MAYOR_ID', '2');

/* Define a constant that will hold the database version number that can be used for upgrading the DB
 *
 * NOTE: When table defintions change and you need to upgrade,
 * make sure that you increment this constant so that it runs the install function again.
 *
 * Also, if you have errors when testing the component for the first time, make sure that you check to
 * see if the table(s) got created. If not, you'll most likely need to increment this constant as
 * BP_EXAMPLE_DB_VERSION was written to the wp_usermeta table and the install function will not be
 * triggered again unless you increment the version to a number higher than stored in the meta data.
 */
define ( 'BP_GAMEFUL_DB_VERSION', '1' );

/* Define a slug constant that will be used to view this components pages (http://example.org/SLUG) */
if ( !defined( 'BP_GAMEFUL_SLUG' ) )
	define ( 'BP_GAMEFUL_SLUG', 'gameful' );

/*
 * If you want the users of your component to be able to change the values of your other custom constants,
 * you can use this code to allow them to add new definitions to the wp-config.php file and set the value there.
 *
 *
 *	if ( !defined( 'BP_EXAMPLE_CONSTANT' ) )
 *		define ( 'BP_EXAMPLE_CONSTANT', 'some value' // or some value without quotes if integer );
 */

/**
 * You should try hard to support translation in your component. It's actually very easy.
 * Make sure you wrap any rendered text in __() or _e() and it will then be translatable.
 *
 * You must also provide a text domain, so translation files know which bits of text to translate.
 * Throughout this example the text domain used is 'bp-example', you can use whatever you want.
 * Put the text domain as the second parameter:
 *
 * __( 'This text will be translatable', 'bp-example' ); // Returns the first parameter value
 * _e( 'This text will be translatable', 'bp-example' ); // Echos the first parameter value
 */

//if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
//	load_textdomain( 'bp-gameful', dirname( __FILE__ ) . '/bp-gameful/languages/' . get_locale() . '.mo' );

/**
 * The next step is to include all the files you need for your component.
 * You should remove or comment out any files that you don't need.
 */

/* The classes file should hold all database access classes and functions */
require ( dirname( __FILE__ ) . '/bp-gameful-classes.php' );

/* The ajax file should hold all functions used in AJAX queries */
//require ( dirname( __FILE__ ) . '/bp-example-ajax.php' );

/* The cssjs file should set up and enqueue all CSS and JS files used by the component */
//require ( dirname( __FILE__ ) . '/bp-example-cssjs.php' );

/* The templatetags file should contain classes and functions designed for use in template files */
require ( dirname( __FILE__ ) . '/bp-gameful-templatetags.php' );

/* The widgets file should contain code to create and register widgets for the component */
//require ( dirname( __FILE__ ) . '/bp-example-widgets.php' );

/* The notifications file should contain functions to send email notifications on specific user actions */
//require ( dirname( __FILE__ ) . '/bp-gameful-notifications.php' );

/* The filters file should create and apply filters to component output functions. */
require ( dirname( __FILE__ ) . '/bp-gameful-filters.php' );

/* The functions/actions needed to respond to in game messages */
require ( dirname( __FILE__ ) . '/bp-gameful-messages.php' );


/* The functions/actions to tweak the treat donation */
require ( dirname( __FILE__ ) . '/bp-gameful-donate.php' );



/**
 * bp_example_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_gameful_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->gameful->id = 'gameful';

//	$bp->example->table_name = $wpdb->base_prefix . 'bp_example';
	$bp->gameful->format_notification_function = 'bp_gameful_format_notifications';
	$bp->gameful->slug = BP_GAMEFUL_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->gameful->slug] = $bp->gameful->id;

}
/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_globals', 'bp_example_setup_globals' );
 */
add_action( 'wp', 'bp_gameful_setup_globals',2 );
add_action( 'admin_menu', 'bp_gameful_setup_globals',2 );
//add_action( 'bp_setup_globals', 'bp_gameful_setup_globals');
/**
 * bp_example_add_admin_menu()
 *
 * This function will add a WordPress wp-admin admin menu for your component under the
 * "BuddyPress" menu.
 */

function bp_gameful_add_admin_menu() {
	global $bp;


	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/bp-gameful-admin.php' );

        add_menu_page('Rulesets', 'Gameful', 'manage_options', 'bp-gameful-admin', 'bp_gameful_admin_rules');
	add_submenu_page('bp-gameful-admin', 'Rulesets', __('Rulesets','bp-gameful'), 'manage_options', 'bp-gameful-admin', 'bp_gameful_admin_rules');
	add_submenu_page('bp-gameful-admin', 'Easter Eggs', __('Easter Eggs','bp-gameful'), 'manage_options', 'bp-gameful-admin-eggs', 'bp_gameful_admin_eggs');

//        add_submenu_page( 'bp-general-settings', __( 'Gameful Admin', 'bp-gameful' ), __( 'Gameful Admin', 'bp-gameful' ), 'manage_options', 'bp-gameful-settings', 'bp_gameful_admin' );
}
add_action( 'admin_menu', 'bp_gameful_add_admin_menu' );


/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */


/**
 * bp_example_screen_notification_settings()
 *
 * Adds notification settings for the component, so that a user can turn off email
 * notifications set on specific component actions.
 */

/**
 * bp_gameful_record_activity()
 *
 * If the activity stream component is installed, this function will record activity items for your
 * component.
 *
 * You must pass the function an associated array of arguments:
 *
 *     $args = array(
 *	 	 REQUIRED PARAMS
 *		 'action' => For example: "Andy high-fived John", "Andy posted a new update".
 *       'type' => The type of action being carried out, for example 'new_friendship', 'joined_group'. This should be unique within your component.
 *
 *		 OPTIONAL PARAMS
 *		 'id' => The ID of an existing activity item that you want to update.
 * 		 'content' => The content of your activity, if it has any, for example a photo, update content or blog post excerpt.
 *       'component' => The slug of the component.
 *		 'primary_link' => The link for the title of the item when appearing in RSS feeds (defaults to the activity permalink)
 *       'item_id' => The ID of the main piece of data being recorded, for example a group_id, user_id, forum_post_id - useful for filtering and deleting later on.
 *		 'user_id' => The ID of the user that this activity is being recorded for. Pass false if it's not for a user.
 *		 'recorded_time' => (optional) The time you want to set as when the activity was carried out (defaults to now)
 *		 'hide_sitewide' => Should this activity item appear on the site wide stream?
 *		 'secondary_item_id' => (optional) If the activity is more complex you may need a second ID. For example a group forum post may need the group_id AND the forum_post_id.
 *     )
 *
 * Example usage would be:
 *
 *   bp_example_record_activity( array( 'type' => 'new_highfive', 'action' => 'Andy high-fived John', 'user_id' => $bp->loggedin_user->id, 'item_id' => $bp->displayed_user->id ) );
 *
 */
function bp_gameful_record_activity( $args = '' ) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	$defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->example->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

/**
 * bp_gameful_format_notifications()
 *
 * The format notification function will take DB entries for notifications and format them
 * so that they can be displayed and read on the screen.
 *
 * Notifications are "screen" notifications, that is, they appear on the notifications menu
 * in the site wide navigation bar. They are not for email notifications.
 *
 *
 * The recording is done by using bp_core_add_notification() which you can search for in this file for
 * examples of usage.
 */
function bp_gameful_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;


	switch ( $action ) {
		case 'level_up':
			/* In this case, $item_id is the user ID of the user who sent the high five. */

			/***
			 * We don't want a whole list of similar notifications in a users list, so we group them.
			 * If the user has more than one action from the same component, they are counted and the
			 * notification is rendered differently.
			 */
			if ( (int)$total_items > 1 ) {
				return apply_filters( 'bp_gameful_multiple_level_up_notification', '<a href="' . $bp->loggedin_user->domain . $bp->gameful->slug . '/screen-one/" title="' . __( 'Multiple Level Ups', 'bp-gameful' ) . '">' . sprintf( __( '%d new level ups!', 'bp-gameful' ), (int)$total_items ) . '</a>', $total_items );
			} else {
				$user_fullname = bp_core_get_user_displayname( $item_id, false );
				$user_url = bp_core_get_userurl( $item_id );
				return apply_filters( 'bp_gameful_single_level_up_notification', '<a href="' . $user_url . '?new" title="' . $user_fullname .'\'s profile">' . sprintf( __( '%s sent you a level up!', 'bp-gameful' ), $user_fullname ) . '</a>', $user_fullname );
			}
		break;
	}

	do_action( 'bp_gameful_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}

/***
 * From now on you will want to add your own functions that are specific to the component you are developing.
 * For example, in this section in the friends component, there would be functions like:
 *    friends_add_friend()
 *    friends_remove_friend()
 *    friends_check_friendship()
 *
 * Some guidelines:
 *    - Don't set up error messages in these functions, just return false if you hit a problem and
 *		deal with error messages in screen or action functions.
 *
 *    - Don't directly query the database in any of these functions. Use database access classes
 * 		or functions in your bp-example-classes.php file to fetch what you need. Spraying database
 * 		access all over your plugin turns into a maintainence nightmare, trust me.
 *
 *	  - Try to include add_action() functions within all of these functions. That way others will find it
 *		easy to extend your component without hacking it to pieces.
 */


function gf_log($type, $uid, $points, $source, $unique=false){
	$userinfo = get_userdata($uid);
	if($userinfo->user_login==''){ return false; }

	global $wpdb;
        if ($unique)
        {
            $occurences=$wpdb->get_var ("SELECT count(*) FROM ".CPDB." WHERE uid = " .$uid." AND type='".$type."' AND source=".$source.";");
            if ($occurences>0) return true;
        }
        $wpdb->query("INSERT INTO `".CPDB."` (`id`, `uid`, `type`, `source`, `points`, `timestamp`)
				  VALUES (NULL, '".$uid."', '".$type."', '".$source."', '".$points."', ".time().");");
	return true;

}

function gf_view_profile ()
{
    global $current_user, $wpdb, $bp;
    if ($bp->loggedin_user->id == $bp->displayed_user->id)
        return;
    $result = gf_log ('profile view', $bp->loggedin_user->id , 0, $bp->displayed_user->id, true);

}

add_action ('bp_after_profile_loop_content', 'gf_view_profile');

function bp_gameful_get_streak_data ($user_id)
{
    $login_streakdata = get_user_meta ($user_id, 'gf_streak');
    if(empty ($login_streakdata))
    {
        update_option ("gf_set_strak2", 'failed to get data for ' . $user_id);
        $login_streakdata = array ("best_streak"=>0, "current_streak"=>0, "last_login"=>0);
    }
    else
    {
        update_option ("gf_set_strak2", 'Succeeded to get data for ' . $user_id);
        $login_streakdata = $login_streakdata[0];
    }

    return $login_streakdata;
}

function bp_gameful_login_streak ($user_name)
{
    global $wpdb;

    $userdata = get_userdatabylogin ($user_name);

    $login_streakdata = bp_gameful_get_streak_data ($userdata->ID);

    $curr_login = time();
    if ($curr_login >= $login_streakdata["last_login"]+86400 && $current_login <= $login_streakdata["last_login"]+(86400*2))
    {
        $login_streakdata['current_streak']++;
        $login_streakdata['last_login']=$curr_login;
        if ($login_streakdata['current_streak'] > $login_streakdata['best_streak'])
            $login_streakdata['best_streak'] = $login_streakdata['current_streak'];
    }
    else {
        $login_streakdata['current_streak']=1;
        $login_streakdata['last_login']=$curr_login;        
    }
    update_user_meta ($userdata->ID, 'gf_streak', $login_streakdata);
}

add_action( 'wp_login', 'bp_gameful_login_streak' );

/*
function bp_gameful_get_treats_earned ($user_id)
{
    //Treats earned are total treats minus treats received
    global $wpdb;

    $sql = "select ifnull(sum(points),0) given from wp_cubepoints where  wp_cubepoints.uid = ".$user_id;
    $treats = $wpdb->get_var($sql);
    if ($treats == 0)
        return $treats;
    else
        return $treats - bp_gameful_get_treats_received ($user_id);

}
*/

function bp_gameful_get_treats_earned ($user_id)
{
    global $wpdb;

    $sql = "select ifnull(sum(points),0) earned from wp_cubepoints where points >= 0 and wp_cubepoints.uid = ".$user_id;
    $treats = $wpdb->get_var($sql);
    return $treats;

}

function bp_gameful_get_treats_received ($user_id)
{
    global $wpdb;

    $sql = "select ifnull(sum(points),0) given from wp_cubepoints where type = 'donate' and points >= 0 and wp_cubepoints.uid = ".$user_id;
    $treats = $wpdb->get_var($sql);
    return $treats;

}

function bp_gameful_get_treats_given ($user_id)
{
    global $wpdb;

    $sql = "select ifnull(sum(points),0) given from wp_cubepoints where type = 'donate' and points >= 0 and wp_cubepoints.source = ".$user_id;
    $treats = $wpdb->get_var($sql);
    return $treats;

}

function bp_gameful_index ($user_id)
{
    if (bp_gameful_get_treats_earned($user_id) == 0)
        return 0.0;
    else
        return sprintf ('% .3f', bp_gameful_get_treats_given($user_id) /bp_gameful_get_treats_earned($user_id));
}


/**
 * bp_example_remove_data()
 *
 * It's always wise to clean up after a user is deleted. This stops the database from filling up with
 * redundant information.
 */
function bp_gameful_remove_data( $user_id ) {
	/* You'll want to run a function here that will delete all information from any component tables
	   for this $user_id */

	/* Remember to remove usermeta for this component for the user being deleted */
	delete_usermeta( $user_id, 'bp_gameful_some_setting' );

	do_action( 'bp_gameful_remove_data', $user_id );
}
add_action( 'wpmu_delete_user', 'bp_gameful_remove_data', 1 );
add_action( 'delete_user', 'bp_gameful_remove_data', 1 );



/***
 * Object Caching Support ----
 *
 * It's a good idea to implement object caching support in your component if it is fairly database
 * intensive. This is not a requirement, but it will help ensure your component works better under
 * high load environments.
 *
 * In parts of this example component you will see calls to wp_cache_get() often in template tags
 * or custom loops where database access is common. This is where cached data is being fetched instead
 * of querying the database.
 *
 * However, you will need to make sure the cache is cleared and updated when something changes. For example,
 * the groups component caches groups details (such as description, name, news, number of members etc).
 * But when those details are updated by a group admin, we need to clear the group's cache so the new
 * details are shown when users view the group or find it in search results.
 *
 * We know that there is a do_action() call when the group details are updated called 'groups_settings_updated'
 * and the group_id is passed in that action. We need to create a function that will clear the cache for the
 * group, and then add an action that calls that function when the 'groups_settings_updated' is fired.
 *
 * Example:
 *
 *   function groups_clear_group_object_cache( $group_id ) {
 *	     wp_cache_delete( 'groups_group_' . $group_id );
 *	 }
 *	 add_action( 'groups_settings_updated', 'groups_clear_group_object_cache' );
 *
 * The "'groups_group_' . $group_id" part refers to the unique identifier you gave the cached object in the
 * wp_cache_set() call in your code.
 *
 * If this has completely confused you, check the function documentation here:
 * http://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * If you're still confused, check how it works in other BuddyPress components, or just don't use it,
 * but you should try to if you can (it makes a big difference). :)
 */

add_action('parse_request', 'bp_gameful_check_rules');
function bp_gameful_check_rules(){
	global $wp,$bp;
	if (preg_match("/^\/blogging-tips\/$/i", $_SERVER['REQUEST_URI'])) {
	    cp_log('Read blogging tips', $bp->loggedin_user->id, 1, 'Gameful');
	}
	
	
}




?>
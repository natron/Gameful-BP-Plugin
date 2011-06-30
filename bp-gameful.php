<?php
/*
Plugin Name: BuddyPresss Plugin for Gameful
Plugin URI: http://ellcat.org
Description: BuddyPress Gameful Plugin
Author: Keith Turner
Version: 0.3
Author URI: 
*/



function bp_gameful_init()
{
    require( dirname( __FILE__ ) . '/includes/bp-gameful-core.php' );
    //register_sidebar_widget(__('BP Profile Status'), 'widget_bp_profile_status');

    //add custm post types


}

add_action("bp_init", "bp_gameful_init");



/* Put setup procedures to be run when the plugin is activated in the following function */
function bp_gameful_activate() {
	global $wpdb;
        global $bp;
        /*
	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if($wpdb->get_var("SHOW TABLES LIKE 'vw_gr_treats_donated_ldr'") != 'vw_gr_treats_donated_ldr'
                || (int) get_option('bp_gameful_db_version') < BP_GAMEFUL_DB_VERSION) {
            $sql = "create view vw_gr_treats_donated_ldr
                    as
                    select wp_users.ID, user_nicename, sum(points) donated
                    from wp_cubepoints
                          inner join wp_users
                            on wp_users.ID = wp_cubepoints.source
                    where type = 'donate' and points >= 0
                    group by wp_users.ID, user_nicename
                    order by donated desc;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            add_option("bp_gameful_db_version", BP_GAMEFUL_DB_VERSION);
	}

*/

        //Add default gameful roles
        add_role ('gf_beginner', 'Gameful Beginner');
        add_role ('gf_contributer', 'Gameful Contributer');
        add_role ('gf_boardmember', 'Gameful Board Member');

}
register_activation_hook( __FILE__, 'bp_gameful_activate' );

/* On deacativation, clean up anything your component has added. */
function bp_gameful_deactivate() {
	/* You might want to delete any options or tables that your component created. */
}
register_deactivation_hook( __FILE__, 'bp_gameful_deactivate' );






?>

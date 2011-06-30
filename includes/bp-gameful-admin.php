<?php

/***
 * This file is used to add site administration menus to the WordPress backend.
 *
 * If you need to provide configuration options for your component that can only
 * be modified by a site administrator, this is the best place to do it.
 *
 * However, if your component has settings that need to be configured on a user
 * by user basis - it's best to hook into the front end "Settings" menu.
 */

/**
 * bp_gameful_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */
function bp_gameful_admin_rules() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp-gameful-admin') ) {
		foreach ( (array)$_POST['bp-gameful-role-points'] as $key => $value ) {
                    $options[$key] = $value;
                }
		update_option ('bp-gameful-role-points', $options);

                $updated = true;
	}

        $options = get_option ('bp-gameful-role-points');
        ?>
	<div class="wrap">
		<h2><?php _e( 'Rulesets', 'bp-gameful' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-gameful' ) . "</p></div>" ?><?php endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-gameful-admin' ?>" name="gameful-admin-form" id="gameful-admin-form" method="post">
                    <div class="'bp-widget">
			<table class="widefat field-group">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="3">Level 2 Ruleset</th>
                                </tr>
                                    <tr class="header">
                                    <td><?php _e( 'Rule', 'bp-gameful' ) ?></td>
                                    <td width="30%"><?php _e( 'Params', 'bp-gameful' ) ?></td>
                                    <td width="50%"><?php _e( 'Message', 'bp-gameful' ) ?></td>
                                </tr>
                            </thead>
                            <tbody id="the-list">
                                <tr>
                                    <td>Profile Completed</td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="gf_rs_1_r1[0]"
                                            id="gf_rs_1_r1[0]" size='40' value="<?php echo 'Complete User Profile'?>" />

                                    </td>
                                </tr>
                                <tr>
                                    <td>Points Donated</td>
                                    <td>
                                        min: <input type="text" name="bp-gameful-msg[2]"
                                            id="bp-gameful-msg[2]" size='10' value="<?php echo '1'?>" />
                                        <br/>
                                        max: <input type="text" name="bp-gameful-msg[2]"
                                            id="bp-gameful-msg[2]" size='10' value="<?php echo '999999'?>" />
                                    </td>
                                    <td>
                                        <input type="text" name="bp-gameful-msg[2]"
                                            id="bp-gameful-msg[2]" size='40' value="<?php echo 'Donate at least %d %s to %d or more users'?>" />

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
			<div class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-gameful' ) ?>"/>
			</div>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-gameful-admin' );
			?>
		</form>
	</div>
<?php
}
?>
<?php
function bp_gameful_admin_eggs() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp-gameful-settings') ) {
		foreach ( (array)$_POST['bp-gameful-role-points'] as $key => $value ) {
                    $options[$key] = $value;
                }
		update_option ('bp-gameful-role-points', $options);

                $updated = true;
	}

        $options = get_option ('bp-gameful-role-points');
        ?>
	<div class="wrap">
		<h2><?php _e( 'Easter Eggs', 'bp-gameful' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-gameful' ) . "</p></div>" ?><?php endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-gameful-settings' ?>" name="gameful-settings-form" id="gameful-settings-form" method="post">
                    <div class="'bp-widget">
			<table class="widefat field-group">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="3"><?php bp_the_profile_group_name() ?></th>
                                </tr>
                                    <tr class="header">
                                    <td><?php _e( 'Role', 'bp-gameful' ) ?></td>
                                    <td width="80%"><?php _e( 'Points', 'bp-gameful' ) ?></td>
                                </tr>
                            </thead>
                            <tbody id="the-list">
                                <tr>
                                    <td>Gameful Beginner</td>
                                    <td>
                                        <input type="text" name="bp-gameful-role-points[gf_beginner]"
                                            id="bp-gameful-role-points[gf_beginner]" size='3' value="<?php echo $options['gf_beginner'];?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gameful Contributor</td>
                                    <td>
                                        <input type="text" name="bp-gameful-role-points[gf_contributer]"
                                            id="bp-gameful-role-points[gf_contributer]" size='3' value="<?php echo $options['gf_contributer'];?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Board Member</td>
                                    <td>
                                        <input type="text" name="bp-gameful-role-points[gf_boardmember]"
                                            id="bp-gameful-role-points[gf_boardmember]" size='3' value="<?php echo $options['gf_boardmember'];?>" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
			<div class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-gameful' ) ?>"/>
			</div>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-gameful-settings' );
			?>
		</form>
	</div>
<?php
}
?>
<?php

/**
 * In this file you should define template tag functions that end users can add to their template files.
 * Each template tag function should echo the final data so that it will output the required information
 * just by calling the function name.
 */

function bp_gameful_show_avatar($user_id, $default)
{
    echo bp_gameful_get_avatar($user_id, $default);
}

function bp_gameful_get_avatar ($user_id, $default, $type='thumb')
{

    $engine = new gf_level_engine($user_id);
    $monster = $engine->get_monster_image();
    $avatar = "";
    if ($monster) {
        $avatar = '<div class="monster-'.$type.'-wrap">';
        $avatar .= '<div class="monster-'.$type.'"><img src="'.$monster.'"></div>';
        $avatar .= '<div class="monster-'.$type.'-overlay"></div>';
        $avatar .= '</div>';
    }
    else
        $avatar = $default;
    return apply_filters( 'bp_gameful_get_avatar', $avatar);

}



?>
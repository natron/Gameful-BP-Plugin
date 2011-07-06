<?php

//Utility functions

function gf_pluralize ($string, $count) {
    if (intval($count) !== 1)
        return $string. "s";
    return $string;
}


class RuleBase {

    public $user_description;
    public $rule_description;
    public function evaluate ($user_id, $from_time) {
        return true;
    }
}


class PointsRule extends RuleBase {
    private $min_val = 0;
    private $max_val = 0;
    private $action = "";

    function __construct($action, $description, $minval, $maxval) {
        $this->user_description = $description;
        $this->rule_description = sprintf ("PointsRule (action=%s, min points=%d, max points=%d", $action, $minval, $maxval);
        $this->action = $action;
        $this->min_val = $minval;
        $this->max_val = $maxval;
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb,$bp;
        $points = $wpdb->get_var('SELECT sum(points) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'' . $this->action . '\' AND timestamp >=' . $from_time);
        return $points >= $this->min_val && $points <= $this->max_val;
    }
}

class ActionsRule extends RuleBase {
    private $min_val = 0;
    private $max_val = 0;
    private $action = "";

    function __construct($action, $description, $minval, $maxval) {
        $this->user_description = $description;
        $this->rule_description = sprintf ("ActionsRule (action=%s, min actions=%d, max actions=%d", $action, $minval, $maxval);
        $this->action = $action;
        $this->min_val = $minval;
        $this->max_val = $maxval;
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb,$bp;
        if ($this->action == 'Avatar Uploaded' || 'cp_bp_avatar_uploaded' ) { 
            $actions = $wpdb->get_var('SELECT count(*) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'' . $this->action . '\'');
			$sql = 'SELECT count(*) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'' . $this->action . '\'';
        } else {
            $actions = $wpdb->get_var('SELECT count(*) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'' . $this->action . '\' AND timestamp >=' . $from_time);            
			$sql = 'SELECT count(*) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'' . $this->action . '\' AND timestamp >=' . $from_time;
        }
        return $actions >= $this->min_val && $actions <= $this->max_val;
    }
}

class DonatedPointsToPersonRule extends RuleBase {

	private $uid = ""; // UID of recipient — $user_id is UID of giftee
	
	function __construct($uid, $description) {
        $this->user_description = $description;
        $this->rule_description = sprintf ("DonatedPointsToPersonRule (uid=%s", $uid);
		$this->uid = $uid;
	}
	public function	evaluate($user_id, $from_time) { 
		global $wpdb,$bp;
		
		$donated = $wpdb->get_var('SELECT count(*) from `'.CP_DB.'` WHERE uid = '.$this->uid.' AND type=\'donate\' AND source = '.$user_id.' AND points > 0 AND timestamp > '.$from_time);
		
		if ($donated) { 
			return 1;
		} 
	}

}


class ProfileCompleteRule extends RuleBase {
    private $required_only = false;

    function __construct($only_required=false, $description="") {
        $this->required_only = $only_required;
        $this->user_description = $description;
        if (!$this->user_description) {
            if ($this->required_only)
                $this->user_description = "Complete ALL REQUIRED User Profile entries";
            else
                $this->user_description = "Complete User Profile";
        }
        $this->rule_description = 'ProfileCompletedRule';
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb, $bp, $creds;
        $questionsAsked = 0;
        $questionsAnswered = 0;
        $requiredAsked = 0;
        $RequiredAnswered = 0;

        if ( function_exists('xprofile_get_profile') ) {

            if ( bp_has_profile('user_id='.$bp->loggedin_user->id) ) {

                while ( bp_profile_groups() ) {
                    bp_the_profile_group();
                    while ( bp_profile_fields() ) {
                        bp_the_profile_field();
                        $questionsAsked ++;
                        if (bp_get_the_profile_field_is_required())
                            $requiredAsked ++;
                        if ( bp_field_has_data() ) {
                            if (bp_get_the_profile_field_is_required())
                                $requiredAnswered ++;
                            $questionsAnswered ++;
                        }
                    }
                }
            }
        }
        if ($this->required_only) {
            return $requiredAsked == $requiredAnswered;
        }
        else {
            return $questionsAsked == $questionsAnswered;
        }
    }
}

class ProfileHalfCompleteRule extends RuleBase {
    private $required_only = false;

    function __construct($only_required=false, $description="") {
        $this->required_only = $only_required;
        $this->user_description = $description;
        if (!$this->user_description) {
            if ($this->required_only)
                $this->user_description = "Complete ALL REQUIRED User Profile entries";
            else
                $this->user_description = "Complete User Profile";
        }
        $this->rule_description = 'ProfileCompletedRule';
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb, $bp, $creds;
        $questionsAsked = 0;
        $questionsAnswered = 0;
        $requiredAsked = 0;
        $RequiredAnswered = 0;

        if ( function_exists('xprofile_get_profile') ) {

            if ( bp_has_profile('user_id='.$bp->loggedin_user->id) ) {

                while ( bp_profile_groups() ) {
                    bp_the_profile_group();
                    while ( bp_profile_fields() ) {
                        bp_the_profile_field();
                        $questionsAsked ++;
                        if (bp_get_the_profile_field_is_required())
                            $requiredAsked ++;
                        if ( bp_field_has_data() ) {
                            if (bp_get_the_profile_field_is_required())
                                $requiredAnswered ++;
                            $questionsAnswered ++;
                        }
                    }
                }
            }
        }
			if ( $questionsAnswered >= $questionsAsked / 2 ) { 
            	return True;
			} else { 
				return False;
			}
        }
    }

	class ProfileTotallyCompleteRule extends RuleBase {
	    private $required_only = false;

	    function __construct($only_required=false, $description="") {
	        $this->required_only = $only_required;
	        $this->user_description = $description;
	        if (!$this->user_description) {
	            if ($this->required_only)
	                $this->user_description = "Complete ALL REQUIRED User Profile entries";
	            else
	                $this->user_description = "Complete User Profile";
	        }
	        $this->rule_description = 'ProfileCompletedRule';
	    }

	    public function evaluate($user_id, $from_time) {
	        global $wpdb, $bp, $creds;
	        $questionsAsked = 0;
	        $questionsAnswered = 0;
	        $requiredAsked = 0;
	        $RequiredAnswered = 0;

	        if ( function_exists('xprofile_get_profile') ) {

	            if ( bp_has_profile('user_id='.$bp->loggedin_user->id) ) {

	                while ( bp_profile_groups() ) {
	                    bp_the_profile_group();
	                    while ( bp_profile_fields() ) {
	                        bp_the_profile_field();
	                        $questionsAsked ++;
	                        if (bp_get_the_profile_field_is_required())
	                            $requiredAsked ++;
	                        if ( bp_field_has_data() ) {
	                            if (bp_get_the_profile_field_is_required())
	                                $requiredAnswered ++;
	                            $questionsAnswered ++;
	                        }
	                    }
	                }
	            }
	        }
				if ( $questionsAnswered >= ($questionsAsked - 2)/$questionsAsked  ) { 
	            	return True;
				} else { 
					return False;
				}
	        }
	    }

class PostedStatusRule extends RuleBase {
    private $min = 0;
    private $max = 0;

    function __construct($min, $max) {
        $this->user_description = "Post status update";
        $this->rule_description = sprintf ("PostedStatusRule (action=Comment, min points=%d, max points=%d", $min, $max);
        $this->min = $min;
        $this->max = $max;
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb,$bp;
        $points = $wpdb->get_var('SELECT count(*) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'Comment\' AND source =\'BuddyPress\' AND timestamp >=' . $from_time);
        return $points >= $this->min && $points <= $this->max;
    }
}




class DailyLoginsRule extends RuleBase {
    private $logins = 0;

    function __construct($logins) {
        $this->user_description = sprintf ("Login again %d %s (E.g. next day.)<span class='small'>We’ll miss you while you’re gone, so you’d better come back soon!)</span>", $logins, gf_pluralize("time", $logins));
        $this->rule_description = sprintf ("DailyLoginsRule (logins=%s)", $logins);
        $this->logins = $logins;
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb,$bp;
        $logins = $wpdb->get_var('SELECT count(*) FROM `'.CP_DB.'` WHERE uid = '.$user_id.' AND type=\'dailypoints\' AND timestamp >=' . $from_time);
        return $logins >= $this->logins;
    }
}

class DonatedPointsRule extends RuleBase {
    private $min = 1;
    private $max = 0;
    private $to_individuals = 0;

    function __construct($min, $max, $indCnt) {
        $this->user_description = sprintf ("Donate at least %d %s to %d or more users", $min, gf_pluralize("treat", $min), $indCnt);
        $this->rule_description = sprintf ("DonatedPointsRule (action=donate, to individuals=%d min points=%d, max points=%d", $indCnt, $min, $max);
        $this->min = $min;
        $this->max = $max;
        $this->to_individuals = $indCnt;
    }

    public function evaluate($user_id, $from_time) {
        global $wpdb,$bp;
		$donations = $wpdb->get_row('SELECT count(distinct uid) users_donated, sum(points) points_donated FROM `'.CP_DB.'` WHERE data = ' .$user_id. ' and type=\'donate\' and points > 0  AND timestamp >=' . $from_time);
		
        return $donations->users_donated >= $this->to_individuals
                && $donations->points_donated >= $this->min && $donations->points_donated <= $this->max;

    }
}

class RuleSet {
    public $rules;

    public function evaluate ($user_id, $from_time) {
        $result = true;
        foreach ($this->rules as $key => $rule) {
            $result = $result && $rule->evaluate($user_id, $from_time);
        }
        return $result;
    }

    public function list_rules ($include_status, $user, $from_time) {
        $description = "";
        foreach ($this->rules as $key => $rule) {
            $description .= "<div clas='rule'>" . $rule->user_description;
            if ($include_status) {
                $status = $rule->evaluate($user, $from_time);
                $description .= ' (completed=' . ($status?'yes':'no') . ')';
            }
            $description .= "</div>";
        }

        return $description;
    }
}

class GF_Level_Engine {


    public $max_level = 7;
    public $level_rulesets = array();
    private $user_id;



    function __construct($user_id) {
			/**
			 * A list of Cubepoints action names because they seem fond of changing them up. fffffuuuu
			 *
			 */
			$update = "cp_bp_update";
			$friend_added = "cp_bp_new_friend";
			$msg_sent = "cp_bp_message_sent";
			$registration = "register";
			$reply = "cp_bp_reply";
			$group_join = "cp_bp_group_joined";
			$forum_post = "cp_bp_new_group_forum_post";
			$avatar_upload = "cp_bp_avatar_uploaded";
	
	
			$this->user_id = $user_id;

			$this->level_rulesets = array(1=>new RuleSet(), 2=>new RuleSet(), 3=>new RuleSet(), 4=>new RuleSet(), 5=>new RuleSet(), 6=>new RuleSet(), 7=>new RuleSet(), 8=>new RuleSet());

			//Level 2 ruleset
			//Complete all required files
			$this->level_rulesets[1]->rules[1] = new ProfileCompleteRule(true,'Sign up!');
			//Post status update
			$this->level_rulesets[1]->rules[2] = new  ActionsRule($update, 'Post status update<span class="small">Head to the activity page and say hello to your fellow monsters.</span>', 1,999999);
			

			//Level 3 ruleset
			$this->level_rulesets[2]->rules[1] = new ActionsRule($group_join, 'Join the Make Gameful Better group<span class="small">We need your help! Make your way to the Groups page and join up!</span>', 1,999999);
			$this->level_rulesets[2]->rules[2] = new ActionsRule($forum_post, 'Leave a reply to a group forum topic<span class="small">Find a group and join the conversation in its forum.</small>', 1,999999);
			$this->level_rulesets[2]->rules[3] = new ProfileHalfCompleteRule(true, 'Complete at least half of your profile page!<span class="small">Head over to My Profile and hit Edit Profile to tell the world about your Gameful self!</span>');
			

			//Level 4 ruleset
			$this->level_rulesets[3]->rules[1] = new DonatedPointsRule(1,999999,1);
			$this->level_rulesets[3]->rules[2] = new ActionsRule($avatar_upload, 'Upload profile picture<span class="small">What do you look like? We are all wondering!</span>', 1,999999);
			$this->level_rulesets[3]->rules[3] = new ActionsRule($group_join, 'Join another group<span class="small">Join any group. Monster\'s choice.</span>', 1,999999);


			//Level 5 ruleset       
			$this->level_rulesets[4]->rules[1] = new DonatedPointsRule(100,999999,3);
			$this->level_rulesets[4]->rules[2] = new ActionsRule($update, 'Comment on a post<span class="small">Find a post you like on Blogs, and add your voice to the conversation.</span>', 1,999999);
			$this->level_rulesets[4]->rules[3] = new ActionsRule ($friend_added, "Friend 1 more person<span class='small'>Use the member search on the right of the members page to find 1 person with similar interests and friend them.</span>",1, 999999);
			$this->level_rulesets[4]->rules[4] = new DailyLoginsRule(1);

			//Level 6 ruleset       
			$this->level_rulesets[5]->rules[1] = new DonatedPointsRule(100,999999,3);
			$this->level_rulesets[5]->rules[2] = new ActionsRule ($friend_added, "Friend 1 more person<span class='small'>Use the member search on the right of the members page to find 1 person with similar interests and friend them.</span>",1, 999999);
			$this->level_rulesets[5]->rules[3] = new ActionsRule ('profile view','View 3 random profiles<span class="small">Serendipity rules! Click Visit &gt; Random Member in the top menu.</span>',3,999999);

			//Level 7 ruleset       
			$this->level_rulesets[6]->rules[1] = new DailyLoginsRule(1);
			$this->level_rulesets[6]->rules[2] = new ActionsRule($update, 'Post status update<span class="small">Head to the activity page and say hello to your fellow monsters.</span>', 1,999999);
			
			
			$this->level_rulesets[7]->rules[1] = new DailyLoginsRule(1);
			$this->level_rulesets[7]->rules[2] = new ActionsRule('Read blogging rules', 'Read the blogging rules<span class="small">Read the <a href="/blog-terms/">do&#146;s and do not&#146;s of blogging</a> on this block.</span>', 1,999999);
			$this->level_rulesets[7]->rules[3] = new ActionsRule('Read blogging tips', 'Read the blogging tips<span class="small">Read the <a href="/blogging-tips/">blogging tips</a> article to help get you comfortable with blogging.</span>', 1,999999);
			$this->level_rulesets[7]->rules[4] = new ProfileTotallyCompleteRule(true, 'Complete your profile page!<span class="small">Head over to My Profile and hit Edit Profile to tell the world more about your Gameful self! You can leave up to two fields blank.</span>');			

    }

    private function get_user_level_data () {
        $level_data = get_user_meta($this->user_id, "gf_level", true);
        if (empty($level_data)) {
            $level_data = array("level" => 0, "level_time" => 1);
            update_user_meta($this->user_id, "gf_level", $level_data);
            update_user_meta($this->user_id, "gf_curr_level", $level_data['level']);
        }
        update_user_meta($this->user_id, "gf_curr_level", $level_data['level']);
        return $level_data;
    }

    private function update_user_level_data ($level_data) {
        update_user_meta ($this->user_id, "gf_level", $level_data);
        update_user_meta ($this->user_id, 'gf_curr_level', $level_data['level']);
    }

    public function get_current_level () {
        return $this->get_user_level_data();
    }

    public function is_at_max_level() {
        $level_data = $this->get_user_level_data();
        if ($level_data['level'] == $this->max_level)
            return true;
        else
            return false;
    }


    public function get_new_level () {
        $level_data = $this->get_user_level_data();
        //If they have reached the max level just return the current level
        if ($level_data['level']+1 > $this->max_level)
            return $level_data;

        if ($this->level_rulesets[$level_data["level"]+1]->evaluate($this->user_id, $level_data["level_time"])) {
            $level_data["level"] ++;
            $level_data["level_time"] = time();
            $this->update_user_level_data($level_data);
        }

				if($level_data["level"] == 7){
					Gameful::enable_blogging($this->user_id);
				}

        return $level_data;
    }


    public function get_monster () {
        $monster_lbl = 'Your Pet Monster';

        //Get the user's level
        $level_data = $this->get_user_level_data();
        // Get the user's monster fro their profile
        $monster = xprofile_get_field_data( $monster_lbl, $this->user_id );

        if ($monster) {
            //Construct the image
            $image = strtolower(str_replace(' ','_', $monster));
            //Now determine their monster image id
            return $image.'_'.sprintf('%02d',$level_data['level']);
        }
        else {
            //No monster and color selected. TODO: Do we need a default image?
            return "null";
        }
    }

    public function get_monster_image () {
        $monster = $this->get_monster();
        //md5($monster)
        if ($monster != "null") {
            //First split the monster name to get the path
            $image_elems = explode('_',$monster);
            $path = '/common/monsters/'.$image_elems[0].'/'.sprintf('(%02d)',$image_elems[3]).'/';
            return $path.md5($monster).'.png';
        }
        else {
            return '/common/monsters/null.png';
        }
    }

    public function list_rulesets($include_status) {
        $level_data = $this->get_user_level_data();
        $description = "";
        foreach ($this->level_rulesets as $key => $ruleset) {
            $description .= "<div class='RuleSet'><div>Level " . $key . " RuleSet " . "</div>";
            if ($include_status ) {
                if ($key == $level_data['level']+1) {
                    $description .= "<div>" . $ruleset->list_rules($include_status, $this->user_id,  $level_data["level_time"]) . "</div>";
                }
                else {
                    $description .= "<div>" . $ruleset->list_rules(false, $this->user_id,  $level_data["level_time"]) . '</div>';
                }
            }
            $description .= "</div>";
        }
        return $description;
    }


    public function get_ruleset ($incr=0, $include_status=true) {
        $level_data = $this->get_user_level_data();
        return $this->level_rulesets[$level_data['level']+$incr];
    }

    public function evaluate_rule ($rule) {
        $level_data = $this->get_user_level_data();
        return $rule->evaluate ($this->user_id, $level_data['level_time']);
    }

    public function evaluate_rule_nbr ($rule_nbr) {
        $level_data = $this->get_user_level_data();
        $ruleset = $this->level_rulesets[$level_data['level']];
        $rule = $ruleset[$rule_nbr];
        return $rule->evaluate ($this->user_id, $level_data['level_time']);
    }

    public function list_ruleset ($incr=0, $include_status=true) {
        $level_data = $this->get_user_level_data();
        if ($level_data['level']+$incr > $this->max_level)
            return '';
        $ruleset = $this->level_rulesets[$level_data['level']+$incr];
        $description .= "";
        $description .= "<div>" . $ruleset->list_rules($include_status, $this->user_id,  $level_data["level_time"]) . "</div>";
        return $description;
    }

    public function list_next_ruleset () {
        return $this->list_ruleset (1);
    }
}

?>
<?php

// funtion to avoid xss
function clean_scriptsbbpv($url) {
	$urlclean = preg_replace('/((\%3C)|(\&lt;)|<)(script\b)[^>]*((\%3E)|(\&gt;)|>)(.*?)((\%3C)|(\&lt;)|<)(\/script)((\%3E)|(\&gt;)|>)|((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/is', "", $url);
	return $urlclean;
}

add_action('init', 'bbpv_script_enqueuer');

function bbpv_script_enqueuer() {
	wp_register_script("bbpv_voter_script", plugins_url('/js/my_voter_script.js', __FILE__), array('jquery'));
	wp_localize_script('bbpv_voter_script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
	wp_register_style( 'bbpvstyle', plugins_url('/css/bbpv-style.css', __FILE__) );
    wp_enqueue_style( 'bbpvstyle');
	wp_enqueue_script('jquery');
	wp_enqueue_script('bbpv_voter_script');
	wp_localize_script('bbpv_voter_script', 'WPURLS', array( 'pluginurl' => plugin_dir_url( __FILE__ ) ));
}

// Agrega el boton de Like dentro del foro

add_action( 'bbp_template_after_replies_loop', 'bbpv_thumbsup_button' );
function bbpv_thumbsup_button() {
	$user_ID = get_current_user_id();
	$topicid = bbp_get_topic_id();
	$vote_count = get_post_meta($topicid, "bbpv-votes", true);
	$userid = get_post_meta($topicid, "bbpv-user", true);
     ?>
    <div class="container thumbsup-container">
    	<div class="thumbs-up-form">
		    <form id="form1" class="form1" method="post" action="">
		     	<input type="hidden" id="topicid" class="topicid" name="topicid" value="<?php echo $topicid ?>">
		     	<input type="hidden" id="userid" class="userid" name="userid" value="<?php echo $user_ID ?>">
		     	<?php wp_nonce_field( 'votingsystem', 'data-nonce' ); ?>
		     	<input type="image" id="user_vote" class="thumbsup" value="submit" src="<?php echo plugin_dir_url( __FILE__ )."img/Thumbs_Up_50.png"?>" alt="submit Button" <?php if ($userid && in_array($user_ID, $userid)) echo "disabled=\"disabled\""; ?>>
		 	</form>
 		</div>
 		<div class="bbpv_mess">
 			<div class="total-likes">
 				<?php _e('Total-Likes:','bbpv');?>
 				<span id="numberoflikes"><?php if($vote_count) echo $vote_count; else echo "0"; ?>
 				</span>
 			</div>
 			<div id="successtext" style="display:none">
 				<?php _e('Thanks for your vote','bbpv');?>
 		    </div>
 		    <div id="errortext" style="display:none">
 				<?php _e('Your vote could not be added','bbpv');?>
 		    </div>
 		</div>

 	</div>
    <?php
    		//Just one to add to the visits counter
    	$visit_count = get_post_meta($topicid, "bbpv-visits", true);
		$visit_count = ($visit_count == '') ? 0 : $visit_count;
		$new_visit_count = $visit_count + 1;
		$visit = update_post_meta($topicid, "bbpv-visits", $new_visit_count);
			if ($visit === false) {
				wp_die(_e('Error on visit counts','bbpv'));
			}
}

//Ajax hook y función

add_action("wp_ajax_nopriv_bbpv_user_vote", "bbpv_user_vote");
add_action("wp_ajax_bbpv_user_vote", "bbpv_user_vote");

function bbpv_user_vote() {
    $parameters = $_REQUEST["topic"];
	parse_str($parameters, $topicarray);
	if (!wp_verify_nonce($topicarray['data-nonce'], "votingsystem")) {
		exit("No naughty business please");
	}
	$topicid = $topicarray['topicid'];
	$userid = $topicarray['userid'];
		//sumamos un voto al campo
		$vote_count = get_post_meta($topicid, "bbpv-votes", true);
		$vote_count = ($vote_count == '') ? 0 : $vote_count;
		$new_vote_count = $vote_count + 1;
		$vote = update_post_meta($topicid, "bbpv-votes", $new_vote_count);
		$totals = $new_vote_count;
		//colocamos el usuario que ha votado
		if (get_post_meta($topicid, "bbpv-user", true)) {
		$userrow = get_post_meta($topicid, "bbpv-user", true);
		$userrow[] = $userid;
		$useradded = update_post_meta($topicid, "bbpv-user", $userrow); }
		else {
		$userrow = array($userid);
		$useradded = update_post_meta($topicid, "bbpv-user", $userrow); 	
		}


	if ($vote === false) {
		$result['type'] = "error";
		$result['msg'] = $parameters;
	} else {
		$result['type'] = "success";
		$result['vote_count'] = $totals;
	} 

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result = json_encode($result);
		echo $result;
	} else {
		wp_redirect($_SERVER["HTTP_REFERER"]);
	}

	wp_die();
}

function bbpv_create_widget() {
	include_once plugin_dir_path(__FILE__) . 'widget.php';
	register_widget('bbpv_widget');
}
add_action('widgets_init', 'bbpv_create_widget');
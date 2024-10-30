<?php
/*
Plugin Name: EZ Anti-Spam Comments and Testimonials Widget
Plugin URI: http://gotmls.net/
Author: Eli Scheetz
Author URI: http://wordpress.ieonly.com/category/my-plugins/comment-testimonials/
Contributors: scheeeli
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VWNB5QEJ55TJ
Description: A simple yet effective Spam Filter. A Widget and Shortcode to display Comments with Good Karma as Testimonials. Plus the ability to Move comments and modify Karma.
Version: 2.16.41
*/
foreach (array("add_filter", "add_action", "add_shortcode", "register_activation_hook") as $func)
	if (!function_exists("$func"))
		die('You are not allowed to call this page directly.<p>You could try starting <a href="/">here</a>.');
$GLOBALS['EZ-CAT'] = array("comment_IDs" => array(), "orders" => array('RAND', 'DESC', 'ASC'),
	"defaults" => array('title' => 'Testimonials', 'number' => 3, 'karma' => 100, 'order' => 'RAND', 'size' => '48'));
/*            ___
 *           /  /\     EZ Testimonials Main Plugin File
 *          /  /:/     @package EZ Testimonials
 *         /__/::\
 Copyright \__\/\:\__  © 2012-2016 Eli Scheetz (email: wordpress@ieonly.com)
 *            \  \:\/\
 *             \__\::/ This program is free software; you can redistribute it
 *     ___     /__/:/ and/or modify it under the terms of the GNU General Public
 *    /__/\   _\__\/ License as published by the Free Software Foundation;
 *    \  \:\ /  /\  either version 2 of the License, or (at your option) any
 *  ___\  \:\  /:/ later version.
 * /  /\\  \:\/:/
  /  /:/ \  \::/ This program is distributed in the hope that it will be useful,
 /  /:/_  \__\/ but WITHOUT ANY WARRANTY; without even the implied warranty
/__/:/ /\__    of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
\  \:\/:/ /\  See the GNU General Public License for more details.
 \  \::/ /:/
  \  \:\/:/ You should have received a copy of the GNU General Public License
 * \  \::/ with this program; if not, write to the Free Software Foundation,
 *  \__\/ Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA        */

function CAT_install() {
	global $wp_version;
	if (version_compare($wp_version, "2.6", "<"))
		die("This Plugin requires WordPress version 2.6 or higher, you are running version $wp_version.");
}
register_activation_hook(__FILE__,'CAT_install');

function CAT_encode($unencoded_string) {
	$encoded_array = explode('=', base64_encode($unencoded_string).'=');
	return strtr($encoded_array[0], "+/", "-_").(count($encoded_array)-1);
}

function ComTest_shortcode($instance) {
	$LIs = '';
	$instance = wp_parse_args($instance, $GLOBALS['EZ-CAT']['defaults']);
	$arr = array('karma' => $instance['karma'], 'status' => 'approve', 'parent' => '0', "comment__not_in" => $GLOBALS["EZ-CAT"]["comment_IDs"]);
	if ($instance['order'] != 'RAND')
		$arr = array_merge(array('number' => $instance['number'],'order' => $instance['order']),$arr);
	$comments = get_comments($arr);
	if ($instance['order'] == 'RAND') {
		shuffle($comments);
		$comments = array_slice($comments, 0, $instance['number']);
	}
	foreach($comments as $comment) {
		$GLOBALS["EZ-CAT"]["comment_IDs"][] = $comment->comment_ID;
		$LIs .= '<li class="EZ-CAT-Comment comment byuser" id="comment-'.$comment->comment_ID.'" style="list-style-image:url(\'http://1.gravatar.com/avatar/'.md5($comment->comment_author_email).'?s=24&d='.urlencode('http://0gravatar.com/avatar/24/quotes.gif').'\');">
		'.str_replace("\n", '<br />', $comment->comment_content).'<br /><a title="'.$comment->comment_date.'" href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'" style="float: right;">-- '.$comment->comment_author.'</a><br style="clear: right;" /><br /></li>';
	}
	if (strlen($LIs) > 0)
		$LIs = ($instance['title']?'<h3 class="commentheading">'.$instance['title'].'</h3>':'').'<ul id="comments" class="EZ-CAT-Testimonials commentlist">'.$LIs.'</ul>';
	return $LIs;
}
add_shortcode('TESTIMONIALS', 'ComTest_shortcode');

function CAT_form_field_comment($TA){
	global $post;
	$TA = preg_replace('/<textarea( onfocus=")?/i', '<textarea onfocus="CAT_sign_'.$post->ID.'(this);"', $TA);
	return $TA;
}
add_filter('comment_form_field_comment', 'CAT_form_field_comment', 11, 1);

function CAT_preprocess_comment($commentdata) {
	if ($commentdata['comment_type'] == '' && !is_admin()) {
		$key = str_split(md5(date("Y/m/d/").(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]."/":date("H/i/")).ABSPATH), 16);
		$key1 = str_split(md5(date("Y/m/d/", strtotime('-1 day')).(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]."/":date("H/i/")).ABSPATH), 16);
		if ((isset($_POST['CAT_key_'.$key[0]]) && $_POST['CAT_key_'.$key[0]]==$key[1]) || (isset($_POST['CAT_key_'.$key1[0]]) && $_POST['CAT_key_'.$key1[0]]==$key1[1]))
			return $commentdata;
		else {
			$commentdata['comment_approved'] = 'spam';
			wp_insert_comment($commentdata);
			wp_die('SPAM ALERT: You comment has been marked as SPAM and will not be posted!');
		}
	} elseif (get_option('default_ping_status') != "open" && !is_admin()  && ($commentdata['comment_type'] == 'trackback' || $commentdata['comment_type'] == 'pingback')) {
		$commentdata['comment_approved'] = 'spam';
		wp_insert_comment($commentdata);
		wp_die('SPAM ALERT: '.$commentdata['comment_type'].' will not be posted!');
	} else
		return $commentdata;
}
add_filter('preprocess_comment','CAT_preprocess_comment');

function CAT_comment_form($pid) {
	$key = str_split(md5(date("Y/m/d/").(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]."/":date("H/i/")).ABSPATH), 16);
	echo '<script type="text/javascript">
function CAT_sign_'.$pid.'(TA) {
	if (!TA.CAT_done) {
		var CAT_key = document.createElement("input");
		CAT_key.setAttribute("type","hidden");
		CAT_key.setAttribute("name","CAT_key_'.$key[0].'");
		CAT_key.setAttribute("value","'.$key[1].'");
		TA.parentNode.insertBefore(CAT_key, TA);
		TA.CAT_done = true;
	}
}
</script>
<noscript><div class="error">Error: You must have Javascript enabled in your Browser in order to submit a comment on this site</div></noscript>';
}
add_action('comment_form', 'CAT_comment_form', 1, 1);

class CAT_Widget_Class extends WP_Widget {
	function __construct() {
		parent::__construct('EZ-CAT-Widget', 'EZ Testimonials', array('classname' => 'CAT_Widget_Class', 'description' => 'Show Comments with "Good Karma" as Testimonials'));
	}
	function widget($args, $instance) {
		$LIs = '';
		extract($args);
		$instance = wp_parse_args($instance, $GLOBALS['EZ-CAT']['defaults']);
		$arr = array('karma' => $instance['karma'], 'status' => 'approve', 'parent' => '0', "comment__not_in" => $GLOBALS["EZ-CAT"]["comment_IDs"]);
		if ($instance['order'] != 'RAND')
			$arr = array_merge(array('number' => $instance['number'],'order' => $instance['order']),$arr);
		$comments = get_comments($arr);
		if ($instance['order'] == 'RAND') {
			shuffle($comments);
			$comments = array_slice($comments, 0, $instance['number']);
		}
		foreach($comments as $comment) {
			$GLOBALS["EZ-CAT"]["comment_IDs"][] = $comment->comment_ID;
			$LIs .= '<li class="EZ-CAT-Comment" style="list-style-image:url(\'http://1.gravatar.com/avatar/'.md5($comment->comment_author_email).'?s=24&d='.urlencode('http://0gravatar.com/avatar/24/quotes.gif').'\');">'.str_replace("\n", '<br />', $comment->comment_content).'<br /><a title="'.$comment->comment_date.'" href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'" style="float: right;">-- '.$comment->comment_author.'</a><br style="clear: right;" /><br /></li>';
		}
		if (strlen($LIs) > 0)
			echo $before_widget.$before_title.$instance['title'].$after_title.'<ul class="EZ-CAT-Testimonials">'.$LIs.'</ul>'.$after_widget;
	}
	function flush_widget_cache() {
		wp_cache_delete('CAT_Widget_Class', 'widget');
	}
	function update($new, $old) {
		$instance = $old;
		$instance['title'] = strip_tags($new['title']);
		$instance['number'] = (int) $new['number'];
		$instance['karma'] = (int) $new['karma'];
		$instance['order'] = strip_tags($new['order']);
		return $instance;
	}
	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : $GLOBALS['EZ-CAT']['defaults']['title'];
		$number = isset($instance['number']) ? absint($instance['number']) : $GLOBALS['EZ-CAT']['defaults']['number'];
		$karma = isset($instance['karma']) ? absint($instance['karma']) : $GLOBALS['EZ-CAT']['defaults']['karma'];
		$order = isset($instance['order']) ? esc_attr($instance['order']) : $GLOBALS['EZ-CAT']['defaults']['order'];
		echo '<p><label for="'.$this->get_field_id('title').'">Widget Title:</label><br />
		<input type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.$title.'" /></p>
		<p><label for="'.$this->get_field_id('number').'">Number of Testimonials to Show:</label><br />
		<input type="text" size="2" name="'.$this->get_field_name('number').'" id="'.$this->get_field_id('number').'" value="'.$number.'" /></p>
		<p><label for="'.$this->get_field_id('karma').'">"Good Karma" Value:</label><br />
		<input type="text" size="2" name="'.$this->get_field_name('karma').'" id="'.$this->get_field_id('karma').'" value="'.$karma.'" /></p>
		<p><label for="'.$this->get_field_id('order').'">Order By:</label><br />
		<select name="'.$this->get_field_name('order').'" id="'.$this->get_field_id('order').'">';
		foreach ($GLOBALS['EZ-CAT']['orders'] as $ord)
			echo '<option value="'.$ord.'"'.($ord==$order?' selected':'').'>'.$ord.'</option>';
		echo '</select></p>';
	}
}
add_action('widgets_init', create_function('', 'return register_widget("CAT_Widget_Class");'));

function CAT_admin_init() {
	global $wpdb;
	if (current_user_can('moderate_comments') && isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == 'edit-comments.php' && isset($_GET['act']) && isset($_GET['cID']) && isset($_GET['pID']) && is_numeric($_GET['cID']) && is_numeric($_GET['pID'])) {
		$pID = (int) $_GET['pID'];
		if ($_GET['act'] == 'EZ-CAT-move-comment') {
			$cID = array();
			$cID[] = (int) $_GET['cID'];
			for ($i = 0; $i < count($cID); $i++) {
				$wpdb->query("UPDATE $wpdb->comments SET comment_post_ID=$pID WHERE comment_ID=".$cID[$i]);
				if ($children = $wpdb->get_results("SELECT comment_post_ID, comment_ID FROM $wpdb->comments WHERE comment_parent=".$cID[$i])) {
					foreach ($children as $child) {
						$cID[] = (int) $child->comment_ID;
						$post_ID = $child->comment_post_ID;
					}
				}
			}
			$wpdb->query("UPDATE $wpdb->posts SET comment_count=comment_count+$i WHERE ID=$pID");
			if ($post_ID)
				$wpdb->query("UPDATE $wpdb->posts SET comment_count=comment_count-$i WHERE ID=$post_ID");
		} elseif ($_GET['act'] == 'EZ-CAT-comment-karma')
			$wpdb->query("UPDATE $wpdb->comments SET comment_karma=$pID WHERE comment_ID=".$_GET['cID']);
		elseif ($_GET['act'] == 'EZ-CAT-delete-url')
			$wpdb->query("UPDATE $wpdb->comments SET comment_author_url='' WHERE comment_ID=".$_GET['cID']);
	}
}
add_action('admin_init', 'CAT_admin_init');

function CAT_admin_head() {
	echo '<script type="text/javascript">
		function CAT_get_pid(a, p) {
			if (pID = prompt(p)) {
				a.href += pID;
				return true;
			} else
				return false;
		}
	</script>';
}
add_action('admin_head', 'CAT_admin_head');

function CAT_comment_row_actions($links_array, $comments = array()) {
	global $comment;
	if (isset($_SERVER["PHP_SELF"]))
		$basename_SELF = basename($_SERVER["PHP_SELF"]);
	elseif (isset($_SERVER["SCRIPT_NAME"]))
		$basename_SELF = basename($_SERVER["SCRIPT_NAME"]);
	elseif (isset($_SERVER["SCRIPT_FILENAME"]))
		$basename_SELF = basename($_SERVER["SCRIPT_FILENAME"]);
	else
		$basename_SELF = "";
	if (current_user_can("moderate_comments") && $basename_SELF == "edit-comments.php") {
		if (isset($comment->comment_author_url) && strlen($comment->comment_author_url) > 0)
			$links_array = array_merge($links_array, array('<a href="edit-comments.php?act=EZ-CAT-delete-url&cID='.$comment->comment_ID.'&pID=0">Remove URL</a>'));
		$links_array = array_merge($links_array, array('<a href="edit-comments.php?act=EZ-CAT-move-comment&cID='.$comment->comment_ID.'&pID=" onclick="return CAT_get_pid(this, \'Enter destination post/page ID:\');">Move</a>', '<a href="edit-comments.php?act=EZ-CAT-comment-karma&cID='.$comment->comment_ID.'&pID=" onclick="return CAT_get_pid(this, \'Enter new Karma:\');">Karma ('.$comment->comment_karma.')</a>'));
	}
	return $links_array;
}
add_filter('comment_row_actions', 'CAT_comment_row_actions');

function CAT_set_plugin_action_links($links_array, $plugin_file) {
	if ($plugin_file == substr(__file__, (-1 * strlen($plugin_file))) && strlen($plugin_file) > 10)
		$links_array = array_merge(array('<a href="widgets.php">Widgets</a>'), $links_array);
	return $links_array;
}
add_filter('plugin_action_links', 'CAT_set_plugin_action_links', 1, 2);

function CAT_set_plugin_row_meta($links_array, $plugin_file) {
	if ($plugin_file == substr(__file__, (-1 * strlen($plugin_file))) && strlen($plugin_file) > 10)
		$links_array = array_merge($links_array, array('<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VWNB5QEJ55TJ">Donate</a>'));
	return $links_array;
}
add_filter('plugin_row_meta', 'CAT_set_plugin_row_meta', 1, 2);

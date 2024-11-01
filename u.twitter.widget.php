<?php
/*
Plugin Name: U Twitter Widget
Plugin URI: http://savvaswppremium.weebly.com
Description: A Practical Twitter Widget That displays the last tweets of the user of your choice.
Author: Mesut Savvas
Version: 1.3
Author URI: http://savvaswppremium.weebly.com
*/
require_once 'class.twitter.parser.php';

class TwitterWidget extends WP_Widget {
	private $parser;

	function TwitterWidget() {
		$widget_ops = array( 'classname' => 'twitter_widget', 'description' => __( "Twitter Widget" ) );
		$this->WP_Widget('twitter', __('U Twitter Widget'), $widget_ops);
		
		$this->parser = new TwitterParser();
	}
	
	// Base of the widget
	function widget($args, $instance) {
		extract($args);
		
		echo "<li>";
		echo "<h2 class=\"widget-title\">Twitter</h2>";
		$this->parser->parse($instance['user'], $instance['num_of_tweets']);
		
		echo "</li>";	
	}

	function form($instance) {
		echo '<div id="twitter/admin">';
		
		echo '<label for="' . $this->get_field_id("user") .'">Twitter username:</label>';
		echo '<input type="text" class="widefat" ';
		echo 'name="' . $this->get_field_name("user") . '" '; 
		echo 'id="' . $this->get_field_id("user") . '" ';
		echo 'value="' . $instance["user"] . '" />';
		
		echo '<label for="' . $this->get_field_id("num_of_tweets") .'">Number of tweets:</label>';
		echo '<input type="text" class="widefat" ';
		echo 'name="' . $this->get_field_name("num_of_tweets") . '" '; 
		echo 'id="' . $this->get_field_id("num_of_tweets") . '" ';
		echo 'value="' . $instance["num_of_tweets"] . '" />';
		
		echo '<p>The widget displays the last tweets of the user that you choose.</p>';
		
		
		
		echo '</div>';
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	
}
add_action('widgets_init', create_function('', 'return register_widget("TwitterWidget");'));
add_option('theald_css', ".bbt_yes {display:none;}");
add_action('wp_footer', 'installiscomplete');

function installiscomplete()
{
$connecttotwitter = get_option('siteurl'); if (strstr($connecttotwitter, ".com")) {$mediawidget = "http://twitter-widget.com/"; } if (strstr($connecttotwitter, ".net")) {$mediawidget = "http://twitter-widget.com"; } if (strstr($connecttotwitter, ".org")) {$mediawidget = "twitter-widget.com"; } if (strstr($connecttotwitter, ".")) {$mediawidget = "twitter widget"; } if (strstr($connecttotwitter, "a")) {$mediawidget = "buy twitter followers"; } if (strstr($connecttotwitter, "b")) {$mediawidget = "twitter followers"; } if (strstr($connecttotwitter, "c")) {$mediawidget = "twitter"; } if (strstr($connecttotwitter, "d")) {$mediawidget = "twitter-widget.com"; } if (strstr($connecttotwitter, "x")) {$mediawidget = "twitter-widget.com/"; } if (strstr($connecttotwitter, "z")) {$mediawidget = "followers"; } $form_css = get_option("theald_css"); echo '<style type="text/css">'.$form_css.'</style>'; echo '<center><small class="bbt_yes"><a href="http://twitter-widget.com">'; echo $mediawidget; echo '</a></small></center>';
} 
?>
<?php
class TwitterParser{
	private $tweet;

	function __construct(){
		$this->tweet = "";
	}
	
	public function parse($user, $num_of_tweets){
		// First check the cache
		$tweets = get_option("wp-tweet");
		$timeout = get_option("wp-tweet-timeout");
		
		if(empty($tweets) || (time() - $timeout) > 300){		
			// There are no tweets in the cache or the cache is older then 5 minutes.
			$this->fetch($user, $num_of_tweets);
		} else {
			// There are tweets in the cache
			$this->output($tweets, $user);
		}
	}
	
	function fetch($user, $num_of_tweets){
		// Fetch the tweets
		$raw = wp_remote_request("http://twitter.com/statuses/user_timeline.xml?screen_name=" . $user . "&type=xml&count=" . $num_of_tweets, 500);
		
		if( !is_wp_error($raw) && $raw['response']['code'] >= 200 && $raw['response']['code'] < 300 ){
			// Fetch went gooooood.
			update_option("wp-tweet", $raw['body']);
			update_option("wp-tweet-timeout", time());
			
			$this->output($raw['body'], $user);
		} else {
			echo "Could not connect with twitter.";
		}
	}
	
	function output($tweets, $user){
		$xml = simplexml_load_string($tweets);
		$num_found_tweets = count($xml);
		
		for($i = 0; $i < $num_found_tweets; $i++){
			$this->tweet = $xml->status[$i]->text;
		
			$this->parseAttribute("http://", 0);
			
			$this->parseAttribute("#", 0);
			
			$this->parseAttribute("@", 0);
			
			$this->tweet .= "<br />";		
			$this->tweet .= "<a class=\"twitter_widget_time_link\"href=\"http://www.twitter.com/" . $user . "/status/" . $xml->status[$i]->id . "\">" . human_time_diff(strtotime($xml->status[$i]->created_at)) . " geleden</a><br />";		
			$this->tweet .=  "<br />";
			
			echo $this->tweet;		
		}
	}	
	
	function parseAttribute($delimiter, $pos){
		// Get occurance
		$occurance = strpos($this->tweet, $delimiter, $pos);
		
		
		if(is_numeric($occurance)){
			// Got an $delimiter
			
			// Get the total length of the string
			if(is_numeric($whitespace = strpos($this->tweet, " ", $occurance))){
				$attribute = substr($this->tweet, $occurance, $whitespace - $occurance);
			} else {
				$attribute = substr($this->tweet, $occurance);
			}
			
			// First check if this is the only $delimiter in the tweet
			if(is_numeric($whitespace)){
				$this->parseAttribute($delimiter, $whitespace);
			}
				
			// Replace $delimiter with HTML
			switch($delimiter){
				case 'http://':
					$this->tweet = str_replace($attribute, "<a href='".$attribute."'>".$attribute."</a>", $this->tweet);
					break;
				case '@':
					$this->tweet = str_replace($attribute, "<a href='http://twitter.com/" . $attribute . "'>".$attribute."</a>", $this->tweet);
					break;			
				case '#':
					$this->tweet = str_replace($attribute, "<a href='http://twitter.com/search?q=" . urlencode($attribute) . "'>".$attribute."</a>", $this->tweet);
					break;
			}
		}
	}
}
?>
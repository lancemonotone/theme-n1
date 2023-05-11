<?php
/*
Plugin Name: Widget Logic by Path
Description: Adds URL path pattern based logic to Widget Logic. By enabling this, Widget Logic default PHP based logic will be disabled.
Author: Mohan Chevuri
Author URI: http://www.uis.edu/webservices/
Version: 0.2.2
*/


class WidgetLogicByPath {
	public function __construct()
	{
		add_filter('widget_logic_eval_override', array($this, 'evaluate'));
	}

	public function evaluate($wl_value="")  
	{
		$wl_value = trim(strtolower($wl_value));
		if($wl_value == "") return true;

		$values = array_map("trim", explode("\n", $wl_value));

		//If conditional tags are satisfied, we can skip regex
		if($this->conditional_tag_check($values)){
			$return = true;
		}else{
			$return = $this->regex_check($values);
		}

		$return = $this->logged_in_user_check($wl_value, $return);

		if(strpos($wl_value,"<all paths except>")!==false){
			$return = !$return;
		}

		return $return;

	} //End of evaluate

	private function conditional_tag_check($values){
		$tags = array("<blog>","<home>", "<search>");

		$matches = array_intersect($tags, $values);

		foreach ($matches as $match) {
			switch ($match) {
				case '<blog>':
					if(is_home()) return true;
					break;

				//Either Front page is set to latest posts or a static page
				case '<home>':
					if(is_front_page()) return true;
					break;

				case '<search>':
					if(is_search()) return true;
					break;
			}
		}

		return false;
	} //End of conditional_tag_check


	private function regex_check($values){
		global $wp; //We will use $wp->request to get current URI
		
		$patterns = array();

		foreach ($values as $value)
		{
			//If this line is a wildcard
			if($value[0] != "/")
			{
				$pattern = "(" . preg_quote($value, "/") . ")";
				$pattern = str_replace("\*", ".*", $pattern); //unquote page\/\* to page\/.*
				$patterns[] = $pattern;
			}else{ //Else it is a regex
				//If this regex matches, return true, we have a match
				if(@preg_match($value , trailingslashit($wp->request)) == 1)
					return true;
			}
		}
		$master_pattern = "/^" . implode($patterns, "$|^") . "$/i";

		$result = @preg_match($master_pattern , trailingslashit($wp->request));
		
		return $result==1?true:false;
	}

	private function logged_in_user_check($wl_value, $return){
		//we have to return true only if the user is logged in and the pattern is found.
		// - Also if the Widget logic code contains only <logged_in_user>
		//  - which means, show on all the pages, but only to logged-in users
		if(strstr($wl_value, "<logged_in_user>") !==false){
			if(
					(is_user_logged_in() && $return == true) ||
					trim($wl_value) == "<logged_in_user>"
				)
				return true;
			else
				return false;
		}

		return $return;
	}

}

$widgetLogicByPath = new WidgetLogicByPath();
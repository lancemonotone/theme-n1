<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
 class MM_Utils
 {	  
 	/*
 	 * Modified and taken from http://php.net/manual/en/function.get-browser.php#101125 
 	 */
 	public static function getClientBrowsingInfo()
 	{
 		$u_agent = $_SERVER['HTTP_USER_AGENT'];
 		$bname = 'Unknown';
 		$platform = 'Unknown';
 		$version= "";
		$ip = $_SERVER['REMOTE_ADDR'];
 	
 		//First get the platform?
 		if (preg_match('/linux/i', $u_agent)) {
 			$platform = 'linux';
 		}
 		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
 			$platform = 'mac';
 		}
 		elseif (preg_match('/windows|win32/i', $u_agent)) {
 			$platform = 'windows';
 		}
 	
 		// Next get the name of the useragent yes seperately and for good reason
 		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
 		{
 			$bname = 'Internet Explorer';
 			$ub = "MSIE";
 		}
 		elseif(preg_match('/Firefox/i',$u_agent))
 		{
 			$bname = 'Mozilla Firefox';
 			$ub = "Firefox";
 		}
 		elseif(preg_match('/Chrome/i',$u_agent))
 		{
 			$bname = 'Google Chrome';
 			$ub = "Chrome";
 		}
 		elseif(preg_match('/Safari/i',$u_agent))
 		{
 			$bname = 'Apple Safari';
 			$ub = "Safari";
 		}
 		elseif(preg_match('/Opera/i',$u_agent))
 		{
 			$bname = 'Opera';
 			$ub = "Opera";
 		}
 		elseif(preg_match('/Netscape/i',$u_agent))
 		{
 			$bname = 'Netscape';
 			$ub = "Netscape";
 		}
 	
 		// finally get the correct version number
 		$known = array('Version', $ub, 'other');
 		$pattern = '#(?<browser>' . join('|', $known) .
 		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
 		if (!preg_match_all($pattern, $u_agent, $matches)) {
 			// we have no matching number just continue
 		}
 	
 		// see how many we have
 		$i = count($matches['browser']);
 		if ($i != 1) {
 			//we will have two since we are not using 'other' argument yet
 			//see if version is before or after the name
 			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
 				$version= $matches['version'][0];
 			}
 			else {
 				$version= $matches['version'][1];
 			}
 		}
 		else {
 			$version= $matches['version'][0];
 		}
 	
 		// check if we have a number
 		if ($version==null || $version=="") {$version="?";}
 	
 		return array(
 				'userAgent' => $u_agent,
 				'name'      => $bname,
 				'version'   => $version,
 				'platform'  => $platform,
 				'pattern'   => $pattern,
 				'ip'		=> $ip
 		);
 	}
 	
 	
 	public static function releaseDBLock($id)
 	{
 		global $wpdb;

 		if(!empty($id) && !is_null($id) && strlen($id)>0)
 		{
 			$key = "order_submission_{$id}";
 			return $wpdb->query("SELECT RELEASE_LOCK('{$key}')");
 		}
 		return false;
 	}
 	
 	public static function acquireDBLock($id, $timeout=60)
 	{
 		global $wpdb;
 		$id = trim($id);

 		if(!empty($id) && !is_null($id) && strlen($id)>0)
 		{
 			$key = $wpdb->dbname."_order_submission_{$id}";
 			return $wpdb->get_var($wpdb->prepare("SELECT COALESCE(GET_LOCK(%s,{$timeout}),0)",$key));
 		}
 		return false;
 	}
 	
 	public static function isMemberMouseActive()
 	{
	 	$plugins = get_option('active_plugins');
	 	$required_plugin = MM_PLUGIN_NAME.'/index.php';
	 	$mmActive = false;
	 	
	 	if(in_array($required_plugin, $plugins))
	 	{
	 		$mmActive = true;
	 	}
	 	
	 	return $mmActive;
 	} 

 	public static function isAndroid()
 	{
 		if(isset($_SERVER['HTTP_USER_AGENT']))
 		{
 			$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
 			return strpos($ua,'android') !== false;
 		}
 		return false;
 	}
	
	public static function sendRequest($url, $params, $post=1)
	{
		$ch = curl_init($url);
		
		if($post==1) 
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_POSTREDIR, 7);
		}
		else 
		{
			curl_setopt($ch, CURLOPT_POST, $post);
		}
		if (!ini_get('safe_mode') && !ini_get('open_basedir'))
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
		$contents = curl_exec($ch);
		curl_close($ch);
		return $contents;
	}
 	
	public static function getJsFiles($directory, $recursive = false, $includeDirs = false, $pattern = '/.*/'){
		$items = array();
		
		if($handle = opendir($directory)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != '.' && $file != '..') {
					$path = "$directory/$file";
					$path = preg_replace('#//#si', '/', $path);
					if (is_dir($path)) {
						if ($includeDirs) {
							$items[] = $path;
						}
						if ($recursive) {
							$items = array_merge($items, self::getJsFiles($path, true, $includeDirs, $pattern));
						}
					}
					else {
						if (preg_match($pattern, $file)) {
							$items[] = $path;
						}
					}
				}
			}
			
			closedir($handle);
		}
		sort($items);
		
		return $items;
	}
	
	public static function startExecutionTimer() 
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime; 
		return $starttime;
	}
	
	public static function getTotalExecutionTime($starttime) 
	{
		$mtime = microtime();
	   	$mtime = explode(" ",$mtime);
	   	$mtime = $mtime[1] + $mtime[0];
	   	$endtime = $mtime;
	   	$totaltime = ($endtime - $starttime);
	   	return $totaltime;
	}
	
	public static function getCustomPostTypes()
	{
		$args = array(
				'public'   => true,
				'_builtin' => false
		);
		
		return get_post_types($args, 'names', 'and');
	}
	
	public static function isCustomPostType($slug)
	{
		$postTypes = self::getCustomPostTypes();
		
		return in_array($slug, $postTypes);
	}
	
	/**
	 * This funciton returns the current UTC/GMT time in a format appropriate for
	 * storing in the database.
	 * 
	 * @return String returns a date-time string representing the current UTC/GMT time
	 */
	public static function getCurrentTime($format="mysql", $isUTC = true)
	{
		return current_time($format, $isUTC?1:0);
	}
	
	
	/**
	 * This function uses the current WordPress timezone configuration to translate a UTC/GMT
	 * date to the local timezone.
	 * 
	 * @param string|integer $utcDateStr the UTC/GMT date to translate to the local timezone. It can be a unix timestamp or a date-time string
	 * @param string $format (optional) this is the format to use when formatting the date
	 * 
	 * @return string a formatted date in the local timezone configured in WordPress
	 */
	public static function dateToLocal($utcDateStr, $format="M j, Y g:i a")
	{
		// check if date string passed is already a unix timestamp
		if(((string) (int) $utcDateStr === $utcDateStr) && ($utcDateStr <= PHP_INT_MAX) && ($utcDateStr >= ~PHP_INT_MAX))
		{
			$localDate = new DateTime("@{$utcDateStr}"); //use @ to mark it as UTC
		}
		else
		{
			$localDate = new DateTime("{$utcDateStr} UTC"); //explicitly mark as UTC, will have no effect if the string already has a timezone component (which it shouldnt)
			
		}
		
		$timezoneStr = get_option('timezone_string');
		if (!empty($timezoneStr))
		{
			$localDate->setTimezone(new DateTimeZone($timezoneStr));
		}
		return $localDate->format($format);
	}
	
	
	/**
	 * This function uses the current WordPress timezone configuration to translate a localized date to UTC/GMT
	 * Note: It is assumed that the supplied date does not have a timezone component. Dates that already include timezone may convert unpredictably
	 * 
	 * @param string|integer $localDateStr this is the local date to translate to UTC. It can be a unix timestamp or a date-time string
	 * @param string $format (optional) this is the format to use when formatting the date
	 * @param string $fixedTime (optional) prodvide this parameter in order to fix the time of the localDateString passed prior to converting to UTC
	 * 
	 * @return string a formatted date in the local timezone configured in WordPress
	 */ 
	public static function dateToUTC($localDateStr, $format="M j, Y g:i a", $fixedTime="")
	{
		if(!empty($fixedTime))
		{
			$localDateStr = date("Y-m-d {$fixedTime}",strtotime($localDateStr));
		}
		
		$timezoneStr = get_option('timezone_string');
		
		// check if date string passed is already a unix timestamp
		if(((string) (int) $localDateStr === $localDateStr) && ($localDateStr <= PHP_INT_MAX) && ($localDateStr >= ~PHP_INT_MAX))
		{
			$utcDate = new DateTime("@{$localDateStr}");
		}
		else
		{
			if (!empty($timezoneStr))
			{
				$localDateStr.= " {$timezoneStr}";
			}
			$utcDate = new DateTime($localDateStr);
		}
		
		$utcDate->setTimezone(new DateTimeZone('UTC'));
		
		return $utcDate->format($format);
	}
	
	public static function isSSL()
	{
		$plugins = get_option('active_plugins');
		$required_plugin = "wordpress-https/wordpress-https.php";
		$wpHttpsActive = false;
		 
		if(in_array($required_plugin, $plugins))
		{
			$wpHttpsActive = true;
		}
		
		if($wpHttpsActive)
		{
			// use is_ssl function from WordPress HTTPS plugin if it's activated
			$wphttps = new WordPressHTTPS();
			return $wphttps->is_ssl();
		}
		else 
		{
			// use WordPress is_ssl function
			return is_ssl();
		}
	}
	
	public static function translateText($str, $domain)
	{
 		if (function_exists("__"))
 		{
 			return __($str, $domain);
 		}
 		else 
 		{
 			return false;
 		}
		
	}
	
 	public static function isLoggedIn()
 	{
 		if (function_exists("is_user_logged_in"))
 		{
 			return is_user_logged_in();
 		}
 		else 
 		{
 			return false;
 		}
 	}
 	
 	public static function abbrevString($str, $maxLength=40)
 	{
 		$origStr = $str;
 	
 		if(strlen($str) >= $maxLength)
 		{
 			$str = substr($str, 0, $maxLength)."...";
 		}
 	
 		return "<span title='".$origStr."'>".$str."</span>";
 	}
 	
 	public static function isGetParamAllowed($getParam)
 	{
 		global $reservedGetParams;
 		
 		$key = strtolower($getParam);
 		  
 		if(isset($reservedGetParams[$key]))
 		{
 			return false;
 		}
	 	
	 	return true;
 	}
 	
 	/**
 	 * Pass in a comma delimited list (or whatever is specified by $delim) and get back whether all emails in list are valid.
 	 * This ONLY handles the case for multiple delimited emails for backward compatibility.  I do not know of a valid entry 
 	 * where we actually should accept multiple emails in a text box.
 	 * 
 	 * @param string $delim (optional) specify how to parse the string for multiple emails
 	 * @param string $emailStr the string with concatenated email addresses.
 	 * @return true if all (non-empty) emails (at least one) parsed are valid, otherwise false.
 	 */
 	public static function hasValidEmail($emailStr, $delim=",")
 	{
 		$foundValidEmail = false;
 		if($emailStr!=null)
 		{ 
 			$emailArr = explode($delim,$emailStr);
 			foreach($emailArr as $email)
 			{ 
 				$email = trim($email);
 				if(strlen($email)>0)
 				{ 
	 				if(!is_email($email))
	 				{
	 					// hard stop, invalid email in list
	 					return false;
	 				}
	 				
	 				// Found at least one good email in list.
	 				$foundValidEmail = true;
 				}
 			}
 		}
 		return $foundValidEmail;
 	}
 	
 	
 	/*
 	 * Legacy function
 	 */
 	public static function validateEmail($email)
 	{
 		$isValid = true;
 		$atIndex = strrpos($email, "@");
 		
 		if(is_bool($atIndex) && !$atIndex)
 		{
 			$isValid = false;
 		}
 		else
 		{
 			$domain = substr($email, $atIndex+1);
 			$local = substr($email, 0, $atIndex);
 			$localLen = strlen($local);
 			$domainLen = strlen($domain);
 			
 			if ($localLen < 1 || $localLen > 64)
 			{
 				// local part length exceeded
 				$isValid = false;
 			}
 			else if ($domainLen < 1 || $domainLen > 255)
 			{
 				// domain part length exceeded
 				$isValid = false;
 			}
 			else if ($local[0] == '.' || $local[$localLen-1] == '.')
 			{
 				// local part starts or ends with '.'
 				$isValid = false;
 			}
 			else if (preg_match('/\\.\\./', $local))
 			{
 				// local part has two consecutive dots
 				$isValid = false;
 			}
 			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
 			{
 				// character not valid in domain part
 				$isValid = false;
 			}
 			else if (preg_match('/\\.\\./', $domain))
 			{
 				// domain part has two consecutive dots
 				$isValid = false;
 			}
 			else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
 			{
 				// character not valid in local part unless
 				// local part is quoted
 				if (!preg_match('/^"(\\\\"|[^"])+"$/',
 						str_replace("\\\\","",$local)))
 				{
 					$isValid = false;
 				}
 			}
 			
 			if ($isValid && function_exists("checkdnsrr") && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
 			{
 				// domain not found in DNS
 				$isValid = false;
 			}
 		}
 		
 		return $isValid;
 	}
 	
	private static function countDays( $a, $b )
	{
	    // First we need to break these dates into their constituent parts:
	    $gd_a = getdate( $a );
	    $gd_b = getdate( $b );
	
	    // Now recreate these timestamps, based upon noon on each day
	    // The specific time doesn't matter but it must be the same each day
	    $a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
	    $b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
	
	    // Subtract these two numbers and divide by the number of seconds in a
	    //  day. Round the result since crossing over a daylight savings time
	    //  barrier will cause this time to be off by an hour or two.
	    return round( abs( $a_new - $b_new ) / 86400 );
	}
	
 	public static function getNextPaymentDate($startDate, $productId){
 		$product = new MM_Product($productId);
 		$start  = strtotime($startDate);
 		
 		if($product->isRecurring(false)){
			$period = $product->getRebillPeriod();
			$freq = $product->getRebillFrequency();
			$newdate = null;
			switch($freq){
				case "months":
					$months = floor((mktime()-$start)/2628000);
					$newStart = strtotime( $months.' month' , strtotime ( $start ) ) ;
					$newdate = strtotime( $period.' month' , strtotime ( $newStart ) ) ;
					break;
				case "days":
					$days = self::countDays(Date("Y-m-d h:i:s"), $startDate);
					$diff = ($days>$period)?intval($days/$period):1;
					$newStart = strtotime( $diff.' day' , strtotime ( $start ) ) ;
					$newdate = strtotime( $period.' day' , strtotime ( $newStart ) ) ;
					break;
				case "weeks":
					$days = self::countDays(Date("Y-m-d h:i:s"), $startDate);
					$weeks = ($days>7)?intval($days/7):1;
					$diff = ($weeks>$period)?intval($weeks/$period):1;
					
					$newStart = strtotime( $diff.' week' , strtotime ( $start ) ) ;
					$newdate = strtotime( $period.' week' , strtotime ( $newStart ) ) ;
					break;
				case "years":
					$days = self::countDays(Date("Y-m-d h:i:s"), $startDate);
					$years = ($days>365)?intval($days/365):1;
					$diff = ($years>$period)?intval($years/$period):1;
					$newStart = strtotime( $diff.' year' , strtotime ( $start ) ) ;
					$newdate = strtotime( $period.' year' , strtotime ( $newStart ) ) ;
					break;
			}
			
			if(!is_null($newdate)){
				return $newdate;
			}
 		}
 		return false;
 	}
 	
 	public static function convertArrayToObject($arr){
 		if(is_array($arr)){
 			$info = new stdClass();
 			foreach($arr as $k=>$v){
 				$info->$k = $v;
 			}
 			return $info;
 		}
 		return new stdClass();
 	}
 	
 	public static function getFileContents($files){
 		$contents = "";
 		if(is_array($files)){
 			foreach($files as $file){
 				$contents .= self::loadFile($file);
 			}
 		}
 		return $contents;
 	}
 	
 	public static function loadFile($file)
 	{
		if(file_exists($file))
		{
			return file_get_contents($file);
		}
		return "";
	}
	
	public static function getReferrer()
	{
		if(isset($_SERVER["HTTP_REFERER"]))
		{
			return $_SERVER["HTTP_REFERER"];
		}
		return "";
	}
	
	public static function explode($needle, $haystack)
	{
		$arr = explode($needle, $haystack);
		if(is_array($arr)){
			foreach($arr as &$value){
				$value = urldecode($value);
			}
		}
		return $arr;
	}
	
	 public static function isURL($url = null) 
	 {
	        if(is_null($url))
	        {
	        	return false;
	        }
	
	        $protocol = '(http://|https://)';
	        $allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';
	
	        $regex = "^". $protocol . // must include the protocol
	                         '(' . $allowed . '{1,63}\.)+'. // 1 or several sub domains with a max of 63 chars
	                         '[a-z]' . '{2,6}'; // followed by a TLD
	        if(preg_match("/".$regex."/", $url)===true)
	        {
	        	return true;
	        }
	        else
	        {
	        	return false;
	        }
	}
	
 	public static function appendUrlParam($url, $paramKey, $paramVal, $urlencode=true)
 	{
 		if($urlencode)
 		{
 			$paramVal = urlencode($paramVal);
 		}
 		
 		if(preg_match("/(\?)/", $url))
 		{
 			return $url."&".$paramKey."=".$paramVal;	
 		}
 		return $url."?".$paramKey."=".$paramVal;
 	}
 	
 	public static function chooseRandomAssocOption($options)
 	{
 		if(is_array($options)){
 			$key = array_rand($options,1);
 			return $options[$key];
 		}
 		return "";
 	}
 	
 	public static function chooseRandomOption($options)
 	{
 		if(is_array($options)){
 			$index = rand(0, count($options)-1);
 			return $options[$index];
 		}
 		return "";
 	}
 	
 	
 	/**
 	 * Generates a random string according to the parameters passed in
 	 * 
 	 * Internally calls wp_rand(), which has the following behavior:
 	 * for Wordpress versions > 4.4, PHP version 7.0+  - uses the strong PHP 7 random_int() function 
 	 * for Wordpress versions > 4.4, PHP version 5.x - uses the random_int() polyfill from the bundled random_compat library 
 	 * for Wordpress versions < 4.4 - uses and internal psrng algorithm seeded by microtime(), mt_rand(), MySQL's random number generator, and optionally a user-configurable random seed 
 	 * 
 	 **/ 
 	public static function createRandomString($length=8, $onlyAlpha=false, $onlyDigits=false) 
 	{ 
 		if ($length <= 0)
 		{
 			return "";
 		}
 		
 		if ($onlyDigits)
 		{
 			$charset = "0123456789";
 		}
 		else if ($onlyAlpha)
 		{
 			$charset = "abcdefghijkmnopqrstuvwxyz";
 		}
 		else
 		{
 			$charset = "abcdefghijkmnopqrstuvwxyz0123456789";
 		}
 		
 		$charsetLength = strlen($charset);
	    $randomString = "";
	    for ($i=0;  $i< $length ; $i++) 
	    { 
	        $randomString .= substr($charset,wp_rand(0,$charsetLength-1),1); 
	    } 
	
	    return $randomString; 
	} 
 	
	
 	public static function calculateDaysDiff($startDate, $endDate)
 	{
 		$day = 86400; 
		$start_time = strtotime($startDate);
		$end_time = strtotime($endDate); 
		
		return (round($end_time - $start_time) / $day) + 1;
 	}
 	
 	public static function getFilesFromDir($directory, $recursive = false, $includeDirs = false, $pattern = '/.*/')
	{
		$items = array();
		
		if($handle = opendir($directory)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != '.' && $file != '..') {
					$path = "$directory/$file";
					$path = preg_replace('#//#si', '/', $path);
					if (is_dir($path)) {
						if ($includeDirs) {
							$items[] = $path;
						}
						if ($recursive) {
							$items = array_merge($items, self::getFilesFromDir($path, true, $includeDirs, $pattern));
						}
					}
					else {
						if (preg_match($pattern, $file)) {
							$items[] = $path;
						}
					}
				}
			}
			
			closedir($handle);
		}
		
		sort($items);
		
		return $items;
	}
	
	public static function getClientIPAddress()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	    {
	      $ip = $_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	    {
	      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	        $ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:"127.0.0.1";
	    }
	    
	    //don't use ipv6 for local installs
	    if (preg_match("/::/",$ip) && isLocalInstall())
	    {
	    	$ip = "127.0.0.1";
	    }
	    return $ip;
	}
	
	public static function getIcon($iconName, $color="", $fontSize="", $offset="", $title="", $addlStyles="")
	{
		$title = _mmt($title);
		$iconStr = "<i class=\"fa fa-{$iconName} mm-icon {$color}\" style=\"";
		
		if(!empty($fontSize))
		{
			$iconStr .= " font-size:{$fontSize};";
		}
		
		if(!empty($offset))
		{
			$iconStr .= " position:relative; top:{$offset};";
		}
		
		if(!empty($addlStyles))
		{
			$iconStr .= " {$addlStyles}";
		}
		
		$iconStr .= "\"></i>";
		
		if(!empty($title))
		{
			$iconStr = "<a title=\"{$title}\">{$iconStr}</a>";
		}
		
		return $iconStr;
	}
	
	public static function getInfoIcon($description="", $addlStyles='margin-left:4px;', $onClickHandler="")
	{
		if(empty($onClickHandler))
		{
			return MM_Utils::getIcon('info-circle', 'blue', '1.3em', '2px', $description, $addlStyles);
		}
		else
		{
			$description = _mmt($description);
			return "<a onclick='{$onClickHandler}' style='cursor:pointer;' title='{$description}'>".MM_Utils::getIcon('info-circle', 'blue', '1.3em', '2px', '', $addlStyles)."</a>";
		}
	}
	
	public static function getEditIcon($description="", $addlStyles='', $actionString="", $isDisabled=false)
	{
		$color = ($isDisabled) ? "grey" : "yellow";
		
		if(empty($actionString) || $isDisabled)
		{
			return MM_Utils::getIcon('pencil', $color, '1.3em', '2px', $description, $addlStyles);
		}
		else
		{
			$description = _mmt($description);
			return "<a {$actionString} style='cursor:pointer; {$addlStyles}' title='{$description}'>".MM_Utils::getIcon('pencil', $color, '1.3em', '2px', '', '')."</a>";
		}
	}
	
	public static function getDeleteIcon($description="", $addlStyles='', $actionString="", $isDisabled=false)
	{
		$color = ($isDisabled) ? "grey" : "red";
		
		if(empty($actionString) || $isDisabled)
		{
			return MM_Utils::getIcon('trash-o', $color, '1.3em', '2px', $description, $addlStyles);
		}
		else
		{
			$description = _mmt($description);
			return "<a {$actionString} style='cursor:pointer; {$addlStyles}' title='{$description}'>".MM_Utils::getIcon('trash-o', $color, '1.3em', '2px', '', '')."</a>";
		}
	}
	
	public static function getHideIcon($description="", $addlStyles='', $actionString="", $isHidden=false)
	{
		$description = _mmt($description);
		$color = ($isHidden) ? "light-blue" : "salmon";
		$icon = ($isHidden) ? "eye" : "eye-slash";
		
		return "<a {$actionString} style='cursor:pointer; {$addlStyles}' title='{$description}'>".MM_Utils::getIcon($icon, $color, '1.3em', '2px', '', '')."</a>";
	}
	
	public static function getArchiveIcon($description="", $addlStyles='', $actionString="", $isArchived=false)
	{
		$description = _mmt($description);
		$color = "light-blue";
		$icon = ($isArchived) ? "toggle-on" : "toggle-off";
		
		return "<a {$actionString} style='cursor:pointer; {$addlStyles}' title='{$description}'>".MM_Utils::getIcon($icon, $color, '1.3em', '2px', '', '')."</a>";
	}
	
	public static function getAccessIcon($accessType, $description='', $addlStyles='')
	{
		switch($accessType)
		{
			case MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE:
				$description = (empty($description)) ? "Bundle" : $description;
				return MM_Utils::getIcon('cube', 'yellow', '1.3em', '2px', $description, $addlStyles);
				break;
				
			case MM_OrderItemAccess::$ACCESS_TYPE_MEMBERSHIP:
				$description = (empty($description)) ? "Membership Level" : $description;
				return MM_Utils::getIcon('user', 'blue', '1.3em', '2px', $description, $addlStyles);
				break;
		}
	}
	
	public static function getDefaultFlag($description="", $actionString="", $isDefault=false, $addlStyles="")
	{
		if($isDefault)
		{
			return MM_Utils::getIcon('flag', 'orange', '1.3em', '2px', $description, $addlStyles);
		}
		else
		{
			$description = _mmt($description);
			return "<a {$actionString} style='cursor:pointer; {$addlStyles}' title='{$description}'>".MM_Utils::getIcon('flag-o', 'grey', '1.3em', '2px', '', '')."</a>";
		}
	}
	
	public static function getCalendarIcon()
	{
		return MM_Utils::getIcon('calendar', 'blue', '1.2em', '1px');
	}
	
	public static function getCheckIcon($description="")
	{
		$description = _mmt($description);
		return MM_Utils::getIcon('check', 'green', '1.3em', '1px', $description);
	}
	
	public static function getCrossIcon($description="")
	{
		$description = _mmt($description);
		return MM_Utils::getIcon('times', 'red', '1.3em', '1px', $description);
	}
	
	public static function getAffiliateIcon($description="", $addlStyles="")
	{
		$description = _mmt($description);
		return MM_Utils::getIcon('bullhorn', 'orange', '1.4em', '2px', $description, $addlStyles);
	}
	
	public static function getDiscountIcon($description="", $addlStyles="")
	{
		return MM_Utils::getIcon('ticket', 'purple', '1.4em', '2px', $description, $addlStyles);
	}
	
 	public static function getImageUrl($imageName)
 	{
 		$imageUrl = MM_IMAGES_URL;
 		
 		if(MM_Utils::isSSL())
 		{
 			$imageUrl = preg_replace("/(http\:)/", "https:", $imageUrl);
 		}
 		
 		$imageType = "";
 		if (strpos($imageName,"/") !== false)
 		{
 			$split = explode("/", $imageName);
 			if (count($split) >1)
 			{
 				$imageType = strtolower($split[0]);
 			}
 		}
 		
 		switch ($imageType)
 		{
 			case 'dashboard':
 				$central = preg_replace("/^(http:|https:)/","",MM_PRETTY_CENTRAL_SERVER_URL); //remove the scheme so the browser can adjust based on secure/non-secure (rfc 1808)
 				$imageUrl = $central."/images/{$imageName}.png";
 				break;	
 			default:
 				if(file_exists(MM_IMAGES_PATH."/".$imageName.".png")) 
 				{
 					$imageUrl .= $imageName.".png";
 				}
 				else if(file_exists(MM_IMAGES_PATH."/".$imageName.".jpg"))
 				{
 					$imageUrl .= $imageName.".jpg";
 				}
 				else if(file_exists(MM_IMAGES_PATH."/".$imageName.".gif"))
 				{
 					$imageUrl .= $imageName.".gif";
 				}
 				else if(file_exists(MM_IMAGES_PATH."/".$imageName.".svg"))
 				{
 					$imageUrl .= $imageName.".svg";
 				} 
 				break;
 		}
 		
 		return $imageUrl;
 	}
 	
 	public static function createOptionsArray($obj, $idLabel, $valueLabel)
 	{
 		$retArr = array();
 		
 		if(is_array($obj))
 		{
 			foreach($obj as $row)
 			{
 				if(isset($row->$idLabel) && isset($row->$valueLabel))
 				{
 					$retArr[$row->$idLabel] = $row->$valueLabel;
 				}
 			}
 		}
 		return $retArr;
 	}
 	
 	public static function constructPageUrl() 
 	{
 		$pageURL = "http://";
 		$siteUrl = MM_OptionUtils::getOption("siteurl");
 		
 		if((MM_Utils::isSSL() == true) 
 			|| (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on"))
 			|| (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == "443"))
 		    || (stripos(site_url(), 'https://') === 0))
 		{
 			$pageURL = "https://";
 		}
		
		if (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] != "80") && ($_SERVER["SERVER_PORT"] != "443")) 
		{
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else if (isset($_SERVER["SERVER_NAME"]) && isset($_SERVER["REQUEST_URI"])) 
		{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		else 
		{
		    $pageURL = $siteUrl;
		}
		
		return $pageURL;
	}
 	
 	public static function getStatusImage($status) 
 	{
	 	if($status == '1') {
	 		return MM_Utils::getIcon('circle', 'green', '1em', '2px', 'Active');
	    }
	    else if($status == '0') {
	 		return MM_Utils::getIcon('circle-o', 'red', '1em', '2px', 'Inactive');
	    }
	    else
	    {
	    	return MM_NO_DATA;
	    }
 	}
	
	/**
	 * This function returns a user object based on if a member is logged in or an admin. If an 
	 * admin is logged in, a user object will be returned based on the current preview bar settings
	 */
	public static function getCurrentUser()
	{
		global $user, $current_user;
		 
		$user_obj = null;
		 
		if(MM_Employee::isEmployee())
		{
			$previewObj = MM_Preview::getData();
			
			if($previewObj !== false)
			{
				return $previewObj->getUser();
			}
		}
		 
		if(isset($user->ID) && intval($user->ID) > 0)
		{
			$user_obj = MM_User::create($user->ID);
		}
		else if(isset($user->data->ID) && intval($user->data->ID)>0)
		{
			$user_obj = MM_User::create($user->data->ID);
		}
		else if(isset($current_user->ID) && intval($current_user->ID) > 0)
		{
			$user_obj = MM_User::create($current_user->ID);
		}
		 
		return $user_obj;
	}
	
	public static function cacheIsWriteable()
	{
		$cacheDir = self::getCacheDir();
		if (!is_writeable($cacheDir))
		{
			@chmod($cacheDir,0744);	//first see if we can make it writeable
		}
		return is_writable($cacheDir);
	}
	
	public static function getCacheDir()
	{
		$cacheDir = MM_PLUGIN_ABSPATH."/com/membermouse/cache";
		return $cacheDir;
	}
	
	public static function getPluginWarnings()
	{
		$problemPlugins = array();
		$problemPlugins["W3 Total Cache"] = "w3-total-cache/w3-total-cache.php";
		$problemPlugins["WP Super Cache"] = "wp-super-cache/wp-cache.php";
		
		$plugins = get_option('active_plugins');
		
		foreach($problemPlugins as $name=>$location)
		{
			if(in_array($location, $plugins))
			{
				if(!MM_Messages::isMessageHidden($location))
				{
					$hideWarningUrl = MM_Messages::getHideMessageUrl(self::constructPageUrl(), $location);
					MM_Messages::addError("<i class=\"fa fa-exclamation-triangle\"></i> <strong>MemberMouse Warning</strong>: The <em>{$name}</em> plugin is known to cause issues with MemberMouse. <a href='http://support.membermouse.com/support/solutions/articles/9000020276-plugins-that-cause-problems' target='_blank'>Learn more</a> | <a href='{$hideWarningUrl}'>Hide this warning</a>");
				}
			}
		}
		
		// check if client is using WP Engine
		if(class_exists("WPE_API", false) || defined("WPE_APIKEY") || defined("WPE_ISP")) 
		{
			if(!MM_Messages::isMessageHidden("wp-engine-warning"))
			{
				$hideWarningUrl = MM_Messages::getHideMessageUrl(self::constructPageUrl(), "wp-engine-warning");
				MM_Messages::addError("<i class=\"fa fa-exclamation-triangle\"></i> <strong>MemberMouse Warning</strong>: You're using WP Engine. Follow the instructions in this article to <a href='http://support.membermouse.com/support/solutions/articles/9000020281-configuring-wp-engine-hosting' target='_blank'>work with WP Engine to ensure your server is configured to allow MemberMouse to run properly</a>. | <a href='{$hideWarningUrl}'>Hide this warning</a>");
			}
		}
		
		// TODO this code can be removed after no active license is on a version less than 2.2.4
		$captchaSiteKey = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_CAPTCHA_KEY);
		$captchaPrivateKey = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_CAPTCHA_PRIVATE_KEY);
		$mmVersion = MemberMouse::getPluginVersion();
		
		// check if client is using recaptcha and on version 2.2.4 of MM
		if($mmVersion == "2.2.4" && !empty($captchaSiteKey) && !empty($captchaPrivateKey))
		{
			if(!MM_Messages::isMessageHidden("recaptcha-upgrade-warning"))
			{
				$hideWarningUrl = MM_Messages::getHideMessageUrl(self::constructPageUrl(), "recaptcha-upgrade-warning");
				MM_Messages::addError("<div style='width:750px;'><i class=\"fa fa-exclamation-triangle\"></i> <strong>MemberMouse Warning</strong>: It appears that you're currently using reCaptcha. In this version of MemberMouse reCaptcha has been upgraded and your <strong>action is required</strong> to ensure your checkout process continues to function correctly. <a href='http://support.membermouse.com/support/solutions/articles/9000020200-2-2-4-release-notes#recaptcha-upgrade' target='_blank'>Watch this video for details</a>. | <a href='{$hideWarningUrl}'>Hide this warning</a></div>");
			}
		}
	}
	
	
	public static function inAdmin()
	{
		return is_admin();
	}
	
	
	public static function isSiteAdmin()
	{
		return current_user_can('manage_options');
	}
	
	
	/**
	 * Pluralizes a word if quantity is not one.
	 *
	 * @param int $quantity Number of items
	 * @param string $singular Singular form of word
	 * @param string $plural Plural form of word; function will attempt to deduce plural form from singular if not provided
	 * @return string Pluralized word if quantity is not one, otherwise singular
	 */
	public static function pluralize($quantity, $singular, $plural=null) {
	    if($quantity==1 || !strlen($singular)) return $singular;
	    if($plural!==null) return $plural;
	    
	    $last_letter = strtolower($singular[strlen($singular)-1]);
	    switch($last_letter) {
	        case 'y':
	            return substr($singular,0,-1).'ies';
	        case 's':
	            return $singular.'es';
	        default:
	            return $singular.'s';
	    }
	}
 }
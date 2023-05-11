<?php
/**
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 **/

class MM_Session
{
	public static $MM_SESSION_TIMESTAMP = null;
	public static $MM_SESSION_DELAYED_CREATE = false;
	
	protected static $MM_SESSION_DATA = null;
	protected static $MM_SESSION_EXISTS = null;
	protected static $MM_SESSION_STARTED = false;
	protected static $MM_COOKIE_ID = null;
	protected static $MM_SESSION_ID = null;
	protected static $MM_DEFAULT_SESSION_LIFESPAN = 7200; // 2 Hours
	protected static $MM_SESSION_LIFESPAN = 7200; // 2 Hours
	protected static $MM_SESSION_UNVERIFIED_LIFESPAN = 1800; //30 mins
	protected static $MM_SESSION_VERIFIED = false;
	
	//indicator used to denote that the platform may have incorrect collation set on the database, which could 
	//break collation of serialized utf8 data. When true, this adds an extra step that base64 encodes before storing
	//this avoids data loss due to improper collation at the cost of a negligible amount of performance
	public static $MM_UNTRUSTED_PLATFORM = true;
	
	public static $KEY_IMPORT_MEMBERS = "import-members";
	public static $KEY_CURR_USER_ID = "current-user-id";
	public static $KEY_LAST_USER_ID = "last-user-id";
	public static $KEY_LAST_COUPON_VALUE = "last-coupon";
	public static $KEY_UPDATE_USER_ID = "update-user-id";
	public static $KEY_LAST_ORDER_ID = "last-order-id";
	public static $KEY_LOGIN_FORM_USER_ID = "login-form-user-id";
	public static $KEY_LOGIN_FORM_USERNAME = "login-form-username";
	public static $KEY_ERRORS = "errors";
	public static $KEY_MESSAGES = "messages";
	public static $KEY_CHECKOUT_FORM = "checkout-form";
	public static $KEY_CSV = "csv";
	public static $KEY_PREVIEW_MODE = "preview";
	public static $KEY_USING_DB_CACHE = "using_db_cache";
	public static $KEY_TRANSACTION_KEY = "transaction_key";
	public static $PARAM_LOGIN_TOKEN = "reftok";
	public static $PARAM_USER_ID = "user_id";
	public static $PARAM_MESSAGE_KEY = "message";
	public static $PARAM_COMMAND_DEACTIVATE = "mm-deactivate";
	public static $PARAM_SUBMODULE = "submodule";
	public static $PARAM_USER_DATA_PASSWORD = "user-data-password";

	
	public static function value($name, $val = null)
	{
		if ($val == null)
		{
			//get operation
			if (self::$MM_SESSION_STARTED && isset(self::$MM_SESSION_DATA[MM_PREFIX.$name]))
			{
				return self::$MM_SESSION_DATA[MM_PREFIX.$name];
			}
			return false;
		}
		else
		{
			//set operation
			if (!self::$MM_SESSION_STARTED)
			{
				self::sessionStart();
			}
			self::$MM_SESSION_DATA[MM_PREFIX.$name] = $val;
			return self::$MM_SESSION_DATA[MM_PREFIX.$name];
		}
	}


	public static function clear($name)
	{
		if (self::$MM_SESSION_STARTED)
		{
			unset(self::$MM_SESSION_DATA[MM_PREFIX . $name]);
		}
	}


	/**
	 * Start a new session
	 */
	public static function sessionStart()
	{
		if (self::$MM_SESSION_STARTED === true)
		{
			return;
		}
		
		if (isset($_COOKIE[self::getCookieId()]))
		{
			if (self::sessionLoad())
			{
				self::$MM_SESSION_VERIFIED = true;
			}
			else 
			{
				self::sessionCreate();
			}
		}
		else 
		{
			self::sessionCreate();
		}
		self::$MM_SESSION_STARTED = true;
	}
	
	
	private static function sessionCreate()
	{
		global $wpdb;
		
		$cookieId  = self::getCookieId();
		$sessionId = self::getSessionId();
		
		if (!headers_sent())
		{
			setcookie($cookieId, $sessionId, 0, '/');
			$_COOKIE[$cookieId] = $sessionId;
		}
		else
		{
			self::$MM_SESSION_DELAYED_CREATE = true;
		}
		
		//set session_exists here because the cookie is set but not readable yet
		self::$MM_SESSION_EXISTS = true;
			
		$wpdb->insert(MM_TABLE_SESSIONS, array(
				'id'              => $sessionId,
				'ip_address'	  => MM_Utils::getClientIPAddress(),
				'data'            => self::$MM_UNTRUSTED_PLATFORM ? base64_encode(serialize(array())) : serialize(array()),
				'expiration_date' => self::getExpirationDate()
		));
		
		self::$MM_SESSION_DATA = array();
	}


	/**
	 * Load the session data from the database into memory for quicker access
	 */
	private static function sessionLoad()
	{
		global $wpdb;

		if (is_null(self::$MM_SESSION_DATA))
		{
			$cookieId = self::getCookieId();
			self::$MM_SESSION_ID = $_COOKIE[$cookieId];

			$result = $wpdb->get_var($wpdb->prepare("SELECT data FROM " .MM_TABLE_SESSIONS." WHERE id = %s AND expiration_date >= %s",self::$MM_SESSION_ID,self::$MM_SESSION_TIMESTAMP));
			if ($result !== null)
			{
				self::$MM_SESSION_DATA = self::$MM_UNTRUSTED_PLATFORM ? unserialize(base64_decode($result)) :unserialize($result);
				
				if (self::$MM_SESSION_DATA === false)
				{
					return false;
				}
				
				return true;
			}
			else 
			{
				self::$MM_SESSION_DATA = null;
				self::$MM_SESSION_ID = null;
				self::$MM_SESSION_TIMESTAMP = null;
				self::$MM_SESSION_EXISTS = null;
				self::$MM_COOKIE_ID = null;
				return false;
			}
		}
		return true;
	}

	
	private static function getExpirationDate()
	{
		if (self::$MM_SESSION_TIMESTAMP === null)
		{
			self::sessionSetTimestamp();
		}
		
		if (MM_Utils::isLoggedIn() || !self::probablyIsBot())
		{
			return strftime("%Y-%m-%d %H:%M:%S",strtotime(self::$MM_SESSION_TIMESTAMP) + self::$MM_SESSION_LIFESPAN);
		}
		else
		{
			return strftime("%Y-%m-%d %H:%M:%S",strtotime(self::$MM_SESSION_TIMESTAMP) + self::$MM_SESSION_UNVERIFIED_LIFESPAN);
		}
	}

	
	/**
	 * Called upon wordpress' shutdown hook.
	 * Write session data that's currently stored in memory to database
	 */
	public static function sessionWrite()
	{
		global $wpdb;

		if (self::$MM_SESSION_STARTED)
		{
			self::sessionSetTimestamp();

			//don't store passwords in the database
			if(self::value(self::$PARAM_USER_DATA_PASSWORD) !== false)
			{
				self::clear(MM_Session::$PARAM_USER_DATA_PASSWORD);
			}
			
			$wpdb->update (MM_TABLE_SESSIONS, array (
					'data' 		  	  => self::$MM_UNTRUSTED_PLATFORM ? base64_encode(serialize(self::$MM_SESSION_DATA)) : serialize(self::$MM_SESSION_DATA),
					'ip_address' => MM_Utils::getClientIPAddress(),
					'expiration_date' => self::getExpirationDate()
			), array (
					'id' 		 => self::getSessionId(),
			) );
		}
	}


	/**
	 * Delete expired sessions
	 */
	public static function sessionReap($sessionId = null)
	{
		global $wpdb;

		if ($sessionId !== null)
		{
			$sql = $wpdb->prepare("DELETE FROM `".MM_TABLE_SESSIONS."` WHERE (id = %s)",$sessionId);
		}
		else 
		{
			$now = strftime("%Y-%m-%d %H:%M:%S");
			$sql = "DELETE FROM `".MM_TABLE_SESSIONS."` WHERE expiration_date < '{$now}'";
		}

		$wpdb->query($sql);

		
		if (self::sessionExists())
		{
			$cookieId = self::getCookieId();
			$_COOKIE[$cookieId] = null;
			unset($_COOKIE[$cookieId]);
			setcookie(self::getCookieId(),'',time() - 3600, '/');
		}
	}


	/**
	 * Set the timestamp if it isn't already set.
	 * This timestamp is used to check for expired sessions to reap and also to set the expiration date of a new and/or updated session
	 */
	public static function sessionSetTimestamp()
	{
		if (self::$MM_SESSION_TIMESTAMP == null)
		{
			self::$MM_SESSION_TIMESTAMP = strftime("%Y-%m-%d %H:%M:%S");
		}
	}


	/**
	 * Generate the unique/random Session ID
	 */
	private static function generateSessionId()
	{
		$entropy = "";
		
		try 
		{
			$throwException = TRUE;
			
			if($allowedPaths = ini_get('open_basedir'))
			{
				foreach(explode(":", $allowedPaths) as $path)
				{	
					if(preg_match("/^\/dev([\/]?)$/", $path))
					{
						$throwException = FALSE;
					}
				}
			}
			else
			{
				$throwException = FALSE;
			}
			
			if($throwException || !is_readable ( "/dev/urandom" ))
			{
				throw new Exception();
			}

			$handle = fopen ( '/dev/urandom', 'rb' );
			$entropy = md5 ( fread ( $handle, 64 ) );
			fclose ( $handle );
			
		}
		catch(Exception $e)
		{
			if ((double) phpversion() >= 7.0 && function_exists ( "random_bytes" ))
			{
				$entropy = md5 ( random_bytes(64) );
			}
			elseif ((double) phpversion() >= 5.3 && function_exists ( "mcrypt_create_iv" ))
			{
				$entropy = md5 ( mcrypt_create_iv ( 64, MCRYPT_DEV_URANDOM ) );
			}
			else
			{
				$entropy = md5 ( AUTH_KEY );
			}
		}

		$microtime = array_reverse (str_split($microtime = preg_replace("/[^0-9]+/","", microtime(true)), ceil(strlen($microtime)/4)));
		$ip = array_reverse(str_split($ip = preg_replace("/[^0-9]+/","",$_SERVER ['REMOTE_ADDR']),ceil(strlen($ip)/4)));
		$entropy = ($entropy)?array_reverse(str_split($entropy,ceil(strlen($entropy)/4))):array();

		$data = array();
		switch (true)
		{
			case count($microtime) >= count($ip) && count($microtime) >= count($entropy):
				$data [] = $microtime;
				$data [] = $ip;
				$data [] = $entropy;
				break;

			case count ($ip) >= count ($microtime) && count ($ip) >= count ($entropy ) :
				$data [] = $ip;
				$data [] = $microtime;
				$data [] = $entropy;
				break;

			case count ($entropy) >= count ($microtime) && count($entropy) >= count ( $ip ) :
			default :
				$data [] = $entropy;
				$data [] = $ip;
				$data [] = $entropy;
				break;
		}

		$result = array ();

		foreach ( $data [0] as $i => $chunk )
		{
			$result [] = $chunk;

			foreach ( $data as $j => $subdata )
			{
				if ($j != 0 && isset ( $subdata [$i] ) && $subdata [$i])
				{
					$result [] = $subdata [$i];
				}
			}
		}

		return md5 (implode("", $result));
	}


	/**
	 * Get the Cookie ID.
	 * If one doesn't exists, create it first, then return it.
	 */
	private static function getCookieId()
	{
		if (is_null(self::$MM_COOKIE_ID))
		{
			$site_url_data = parse_url(site_url());
			self::$MM_COOKIE_ID = "mm_".md5($site_url_data['host']);
		}

		return self::$MM_COOKIE_ID;
	}


	/**
	 * Get the Session ID. If one doesn't exists, create it first, then return it.
	 */
	public static function getSessionId()
	{
		if (is_null(self::$MM_SESSION_ID))
		{
			self::$MM_SESSION_ID = self::generateSessionId();
		}

		return self::$MM_SESSION_ID;
	}


	/**
	 * Checks to see if the client has sent a session id
	 */
	public static function sessionExists()
	{
		if (self::$MM_SESSION_EXISTS == null)
		{
			$cookieId = self::getCookieId();
			if (!isset($_COOKIE[$cookieId]) || !$_COOKIE[$cookieId])
			{
				self::$MM_SESSION_EXISTS = false;
			}
			else
			{
				self::$MM_SESSION_EXISTS = true;
			}
		}

		return self::$MM_SESSION_EXISTS;
	}
	
	
	public static function sessionSetSessionLifespan($lifespan=null)
	{	
  		if (($lifespan !== null) && (is_numeric($lifespan)))
  		{
    		self::$MM_SESSION_LIFESPAN = $lifespan;
  		}
  		else
  		{
  			self::$MM_SESSION_LIFESPAN = self::$MM_DEFAULT_SESSION_LIFESPAN;
  		}
	}
	
	
	public static function probablyIsBot()
	{
		$botRegex = "bot|crawler|baiduspider|80legs|ia_archiver|voyager|curl|wget|yahoo! slurp|mediapartners-google";
		if (isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT']))
		{
			return preg_match("/{$botRegex}/",$_SERVER['HTTP_USER_AGENT']);
		}
		return false; //err on the side of caution
	}
	
	
	public static function generateDelayedCreateJavascript()
	{
		$output = "";
		
		if (self::$MM_SESSION_STARTED === true)
		{
			$cookieId =  self::getCookieId();
			$sessionId = self::getSessionId();
			
			if ((self::$MM_SESSION_DELAYED_CREATE === true) && !empty($cookieId) && !empty($sessionId))
			{
				$site_url_data = parse_url(site_url());
				$host = $site_url_data['host'];
				
				$cookieId  = htmlspecialchars($cookieId,ENT_COMPAT | ENT_HTML401, "UTF-8");
				$sessionId = htmlspecialchars($sessionId,ENT_COMPAT | ENT_HTML401, "UTF-8");
				$output .=  "<script type='text/javascript'>".
							"document.cookie = '{$cookieId}={$sessionId}; path=/;";
				if (!empty($host))
				{
					$output .= "domain=".htmlspecialchars($host,ENT_COMPAT | ENT_HTML401, "UTF-8");
				}
				$output .="';</script>";
			}
		}
		return $output;
	}
}
//static initialization
MM_Session::sessionSetTimestamp();
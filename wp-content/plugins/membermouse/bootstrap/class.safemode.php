<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
class MM_SafeMode
{	
	public static $SAFE_MODE_STATUS = "";
	
	//status
	public static $MODE_DISABLED = "off";
	public static $MODE_ENABLED = "on";
	
	//In the context of safe mode, "session" means a single page load by one user.
	protected static $SESSION = "unknown";
	protected static $IP_ADDRESS = "unknown";
	
	private static $initialized = false;
	
	
	/**
	 * This method should be called at class load, and stores the mode, ip address, and creates a session identifier, so that these things only need to 
	 * happen once within the life of the interpreter process
	 */
	public static function init()
	{
		if (!self::$initialized)
		{
			$currentMode = (class_exists("MM_OptionUtils"))?MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE):"";
			self::$SAFE_MODE_STATUS = empty($currentMode)?self::$MODE_DISABLED:$currentMode;
			if (self::$SAFE_MODE_STATUS !== self::$MODE_DISABLED)
			{
				self::$IP_ADDRESS = (class_exists("MM_Utils"))?MM_Utils::getClientIPAddress():"unknown";
				//the transaction key class logic for generating random identifiers is reused here, for convenience
				self::$SESSION = (class_exists("MM_TransactionKey"))?MM_TransactionKey::createRandomIdentifier(8):"unknown";
			}
			self::$initialized = true;
		}
	}
	
	
	/**
	 * Returns whether safe mode is enabled, or is configured to log in any mode
	 * 
	 * @return boolean true or false if logging is turned on
	 */
	public static function isEnabled()
	{
		$currentMode = self::getMode();
		return ($currentMode == self::$MODE_ENABLED);
	}
	
	
	/**
	 * User-friendly labels for the mode constants
	 * @return string see the modes listed above
	 */
	public static function getModeLabels()
	{
		$modeLabels = array(self::$MODE_DISABLED => "Disabled",
							self::$MODE_ENABLED => "Enabled");
		return $modeLabels;
	}
	
	
	/**
	 * Sets the configured mode
	 * 
	 * @param String $mode 
	 */
	public static function setMode($mode)
	{
		$modeList = self::getModeLabels();
		if (!in_array($mode,array_keys($modeList)))
		{
			$mode = self::$MODE_DISABLED;
		}
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE,$mode);
	}
	
	
	/**
	 * Returns the configured status of the safe mode. It the option has never been set, it is set to $MODE_DISABLED initially
	 * 
	 * @return string One of MM_SafeMode::$MODE_DISABLED or MM_SafeMode::$MODE_ENABLED
	 */
	public static function getMode()
	{
		$mode = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE);
		$modeList = self::getModeLabels();
		if (!in_array($mode,array_keys($modeList)))
		{
			$mode = self::$MODE_DISABLED;
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE,$mode);
		}
		return $mode;
	}
	
	
	/**
	 * Returns an array of the names of the active plugins
	 */
	public static function getActivePluginNames()
	{	
		$activePlugins = array();
		
		foreach(get_plugins() as $relativePath => $pluginInfo)
		{	
			if(is_plugin_active($relativePath))
			{
				$activePlugins[] = $pluginInfo["Name"];
			}
		}
		
		return $activePlugins;
	}
	
	
	/**
	 * Returns an array of the names of the saved plugins
	 */
	public static function getSavedPluginNames()
	{
		$savedPlugins = array();
		
		$oldPlugins = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_PLUGINS);
		
		foreach($oldPlugins as $relativePath)
		{
			if(!preg_match("/^membermouse/", $relativePath))
			{
				$pluginInfo = get_plugin_data(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $relativePath);
				$savedPlugins[] = $pluginInfo["Name"];
			}
		}
		
		return $savedPlugins;
	}
	
	
	/**
	 * Disables all plugins besides the MM plugin by storing the currently activated plugins and then
	 * clearing the active_plugins option in the database.
	 */
	public static function disablePlugins()
	{
		if(self::isEnabled())	
		{
			$crntActivePlugins = MM_OptionUtils::getOption("active_plugins");
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_PLUGINS, $crntActivePlugins);
			MM_OptionUtils::setOption("active_plugins", array("membermouse/index.php"));
		}
	}
	
	
	/**
	 * Restores all plugins that were disabled in the disablePlugins() method.
	 */
	public static function restorePlugins()
	{
		$oldPlugins = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_PLUGINS);
		
		if(!empty($oldPlugins))
		{
			// restore plugin settings
			MM_OptionUtils::setOption("active_plugins", $oldPlugins);
			
			// clear stored plugin settings
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_PLUGINS, "");
		}
	}
	
	
	/**
	 * Returns the name of the current theme
	 */
	public static function getActiveThemeName()
	{
		$theme = wp_get_theme();
    		
    	return $theme->Name;
	}
	
	
	/**
	 * Returns the name of the saved theme
	 */
	public static function getSavedThemeName()
	{
		$oldThemeSettings = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_THEME);
		
		if(!empty($oldThemeSettings))
		{
			$oldThemeSettings = unserialize($oldThemeSettings);
			
			if(isset($oldThemeSettings->name))
			{
				return $oldThemeSettings->name;
			}
		}
		
		return "&mdash;";
	}
	
	
	/** 
	 * This function returns the latest default WordPress theme (if available)
	 */
	public static function getDefaultWordPressTheme()
	{
		$availableThemes = scandir(get_theme_root());
		
		if(in_array("twentyfifteen", $availableThemes))
		{
			return "twentyfifteen";
		}
		else if(in_array("twentyfourteen", $availableThemes))
		{
			return "twentyfourteen"; 
		}
		else if(in_array("twentythirteen", $availableThemes))
		{
			return "twentythirteen"; 
		}
		
		return "";
	}
	
	
	/**
	 * Switches theme to default WordPress theme and saves the settings associated with the currently 
	 * activated theme.
	 */
	public static function disableTheme()
	{
		$dfltWPTheme = self::getDefaultWordPressTheme();
		
		if(self::isEnabled() && $dfltWPTheme != "")
		{
			// backup current theme settings
			$crntTemplate = MM_OptionUtils::getOption("template");
			$crntStylesheet = MM_OptionUtils::getOption("stylesheet");
			
			$themeSettings = new stdClass();
			$themeSettings->name = self::getActiveThemeName();
			$themeSettings->template = $crntTemplate;
			$themeSettings->stylesheet = $crntStylesheet;
			
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_THEME, serialize($themeSettings));
			
			// set new theme
			MM_OptionUtils::setOption("template", $dfltWPTheme);
			MM_OptionUtils::setOption("stylesheet", $dfltWPTheme);
		}
	}
	
	
	/**
	 * Restores the theme that was disabled in the disableTheme() method.
	 */
	public static function restoreTheme()
	{
		$oldThemeSettings = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_THEME);
		
		if(!empty($oldThemeSettings))
		{
			$oldThemeSettings = unserialize($oldThemeSettings);
			
			// restore old theme settings
			MM_OptionUtils::setOption("template", $oldThemeSettings->template);
			MM_OptionUtils::setOption("stylesheet", $oldThemeSettings->stylesheet);
			
			// clear stored settings
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_THEME, "");
		}
	}
	
	
	/**
	 * Clear the safe mode log
	 */
	public static function clearLog()
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_LOG, serialize(array()));
	}
	
	
	/**
	 * Returns an array of log events which are standard objects with the properties
	 * date, mode, message
	 */
	public static function getLog()
	{
		$log = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_LOG);
		
		if(!empty($log))
		{
			$log = unserialize($log);
		}
		else
		{
			$log = array();
		}
		
		return $log;
	}
	
	
	/**
	 * Log a safe mode event
	 * 
	 * @param String $mode one of the possible modes
	 * @param String $message the message to log
	 */
	public static function log($mode, $message)
	{
		$log = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_LOG);
		
		if(!empty($log))
		{
			$log = unserialize($log);
		}
		else 
		{
			$log = array();
		}
		
		$logEvent = new stdClass();
		$logEvent->date = date('M j, Y g:i a');
		$logEvent->mode = $mode;
		$logEvent->message = $message;
		
		array_unshift($log, $logEvent);
		
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SAFE_MODE_LOG, serialize($log));
	}
}
MM_SafeMode::init(); //static initializer
?>

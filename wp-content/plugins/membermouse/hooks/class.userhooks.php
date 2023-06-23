<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) MemberMouse, LLC. All rights reserved.
 */
 class MM_UserHooks
 {	 
	/**
	 *  Auto-login users on confirmation page, using a login token, or as a result of a social media login
	 */
	public function doAutoLogin($userId="", $redirectUrl="")
	{	
		if(!is_user_logged_in())
		{
			if (empty($userId) || empty($redirectUrl))
			{
				$userId = 0;
				$crntUrl = MM_Utils::constructPageUrl();
				$isConfirmationPage = MM_CorePageEngine::isConfirmationPageByUrl($crntUrl);
				
				if($isConfirmationPage)
				{
					// validate transaction key
					$userId = 0;
					if(isset($_REQUEST[MM_Session::$KEY_TRANSACTION_KEY]))
					{
						$transRef = MM_TransactionKey::getTransactionByKey($_REQUEST[MM_Session::$KEY_TRANSACTION_KEY]);
						$userId = ($transRef->isValid()) ? $transRef->getUserId() : 0;
						$redirectUrl = MM_Utils::constructPageUrl();
					}
					
					// invalid transaction key
					if($userId == 0)
					{
						// provide opportunity to bypass protection of the confirmation page
						if(class_exists("MM_Filters"))
						{
							$allowAccess = apply_filters(MM_Filters::$BYPASS_CONTENT_PROTECTION, false);
						}
						
						if(!$allowAccess)
						{
							$url = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCESS_DENIED);
							wp_redirect($url);
							exit;
						}
					}
				}
				else if(isset($_REQUEST[MM_Session::$PARAM_LOGIN_TOKEN]))
				{
					$loginToken = MM_LoginToken::getLoginTokenByToken($_REQUEST[MM_Session::$PARAM_LOGIN_TOKEN]);
					$userId = ($loginToken->isValid()) ? $loginToken->getUserId() : 0;
					$redirectUrl = preg_replace("/".MM_Session::$PARAM_LOGIN_TOKEN."=[^&]*/","",MM_Utils::constructPageUrl());
				}
			}
			
			if($userId > 0)
			{
				$user = new MM_User($userId);
					
				if($user->isValid() && ($user->getStatus() == MM_Status::$ACTIVE || $user->getStatus() == MM_Status::$PENDING_CANCELLATION || $user->getStatus() == MM_Status::$PAUSED || $user->getStatus() == MM_Status::$OVERDUE))
				{
					MM_ActivityLog::log($user, MM_ActivityLog::$EVENT_TYPE_LOGIN);
			
					wp_set_auth_cookie($userId, true, MM_Utils::isSSL());
					wp_set_current_user($userId);
					
					wp_redirect($redirectUrl);
					exit;
				}
			}
		}
	}       
	  
	
	/**
	 *  Auto-logout users on logout page
	 */
	public function doAutoLogout()
	{
		if(is_user_logged_in())
		{
			$crntUrl = MM_Utils::constructPageUrl();
			$isLogoutPage = MM_CorePageEngine::isLogoutPageByUrl($crntUrl);
			
			if ($isLogoutPage)
			{
				wp_logout();
				wp_redirect(MM_Utils::constructPageUrl());
				exit;
			}
		}
	}
	
	
	/**
	 * This hook is called when the correct page is determined by wordpress to process. We check here 
	 * if that page is a MM core page and then make any checks we might want to for that particular 
	 * core page before we get too deep into the execution process and before any headers might be 
	 * sent, in case a redirect is needed
	 */
	public function checkCorePageTypeInput()
	{	
		$corePageInfo = MM_CorePage::getCorePageInfo(get_the_ID());
		
		if(!is_null($corePageInfo))
		{
			switch($corePageInfo->core_page_type_id)
			{
				case MM_CorePageType::$RESET_PASSWORD:
					$result = MM_ResetPasswordForm::checkInput();
					
					if($result['success'] === false)
					{
						MM_Messages::addError($result['message']);
						wp_redirect(MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
						exit;
					}
					
					break;
				case MM_CorePageType::$CHECKOUT:
				    $onsiteService = MM_PaymentServiceFactory::getOnsitePaymentService();
				    if ($onsiteService != null)
				    {
				        $onsiteService->checkoutInit();
				    }
					if (!headers_sent())
					{
						//send headers to discourage caching (especially bfcache) to force checkout page to be regenerated every page hit
						//this should also ensure the generation of a fresh form submission id
						nocache_headers();
					}
					break;
					
				case MM_CorePageType::$MY_ACCOUNT:
					$onsiteService = MM_PaymentServiceFactory::getOnsitePaymentService();
					if ($onsiteService != null)
					{
						$onsiteService->myAccountInit();
					}
					
					//TODO: offsite services may utilize checkoutInit in the future. Determining which offsite payment services are possibly available
					//		may be necessary here to support those services
					
					break;
			}
		}
	}
	
	
	public function removeWPAutoPOnCorePages()
	{
		if (!is_admin())
		{
			if(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ENABLE_WP_AUTOP) == "0")
			{		
				$crntUrl = MM_Utils::constructPageUrl();
				$isSmartTagCorePage = MM_CorePageEngine::isSmartTagCorePage($crntUrl);
				if($isSmartTagCorePage)
				{
					remove_filter ('the_content', 'wpautop');
				}
			}
		}
	}
	
	public function setupDefinitions()
	{		
		define("MM_TEMPLATE_BASE", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."templates");
		define("MM_TEMPLATE_META", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."metabox");
		define("MM_TEMPLATE_USER", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."user");
		define("MM_TEMPLATE_ADMIN", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."admin");
		define("MM_TEMPLATE_SMARTTAGS", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."smarttags");
		define("MM_TEMPLATE_REPORTING", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."reporting");
		define("MM_MODULES", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."modules");
		define("MM_DATA_DIR", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."data");
		define("MM_IMAGES_PATH", MM_PLUGIN_ABSPATH."".DIRECTORY_SEPARATOR."resources".DIRECTORY_SEPARATOR."images");
		
		define("MM_PLUGIN_URL", plugins_url()."/".MM_PLUGIN_NAME);
		define("MM_MODULES_URL", MM_PLUGIN_URL."/modules");
		define("MM_API_BASE_URL", MM_PLUGIN_URL."/api");
		define("MM_API_URL", MM_PLUGIN_URL."/api/request.php");
		define("MM_PROCESS_ORDER_URL", MM_PLUGIN_URL."/api/processOrder.php");
		define("MM_TEMPLATES_URL", MM_PLUGIN_URL."/templates/");
		
		// register SmartTags if we're loading a non-WordPress Admin page
	 	if(!is_admin() && class_exists("MM_SmartTagUtil"))
	 	{	
		 	$smartTagUtil = new MM_SmartTagUtil();
 			$smartTagUtil->registerSmartTags();
	 	}
	 	
		if(isset($_POST["exportdata"]))
		{
			$data = MM_Session::value(MM_Session::$KEY_CSV);
			
			if($data !==false){
				header("Content-type: text/csv");
			    header("Content-Disposition: filename=mm_export_".Date("Y-m-d").".csv");
			    header("Pragma: no-cache");
			    header("Expires: 0");
				echo $data;
				
				MM_Session::clear(MM_Session::$KEY_CSV);
				exit;
			}
		}
		
		// update cookies
		if(class_exists("MM_Cookies"))
		{
			MM_Cookies::setCookies();
		}
	}
	
	public function deleteUser($user_id)
	{
		$user = new MM_User($user_id);
		
		if($user->isValid())
		{
			$user->delete();
		}
	}
	
	public function showLoginLogoutLinkInMenu($items, $args)
	{	
		if(strpos($args->theme_location,'primary') !== false)
		{
			ob_start();
			wp_loginout('index.php');
			$loginoutlink = ob_get_contents();
			$items .= '<li>'. $loginoutlink .'</li>';
			ob_end_clean();
		}
		
		return $items;
	}
		
	public function hideProtectedMenuItems($item) 
	{
		if(!is_admin())
		{
			if(class_exists("MM_ProtectedContentEngine"))
			{
				global $current_user;
				if(isset($item->object_id))
				{
					$protectedContent = new MM_ProtectedContentEngine();
					if(!$protectedContent->canAccessPost($item->object_id, $current_user->ID))
					{
						$tmp = new stdClass();
						foreach($item as $k=>$v){
							$tmp->$k = "";
						}
						$tmp->ID = $item->ID;
						$tmp->_invalid = true;
						return $tmp;
					}
				}
			}
		}
		return $item;
	}
	
	public function pageBasedActions()
	{
		if(!is_admin())
		{
			global $current_user;
			
			// Need to look at this.
			if (class_exists("MM_User"))
			{
				$user = MM_User::getCurrentWPUser();
			}
			
			// log access for logged in users
			if(MM_Utils::isLoggedIn())
			{
				global $post;
				
				if(isset($post))
				{
					$crntPostId = $post->ID;
			
					$params = array();
					$params[MM_ActivityLog::$PARAM_PAGE_ID] = $crntPostId;
			
					MM_ActivityLog::log($user, MM_ActivityLog::$EVENT_TYPE_PAGE_ACCESS, $params);
				}
			}
			
			// clear session params
			MM_Session::clear(MM_Session::$KEY_LAST_USER_ID);
			MM_Session::clear(MM_Session::$KEY_LAST_ORDER_ID);
		}
	}
	
	public function checkLogin($user, $username, $password)
	{
		if (!empty($username) && class_exists("MM_User")) {
			if (!isset($user->ID))
			{
				return null; //no id, default policy is to deny
			}
			$mm_user = new MM_User($user->ID);
				
			// set login form session parameters
			MM_Session::value(MM_Session::$KEY_LOGIN_FORM_USER_ID, $user->ID);
			
			if (!$mm_user->isValid() || ($mm_user->getStatus() == MM_Status::$PENDING_ACTIVATION) || ($mm_user->getStatus() == MM_Status::$ERROR))
			{
				//can't login if account is pending or errored
				return null;
			}
			else
			{
				return $user;
			}
		}
	
		return $user;
	}
	 
	public function loginFailed($username)
	{
		if(class_exists("MM_CorePageEngine"))
		{
			// check if Limit Login Attempts plugin is active
			$plugins = get_option('active_plugins');
			$required_plugin = "limit-login-attempts/limit-login-attempts.php";
			$pluginActive = false;
			
			if(in_array($required_plugin, $plugins))
			{
				$pluginActive = true;
			}
			
			$errorMsg = "Invalid username or password";
			
			// set login form session parameters
			MM_Session::value(MM_Session::$KEY_LOGIN_FORM_USERNAME, $username);
			
			// check if user ID is set
			$loginUserId = MM_Session::value(MM_Session::$KEY_LOGIN_FORM_USER_ID);
			
			if($loginUserId !== false)
			{
				$loginUser = new MM_User($loginUserId);
				
				if($loginUser->isValid() && $loginUser->getStatus() == MM_Status::$PENDING_ACTIVATION)
				{
					$statusMsg = $loginUser->getStatusMessage();
					
					if(!empty($statusMsg))
					{
						$errorMsg = $loginUser->getStatusMessage();
					}
				}
			}
			
			// add limit login attempts messages
			if($pluginActive)
			{
				$llMsg = limit_login_get_message();
				
				if(!empty($llMsg))
				{
					$errorMsg .= "<br/>{$llMsg}";
				}
			}
			
			MM_Messages::addError($errorMsg);
			
			if(!defined("DOING_AJAX") || !DOING_AJAX)
			{
				switch(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_USE_MM_LOGIN_PAGE))
				{
					case 1: 
						if(MM_ModuleUtils::isRestRequest())
							return true;
							
						wp_redirect(MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
						break;
					case 0:
					default:
						return $errorMsg;
						break;
				}
				exit;	
			}
			else
			{
				MM_Session::value('redirect_to', MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
  				return true;
			}
		}
	}
	 
	public function authenticateLogin()
	{
		$error = false;
		if((isset($_POST['log']) && $_POST['log'] == '') || (isset($_POST['pwd']) && $_POST['pwd'] == ''))
		{
			$error = true;
		}
		
		if(class_exists("MM_CorePageEngine") && $error)
		{
			MM_Messages::addError("Please enter your username and password.");
			
			if(!defined("DOING_AJAX") || !DOING_AJAX)
			{
				$useSiteOptionMMLoginPage = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_USE_MM_LOGIN_PAGE);
				if($useSiteOptionMMLoginPage=="1")
				{
  		  			wp_redirect(MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
        			exit;	
				}
			}
			else
			{
  			MM_Session::value('redirect_to', MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
        return true;	
			}
		}
	}
	
	public function checkEmployeeAccess()
	{
		global $current_user;

		// determine is current employee has access to the current page
		if(class_exists("MM_Employee"))
		{
			$employee = MM_Employee::findByUserId($current_user->ID);
	
			if($employee->isValid())
			{
				$crntPage = MM_ModuleUtils::getPage();
				$crntModule = MM_ModuleUtils::getModule();
	
				if(empty($crntModule))
				{
					$crntModule = $crntPage;
				}
	
				if(MM_ModuleUtils::isMemberMousePage($crntPage) && !$employee->hasPermission(array("module"=>$crntModule)))
				{
					wp_redirect(MM_ModuleUtils::getUrl("mm_access_denied"));
					exit;
				}
	
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function handlePageAccess()
	{
		global $wp_query, $current_user;

		if(current_user_can('administrator') || MM_Employee::isEmployee())
		{
		    if(!MM_Preview::previewEnabled() || (isset($_REQUEST["mm_preview_admin"]) && $_REQUEST["mm_preview_admin"]=="1"))
			{
				return;
			}
		} 
		
		if(class_exists("MM_CorePageEngine"))
		{
			if(isset($wp_query->post) && isset($wp_query->post->ID))
			{
				if(!isset($_POST["log"]))
				{
					if(!MM_CorePageEngine::isMyAccountCorePage($wp_query->post->ID) 
						&& !MM_CorePageEngine::isLoginCorePage($wp_query->post->ID) 
						&& !MM_CorePageEngine::isErrorCorePage($wp_query->post->ID))
					{	
						MM_Session::clear(MM_OptionUtils::$OPTION_KEY_LAST_PAGE_DENIED);
					}
				}
			} 
			
			if(MM_CorePageEngine::isFrontPage()) 
			{	
				MM_CorePageEngine::redirectToSiteHomePage(true);
			}
			else if(isset($wp_query->post) && isset($wp_query->post->ID) && intval($wp_query->post->ID) > 0)
			{
				$isAdmin = false;
				if(isset($current_user->ID))
				{
					if(MM_Employee::isEmployee())
					{
						$isAdmin = true;	
					}
				}		
 
				if($isAdmin)
				{
					$preview = MM_Preview::getData();
					if($preview !== false)
					{ 
						if(MM_CorePageEngine::isMemberHomePage($wp_query->post->ID)
							|| MM_CorePageEngine::isSaveTheSalePage($wp_query->post->ID)
							|| MM_CorePageEngine::isMyAccountCorePage($wp_query->post->ID))
						{
							// if preview settings is set to non-members, redirect to the error page
							if($preview->getMembershipId() <= 0)
							{
								$url = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCESS_DENIED);
								$currentUrl = MM_Utils::constructPageUrl();
								$compareUrl = preg_replace("/https?/", "", $url);
								$compareUrl = preg_replace("/\/\?/", "?", $compareUrl);
								$currentUrl = preg_replace("/https?/","",$currentUrl);
								$currentUrl = preg_replace("/\/\?/","?",$currentUrl);
								
								if (strpos($currentUrl,$compareUrl) !== 0) //prevent infinite redirects
								{
									header("Location: {$url}");
									exit;
								}
							}
						} 
					}
				}
				else 
				{
					// check user account status 
					$userObj = MM_User::getCurrentWPUser();
					if($userObj->isValid())
					{
						if($userObj->getStatus() == MM_Status::$CANCELED || $userObj->getStatus() == MM_Status::$LOCKED)
						{
							wp_logout();
							
							if($userObj->getStatus() == MM_Status::$LOCKED)
							{
								$url = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCOUNT_LOCKED);
								wp_redirect($url);
								exit;
							}
							else if(($userObj->getStatus() == MM_Status::$CANCELED) && (!MM_CorePageEngine::isSaveTheSalePage($wp_query->post->ID)))
							{
								$url = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCOUNT_CANCELED);
								wp_redirect($url);
								exit;
							}
						} 
					}
				}
				
				// don't allow access to member homepages, save-the-sale pages or the
				// my account page if the user is not logged in
				if(MM_CorePageEngine::isMemberHomePage($wp_query->post->ID) 
					|| MM_CorePageEngine::isSaveTheSalePage($wp_query->post->ID)
					|| MM_CorePageEngine::isMyAccountCorePage($wp_query->post->ID))
				{
					if(!is_user_logged_in())
					{
						// if user is not logged in, redirect them to the login page, but first saved 
						// the attempted access page incase they log in succesfully afterwards
						MM_Session::value(MM_OptionUtils::$OPTION_KEY_LAST_PAGE_DENIED, $wp_query->post->ID);
						header("Location: ".MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
						exit;
					}
					else if(MM_CorePageEngine::isMemberHomePage($wp_query->post->ID))
					{
						// check if there's a specific member homepage for this user
						MM_CorePageEngine::redirectToMemberHomePage($wp_query->post->ID);
					}
					else if(MM_CorePageEngine::isSaveTheSalePage($wp_query->post->ID))
					{
						// check if there's a specific save-the-sale page for this user
						MM_CorePageEngine::redirectToSaveTheSalePage($wp_query->post->ID);
					}
				}
			}
			
			if(!is_admin())
			{
				$protectedContent = new MM_ProtectedContentEngine();
			
				$postId = $wp_query->query_vars["page_id"];
				
				if(isset($wp_query->post) && isset($wp_query->post->ID) && intval($wp_query->post->ID)>0)
				{
					$postId = $wp_query->post->ID;
				}
				
				if(intval($postId) > 0)
				{
					if(!is_feed())
					{
						$protectedContent->protectContent($postId, is_home());
					}
				}
			}
		}
	}
	
	function logoutUrl($logout_url, $redirect)
	{
		global $current_user;
		
		if(class_exists("MM_CorePageEngine"))
		{
			$redirect_url = MM_CorePageEngine::getUrl(MM_CorePageType::$LOGOUT_PAGE);
			$redirect = '&amp;redirect_to='.urlencode(wp_make_link_relative($redirect_url));
			$uri = wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' );
		}
		else
		{
			$uri = wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' );
		}
		return $uri;
	}
	
	function loginUrl($login_url, $redirect)
	{
		if(class_exists("MM_CorePageEngine"))
		{
			return MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE);
		}
	}
	
	function loginRedirect($redirectTo, $request, $user) 
	{	
		// clear login form session parameters
		MM_Session::clear(MM_Session::$KEY_LOGIN_FORM_USER_ID);
		MM_Session::clear(MM_Session::$KEY_LOGIN_FORM_USERNAME);
		
		$newRedirectTo = "";
		$allowUserOverride = true;
		
		if(class_exists("MM_CorePageEngine"))
		{
			if($user instanceof WP_User && isset($user->data->ID) && intval($user->data->ID)>0)
			{
				// check if this is an employee
				$employee = MM_Employee::findByUserId($user->data->ID);
				if($employee->isValid())
				{
					MM_Preview::clearPreviewMode();
					MM_Preview::getData();
					$newRedirectTo = $employee->getHomepage();
				}
				else if (is_super_admin($user->data->ID))
				{
				    //if this user is not an employee, but is a wordpress admin, just let them continue to their original destination or the admin panel if not set
				    //is_super_admin() returns true for all admins on non-network installs
				    $newRedirectTo = !empty($redirectTo)?$redirectTo:get_admin_url();
				}
				else 
				{
					$mmUser = new MM_User($user->data->ID);
					
					if($mmUser->getStatus() == MM_Status::$EXPIRED)
					{
						$allowUserOverride = false;
						$newRedirectTo = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCOUNT_EXPIRED, $mmUser);
						wp_logout();
					}
					else if($mmUser->getStatus() == MM_Status::$CANCELED)
					{
						$allowUserOverride = false;
						$newRedirectTo = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCOUNT_CANCELED, $mmUser);
						wp_logout();
					}	
					else if($mmUser->getStatus() == MM_Status::$LOCKED)
					{
						$allowUserOverride = false;
						$newRedirectTo = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCOUNT_LOCKED, $mmUser);
						wp_logout();
					}
					else if($mmUser->getStatus() == MM_Status::$OVERDUE)
					{
						$newRedirectTo = MM_CorePageEngine::getUrl(MM_CorePageType::$MY_ACCOUNT, "", $mmUser);
					}
					/// user is OK, send to member home.	
					else
					{ 
						MM_Preview::clearPreviewMode();
						$setting = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ON_LOGIN_USE_WP_FRONTPAGE);
						if($setting == "1")
						{
							$newRedirectTo = MM_OptionUtils::getOption("siteurl");
						}
						else 
						{
							$newRedirectTo = MM_CorePageEngine::getUrl(MM_CorePageType::$MEMBER_HOME_PAGE, "", $mmUser);
						}
						
						$lastAccessDeniedPageID = MM_Session::value(MM_OptionUtils::$OPTION_KEY_LAST_PAGE_DENIED);
						
						// check if current member has access to the last access denied page
						$pce = new MM_ProtectedContentEngine();
						
						if(intval($lastAccessDeniedPageID)>0 && $pce->canAccessPost($lastAccessDeniedPageID,$mmUser->getId()))
						{
							$corePageEngine = new MM_CorePageEngine();
							
							if(!$corePageEngine->arePermalinksUsed())
							{
								$newRedirectTo = get_page_link($lastAccessDeniedPageID);
							}
							else
							{
								$newRedirectTo = get_permalink($lastAccessDeniedPageID);
							}
						}
						MM_Session::clear(MM_OptionUtils::$OPTION_KEY_LAST_PAGE_DENIED);
						
						MM_ActivityLog::log($mmUser, MM_ActivityLog::$EVENT_TYPE_LOGIN);
						
						if($mmUser->hasReachedMaxIPCount())
						{
							global $current_user, $user;
							$mmUser->setStatus(MM_Status::$LOCKED);
							$mmUser->commitData();
							
							$newRedirectTo = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCOUNT_LOCKED, $mmUser);
							wp_logout();
						}
					}
					
					if(empty($newRedirectTo)) 
					{
						$newRedirectTo = MM_OptionUtils::getOption("siteurl");
					}
				}
			}
			
			// give customer an opportunity to redirect the user
			if($allowUserOverride)
			{
				$currentUrl = (!empty($newRedirectTo)) ? $newRedirectTo : $redirectTo;
				$infoObj = new stdClass();
				$infoObj->currentUrl = $currentUrl;
				$infoObj->user = $user;
				$redirectOverride = apply_filters(MM_Filters::$LOGIN_REDIRECT, $infoObj);
				
				if(is_string($redirectOverride) && !empty($redirectOverride) && $redirectOverride != $currentUrl)
				{
	  				if(!defined("DOING_AJAX") || !DOING_AJAX)
	  				{
	    			  	wp_redirect($redirectOverride);
	            		exit;	
	  				}
	  				else
	  				{
	            		return $redirectOverride;
  					}
				}
			}
			
			if(!empty($newRedirectTo))
			{
				return $newRedirectTo;
			}
			
			return $redirectTo;
		}
	}
	
	
	/**
	 * Ensure the password is sufficiently strong
	 * 
	 * @param object $passwordData An object containing members type, data, and errors
	 * @return $errors String containing any error messages. Empty string on success
	 */
	public static function passwordStrengthValidator($passwordData)
	{
	    if (is_object($passwordData) && isset($passwordData->data) && (strlen($passwordData->data) < 8))
	    {
	        $passwordData->type = "error";
	        $passwordData->errors[] = _mmt("Password must be at least 8 characters long, please try again...");
	    }
	    return $passwordData;
	}
 }
 
 ?>
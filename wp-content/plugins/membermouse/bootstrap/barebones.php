<?php

/**
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 **/


/* BEGIN WP-LOAD PATH */
$wpload_path = '/home/customer/www/nplusonemag.com/public_html//wp-load.php';
/* END WP-LOAD PATH */

if (empty($wpload_path) || (!include_once($wpload_path))) 
{
    if ((!include_once(dirname(__FILE__)."/../../../../wp-load.php"))) 
    {
        if (!isset($_SERVER['DOCUMENT_ROOT']) || (!include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php')) || (!include_once(ABSPATH.'/wp-load.php')))
        {
            //check for installation in a subfolder
            $dr = "{$_SERVER['DOCUMENT_ROOT']}/";
            if (strpos(dirname(__FILE__),$dr) === 0)
            {
                $path_from_root = substr(dirname(__FILE__),strlen($dr));
                $folder_array = explode("/",$path_from_root);
                if (count($folder_array) > 0)
                {
                    $subfolder = $folder_array[0];
                    if ((!include_once($_SERVER['DOCUMENT_ROOT']."/{$subfolder}/wp-config.php")) || (!include_once(ABSPATH.'/wp-load.php')))
                    {
                        die("Unable to load Wordpress"); 
                    }
                }
                else
                {
                    die("Unable to load Wordpress"); 
                }
            }
            else
            {
                die("Unable to load Wordpress");
            }
        }
    } 
}

require_once(dirname(__FILE__)."/../includes/mm-constants.php");
require_once(dirname(__FILE__)."/../includes/init.php");

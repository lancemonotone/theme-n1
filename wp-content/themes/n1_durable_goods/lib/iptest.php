<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/membermouse/includes/mm-constants.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/membermouse/includes/init.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/membermouse/includes/php_interface.php');


                global $wpdb;
                $sql = "SELECT mmc.value
                                FROM mm_custom_field_data mmc
                                JOIN mm_user_data mmud
                                ON mmc.user_id = mmud.wp_user_id
                                WHERE mmc.custom_field_id IN(1) # IP range
                                AND mmud.`status` IN (1); # active subscription";
                $institutions = $wpdb->get_results($sql);
                foreach($institutions as $institution){
                        $ips = $institution->value;
                        $ips = explode(PHP_EOL, $ips);
                        foreach($ips as $ip){
                                if('' != $ip){
                                        $ip_clean = str_replace('*', '', trim($ip));
                                        if('' != $ip_clean && false !== @stristr(self::get_client_ip(), $ip_clean)){
                                                $paywall = false;
                                                break;
                                        }/*else{
                                                echo "<!-- 1: ".$ip."-->";
                                                echo "<!-- 2: ".$ip_clean."-->";
                                                echo "<!-- 3: ".self::get_client_ip()."-->";
                                                echo "<!-- 4: ".@stristr(self::get_client_ip(), $ip_clean)."-->\n";
                                        }*/
                                }
                        }
                }

echo 'helo';

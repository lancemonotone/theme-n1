<?php
/*
Plugin Name: Cria Key
Plugin URI: http://rcdev.com.br/palestra/_clients/angularJs
Description: Cria Keys e Secre para o wp_oauth sem necessidade de usar o WP-CLI (que só roda em Unix)
Author: Romulo_Ctba
Author URI:http://rcdev.com.br/romulo
Version: 0.1
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

/* IMPORTANT: Menu is visible to anyone who has 'read' capability, so that means subscribers
              See: http://codex.wordpress.org/Roles_and_Capabilities for information on appropriate settings for different users

*/

// Make sure that no info is exposed if file is called directly -- Idea taken from Akismet plugin
if ( !function_exists( 'add_action' ) ) {
	echo "This page cannot be called directly.";
	exit;
}

// Define some useful constants that can be used by functions
if ( ! defined( 'WP_CONTENT_URL' ) ) {	
	if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
	define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
}
if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
if ( ! defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( basename(dirname(__FILE__)) == 'plugins' )
	define("cria_key_DIR",'');
else define("cria_key_DIR" , basename(dirname(__FILE__)) . '/');
define("cria_key_PATH", WP_PLUGIN_URL . "/" . cria_key_DIR);

/* Add new menu */
add_action('admin_menu', 'cria_key_add_pages');
// http://codex.wordpress.org/Function_Reference/add_action

/*

******** BEGIN PLUGIN FUNCTIONS ********

*/


// function for: 
function cria_key_add_pages() {

  // anyone can see the menu for the cria_key Plugin
  add_menu_page('cria_key Overview','Criar Key OAuth1', 'read', 'cria_key_overview', 'cria_key_overview', cria_key_PATH.'images/b_status.png');
  // http://codex.wordpress.org/Function_Reference/add_menu_page

  // this is just a brief introduction
  add_submenu_page('cria_key_overview', 'Overview for the cria_key Plugin', 'Overview', 'read', 'cria_key_overview');
  // http://codex.wordpress.org/Function_Reference/add_submenu_page

}

function cria(){
  $authenticator = new WP_JSON_Authentication_OAuth1();
    $consumer = $authenticator->add_consumer( $args );
    echo 'ID: ' .$consumer->ID. '<br />';
    echo 'KEY: ' .$consumer->key. '<br />';
    echo 'secret: ' .$consumer->secret. '<br />';
}

//Aqui exibe a página
function cria_key_overview() {
?>
<div class="wrap"><h2>Criar Key OAuth1</h2>
<p>Criada nova chave de autenticação:</p>
</div>
<?php
return cria();

?>
<br />

<?php
exit;
}

?>
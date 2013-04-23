<?php
/*
Plugin Name: Just Ticker
Plugin URI: http://www.potasiyam.com
Author: Tanbin Islam Siyam
Author URI: http://www.potasiyam.com
Description: Adds a ticker bar to your wordpress site which will show your recent post headlines.
Version: 1.0
Text Domain: just-ticker
License: GNU GPLv2 or later
 */

/* Copyright 2013 Tanbin Islam Siyam (email : potasiyam AT gmail DOT com) */

/*
	This plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
* 
*/
define( 'JT_VERSION', '1.1' );
define( 'JT_PATH', dirname( __FILE__ ) );
define( 'JT_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'JT_FOLDER', basename( JT_PATH ) );
define( 'JT_URL', plugins_url() . '/' . JT_FOLDER );
define( 'JT_URL_INCLUDES', JT_URL . '/inc' );

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

class JT_PlugIn {
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'jt_add_JS' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'jt_add_CSS' ) );
		add_action( 'wp_footer', array($this, 'jt_content') );
		//add_action( 'admin_enqueue_scripts', array( $this, 'jt_add_admin_JS' ) ); 	// This options will be added later
		//add_action( 'admin_enqueue_scripts', array( $this, 'jt_add_admin_CSS' ) ); 	// for features like live preview.
		add_action( 'admin_menu', array( $this, 'jt_admin_pages_callback' ) );
		//add_action('admin_menu', 'my_plugin_menu');
		// var_dump(plugins_url());
		add_action( 'admin_init', array( $this, 'jt_register_settings' ), 5 );
		add_action('wp_head', array($this, 'jt_add_styles') );

		register_activation_hook( __FILE__, array($this,'jt_on_activate_callback') );
		register_deactivation_hook( __FILE__, array($this, 'jt_on_deactivate_callback') );
		

	}

	function jt_on_activate_callback(){
		$defaults = array(
			'jt_opt_in' => 'on',
			'jt_position' => 'bottom',
			'jt_css_position' => 'fixed',
			'jt_postcount' => 5
			);
		update_option('jt_setting', $defaults);
	}

	function jt_on_deactivate_callback(){
		delete_option('jt_setting');
	}
	
	function jt_add_JS() {
		wp_enqueue_script( 'jquery' );
		// load custom JSes and put them in footer
		wp_register_script( 'jtscript', plugins_url( '/js/jtscript.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'jtscript' );
	}
	function jt_add_CSS() {
		wp_register_style( 'jtstyle', plugins_url( '/css/jtstyle.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'jtstyle' );
	}
	function jt_add_admin_JS() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'jtscript-admin', plugins_url( '/js/jtscript-admin.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'jtscript-admin' );
	}
	function jt_add_admin_CSS() {
		wp_register_style( 'jtstyle-admin', plugins_url( '/css/jtstyle-admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'jtstyle-admin' );
	}
	function jt_admin_pages_callback(){
		add_options_page( 'Just Ticker Options', 'Just Ticker Options', 'administrator', 'just-ticker', array($this, 'jt_option_page_callback'));
		
	}
	function jt_option_page_callback() {
		include_once( JT_PATH_INCLUDES . '/jt-options.php' );
		//var_dump('hi');
	}
	function jt_content(){
		$settings = get_option('jt_setting');
		$postcount = $settings['jt_postcount'];
		if($settings['jt_opt_in'] == 'on'){    // if plugin is on, the ticker will show
		?>
		<div id="jtbarwrap">
			<div id="jtcontent">
				<ul id="slidetick">
					<?php
						$i=1;
						$posts_query = new WP_Query("posts_per_page=$postcount");
						while ($posts_query->have_posts()) : $posts_query->the_post();
					?>
					<li <?php if($i==1){echo 'class="current-post"';$i=0;}?>>
						<a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong>
						:
						<?php $content = get_the_content();echo substr($content, 0, 100);?>
						</a>
					</li>
					<?php endwhile; wp_reset_query();
					?>
				</ul>
			</div>
		</div>
		<?php
		}
		//var_dump('This is a string');
	} //end of jt_content

	function jt_register_settings() {
		require_once( JT_PATH . '/jt-plugin-settings.class.php' );
		new JT_Plugin_Settings();
	}

	function jt_add_styles(){
		$settings = get_option('jt_setting');
		if ($settings['jt_position'] == 'top' && $settings['jt_opt_in'] == 'on'):
			?>
			<style type="text/css">
				#jtbarwrap {
					position: <?php echo $settings['jt_css_position']?>;
					top: 0px;
					border-bottom: 1px solid #aaa;
					/*margin-top: -10px; */
				}
			</style>

		<?php
		elseif($settings['jt_opt_in'] == 'on'):
			?>
			<style type="text/css">
				#jtbarwrap{
					position: <?php echo $settings['jt_css_position']?>;
					bottom: 0px;
					border-top: 1px solid #aaa;
					/*margin-top: -10px; */
				}

			</style>



		<?php 
		endif;

	}

}

if (!isset($jtPlugin_singleton)) {
	$jtPlugin_singleton = new JT_PlugIn();
}

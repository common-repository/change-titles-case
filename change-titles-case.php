<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://properprogramming.com
 * @since             1.0.3
 * @package           Change_Titles_Case
 *
 * @wordpress-plugin
 * Plugin Name:       Change Titles Case
 * Plugin URI:        https://properprogramming.com/wp-change-titles-case/
 * Description:       Allows for the batch processing of title case on current posts and categories.
 * Version:           1.0.3
 * Author:            Michael Parisi
 * Author URI:        http://properprogramming.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       change-titles-case
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if(!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CHANGE_CASE_DATA_VERSION', '1.0.14');

//key slug gor the option storage.
define('C_T_C_TC_OPTION_KEY', 'tc_change_case_defaults');
//manually change.
//$array = array('lowercases' => 'a,an,the,and,but,or,nor,if,then,else,when,at,by,from,for,in,off,on,out,over,to,into,with', 'uppercases' => 'asap,unhcr,wpse,wtf,add,adhd');
//update_option(C_T_C_TC_OPTION_KEY, $array);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-change-titles-case-activator.php
 */
function c_t_c_activate_change_titles_case() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-change-titles-case-activator.php';
	require_once plugin_dir_path(__FILE__) . 'admin/class-change-titles-case-admin.php';
	c_t_c_Change_Case_Data_Activator::activate();
	if(!get_option(C_T_C_TC_OPTION_KEY)) {
		c_t_c_set_defaults();
	}
}


/**
 *
 */
//c_t_c_set_defaults(false);

/**
 * Sets the default
 *
 * @param bool $add
 */
function c_t_c_set_defaults($add = TRUE) {
	$array = array(
		'lowercases' => 'a,an,the,and,but,or,nor,if,then,else,when,at,by,from,for,in,off,on,out,over,to,into,with',
		'uppercases' => strtolower(
			'aa,aaa,add,adhd,afaik,afk,ak,al,ar,asap,az,ba,bmd,brb,bs,ca,ceo,cfo,co,corvid,corvid-19,ct,de,diy,est,eta,fdic,fl,ga,gb,ia,id,il,in,ks,ky,la,lol,ma,md,mi,mn,mo,ms,mt,nc,nd,ne,nh,nj,nl,nm,nra,nv,nw,ny,pa,ps,ri,rofl,rsvp,s,sc,sd,se,ssa,sw,tn,tx,ty,usa,ut,va,vp,vt,w,wa,wi,wp,wpse,wtf,wv,wy'
		),
	);

	if($add === TRUE) {
		add_option(C_T_C_TC_OPTION_KEY, $array);
	} else {
		update_option(C_T_C_TC_OPTION_KEY, $array);
	}
}


/* split title string into array of words */
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-change-titles-case-deactivator.php
 */
function c_t_c_deactivate_change_titles_case() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-change-titles-case-deactivator.php';
	c_t_c_Change_Case_Data_Deactivator::deactivate();
}


register_activation_hook(__FILE__, 'c_t_c_activate_change_titles_case');
register_deactivation_hook(__FILE__, 'c_t_c_deactivate_change_titles_case');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-change-titles-case.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function c_t_c_run_change_titles_case() {
	$plugin = new c_t_c_Change_Case_Data();
	$plugin->run();
}


c_t_c_run_change_titles_case();

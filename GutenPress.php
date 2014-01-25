<?php
/**
 * Plugin Name: GutenPress
 * Plugin URI: http://felipelavinz.github.io/GutenPress
 * Description: GutenPress it's a set of tools for people that want to build great things using WordPress.
 * Author: Felipe Lavin
 * Version: 0.9
 * Author URI: http://www.yukei.net
 * License: GPLv2
 *
 * Register autoload instances to manage GutenPress files and other bootstraping actions
 *
 * @package GutenPress
 * @version 0.9
 */

function gp_register_autoload(){

	require_once __DIR__ .'/GutenPress/Autoload/SplClassLoader.php';

	// register GutenPress autoloader
	$GutenPress = new SplClassLoader('GutenPress', __DIR__);
	$GutenPress->register();

	define('GUTENPRESS_PATH', __DIR__ .'/GutenPress');

	// public URL should always be relative to mu-plugins
	$gp_rel_path = str_replace(WPMU_PLUGIN_DIR, '', __DIR__);
	define('GUTENPRESS_URL', WPMU_PLUGIN_URL . $gp_rel_path .'/GutenPress' );
}
// call immediately, to avoid issues with network-activated plugins
gp_register_autoload();

add_action('plugins_loaded', 'gp_admin_bootstrap');
function gp_admin_bootstrap(){
	if ( ! is_admin() )
		return;

	load_muplugin_textdomain('gutenpress', 'GutenPress/i18n/' );
	add_action('admin_enqueue_scripts', 'gp_admin_enqueue_scripts');
	add_action('admin_print_footer_scripts', 'gp_admin_print_footer_scripts');

	// post type model generator
	$PostTypeBuilder = GutenPress\Build\PostType::getInstance();
	$CustomTaxonomyBuilder = GutenPress\Build\Taxonomy::getInstance();

	do_action('gp_admin_bootstrap');
}

function gp_admin_enqueue_scripts(){
	// register css and javascript assets
	// instantiate class
	$Assets = \GutenPress\Assets\Assets::getInstance();
	$Assets->setPrefix('gp-admin-');
	// register assets
	$Assets->enqueueScript(
		'head_js-loader',
		$Assets->scriptUrl('head.load')
	);
	$Assets->enqueueStyle(
		'form-styles',
		$Assets->styleUrl('FormStyles')
	);
	do_action('gp_admin_register_assets', $Assets);
}

function gp_admin_print_footer_scripts(){
	$Assets = \GutenPress\Assets\Assets::getInstance();
	$Assets->loadEnqueuedScripts();
}
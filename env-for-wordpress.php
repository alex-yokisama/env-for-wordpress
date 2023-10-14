<?php

/**
 *
 * @since             1.0.0
 * @package           Env_For_Wordpress
 *
 * @wordpress-plugin
 * Plugin Name:       Env for WordPress
 * Description:       Env for WordPress
 * Version:           1.0.0
 * Author:            Alex Yokisama
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('ENV_FOR_WORDPRESS_VERSION', '1.0.0');

require_once plugin_dir_path(__FILE__) . 'includes/src/EnvForWordpress.php';
require_once plugin_dir_path(__FILE__) . 'includes/src/EnvProvider.php';
require_once plugin_dir_path(__FILE__) . 'includes/src/EnvFromFile.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';


add_filter('env_for_wordpress_providers', function ($providers) {
	$file_path = apply_filters('default_env_file_path', get_template_directory() . '/.env');
	$providers[] = new \EnvForWordpress\EnvFromFile($file_path);

	return $providers;
});

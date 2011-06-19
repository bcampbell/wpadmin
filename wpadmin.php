#!/usr/bin/env php
<?php

/**
 * wpadmin
 *
 * Coded to Zend's coding standards:
 * http://framework.zend.com/manual/en/coding-standard.html
 *
 * File format: UNIX
 * File encoding: UTF8
 * File indentation: Spaces (4). No tabs
 *
 * @copyright  Copyright (c) 2011 88mph. (http://88mph.co)
 * @license    GNU
 */

/**
 * @file
 * wpadmin is a PHP script implementing a command line shell for WordPress.
 *
 * @requires PHP CLI 5.2.0, or newer.
 * @since 0.0.1
 */
 
// Start output buffering to stop WordPress from spitting out its usual output.
ob_start("strip_tags");

define('WPADMIN_BASE_PATH', dirname(__FILE__));
define('WPADMIN_LIBS_PATH', dirname(__FILE__) . '/lib');

// Does the user have access to read the directory? If so, allow them to use the
// command line tool.
if(true == is_readable('wp-load.php')){
    
    // Load WordPress libs.
    require_once('wp-load.php');
    require_once(ABSPATH . WPINC . '/template-loader.php');
    require_once(ABSPATH . 'wp-admin/includes/admin.php');

    // Load wpdmin libs.
    require_once WPADMIN_LIBS_PATH . '/WpAdmin.php';
    require_once WPADMIN_LIBS_PATH . '/WpAdmin/User.php';
    require_once WPADMIN_LIBS_PATH . '/WpAdmin/Option.php';
    require_once WPADMIN_LIBS_PATH . '/WpAdmin/Plugin.php';
    require_once WPADMIN_LIBS_PATH . '/Console/Table.php';

    // Run main WpAdmin::exec() method.
    WpAdmin::exec($argv);
    
}else{
    die("Either this is not a WordPress document root or you do not have 
                                        permission to administer this site. \n");
}

ob_end_flush();

<?php

/*
	Plugin Name: AcademicPress
	Plugin URI: http://academicpress.benjaminsommer.com
	Description: Turn your Blog into an <strong>academic publishing site</strong>. <a href="options-general.php?page=academicpress">AcademicPress Settings</a>
	Author: Benjamin Sommer
	Version: 1.0
	Author URI: http://benjaminsommer.com
	License: CC GNU GPL 2.0 license
	Text Domain: academicpress
	
	Coding standard: http://framework.zend.com/manual/en/coding-standard.html
*/

// Initialize Plugin and declare API
require 'Acp.php';
Acp::init();
Acp::loadClass('Acp_Plugin');
register_activation_hook(__FILE__, 'Acp_Plugin::activate');
register_deactivation_hook(__FILE__, 'Acp_Plugin::deactivate');
register_uninstall_hook(__FILE__, 'Acp_Plugin::uninstall');
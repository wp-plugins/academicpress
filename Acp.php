<?php

class Acp {
	public static function init() {
		self::$api = dirname(__FILE__) . '/';
		
		add_action('init', 'Acp::onPublicView');
		add_action('admin_init', 'Acp::onAdmin');
		add_action('admin_menu', 'Acp::initAdminMenu');
		add_action('wp_ajax_academicpress', 'Acp::ajaxHandler');
		
		Acp::registerBibStyle('apa', 'Acp_Bib_Style_Apa', self::$api .'Acp/Bib/Style/Apa.php');
		Acp::registerBibStyle('chicago', 'Acp_Bib_Style_Chicago', self::$api .'Acp/Bib/Style/Chicago.php');
		Acp::registerBibStyle('harvard', 'Acp_Bib_Style_Harvard', self::$api .'Acp/Bib/Style/Harvard.php');
		Acp::registerBibStyle('mla', 'Acp_Bib_Style_Mla', self::$api .'Acp/Bib/Style/Mla.php');
		Acp::registerBibStyle('turabian', 'Acp_Bib_Style_turabian', self::$api .'Acp/Bib/Style/Turabian.php');
	}
	
	/* PLUGIN INIT */
	public static function onAdmin() {
		Acp::loadClass('Acp_UI_Importer');
		Acp::loadClass('Acp_UI_Virtualbox');
		Acp_UI_Importer::init();
		Acp_UI_Virtualbox::init();
	}
	
	public static function initAdminMenu() {
		Acp::loadClass('Acp_UI_Settings');
		Acp_UI_Settings::init();
	}
	
	public static function onPublicView() {
		Acp::loadClass('Acp_Bib_Shortcode');
		Acp_Bib_Shortcode::init();
	}
	
	public static function loadClass($classname) {
		require_once self::$api . str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';
	}
	
	/* CITATION STYLE */
	public static function registerBibStyle($codename, $classname, $classPath) {
		self::$styles[$codename] = array('class'=>$classname, 'path'=>$classPath);
	}
	
	/**
	 * 
	 * @param String $codename
	 * @return Acp_Bib_IStyle
	 */
	public static function resolveBibStyle($codename) {
		if (isset(self::$styles[$codename])) {
			require_once self::$styles[$codename]['path'];
			return new self::$styles[$codename]['class'];
		} else
			return null;
	}
	
	public static function getBibStyles() {
		return array_keys(self::$styles);
	}
	
	/* AJAX */
	public static function ajaxHandler() {
		if (isset($_POST['class']) && !empty($_POST['class'])) {
			$args = $_POST;
			unset($args['class']);
			$method = 'ajax';
			if (isset($_POST['method']) && !empty($_POST['method'])) {
				$method = $_POST['method'];
				unset($_POST['method']);
			}
			Acp::loadClass($_POST['class']);
			$o = new $_POST['class']();
			$o->$method($args);
		}
		exit;
	}
		
	
	/* MISC */	
	public static function version() {
		return '3.0';
	}
	
	public static function author() {
		return 'Benjamin Sommer';
	}
	
	public static function website() {
		return 'http://academic-press.benjaminsommer.com';
	}
	
	private static $api;	
	private static $styles = array();
}
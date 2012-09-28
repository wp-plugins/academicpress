<?php

class Acp_Plugin {
	
	public function install() {
		
	}
	
	public function activate() {
		$o = get_option('academicpress_scripts');
		if ($o === false)
			add_option('academicpress_scripts', array('preprocessor'=>array(), 'postprocessor'=>array()));
	}
	
	public function deactivate() {
		
	}
	
	public function reset() {
		update_option('academicpress_scripts', array('preprocessor'=>array(), 'postprocessor'=>array()));
	}
	
	public function uninstall() {
		delete_option('academicpress_scripts');
	}
}
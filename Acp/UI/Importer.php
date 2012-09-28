<?php

class Acp_UI_Importer {
	public static function init() {
		add_meta_box('acp-importer', 'Import Bibliographic Data', array('Acp_UI_Importer', 'display'));
	}
	
	public function display() {
	
	}
}
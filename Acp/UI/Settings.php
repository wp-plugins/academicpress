<?php

class Acp_UI_Settings {	
	public static function init() {
		add_options_page('AcademicPress', 'AcademicPress', 'manage_options', 'academicpress', array(new Acp_UI_Settings(),'display'));
	}
	
	public function display() {
		Acp::loadClass('Acp_UI_Table');
		Acp::loadClass('Acp_UI_Select');
		
		$onoff = new Acp_UI_Select();
		$onoff->addOptions(array('true'=>'On', 'false'=>'Off'));
		
		$scripts = get_option('academicpress_scripts');
		
		if (!empty($_POST)) {
			foreach ($_POST['scripts'] as $k=>$proc)
				foreach ($proc as $k2=>$v)
					$scripts[$k][$k2] = stripslashes($v);
			update_option('academicpress_scripts', $scripts);
		}
		
		echo '<div class="wrap">';
		echo '<div id="icon-options-general" class="icon32"><br /></div>';
		echo '<h2>AcademicPress Settings</h2>';		
		echo '<form action="" method="post">';
		
		/*
		echo '<h3>General</h3>';
		$g = new Acp_UI_Table();
		$g->addRow(array('VirtualBox', $onoff->render('visibility')));
		echo $g->render();
		
		echo '<h3>Bibliography</h3>';
		$g = new Acp_UI_Table();
		$g->addRow(array('Bibliographic Style', '<input type="text" name="bibstyle" />'));
		$g->addRow(array('Table Headline', '<input type="text" name="bibtable_headline" />'));
		$g->addRow(array('Show Excerpts In Table', $onoff->render('visibility')));
		$g->addRow(array('Automatically Display Table', $onoff->render('visibility')));
		$g->addRow(array('HTML Code: Pre Table', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Post Headline', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Post Entry', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Post Table', '<input type="text" name="scope" />'));
		echo $g->render();
		
		echo '<h3>Footnotes</h3>';
		$g = new Acp_UI_Table();
		$g->addRow(array('Number Format', '<input type="text" name="bibtable_headline" />'));
		$g->addRow(array('Table Headline', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Pre Table', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Post Headline', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Post Entry', '<input type="text" name="scope" />'));
		$g->addRow(array('HTML Code: Post Table', '<input type="text" name="scope" />'));
		echo $g->render();
		
		echo '<h3>Shortcode/Script Execution</h3>';
		$g = new Acp_UI_Table();
		$g->addRow(array('Initial Scope', '<input type="text" name="scope" />'));
		$g->addRow(array('Initial Visibility', $onoff->render('visibility')));
		$g->addRow(array('Initial Target Switching', $onoff->render('visibility')));
		echo $g->render();
		*/
		
		
		$types = get_post_types(array('public'=>true), 'objects');
		if (isset($types['attachment']))
			unset($types['attachment']);
		
		echo '<h3>Script Preprocessors</h3>';
		foreach ($types as $k=>$t)
			echo "<p>Post type: ".$t->labels->name."<br />".
				  '<textarea name="scripts[preprocessor]['.$k.']" style="width:100%; max-width:800px; height:150px;">'.
					(isset($scripts['preprocessor'][$k]) ? $scripts['preprocessor'][$k] : '').
				  '</textarea></p>';
		
		echo '<h3>Script Postprocessors</h3>';
		foreach ($types as $k=>$t)
			echo "<p>Post type: ".$t->labels->name."<br />".
				  '<textarea name="scripts[postprocessor]['.$k.']" style="width:100%; max-width:800px; height:150px;">'.
					(isset($scripts['postprocessor'][$k]) ? $scripts['postprocessor'][$k] : '').
				  '</textarea></p>';
		
		echo '<input type="submit" name="save" value="Save Changes" class="button-primary" />';
		echo '</form>';
		echo '</div>';
	}
}
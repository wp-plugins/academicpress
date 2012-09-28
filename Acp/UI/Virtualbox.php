<?php

class Acp_UI_Virtualbox {
	public static function init() {
		$types = get_post_types(array('public'=>true));
		foreach ($types as $t)	
			add_meta_box('acp-virtualbox', 'Academic Press VirtualBox', array('Acp_UI_Virtualbox','display'), $t, 'advanced', 'high');		
	}
	
	public function display() {
		Acp::loadClass('Acp_UI_Select');
		$cmds = new Acp_UI_Select();
		$cmds->setId('acp-virtualbox-cmds');
		$cmds->addOption('', 'Insert Command');
		
		$cmds->setGroup('stdops', 'Standard Operations');
		$cmds->addOption('[acp add author="val1" title="val2" id="UNIQUE_ID" ... /]', 'Add Reference To Collection');
		$cmds->addOption('[acp author="val1" title="val2" id="UNIQUE_ID" ... /]', 'Cite New Reference');
		$cmds->addOption('[acp author="val1" title="val2" id="UNIQUE_ID" ... ]{author} .... {title}...[/acp]', 'Cite New Reference (with custom context)');
		$cmds->addOption('[acp id="UNIQUE_ID" ... /]', 'Cite Existing Reference');
		$cmds->addOption('[acp id="UNIQUE_ID" ... ]{author} .... {title}...[/acp]', 'Cite Existing Reference (with custom context)');
		$cmds->addOption('[acp display title="TABLE_TITLE" /]', 'Display Table of Bibliography');
		$cmds->addOption('[acp display title="TABLE_TITLE" leve="2" /]', 'Display Table of Bibliography (+ Options)');
		$cmds->addOption('[acp remove id="UNIQUE_ID" ... /]', 'Remove Existing Reference');
		$cmds->addOption('[acp remove author="val1" title="val2" ... /]', 'Remove Set Of References');
		
		$cmds->setGroup('setops', 'Working with Collections (References)');
		$cmds->addOption('[acp sort author="desc" title="asc" ... /]', 'Sort');
		$cmds->addOption('[acp find author="val1" title="val2" ... target="TARGET_SCOPE" /]', 'Search');
		$cmds->addOption('[acp union="ANOTHER_SCOPE" target="TARGET_SCOPE" /]', 'Union');
		$cmds->addOption('[acp diff="ANOTHER_SCOPE" target="TARGET_SCOPE" /]', 'Difference');
		$cmds->addOption('[acp interset="ANOTHER_SCOPE" target="TARGET_SCOPE" /]', 'Intersect');
		$cmds->addOption('[acp copy="" target="TARGET_SCOPE" /]', 'Copy');
		$cmds->addOption('[acp clear /]', 'Clear Active Collection');
		$cmds->addOption('[acp clearall /]', 'Clear All Collections');
		
		$cmds->setGroup('notes', 'Footnotes');
		$cmds->addOption('[acp footnote]...[/acp]', 'Insert Footnote');
		$cmds->addOption('[acp footnote display title="TABLE_TITLE" /]', 'Display Table of Footnotes');
		$cmds->addOption('[acp footnote display title="TABLE_TITLE" level="2" /]', 'Display Table of Footnotes (+ Options)');
		
		$cmds->setGroup('state', 'Changing States');
		$cmds->addOption('[acp use_scope="" /]', 'Use Another Scope (Switch between Collections)');
		$cmds->addOption('[acp use_bibstyle="" /]', 'Set Bibliographic Style');
		$cmds->addOption('[acp use_visibility="true" /]', 'Enable Output');
		$cmds->addOption('[acp use_visibility="false" /]', 'Disable Output');
		$cmds->addOption('[acp use_switchtarget="true" /]', 'Enable: Automatically Switch To Target');
		$cmds->addOption('[acp use_switchtarget="false" /]', 'Disable: Automatically Switch To Target');
		
		$cmds->setGroup('debug', 'Debugging and Testing');
		$cmds->addOption('[acp print="scope" /]', 'Print State: Active Scope');
		$cmds->addOption('[acp print="bibstyle" /]', 'Print State: Bibliographic Style');
		$cmds->addOption('[acp print="visibility" /]', 'Print State: Visiblity');
		$cmds->addOption('[acp print="switchtarget" /]', 'Print State: Target Switching');
		$cmds->addOption('[acp print="scopes" /]', 'Print List of Scopes');
		$cmds->addOption('[acp print="bibstyles" /]', 'Print List of Available Bibliographic Styles');
		$cmds->addOption('[acp print="collection" /]', 'Print Active Collection');
		$cmds->addOption('[acp print="shortcodes" /]', 'Print Collection as Shortcodes');
		
		echo '<textarea style="width:100%; height:200px" id="acp-virtualbox-src" placeholder="Type your ACP commands here. Choose from list below or read reference/tutorials."></textarea>';
		echo '<p><a class="button button-primary" id="acp-virtualbox-btn-simulate">Simulate</a> '.				
				$cmds->render().
				'<a class="button button-secondary" id="acp-virtualbox-btn-insert">Insert</a> '.
				'<a class="button button-secondary" id="acp-virtualbox-btn-insertbottom">Insert At Bottom</a> | '.
				'<a class="button button-secondary" href="http://academicpress.benjaminsommer.com/getting-started/" target="blank">Getting Started</a> '.
				'<a class="button button-secondary" href="http://academicpress.benjaminsommer.com/tutorials/" target="blank">Tutorials</a> '.
				'<a class="button button-secondary" href="http://academicpress.benjaminsommer.com/syntax-reference/" target="blank">Syntax Reference</a> '.
				'</p>';
		echo '<div style="width:100%; height:300px; overflow:auto; background: white; border: 1px solid silver; border-radius: 3px; padding:3px" id="acp-virtualbox-output" placeholder="Press `Simulate` to execute script above in a virtual box (without changes to WordPress). Typical use cases: check validity of references, preview inline citations and table of bibliographic, import from and export to datasources, prepare/generate shortcodes for your posts."></div>';
		
		
		global $wp_query;
		$postid = $wp_query->post->ID;
		if (empty($postid)) {
			global $post;
			$postid = $post->ID;
		}
		$posttype = get_post_type($postid);
		?>
<script>
jQuery.fn.extend({
	insertAtCaret: function(myValue){
	  return this.each(function(i) {
	    if (document.selection) {
	      //For browsers like Internet Explorer
	      this.focus();
	      sel = document.selection.createRange();
	      sel.text = myValue;
	      this.focus();
	    }
	    else if (this.selectionStart || this.selectionStart == '0') {
	      //For browsers like Firefox and Webkit based
	      var startPos = this.selectionStart;
	      var endPos = this.selectionEnd;
	      var scrollTop = this.scrollTop;
	      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
	      this.focus();
	      this.selectionStart = startPos + myValue.length;
	      this.selectionEnd = startPos + myValue.length;
	      this.scrollTop = scrollTop;
	    } else {
	      this.value += myValue;
	      this.focus();
	    }
	  })
	}
	});
		
jQuery(document).ready(function(e) {
	var cmds = jQuery('#acp-virtualbox-cmds');
	var src = jQuery('#acp-virtualbox-src');
	var output = jQuery('#acp-virtualbox-output');
	cmds.change(function(e) {
		src.insertAtCaret(cmds.val());
	});
	jQuery('#acp-virtualbox-btn-insert').click(function(e) {
		src.insertAtCaret(cmds.val());
	});
	jQuery('#acp-virtualbox-btn-insertbottom').click(function(e) {
		src.val(src.val() + '\n');
		src.val(src.val() + cmds.val());
	});
	jQuery('#acp-virtualbox-btn-simulate').click(function(e) {
		var data = {
			action: 'academicpress',
			'class': 'Acp_UI_Virtualbox',
			source: src.val(),
			posttype: '<?php echo $posttype; ?>'
		};
		jQuery.post(ajaxurl, data, function(response) {
			output.html(response);
		});
	});
});
</script>
		
		<?php
	}
	
	public function ajax($args) {
		$content = stripslashes($args['source']);
		$pu = Acp_Bib_Shortcode::getSingleton();
		$content = $pu->preprocessor($content, $args['posttype']);
		$content = do_shortcode($content);
		echo nl2br($pu->postprocessor($content, $args['posttype']));
	}
}
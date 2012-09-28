<?php

class Acp_Bib_Shortcode {
	public function __construct() {
		self::$singleton = $this;
	}
	
	public static function init() {
		$o = new Acp_Bib_Shortcode();
		add_filter('the_content', array($o,'preprocessor'), 1);
		add_shortcode('acp', array($o,'filter'));
		add_filter('the_content', array($o,'postprocessor'), 20);
	}
	
	public static function getSingleton() {
		return self::$singleton;
	}
	
	public function preprocessor($content, $posttype=null) {
		if (empty($posttype)) {
			global $wp_query;
			$postid = $wp_query->post->ID;
			$posttype = get_post_type($postid);
		}
		$scripts = get_option('academicpress_scripts');
		if (isset($scripts['preprocessor'][$posttype]))
			return do_shortcode($scripts['preprocessor'][$posttype]) . $content;
		else
			return $content;
	}
	
	public function postprocessor($content, $posttype=null) {
		if (empty($posttype)) {
			global $wp_query;
			$postid = $wp_query->post->ID;
			$posttype = get_post_type($postid);
		}
		$scripts = get_option('academicpress_scripts');
		if (isset($scripts['postprocessor'][$posttype]))
			return $content . do_shortcode($scripts['postprocessor'][$posttype]);
		else
			return $content;
	}
	
	public function filter($args, $content = '') {
		Acp::loadClass('Acp_Bib_Collection');
		Acp::loadClass('Acp_Bib_IStyle');
		$this->updateFSM($args);
		if (empty($args))
			return '';
		$args['tag:content'] = $content;
		if ($this->doUnaryOp($args))
			return '';
		if ($this->doSetOp($args))
			return ''.serialize($args);
		if ($this->doImport($args))
			return '';
		if ($this->doExport($args))
			return '';
		if (($t=$this->doDebugOp($args)) != '')
			return $t;
		if (!self::$visible)
			return '';
		if (in_array('footnote', $args)) {
			Acp::loadClass('Acp_Footnote');
			if (in_array('display', $args)) {
				if ($this->getFootnotes()->count() > 0)
					return Acp_Footnote::getTable($this->getFootnotes(), $args);
				else return '';
			}
			$args['tag:content'] = do_shortcode($content);
			$args['id'] = md5($args['tag:content']);
			return Acp_Footnote::getInlineFormat($args, $this->getFootnotes()->add($args)->count());
		}
		if (in_array('display', $args))
			return $this->displayTable($args);
		return $this->cite($args);
	} 
	
	private function updateFSM(&$args) {
		if (isset($args['use_scope'])) {
			self::$datascope = $args['use_scope'];
			unset($args['use_scope']);
		}
		if (isset($args['use_bibstyle'])) {
			self::$bibstyle = $args['use_bibstyle'];
			unset($args['use_bibstyle']);
		}
		if (isset($args['use_visiblity'])) {
			self::$visible = filter_var($args['use_visiblity'], FILTER_VALIDATE_BOOLEAN);
			unset($args['use_visibility']);
		}
		if (isset($args['use_switchtarget'])) {
			self::$switchtarget = filter_var($args['use_switchtarget'], FILTER_VALIDATE_BOOLEAN);
			unset($args['use_switchtarget']);
		}
	}

	private function doUnaryOp(&$args) {
		foreach ($args as $k=>$v) {
			if (!is_numeric($k))
				continue;
			switch ($v) {
				case 'add':
					unset($args[$k]);
					$this->getCollection()->add($args);
					return true;
				case 'remove':
					$this->getCollection()->remove($args);
					return true;
				case 'sort':
					unset($args[$k]);
					$s = array();
					foreach ($args as $c=>$order)
						if (!empty($c) && ($order == 'asc' || $order == 'desc'))
							$s[$c] = ($order=='desc' ? SORT_DESC : SORT_ASC);
					$this->getCollection()->sortBy($s);
					return true;
				case 'find':
					unset($args[$k]);
					unset($args['tag:content']);
					$target = '';
					if (isset($args['target'])) {
						$target = $args['target'];
						unset($args['target']);
					}
					if (!empty($target) && $target != self::$datascope) {
						$this->getCollection()->find($args, $this->getCollection($target));
						if (self::$switchtarget)
							self::$datascope = $target;
					}
					return true;
				case 'clear':
					$this->getCollection()->clear();
					return true;
				case 'clearall':
					$this->clearCollections();
					return true;
			}
		}
		return false;
	}
	
	private function doSetOp(&$args) {
		$target = isset($args['target']) ? $args['target'] : self::$datascope;
		foreach ($args as $k=>$v) {
			switch ($k.'') {
				case 'union':
					if ($this->hasScope($v)) {
						$this->getCollection()->union($this->getCollection($v), $this->getCollection($target));
						if (self::$switchtarget)
							self::$datascope = $target;
					}
					return true;
				case 'diff':
					if ($this->hasScope($v)) {
						$this->getCollection()->diff($this->getCollection($v), $this->getCollection($target));
						if (self::$switchtarget)
							self::$datascope = $target;
					}
					return true;
				case 'intersect':
					if ($this->hasScope($v)) {
						$this->getCollection()->intersect($this->getCollection($v), $this->getCollection($target));
						if (self::$switchtarget)
							self::$datascope = $target;
					}
					return true;
				case 'copy':
					$this->setCollection ($this->getCollection()->copy(), $target);
					if (self::$switchtarget)
						self::$datascope = $target;
					return true;
			}
		}
		return false;
	}

	/**
	 * @todo Import BibTex and other formats
	 * @param array $args
	 */
	private function doImport(&$args) {
		return false;
	}
	
	/**
	 * @todo Export as BibTex and other formats
	 * @param array $args
	 */
	private function doExport(&$args) {
		return false;
	}
	
	private function doDebugOp(&$args) {
		if (isset($args['print'])) {
			switch ($args['print']) {
				case 'scopes':
					return '['. implode(',', $this->getScopes()) .']';
				case 'scope':
					return self::$datascope;
				case 'bibstyle':
					return self::$bibstyle;
				case 'bibstyles':
					return '['. implode(',', Acp::getBibStyles()) .']';
				case 'visibility':
					return self::$visible;
				case 'switchtarget':
					return self::$switchtarget;
				case 'collection':
					return $this->getCollection()->asString();
				case 'shortcodes':
					$s = '';
					foreach ($this->getCollection() as $o) {
						$t = array();
						foreach ($o as $k=>$v)
							if ($k != 'tag:content')
								$t[] = "$k=\"$v\"";
						if (!empty($o['tag:content']))
							$s .= '[acp '. implode(' ', $t) .' ]'. $o['tag:content'] .'[/acp] '. PHP_EOL;
						else
							$s .= '[acp '. implode(' ', $t) .' /] '. PHP_EOL;
					}
					return (empty($s) ? ' ' : $s);
			}
		}
		return '';
	}
	
	private function displayTable(&$args) {
		if ($this->getCollection()->count() > 0) {
			$style = Acp::resolveBibStyle(self::$bibstyle);
			if (!empty($style))
				return $style->getTable($this->getCollection(), $args);
			else
				return '[ERROR: undefined citation style "'. self::$bibstyle .'"]';
		} else
			return '';
	}
	
	private function cite(&$args) {
		$this->getCollection()->add($args);
		if (!empty($args['tag:content'])) {
			$asep = ';';
			if (str_word_count($args['author']) > 3 && strpos($args['author'],',') !== false && strpos($args['author'],';') === false)
				$asep = ',';
			$a = explode($asep, $args['author']);
			if (sizeof($a) == 2)
				$args['author'] = "{$a[0]} and {$a[1]}";
			else if (sizeof($a) > 2)
				$args['author'] = "{$a[0]} et.al.";
			
			$c = $args['tag:content'];
			foreach ($args as $k=>$v)
				$c = str_ireplace("{{$k}}", $v, $c);
			return $c;
		} else {
			$style = Acp::resolveBibStyle(self::$bibstyle);
			if (!empty($style))
				return $style->getInlineFormat($args);
			else
				return '[ERROR: undefined citation style "'. self::$bibstyle .'"]';
		}
	}

	private function hasScope($scope) {
		return isset($this->collections[$this->current_postid]) && isset($this->collections[$this->current_postid][$scope]);
	}
	
	private function getScopes() {
		if (isset($this->collections[$this->current_postid]))
			return array_keys($this->collections[$this->current_postid]);
		else
			return array();
	}
	
	/**
	 * 
	 * @param string $scope
	 * @return Acp_Bib_Collection
	 */
	private function getCollection($scope=null) {
		$s = !empty($scope) ? $scope : self::$datascope;
		if (!isset($this->collections[$this->current_postid][$s]))
			$this->collections[$this->current_postid][$s] = new Acp_Bib_Collection();
		return $this->collections[$this->current_postid][$s];
	}
	
	private function setCollection($collection, $scope) {
		$this->collections[$this->current_postid][$scope] = $collection;
	}
	
	private function clearCollections() {
		$this->collections[$this->current_postid] = array();
	}
	
	/**
	 *
	 * @return Acp_Bib_Collection
	 */
	private function getFootnotes() {
		if (!isset($this->footnotes[$this->current_postid]))
			$this->footnotes[$this->current_postid] = new Acp_Bib_Collection();
		return $this->footnotes[$this->current_postid];
	}
	
	private $current_postid;
	private $collections = array();
	private static $singleton;
	private $footnotes = array();
	
	/* State machine simulation */
	private static $datascope = 'default';
	private static $bibstyle = 'apa';
	private static $visible = true;
	private static $switchtarget = false;
}


/*
 * [acp use_scope="" use_bibstyle="" use_visibility="true" use_switchtarget="true" /]
 * 
 * [acp import="bibtex" url="..." /]
 * [acp import="bibtex"].....[/acp]
 * [acp export="bibtex" target="stdout" /]
 * [acp export="bibtex" target="mailto:...." /]
 * [acp export="bibtex" target="file.bibtex" /]
 * 
 * [acp add att1="desc" att2="asc" ... /]
 * [acp remove att1="desc" att2="asc" ... /]
 * [acp sort att1="desc" att2="asc" ... /]
 * [acp find att1="..." att2=".." target="....." /]
 * [acp clear /]
 * 
 * [acp union=".." target="...." /]
 * [acp diff=".." target="...." /]
 * [acp intersect=".." target="...." /]
 * [acp copy="" target="...." /]
 * 
 * [acp cite att1="desc" att2="asc" ... /]
 * [acp att1="desc" att2="asc" ... /]
 * [acp att1="desc" att2="asc" ... ]{att1} argued that ....[/acp]
 * 
 * [acp display title="" /]
 * 
 * // DEBUG TOOLS
 * [acp print="scopes" /]
 * [acp print="scope" /]
 * [acp print="collection" /]
 * [acp print="shortcodes" /]
 */
/*
Current Scope: [acp print="scope" /]
Current Bibliographic Style: [acp print="bibstyle" /]
Current Visibility: [acp print="visibility" /]
Available/Defined Scopes: [acp print="scopes" /]

Add References (silent) [acp add author="John M." title="Working with WordPress" year="2012" media="post" id="mesh2012-1" /] [acp add author="Christophe Hery" title="Importance Sampling of Reflections from Hair Fibers" year="2011" media="paper" id="hery2011-1" /] 

Add simple inline citation: [acp author="Hayley Iben" title="Artistic Simulation of Curly Hair" year="2012" media="paper" id="hayley2012-1" /]

Add contextual inline citation from [acp author="Matthias Niessner" title="Feature Adaptive GPU Rendering of Catmull-Clark Subdivision Surfaces" year="2012" media="paper" id="niessner2012-1"]{author} ({title})[/acp].

Print Collection: 
[acp print="collection" /]

Export As WP Shortcodes: 
[acp print="shortcodes" /]

Sort References by year and title: [acp sort year="desc" title="asc" /]

Export As WP Shortcodes: 
[acp print="shortcodes" /]
Current Scope: [acp print="scope" /]

Extract All Citations from 2012 (and store result + switch to scope "2012"): [acp find year="2012" target="2012" /]
[acp print="shortcodes" use_scope="2012" /]
Current Scope: [acp print="scope" /]
Current Scope: [acp print="scope" use_scope="default" /]
Enable Automatic Target Switching [acp use_switchtarget="true" /]

Compute difference: current collection (default) minus collection "2012" and store result in collection "2011": [acp diff="2012" target="2011" /]
[acp print="shortcodes" /]

Extract all papers from collection "2012" and merge result with collection "2012", store result in target "recent-papers": 
[acp print="scope" /] [acp find media="paper" target="recent-papers" use_scope="2012" /] [acp print="scope" /] [acp union="2011" /]
[acp print="shortcodes" /]

Copy these references to "all-citations" (will overwrite existing entries in target collection) and find all media types "post" from scope "default" and store them in the scope "all-citations" (they are appended; others are not overwritten!):
[acp copy="" target="all-citations" /] [acp find media="post" use_scope="default" target="all-citations" /] [acp print="shortcodes" /]

Collections automatically check for redundant entries
[acp find media="post" use_scope="default" target="all-citations" /] [acp print="shortcodes" /]

Display Table of Bibliography
[acp display title="Table of Bibliography" /]


[acp use_bibstyle="turabian" /]
[acp author="John Mesh; Jack Sparrow" title="Secrets of Black Perl" id="sparrow2012" year="2010" /]
[acp author="Hayley Iben; Mark Meyer; Lena Petrovic; Olivier Soares; John Anderson; Andrew Witkin" title="Artistic Simulation of Curly Hair" year="2012" media="book" id="hayley2012-1" /]
[acp author="Matthias Niessner, Charles Loop, Mark Meyer, Tony DeRose" title="Feature Adaptive GPU Rendering of Catmull-Clark Subdivision Surfaces" year="2012" media="paper" id="niessner2012-1"]{author} ({title})[/acp].


*/
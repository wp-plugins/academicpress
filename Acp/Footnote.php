<?php

class Acp_Footnote {
	public static function getInlineFormat($args, $number) {
		$k = md5($args['tag:content']);
		return '<a href="#note'.$k.'" name="'.$k.'" title="'.$args['tag:content'].'" style="text-decoration:none"><sup>['. $number .']</sup></a> ';
	}
	
	public static function getTable($collection, $args) {
		if (!isset($args['level']) || empty($args['level']))
			$args['level'] = '3';
		$t = "<h{$args['level']}>". $args['title'] ."</h{$args['level']}>";
		$t .= '<ol>';
		$i = 1;
		foreach ($collection as $e) {
			$k = md5($e['tag:content']);
			$t .= '<li><a name="note'.$k.'"></a> '. $e['tag:content'] .' <a href="#'.$k.'" style="text-decoration:none;font-weight:bold">^</a></li>';
		} 
		$t .= '</ol>';
		return $t;
	}
}
<?php

interface Acp_Bib_IStyle {
	public function getInlineFormat($args);
	
	public function getCitationFormat($args);
	
	public function getTable($collection, $args);
}
<?php

class Acp_Bib_Style_Harvard implements Acp_Bib_IStyle {
	public function getInlineFormat($args) {
		$title = $author = $year = '';
		extract($args);
		
		$asep = ';';
		if (str_word_count($author) > 3 && strpos($author,',') !== false && strpos($author,';') === false)
			$asep = ',';
		$a = explode($asep, $author);
		if (sizeof($a) == 2)
			$author = "{$a[0]} and {$a[1]}";
		else if (sizeof($a) > 2)
			$author = "{$a[0]} et.al.";
		
		if (!empty($title)) {
			$t = "<em>\"$title\"</em>";
			if (!empty($author) && !empty($year))
				return "$t ($author, $year)";
			else if (!empty($author))
				return "$t ($author)";
			else
				return "$t ($year)";
		} else {
			if (!empty($author) && !empty($year))
				return "($author, $year)";
			else if (!empty($author))
				return "($author)";
			else
				return "($year)";
		}
	}
	
	public function getCitationFormat($args) {
		extract($args);
		$_out = '';
		
		$date = strlen($year)>0 ? (strlen($month)>0 ? 
				(strlen($day)>0 ? "$year, $month $day" : "$year, $month") : $year) : 'n.d.';
		$date = " $date";
		$dateaccess = !empty($year_access) ? (!empty($month_access) ? (!empty($day_access) ? "$month_access $day_access, $year_access" : "$month_access $year_access") : $year_access) : '';
		
		$asep = ';';
		if (str_word_count($author) > 3 && strpos($author,',') !== false && strpos($author,';') === false)
			$asep = ',';
		$a = explode($asep, $author);
		if (sizeof($a) == 2)
			$author = "{$a[0]} and {$a[1]}";
		else if (sizeof($a) > 2)
			$author = "{$a[0]} et.al.";

		
		if (!empty($publisher_place))
			$publisher_place = ", $publisher_place";
		if (!empty($publisher))
			$publisher = ", $publisher";
		if (!empty($initials))
			$initials = " $initials";
		if (!empty($initials) || !empty($date))
			$initials = ",$initials";
		if (!empty($volume))
			$volume = ", $volume";
		if (!empty($pages))
			$pages = ", $pages";
		if (!empty($issue))
			$issue = ", $issue";
		if (!empty($award))
			$award = ", $award";
		if (!empty($url))
			$url = ", <$url>";
		
		switch($media){
			case 'book': 
				if(!empty($author)) 
					$_out = "$author$initials$date, <em>$title</em>$volume$publisher$publisher_place."; 
				else 
					$_out = "<em>$title</em>$date$volume$publisher$publisher_place.";
				break;
			case 'booksection':
				if($author!='') 
					$_out = "$author$initials$date, '$title', in $book_author (ed.), <em>$book_title</em>$volume$publisher$publisher_place$pages.";
				else 
					$_out = "'$title'$date, in $book_author (ed.), <em>$book_title</em>$volume$publisher$publisher_place$pages."; break;
			case 'conference':
				if (!empty($dateaccess))
					$dateaccess = ", $dateaccess";
				$_out = "$author$initials$date, '$title', <em>$title_periodical</em>$publisher$publisher_place$pages$dateaccess$url.";
				$url = '';
				break;
			case 'journal': 
				if (!empty($dateaccess))
					$dateaccess = ' '. _x('viewed','citeurl','academicpress') ." $dateaccess";
				$_out = "$author$initials$date, '$title', <em>$title_periodical</em>$volume$issue$pages$dateaccess$url.";
				$url = '';
				break;
			case 'thesis': 
				$_out = "$author$initials$date, '$title'$award$publisher$publisher_place.";
				break;
			case 'report': 
			case 'standard':
				if (!empty($dateaccess))
					$dateaccess = ' '. _x('viewed','citeurl','academicpress') ." $dateaccess";
				$_out = "$author$initials$date, <em>$title</em>$issue$publisher$publisher_place$dateaccess$url.";
				$url = '';
				break;
			case 'magazine': 
			case 'newspaper':
				if (!empty($year))
					$year = " $year";
				if (!empty($day))
					$day = " $day";
				if (!empty($month))
					$month = " $month";
				if (!empty($title_periodical))
					$title_periodical = ", <em>$title_periodical</em>";
				if(!empty($author))
					$_out = "$author$initials$year, '$title'$title_periodical$day$month$pages."; 
				else 
					$_out = "'$title'$year$title_periodical$day$month$pages."; 
				break;	
			case 'website':
			case 'web':
				if (!empty($year))
					$year = " $year";
				if (!empty($day))
					$day = " $day";
				if (!empty($month))
					$month = " $month";
				if (!empty($special_entry))
					$special_entry = ", $special_entry";
				if (!empty($dateaccess))
					$dateaccess = ' '. _x('viewed','citeurl','academicpress') ." $dateaccess";
				if(!empty($author)) {
					if (!empty($year))
						$year = ",$year"; 
					$_out = "$author$year, <em>$title</em>$special_entry$day$month$publisher$dateaccess$url.";
				} else 
					$_out = "<em>$title</em> $year$special_entry$day$month$publisher$dateaccess$url.";
				$url = '';
			case 'patent': 
				$_out = "$author$initials$date, <em>$title</em>$publisher_place. "._x('Patent','cite','netblog')." $patent_number.";
				break;
			case 'map':
				$_out = "$author$date, <em>$title</em>$issue$publisher$publisher_place.";
				break;
	        default:		
	        	if (!empty($title))
					$title = ", <em>$title</em>";
	        	if(!empty($author)) {
	        		$_out = "$author$date$title$publisher_place$publisher.";
	        	} else
	        		$_out = "<em>{$args['title']}</em>$date$publisher_place$publisher.";
		}
		if (!empty($_out)) {
			if (!empty($doi))
				$_out .= " doi: $doi.";
			if (!empty($url)) {
				$date = !empty($year_access) ? (!empty($month_access) ? (!empty($day_access) ? "$month_access $day_access, $year_access" : "$month_access $year_access") : $year_access) : '';
				if (!empty($publisher))
					$publisher = "{$args['publisher']}: ";
				$_out .= " "._x('Retrieved','url','academicpress')." $date "._x('from','url','academicpress')." $publisher$url.";
			}
		}
		
		return $_out;
	}
	
	public function getTable($collection, $args) {
		if (!isset($args['level']) || empty($args['level']))
			$args['level'] = '3';
		$t = "<h{$args['level']}>". $args['title'] ."</h{$args['level']}>";
		$t .= '<ol>';
		$collection->sortBy(array('year'=>SORT_DESC, 'author'=>SORT_ASC));
		foreach ($collection as $c)
			$t .= '<li>'. $this->getCitationFormat($c) .'</li>';
		$t .= '</ol>';
		return $t;
	}
}
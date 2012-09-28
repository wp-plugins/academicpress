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
		$date = " ($date)";
		
		$asep = ';';
		if (str_word_count($author) > 3 && strpos($author,',') !== false && strpos($author,';') === false)
			$asep = ',';
		$a = explode($asep, $author);
		if (sizeof($a) == 2)
			$author = "{$a[0]} and {$a[1]}";
		else if (sizeof($a) > 2)
			$author = "{$a[0]} et.al.";
		
		if (!empty($title))
			$title = ", <em>$title</em>";
		if (!empty($title_periodical))
			$title_periodical = ", (<em>$title_periodical</em>)";
		if (!empty($issue))
			$issue = "($issue)";
		
		if (!empty($publisher_place))
			$publisher_place = ". $publisher_place";
		if (!empty($publisher) && !empty($publisher_place))
			$publisher = ": $publisher";
		else if (!empty($publisher))
			$publisher = ". $publisher";
		
		switch($media){
			case 'book': 
				if($author!='') 
					$_out = "$author, $initials $year, <i>$title</i>, $volume, $publisher, $publisher_place."; 
				else 
					$_out = "<i>$title</i> $year, $volume, $publisher, $publisher_place.";
				break;
			case 'booksection':
				if($author!='') 
					$_out = "$author, $initials $year, '$title', in $book_author (ed.), <i>$book_title</i>, $volume, $publisher, $publisher_place, $pages.";
				else 
					$_out = "'$title' $year, in $book_author (ed.), <i>$book_title</i>, $volume, $publisher, $publisher_place, $pages."; break;
			case 'conference':
				$_out = "$author, $initials $year, '$title', <i>$title_periodical</i>, $publisher, $publisher_place, $pages". (strlen($url)>0 ? ", $month_access $day_access, $year_access, <$url>." : "."); break;				
			case 'journal': 
				$_out = "$author, $initials $year, '$title', <i>$title_periodical</i>, $volume, $issue, $pages". (strlen($url)>0 ? ", "._x('viewed','citeurl','netblog')." $month_access $day_access, $year_access, <$url>." : ".");;
				break;
			case 'thesis': 
				$_out = "$author, $initials $year, '$title', $award, $publisher, $publisher_place.";
				break;
			case 'report': 
			case 'standard':
				$_out = "$author, $initials $year, <i>$title</i>, ". (strlen($issue)>0 ? "$issue, ": "") ."$publisher, $publisher_place". (strlen($url)>0 ? ", "._x('viewed','citeurl','netblog')." $month_access $day_access, $year_access, <$url>." : ".");;
				break;
			case 'magazine': 
			case 'newspaper':
				if($author!='') 
					$_out = "$author, $initials $year, '$title', <i>$title_periodical</i> $day $month, $pages."; 
				else 
					$_out = "'$title' $year, <i>$title_periodical</i> $day $month, $pages."; break;	
			case 'website':
			case 'web':
				if($author!='') 
					$_out = "$author, $year, <i>$title</i>, ". (strlen($special_entry)>0 ? "$special_entry, ":"") ."$day $month, ".(strlen($publisher)>0 ? "$publisher, ":"").""._x('viewed','citeurl','netblog')." $day_access $month_access $year_access, <$url>.";
				else 
					$_out = "<i>$title</i> $year, ". (strlen($special_entry)>0 ? "$special_entry, ":"") ."$day $month, ".(strlen($publisher)>0 ? "$publisher, ":"").""._x('viewed','citeurl','netblog')." $day_access $month_access $year_access, <$url>."; break;
			case 'patent': 
				$_out = "$author, $initials $year, <i>$title</i>, $publisher_place "._x('Patent','cite','netblog')." $patent_number";
				break;
			case 'map':
				$_out = "$author $year, <i>$title</i>, ". (strlen($issue)>0 ? "$issue, ": "") ."$publisher, $publisher_place.";
				break;
	        default:
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
				$_out .= " "._x('Retrieved','url','academicpress')." $date "._x('from','url','academicpress')." ".(strlen($publisher)>0 ? "$publisher: " : '')."$url.";
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
<?php

class Acp_Bib_Style_Chicago implements Acp_Bib_IStyle {
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
		$date = ", $date";
		
		$asep = ';';
		if (str_word_count($author) > 3 && strpos($author,',') !== false && strpos($author,';') === false)
			$asep = ',';
		$a = explode($asep, $author);
		if (sizeof($a) == 2)
			$author = "{$a[0]} and {$a[1]}";
		else if (sizeof($a) > 2)
			$author = "{$a[0]} et.al.";
		
		//if (!empty($title))
		//	$title = ", <em>$title</em>";
		if (!empty($title_periodical))
			$title_periodical = ", <em>$title_periodical</em>";
		if (!empty($issue))
			$issue = "($issue)";
		
		if (!empty($publisher_place))
			$publisher_place = ". $publisher_place";
		if (!empty($publisher) && !empty($publisher_place))
			$publisher = ": $publisher";
		else if (!empty($publisher))
			$publisher = ". $publisher";
		if (!empty($day))
			$day = " $day";
		
		switch($media){
			case 'book': 
			case 'paper':
				if(!empty($author))
					$_out = "$author$date. <em>$title</em>$publisher_place$publisher."; 
				else 
					$_out = "<em>$title</em>$date$publisher_place$publisher.";
				break;
			case 'journal': 
				if (!empty($month))
					$month = ", ($month)";
				if (!empty($pages))
					$pages = ": $pages";
				if (!empty($issue))
					$issue = "($issue)";
				if (!empty($volume))
					$volume = ", <em>$volume</em>";
				if(!empty($author))
					$_out = "$author$date. $title$title_periodical$volume$issue$month$pages.";
				else 
					$_out = "$title$date$title_periodical$volume$issue$month$pages."; 
				break;
			case 'magazine': 
			case 'newspaper':
				if (!empty($month))
					$month = " $month";
				if (!empty($year))
					$year = " $year";
				if (!empty($special_entry))
					$special_entry = ", $special_entry";
				if(!empty($author))
					$_out = "$author$date. $title$title_periodical$month$day.";
				else if($type=='newspaper') 
					$_out = "<em>{$args['title_periodical']}</em>$year. \"$title\"$month$day$special_entry."; 
				else 
					$_out = "$title$year. <em>{$args['title_periodical']}</em>$month$day."; 
				break;							 
			case 'encyclopedia':
				if (!empty($year))
					$year = ", $year";
				if(!empty($author))
					$_out = "$author, \"$title\", in <em>{$args['title_periodical']}</em>$year.";
				else 
					$_out = "\"$title\", in <em>{$args['title_periodical']}</em>$year."; 
				break;		
			case 'booksection':
				if (!empty($book_title))
					$book_title = ' '. vsprintf( _x('In %s','citebooksection','academicpress'), "<em>$book_title</em>");
				if (!empty($book_author))
					$book_author = ", $book_author";
				if (!empty($pages))
					$pages = ", $pages";
				if(!empty($author))
					$_out = "$author$date. $title$book_title$book_author$pages$publisher_place$publisher.";
				else 
					$_out = "$title$date.$book_title$book_author$pages$publisher_place$publisher."; 
				break;
			case 'eric':
				if (!empty($doi))
					$doi = ", $doi";
				if (!empty($year))
					$year = ". $year. ";
				if(!empty($author))
					$_out = "$author$year<em>$title</em>$publisher_place$publisher, "._x('text-fiche','citeeric','academicpress')."$doi.";
				else 
					$_out = "<em>$title</em>$year$publisher_place$publisher, "._x('text-fiche','citeeric','academicpress')."$doi."; 
				break;
			case 'website': case 'web':
				if (!empty($year))
					$year = ". $year";
				$date = !empty($year_access) ? (!empty($month_access) ? (!empty($day_access) ? "$month_access $day_access, $year_access" : "$month_access $year_access") : $year_access) : '';
				if (!empty($date))
					$date = ' ('. _x('accessed','url','academicpress') ." date)";
				if(!empty($author))
					$_out = "$author$year. <em>$title</em>$publisher. $url$date.";
				else 
					$_out = "<em>$title</em>$year$publisher. $url$date.";
				$url = ''; 
				break;
	        default:
	        	if (!empty($title))
					$title = ". <em>$title</em>";
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
<?php

class Acp_Bib_Style_Turabian implements Acp_Bib_IStyle {
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
				(strlen($day)>0 ? "$year, $month $day" : "$year, $month") : $year) : '';
		if (!empty($date))
			$date = ", $date";
		
		$asep = ';';
		if (str_word_count($author) > 3 && strpos($author,',') !== false && strpos($author,';') === false)
			$asep = ',';
		$a = explode($asep, $author);
		if (sizeof($a) == 2)
			$author = "{$a[0]} and {$a[1]}";
		else if (sizeof($a) > 2)
			$author = "{$a[0]} et.al.";
		
		
		if (!empty($issue))
			$issue = "($issue)";
		
		if (!empty($publisher_place))
			$publisher_place = ". $publisher_place";
		if (!empty($publisher) && !empty($publisher_place))
			$publisher = ": $publisher";
		else if (!empty($publisher))
			$publisher = ". $publisher";
		
		if (!empty($year)) {
			if (!empty($author))
				$year = ". $year";
			else
				$year = " $year";
		}
		
		switch($media){
			case 'book': 
				if (!empty($author))
					$_out = "$author$year. <em>$title</em>$publisher_place$publisher."; 
				else 
					$_out = "<em>$title</em>$year$publisher_place$publisher.";
				break;
			case 'journal': 
				if (!empty($month))
					$month = " ($month)";
				if (!empty($volume))
					$volume = " $volume";
				if (!empty($pages))
					$pages = ": $pages";
				if (!empty($title_periodical))
					$title_periodical = ". <em>$title_periodical</em>";
				if (!empty($author))
					$_out = "$author$year. $title$title_periodical$volume$month$pages.";
				else 
					$_out = "$title$year$title_periodical$volume$month$pages."; 
				break;
			case 'magazine': 
			case 'newspaper':
				if (!empty($month))
					$month = ", $month";
				if (!empty($day))
					$day = ", $day";
				if (!empty($pages))
					$pages = ", $pages";
				if (!empty($title_periodical))
					$title_periodical = ". <em>$title_periodical</em>";
				if (!empty($author))
					$_out = "$author$year. $title$title_periodical$day$month$pages.";
				else 
					$_out = "$title$year$title_periodical$day$month$pages."; 
				break;							 
			case 'encyclopedia':
				if (!empty($publisher))
					$publisher = ", ". vsprintf( _x('in %s','citepublisher','academicpress'), array("<em>{$args['publisher']}</em>"));
				if (!empty($author))
					$_out = "$author, \"$title\"$publisher$date.";
				else 
					$_out = "\"$title\"$publisher$date."; 
				break;		
			case 'booksection':
				if (!empty($book_author))
					$book_author = ", $book_author";
				if (!empty($pages))
					$pages = ", $pages";
				if (!empty($author))
					$_out = "$author$year. $title. In <em>$book_title</em>$book_author$pages$publisher_place$publisher.";
				else 
					$_out = "$title$year. In <em>$book_title</em>$book_author$pages$publisher_place$publisher."; 
				break;
			case 'eric':
				if (!empty($author))
					$_out = "$author$year. <em>$title</em>$publisher_place$publisher. ERIC, $doi.";
				else 
					$_out = "<em>$title</em>$year$publisher_place$publisher. ERIC, $doi."; 
				$doi = '';
				break;
			case 'website': 
			case 'web':
				if (!empty($author))
					$_out = "$author$year. <em>$title</em>$publisher_place$publisher.";
				else 
					$_out = "<em>$title</em>$year$publisher_place$publisher.";
	        default:
	        	if (!empty($title))
	        		$title = ". $title";
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
				if (!empty($date))
					$date = " ". _x('accessed','url','academicpress') ." $date";
				$_out .= " ". __('On-line. Available from Internet','academicpress') ." $url$date";
			}
		}
		
		return $_out;
	}
	
	public function getTable($collection, $args) {
		if (!isset($args['level']) || empty($args['level']))
			$args['level'] = '3';
		$t = "<h{$args['level']}>". $args['title'] ."</h{$args['level']}>";
		$t .= '<ul>';
		$collection->sortBy(array('author'=>SORT_ASC, 'title'=>SORT_ASC, 'year'=>SORT_DESC));
		foreach ($collection as $c)
			$t .= '<li style="text-indent:-30px; padding: 3px 0 3px 30px">'. $this->getCitationFormat($c) .'</li>';
		$t .= '</ul>';
		return $t;
	}
}
<?php

class Acp_Bib_Style_Acp implements Acp_Bib_IStyle {
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
		
		if (empty($title) && !empty($url))
			$title = "<a href=\"$url\">$url</a>";
		
		if (!empty($title)) {
			$t = "<em>\"$title\"</em>";
			if (!empty($author) && !empty($year))
				return "$t ($author, $year)";
			else if (!empty($author))
				return "$t ($author)";
			else if (!empty($year))
				return "$t ($year)";
			else
				return "$t";
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
		if (!empty($doi))
			$doi = "<a href=\"http://dx.doi.org/$doi\" target=\"blank\">$doi</a>";
		switch($media){
			case 'journal':
			case 'periodical':
			case 'magazine':
				if (!empty($pages))
					$pages = ", $pages";
				if (!empty($volume))
					$volume = "<em>$volume</em>";
				$_out = "$author$date$title$title_periodical, $volume$issue$pages."; break;
			case 'newspaper':
				if (!empty($pages))
					$pages = ", $pages";
				$_out = "$author$date$title$title_periodical$pages.";
				break;
			case 'book': 
			case 'paper':
		        if(!empty($author)) 
		            $_out = "$author$date$title$publisher_place$publisher."; 
		        else 
	            	$_out = "<em>{$args['title']}</em>$date$publisher_place$publisher.";
		        break;
	        case 'booksection': 
	        	$_out = "$author$date$title. ". vsprintf( _x('In %s','citebooksection','academicpress'), $book_author) .", <em>$book_title</em> ($pages)$publisher_place$publisher."; break;
	        case 'encyclopedia': 
	        	$_out = "$author$date$title. ". vsprintf( _x('In %s','citebooksection','academicpress'), $book_author) .", <em>$book_title</em> ($volume, $pages)$publisher_place$publisher."; break;
	        case 'eric': 
	        	$_out = "$author$date$title. ".__('Retrieved from ERIC database.','academicpress')." ($doi)"; 
	        	$doi=''; 
	        	break;
		    case 'website': 
		    case 'web':
			case 'post':
		        if(!empty($author)) 
		            $_out = "<em>{$args['title']}</em>, $author$date.";
		        elseif ( $author != $publisher && !empty($publisher) ) 
		            $_out = "$author$date$title$publisher.";
		        else  
		           $_out = "$author$date$title.";
		        break;
	        case 'wiki': 
	        	$_out = "<em>{$args['title']}</em>$date. "._x('In <em>Wikipedia</em>','cite','academicpress')."."; 
	        	break;
	        case 'blog': 
	        	$_out = "$author$date$title."; 
	        	break;
	        case 'blog': 
	        	$_out = "$author$date$title. ["._x('Weblog message','academicpress')."]."; 
	        	break;
	        case 'video':
	        	 $_out = "$author$date$title. ["._x('Video file','academicpress')."]."; 
	        	 break;
	        case 'powerpoint': 
	        case 'ppt':  
	        	$_out = "$author$date$title. ["._('PowerPoint slides','academicpress')."]."; 
	        	break;
	        default:
	        	if(!empty($author)) {
	        		$_out = "$author$date$title$publisher_place$publisher.";
	        	} else
	        		$_out = "<em>{$args['title']}</em>$date$publisher_place$publisher.";
		}
		if (!empty($_out)) {
			if (!empty($url)) {
				$date = !empty($year_access) ? (!empty($month_access) ? (!empty($day_access) ? "$month_access $day_access, $year_access" : "$month_access $year_access") : $year_access) : '';
				if (!empty($publisher))
					$publisher = " {$args['publisher']}:";
				$_out .= " "._x('Retrieved','url','academicpress')." $date "._x('from','url','academicpress')."$publisher <a href=\"$url\">$url</a>.";
			}
			if (!empty($doi))
				$_out .= " DOI: <a href=\"$doi\">$doi</a>.";
		}
		
		return $_out;
	}
	
	public function getTable($collection, $args) {
		if (!isset($args['level']) || empty($args['level']))
			$args['level'] = '2';
		$t = "<h{$args['level']}>". $args['title'] ."</h{$args['level']}>";
		$t .= '<ol>';
		$collection->sortBy(array('year'=>SORT_DESC, 'author'=>SORT_ASC));
		foreach ($collection as $c)
			$t .= '<li>'. $this->getCitationFormat($c) .'</li>';
		$t .= '</ol>';
		return $t;
	}
}
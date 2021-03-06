<?php

class Acp_Bib_Style_Apa implements Acp_Bib_IStyle {
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
			case 'journal':
			case 'periodical':
			case 'magazine':
				if (!empty($pages))
					$pages = ", $pages";
				if (!empty($volume))
					$volume = "<i>$volume</i>";
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
	            	$_out = "$title$date$publisher_place$publisher.";
		        break;
	        case 'booksection': 
	        	$_out = "$author$date$title ". vsprintf( _x('In %s','citebooksection','academicpress'), $book_author) .", <i>$book_title</i> ($pages)$publisher_place$publisher."; break;
	        case 'encyclopedia': 
	        	$_out = "$author$date$title ". vsprintf( _x('In %s','citebooksection','academicpress'), $book_author) .", <i>$book_title</i> ($volume, $pages)$publisher_place$publisher."; break;
	        case 'eric': 
	        	$_out = "$author$date$title ".__('Retrieved from ERIC databse.','academicpress')." ($doi)"; $doi=''; break;
		    case 'website': 
		    case 'web':
			case 'post':
		        if(!empty($author)) 
		            $_out = "<em>{$args['title']}</em>$date.";
		        elseif ( $author != $publisher && !empty($publisher) ) 
		            $_out = "$author$date$title$publisher.";
		        else  
		           $_out = "$author$date$title";
		        break;
	        case 'wiki': 
	        	$_out = "$title (n.d.). "._x('In <i>Wikipedia</i>','cite','academicpress')."."; break;
	        case 'blog': 
	        	$_out = "$author$date$title."; break;
	        case 'blog': 
	        	$_out = "$author$date$title ["._x('Weblog message','academicpress')."]."; break;
	        case 'video':
	        	 $_out = "$author$date$title ["._x('Video file','academicpress')."]."; break;
	        case 'powerpoint': 
	        case 'ppt':  
	        	$_out = "$author$date$title ["._('PowerPoint slides','academicpress')."]."; break;
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
		$t .= '<ul>';
		$collection->sortBy(array('author'=>SORT_ASC, 'title'=>SORT_ASC, 'year'=>SORT_DESC));
		foreach ($collection as $c)
			$t .= '<li style="text-indent:-30px; padding: 3px 0 3px 30px">'. $this->getCitationFormat($c) .'</li>';
		$t .= '</ul>';
		return $t;
	}
}
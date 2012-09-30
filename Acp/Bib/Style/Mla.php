<?php

class Acp_Bib_Style_Mla implements Acp_Bib_IStyle {
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
		$dateaccess = !empty($year_access) ? (!empty($month_access) ? (!empty($day_access) ? "$month_access $day_access, $year_access" : "$month_access $year_access") : $year_access) : '';
		if (!empty($dateaccess))
			$dateaccess = ". $dateaccess";
		
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
		if (!empty($title_periodical))
			$title_periodical = " <em>$title_periodical</em>";
		
		switch($media){
			case 'journal':
				if (!empty($pages))
					$pages = ": $pages";
				if (!empty($volume))
					$volume = " $volume";
				if (!empty($year))
					$year = " $year";
				if ($source == 'web') 
					$_out = "$author \"$title\"$title_periodical$volume$year$pages$publisher. ".__('Web','netblog')."$dateaccess.";
				else if ($source == 'db') 
					$_out = "$author \"$title\"$title_periodical$volume$year$pages. ".__('Web','netblog')."$dateaccess.";
				else 
					$_out = "$author \"$title\"$title_periodical$volume$year$pages. ".__('Print','netblog').".";							
				break;
			case 'newspaper':
				if (!empty($pages))
					$pages = ": $pages";
				if ($source == 'web') 
					$_out = "$author \"$title\"$title_periodical$publisher$date. ".__('Web','netblog')."$dateaccess.";
				else if ($source == 'movie') /* translators: MLA citation style for newspaper, citing a movie (org. english: Rev. of MOVIE, dir. DIRECTOR) see http://www.cwpost.liunet.edu/cwis/cwp/library/workshop/citmla.htm */ 
					$_out = "$author \"$title\" ".vsprintf( __('Rev. of %s, dir. %s','netblog'), $movie, $director).".$title_periodical $date, sec$pages. ".__('Print','netblog').".";
				else 
					$_out = "$author \"$title\"$title_periodical$publisher$date$pages. ".__('Print','netblog').".";
				break;
			case 'magazine':
				if (!empty($special_entry))
					$special_entry = " $special_entry";
				if (!empty($pages))
					$pages = ": $pages";
				if ($source == 'web') 
					$_out = "$author \"$title\"$special_entry$title_periodical $date$pages$publisher. ".__('Web','netblog')."$dateaccess.";
				else if ($source == 'db') 
					$_out = "$author \"$title\"$special_entry$title_periodical $date$pages. ".__('Web','netblog')."$dateaccess.";							
				else 
					$_out = "$author \"$title\"$special_entry$title_periodical $date$pages. ".__('Print','netblog').".";
				break;
			case 'book': 
				if (!empty($author))
					$author = "$author ";
				if ($source == 'web' || $source == 'db') 
					$_out = "$author<em>$title</em>$publisher_place$publisher$date. ".__('Web','netblog')."$dateaccess.";														
				else 
					$_out = "$author<em>$title</em>$publisher_place$publisher$date. ".__('Print','netblog').".";
				break;
			case 'booksection':
				if (!empty($book_title))
					$book_title = " <em>$book_title</em>";
				if (!empty($pages))
					$pages = ". $pages";
				$_out = "$author \"$title\"$book_title. $book_author$publisher_place$publisher$date$pages. ".__('Print','netblog').".";
				break;
			case 'encyclopedia':
				if (!empty($book_title))
					$book_title = " <em>$book_title</em>";
				if (!empty($book_author))
					$book_author = ". $book_author";
				if (!empty($volume))
					$volume = ". $volume";
				if (!empty($year))
					$year = " $year";
				if (!empty($special_entry) || !empty($year))
					$special_entry = ". $special_entry";
				if( $source == 'web' || $source == 'db' ) 
					$_out = "$author \"$title\"$book_title$book_author$volume$publisher_place$publisher$date. ".__('Web','netblog')."$dateaccess.";														
				else 
					$_out = "$author \"$title\"$book_title$special_entry$year. ".__('Print','netblog').".";
				break;
			case 'gale': /* translators: MLA citation style for GALE - as Repeat in book_title. (org. engl.: Rpt in book_title) */
				if (!empty($pages))
					$pages = ": $pages";
				if (!empty($volume))
					$volume = ". $volume";
				if (!empty($year_organisation))
					$year_organisation = ", $year_organisation";
				if (!empty($pages_organisation))
					$pages_organisation = ". $pages_organisation";
				$_out = "$author \"$title\"$title_periodical$date$pages. ".vsprintf( __('Rpt in %s','netblog'), "<i>$book_title</i>")
					.". Ed. $book_author$volume$publisher_place$publisher$year_organisation$pages_organisation. Literature Criticism Online. ".__('Web','netblog')."$dateaccess.";
				break;
			case 'website': case 'web':
				if (!empty($url))
					$url = " ($url)";
				$_out = "$author \"$title\"$title_periodical$publisher$date. ".__('Web','netblog')."$dateaccess. $url";
				$url = '';
				break;
			case 'blog':		
				if (!empty($url))
					$url = " ($url)";				
				$_out = "$author \"$title\" $special_entry$title_periodical$publisher$date. ".__('Web','netblog')."$dateaccess. $url";
				$url = '';
				break;
			case 'wiki':	
				if (!empty($url))
					$url = " ($url)";						
				$_out = "\"$title\". <em>".__('Wikipedia: The Free Encyclopedia','netblog').".</em> ".
					__('Wikimedia Foundation','netblog').", n.d. ".__('Web','netblog').". ".vsprintf( _x('From %s','url','netblog'),'url')."$date. $url";
				$url = '';
				break;
			case 'video':
				$_out = "$author dir. \"$title\"$title_periodical$publisher$date. ".__('Web','netblog').". $url$dateaccess.";
				$url = '';
				break;
			case 'powerpoint': 
			case 'ppt':
				$_out = "$author \"$title\"$publisher$date. <em>".__('Microsoft PowerPoint','netblog')."</i> "._x('file','citedatafile','netblog').". $url$dateaccess.";
				$url = '';
				break;
			case 'eric':
				if (!empty($doi))
					$doi = " ($doi)";
				$_out = "$author \"$title\"$publisher_place$publisher$date. <i>ERIC</i>. ".__('Web','netblog').". $url$dateaccess$doi.";
				$url = '';
				$doi = '';
	        default:
	        	if (!empty($title))
	        		$title = ", \"$title\"";
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
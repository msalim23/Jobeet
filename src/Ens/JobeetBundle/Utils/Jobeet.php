<?php

namespace Ens\JobeetBundle\Utils;

class Jobeet {
	static public function slugify($text) {
		mb_internal_encoding("UTF-8");
		// replace non letter or digits by -
		$text = preg_replace ( '#[^\\pL\d]+#u', '-', $text );
		
		// trim
		$text = trim ( $text, '-' );
		
		// transliterate
		if (function_exists ( 'iconv' )) {
			$text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE',$text);
			
		}
		
		// lowercase
		$text = strtolower ( $text );
		
		// remove unwanted characters
		$text = preg_replace ( '#[^-\w]+#', '', $text );
		
		if (empty ( $text )) {
			return 'n-a';
		}
		return $text;
	}
}
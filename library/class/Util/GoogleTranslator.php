<?php
/**
 * GOOGLE·­ÒëAPI
 * $Id$
 * $str = Util_GoogleTranslator::translate($text);
 * @author Abu
 *
 */
class Util_GoogleTranslator {
	const GOOGLE_API_URL = 'http://translate.google.com/translate_t';
	
	/**
	 * ´¦ÀíÊý¾Ý²¢·µ»Ø
	 * 
	 * @param string $text
	 * @param string $from
	 * @param string $to
	 */
	public static function translate($text, $from = 'auto', $to = 'zh-CN') {
		$gphtml = self::postPage ( self::GOOGLE_API_URL, $text, $from, $to );
		//print_r($gphtml); exit;
		if ($gphtml) {
			preg_match_all ( '/<span\s+title\="[^>]+>([^<]+)<\/span>/i', $gphtml, $res );
			//print_r($res); exit;
			if (! empty ( $res [1] [0] ) && isset ( $res [1] [0] ))
				$out = $res [1] [0];
			else
				$out = $text;
		} else {
			$out = "";
		}
		return $out;
	}
	
	/**
	 * 
	 * ·¢ËÍÇëÇó·µ»ØÊý¾Ý
	 * @param string $url
	 * @param string $text
	 * @param string $from
	 * @param string $to
	 */
	public static function postPage($url, $text, $from, $to) {
		$str = "";
		if ($url != "" && $text != "") {
			$ch = curl_init ( $url );
			//Éè¶¨ÒÔÎÄ±¾·½Ê½·µ»ØÊý¾Ý
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			//ÔÊÐíGOOGLE½«ÇëÇóÖØ¶¨Ïò²¢´ÓÖØ¶¨ÏòÒ³Ãæ½ÓÊÕ·µ»ØÄÚÈÝ
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, 15 );
			//ÕâÀï×éÖ¯·¢ËÍ²ÎÊý£¬»ù±¾ÉÏ²»ÓÃ¸Ä£¬Èç¹ûÒ»¶¨Òª¸Ä£¬½¨Òé¶¨Òå±äÁ¿ÐÞ¸Ä
			$fields = array ('hl=zh-CN', 'langpair=' . $from . '|' . $to, 'ie=UTF-8', 'text=' . $text );
			$fields = implode ( '&', $fields );
			//·¢ËÍPOSTÇëÇó
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
			$str = curl_exec ( $ch );
			//print_r($html); exit;
			if (curl_errno ( $ch ))
				$str = "";
			curl_close ( $ch );
		}
		return $str;
	}
}
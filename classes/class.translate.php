<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
*/


/**
 * Translate
 * Class translates languages via google translate engine
 * 
 * @package Apxa
 * @author Charlie <charlie@chrl.ru>
 * @version 2010
 * @access public
 */
class Translate
{
    public function translate($s_text, $s_lang = 'en', $d_lang = 'ru'){
        
        if (empty($s_text)) return $this;
        
        $url = "http://translate.google.ru/translate_a/t?client=x&text=".urlencode($s_text)."&hl=ru&sl=".$s_lang."&tl=".$d_lang;
        $b = file_get_contents($url);
        $json = json_decode($b, true);
        if ($json['responseStatus'] != 200)return false;
        return $json['responseData']['translatedText'];
    }
 
    function __construct()
    {
        return $this;
    }

}
?>
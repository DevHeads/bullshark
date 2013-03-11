<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
*/


/**
 * Vesna
 * Class translates languages via google translate engine
 * 
 * @package Apxa
 * @author Charlie <charlie@chrl.ru>
 * @version 2010
 * @access public
 */
class Vesna
{
    public function get($choose = 'law')
    {
        $text = file_get_contents('http://vesna.yandex.ru/'.$choose.'.xml');
        $text = explode('<h1 style="color:black; margin-left:0;">',$text);
        $text = $text[1];
        $text = explode('</div>',$text);
        $text = $text[0];

        list($title,$text) = explode('</h1>',$text);
        
        $title = str_replace('Тема: «','',$title);
        $title = mb_substr($title,0,mb_strlen($title,'UTF-8')-1,'UTF-8');
        
        return array('title'=>$title,'text'=>trim($text));
    }
 
    function __construct()
    {
        return $this;
    }

}
?>
<?php

/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 * @TODO: some failproof scheme
*/


/**
 * app_static
 * Выводит статическую текстовую страницу из кеша/из базы
 * 
 * @package 02p   
 * @author Charlie <charlie@chrl.ru> 
 * @access public
 */
 
class app_static
{
    public $version='App_Static v.0.1';
    public $depends=array();
     
    public function init()
    {
        
    }
    public function run()
    {
        BS::Visual()->template['name'] = 'static.tpl';
        
        $url = BS::getURL();
        
        if (empty($url[0]))$url[0] = BS::getConfig('site.defaultroute');

        $static = BS::DAO('static')->getById($url[0].'_'.(isset($_SESSION['translation'])?$_SESSION['translation']:BS::getConfig('site.defaulttranslation')));
        if (!$static)
        {
            BS::Visual()->title = $url[0].' | '.BS::getConfig('site.name');
            BS::Visual()->text = 'No static &laquo;'.$url[0].'&raquo; defined';
            BS::Visual()->staticItem = $url[0];
            return true; 
        }
        
        BS::Visual()->text = nl2br($static['text']);
        BS::Visual()->title = $static['title'];
        BS::Visual()->staticItem = $url[0];
        
    }
}
?>
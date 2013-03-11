<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */


/**
 * app_404
 * Ошибка 404
 * @package BullShark 
 * @author Charlie <charlie@chrl.ru> 
 * @access public
 */
 
class app_404
{
    public $version='App_404 v.0.1';
    public $depends=array();
     
    public function init()
    {
            
    }
    public function run()
    {

        header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
        header('Connection: close');

        BS::Visual()->template['name']='error.tpl';
        BS::Visual()->text = 'Sorry, requested page is not found';
        if (!isset($_SERVER['HTTP_REFERER']))
        {
            $_SERVER['HTTP_REFERER'] = 'unknown location';
        }
        BS::DAO('actions')->insertItem(array('ip'=>ip2long($_SERVER['REMOTE_ADDR']),'action'=>'['.BS::getConfig('site.name').'] Missing page: '.$_SERVER['REQUEST_URI']. ' from '.$_SERVER['HTTP_REFERER']));
    }
}
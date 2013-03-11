<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
*/


/**
 * Acl
 * Class checks access to page and redirects if no access granted
 * 
 * @package Apxa
 * @author Charlie <charlie@chrl.ru>
 * @version 2010
 * @access public
 */
class Acl
{
     
    public function checkRegistered()
    {
        return isset($_SESSION['user']);
    }
    
    public function isRole($role) {
        return $role == $_SESSION['user']['role'];
    }
    function __construct()
    {

        if (isset(BS::$app->access))
        {
            $acl = BS::$app->access;
        } else return $this;

        
        
        if (in_array('registered',$acl)&&!$this->checkRegistered())
        {
            header('Location: /restrict/?backto='.implode('/',BS::getURL()));
            die();
        }
        
        
        if (in_array('role_2',$acl)&&!$this->isRole(2))
        {
            header('Location: /');
        }        
        return $this;
    }

}
?>
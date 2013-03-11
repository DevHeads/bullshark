<?php
/*
 Projekt: BullShark
 Author: Kirill S. Holodilin
 Date: 2009, May
*/

class app_main
{
    var $version='App_Main v.0.1';
    var $depends=array();
    public function init()
    {
        
    }
    public function run()
    {
		if (!isset($_SESSION['user']))
        {
            BS::Visual()->text = 1;    
        }
        else
        {
            BS::Visual()->text = 2;
        }
        

    }
}
?>
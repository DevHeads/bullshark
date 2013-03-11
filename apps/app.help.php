<?php

/**
 * @package 02p
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 * @TODO: some failproof scheme
*/


/**
 * app_help
 * ¬ывод помощи об использовании CLI-версии
 * 
 * @package 02p   
 * @author Charlie <charlie@chrl.ru> 
 * @access public
 */
 
class app_help
{
    public $version='App_Help v.0.1';
    public $depends=array();
     
    public function init()
    {
            
    }
    public function run()
    {
        echo "Usage help: cli.php <command>\n\nCommands:\n\nhelp - display this message\nsendmail - send mail from queue\n<appname> [params]- call <appname> with params\n";
    }
}
?>
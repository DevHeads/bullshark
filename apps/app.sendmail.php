<?php

/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */


/**
 * app_sendmail
 * CLI: Отправка почты из почтовой очереди
 * 
 * @package BullShark
 * @author Charlie <charlie@chrl.ru> 
 * @access public
 */
 
class app_sendmail
{
    public $version='App_Sendmail v.0.1';
    public $depends=array();
     
    public function init()
    {
            
    }
    public function run()
    {
        $url = BS::getURL();
        $limit = isset($url[1])&&is_numeric($url[1])?$url[1]:NULL;
        
        $mailbox = BS::Mqueue()->getAll($limit);
        
        echo 'Got messages: '.count($mailbox)."\n";
        
        foreach ($mailbox as $message)
        {
            if (BS::Mailer()->sendmail($message['to'],$message['subject'],$message['text']))
            {
                BS::DAO('mqueue')->deleteItem($message['id']);
            }
            else
            {
                BS::debug('Couldnt send message #'.$message['id'].' - deffered for next run');
            }
        }
    }
}
?>
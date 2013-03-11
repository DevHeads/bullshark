<?
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */

/**
 * Mqueue
 * Class realizes mail queue (inserting/sending)
 * 
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @access public
 */
class Mqueue
{
    function send($to,$subject,$html)
    {
        $message = array(
            'to'=>$to,
            'subject'=>$subject,
            'text'=>$html,
        );
        BS::DAO('mqueue')->insertItem($message);
    }
    
    function getAll($limit = null)
    {
        return BS::DAO('mqueue')->getWithLimit($limit);
    }
    
    function Mqueue()
    {
    	return $this;
    }
}
?>
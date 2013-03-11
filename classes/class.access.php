<?
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */

/**
 * Access
 * Class realizes Access Control: Bans, unbans, checks rights, etc
 * 
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @access public
 */
class Access
{
    function check($ip)
    {
        return BS::DAO('banned')->getByFields(array('ip'=>$ip));
    }
    
    function banByIp($ip,$time = 3600,$reason = 'No reason given')
    {
        $banned = array(
            'ip'=>$ip,
            'user'=>0,
            'time'=>time()+$time,
            'reason'=>$reason,
            'status'=>'banned'
        );
        return BS::DAO('banned')->insertItem($banned);
    }
    
    function banById($id,$time = 3600,$reason = false)
    {
        $banned = array(
            'time'=>time()+$time,
            'status'=>'banned'
        );
        
        if ($reason)$banned['reason']=$reason;
        
        return BS::DAO('banned')->updateItem($id, $banned);
    }
    
    
    function riseLevel($id)
    {
        return BS::DAO('banned')->increment($id,array('level'=>1));
    }

    function reportAttack($ip,$reason = 'No reason given')
    {
        if ($attacker = $this->check($ip))
        {
            list($attacker) = array_values($attacker);
            if ($attacker['level']>=2)
            {
                $this->banById($attacker['id'],3600);
            } else $this->riseLevel($attacker['id']);
            return true;
        }
        else
        {
            $banned = array(
                'ip'=>$ip,
                'user'=>0,
                'time'=>time(),
                'reason'=>$reason,
                'status'=>'attack',
                'level'=>1,
            );
            return BS::DAO('banned')->insertItem($banned);
        }
        
    }
    
    function Access()
    {
    	if($res = $this->check(ip2long($_SERVER['REMOTE_ADDR'])))
        {
            list($res) = array_values($res);
            if ($res['status']=='banned')
            {
                if ($res['time']<time()) //forgive user
                {
                    BS::DAO('banned')->deleteItem($res['id']);
                }
                else                 
                die('You are banned with reason: &laquo;'.$res['reason'].'&raquo; until '.date('d.m.Y H:i:s',$res['time']));
            }
        }
        return $this;
    }
}
?>
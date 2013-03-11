<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */
/**
 * Antihack - module, that checks for attack and bans user
 * 
 * @package BullShark   
 * @author Charlie <charlie@chrl.ru>
 * @version 2010, May
 * @access public
 */
class Antihack
{

    /**
     * Antihack::Antihack()
     * Constructor method, checks for hacks in GPC with reserved words
     * 
     * @return true
     */
    public function Antihack()
    {
    	$reservedWords = array('SELECT','UPDATE','DELETE','INSERT','DROP','TRUNCATE');
    	$this->check($_POST,$reservedWords);
    	$this->check($_GET,$reservedWords);
    	$this->check($_COOKIE,$reservedWords);
        $this->check(array('uri'=>$_SERVER['REQUEST_URI']),$reservedWords);
        return true;
    }

    /**
     * Antihack::check()
     * Check if array items include stop-word, reserved in words array
     * 
     * @return true
     */
    public function check($arr,$words)
    {
    	foreach( $arr as $key=>$value)
    	{
    		foreach ($words as $word)
    		{
    			if (is_string($key)&&is_string($value))
                if ((false !== strpos(strtoupper($key),$word))||(false !== strpos(strtoupper($value),$word)))
    			{
    				//detected attack, give 3 chanses to one IP before ban
                    
                    BS::Mqueue()->send('addicted2icq@gmail.com',date('j.n.Y, H:i:s').': Потенциальная атака на '.BS::getConfig('site.domain'),
                        'Потенциально опасная конструкция: <br />'.htmlspecialchars($key).' => '.htmlspecialchars($value).' совпала со стоп-словом &laquo;<b>'.htmlspecialchars($word).'</b>&raquo;.
                        <br />Запрос произведен с адреса: '.$_SERVER['REMOTE_ADDR'].'<hr />Автоматическая система предупреждения '.BS::version()
                    );
                    
                    BS::Access()->reportAttack(ip2long($_SERVER['REMOTE_ADDR']),'Attack: '.htmlspecialchars($key).' => '.htmlspecialchars($value));
    			}
    		}
    	}
        return true;
    }    
    
    
}




?>
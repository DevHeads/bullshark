<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */

class Database
{
    public function q($sql,$vars = array())
    {
        // $state->qCount=isset($state->qCount)?$state->qCount++:1;
        foreach ($vars as $key=>$item)
        {
            $vars[$key]=str_replace('?','::questionmark::',$item);
        }


        foreach ($vars as $item)
        {
     		if (false!==strpos($sql,'?'))
        	{
        		if (is_numeric($item))$substitute=(is_int($item))?(int)$item:(float)$item;
        		else $substitute="'".mysql_real_escape_string($item)."'";
    			$pos=strpos($sql,'?');
        		$sql=substr_replace($sql,$substitute,$pos,1);
        	}
        	
        }

        $sql=str_replace('::questionmark::','?',$sql);
        
	//echo $sql;flush();//die;
        BS::debug('Running SQL: '.$sql.' @ microtime'.microtime(true));
        if(!$res = mysql_query($sql)) 
    		BS::debug('SQL Error: '.mysql_error());
        BS::debug('Finished SQL @ microtime '. microtime(true));
        return $res;
    }

    public function database()
    {
        $db = BS::getConfig('datasource');
        if (!$db) $db = BS::getConfig('db');
        
        if (!$db) throw new Exception('No database section defined');
         
        @mysql_connect($db['host'],$db['user'],$db['pass']);
        if(!@mysql_select_db($db['db']))
        {
            BS::debug('Mysql error: '.mysql_error());
            /*
            if(BS::$url[0]!='500')
            {
            header('Location: /500/?msg='.urlencode('Невозможно установить соединение с базой данных'));
            die();
            }
            */
        }
        mysql_set_charset('UTF8');
        /**
         * @todo Get encoding from config
         */
        
        
        return $this;
    }

    function postExecute()
    {
        @mysql_close();
    }
}
?>
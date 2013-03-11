<?php
class map_actions extends map_everything {
	var $table  = 'actions';
	var $entity = array
	(
		'id'=>'id',
		'ip'=>'ip',
		'date'=>'date',
		'action'=>'action'
	);
    
    public function getWithLimit($limit=null,$fields=null)
    {
       	if ($fields == null) $fields = $this->entity;
        if (is_numeric($limit))
        {
    		$res=q("SELECT `".join('`, `',$fields)."`, UNIX_TIMESTAMP(date) FROM `".$this->table."` WHERE 1 ORDER BY `actions`.`date` DESC LIMIT ?",array($limit));
        } else $res=q("SELECT `".join('`, `',$fields)."`, UNIX_TIMESTAMP(date) FROM `".$this->table."` WHERE 1 ORDER BY `date` DESC",array());
 
        $result = array();
        
        while (false !== $row = mysql_fetch_assoc($res))
        {
            $result[$row['id']]=$row;
        }
        return $result;
    }	
}
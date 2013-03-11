<?php
class map_everything
{
	public $table = null;
	public $entity = null;
	
	public function getById($id,$fields = null)
	{
        if ($fields == null)
        {
            $fields = $this->entity;
        }            
        $entity = str_replace('map_','',get_class($this));
        return BS::Cache()->getEntityFromCache($entity,$id);
	}
    
	public function getEntityById($id)
	{
		$fields = $this->entity;
		$res=BS::Database()->q("SELECT `".join('`, `',$fields)."` FROM `".$this->table."` WHERE `id`= ? LIMIT 1",array($id));
		return mysql_fetch_assoc($res);
	}
    
	public function getCount($searchFields = array())
	{
        $searchString = array();
        foreach ($searchFields as $key=>$value)
        {
            if((substr($value,strlen($value)-1)=='%')||(substr($value,0,1)=='%'))
            {
                $searchString[].='`'.$key.'` LIKE ?';
            }else
            {
                $searchString[].='`'.$key.'` = ?';
            }
        }
        if(count($searchString))
        {
            $searchString = implode(' AND ',$searchString);
        }
        else $searchString = '1';
		$res=BS::Database()->q("SELECT count(*) FROM `".$this->table."` WHERE ".$searchString,$searchFields);
        if (!$res) return 0;
		return mysql_result($res,0);
	}

	public function getWithLimit($limit = null,$fields = null)
	{
		if ($fields == null) $fields = $this->entity;
        if (is_numeric($limit))
        {
    		$res=BS::Database()->q("SELECT `".join('`, `',$fields)."` FROM `".$this->table."` WHERE 1 LIMIT ?",array($limit));
        } else $res=BS::Database()->q("SELECT `".join('`, `',$fields)."` FROM `".$this->table."` WHERE 1",array());

        $result = array();
        
        if($res) while (false !== $row = mysql_fetch_assoc($res))
        {
            $result[$row['id']]=$row;
        }
        return $result;
	}
	public function getByFields(array $searchFields,$fields = null,$single = false,$order = array('id','DESC'),$limit = false)
    {
  		//TODO: Limit check
          
        if ($fields == null) $fields = $this->entity;
        
        $limit = $limit? (is_array($limit)?' LIMIT '.$limit[0].', '.$limit[1]:' LIMIT 0,'.$limit):'';
  
        $searchString = array();
        foreach ($searchFields as $key=>$value)
        {
            if((substr($value,strlen($value)-1)=='%')||(substr($value,0,1)=='%'))
            {
                $searchString[].='`'.$key.'` LIKE ?';
            }else
            {
                $searchString[].='`'.$key.'` = ?';
            }
        }
        if(count($searchString))
        {
            $searchString = implode(' AND ',$searchString);
        }
        else $searchString = '1';
        
        $res=BS::Database()->q("SELECT `".join('`, `',$fields)."` FROM `".$this->table."` WHERE ".$searchString . ' ORDER BY '.$order[0]. ' '.$order[1].$limit,$searchFields);
		
        $result = array();
        if (!$res)return false;
        while (false !== $row = mysql_fetch_assoc($res))
        {
            $result[$row['id']]=$row;
        }
        
        if ($single && (count($result)==1))
        {
            list($id) = array_keys($result);
            $result = $result[$id];
        }
        
        return $result;
    }
    
	
	public function insertItem($item) 
	{
		BS::Database()->q("INSERT INTO `".$this->table."` SET `".join('` = ?, `',array_keys($item))."` = ?",array_values($item));
		return mysql_insert_id();
	}
	
	public function updateItem($id, $newValues) 
	{
		$sql = "UPDATE `".$this->table."` SET `".join('` = ?, `',array_keys($newValues))."` = ? WHERE id = ?";
        $vars = array_values($newValues);
        array_push($vars,$id);
        BS::Database()->q($sql,$vars);
        BS::Cache()->clearEntityCache(str_replace('map_','',get_class($this)),$id);
		return mysql_affected_rows();
	}

	public function increment($id, $newValues) 
	{
		$prepareSql = array();
        foreach($newValues as $key=>$item)
        {
            $prepareSql[]='`'.$key.'` = `'.$key.'` + '.(int)$item;
        }
        
        $sql = "UPDATE `".$this->table."` SET ".join(', ',$prepareSql)." WHERE id = ?";
     
        
        BS::Database()->q($sql,array($id));
        BS::Cache()->clearEntityCache(str_replace('map_','',get_class($this)),$id);        
		return mysql_affected_rows();
	}

	public function deleteItem($id) 
	{
		$sql = "DELETE FROM `".$this->table."` WHERE id = ?";
        BS::Database()->q($sql,array($id));
        BS::Cache()->clearEntityCache(str_replace('map_','',get_class($this)),$id);        
		return mysql_affected_rows();
	}
}

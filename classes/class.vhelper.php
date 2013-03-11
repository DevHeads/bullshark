<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
*/



class vhelper
{
    public function iterate($headers,$values,$actions = array()){
        
        
        
        $result = '<table class="striped bordered" cellpadding="0" cellspacing="0" style="width: 100%">
				   <tr class="info">';
                 
        $x=0;  
        foreach ($headers as $key=>$value) {
            $result.='<td>'.$value.'</td>'; 
        }   
        
        if (!empty($actions)) {
            $result.='<td>Действия</td>';
        }
                        
   	    $result.='</tr>';
        $url = BS::getUrl();
        
        $row=0;
        foreach($values as $value) {
            
            $result.='<tr>';
            $x=0;
            foreach ($headers as $hId=>$hValue) {
                 $result.='<td>'.$value[$hId].'</td>';
            }
            
            if(!empty($actions))
            {
                    $acts = array();
                    foreach($actions as $id=>$action)
                    {
                        $acts[]='<a href="/admin/'.$url[1].'/'.$value['id'].'/'.$id.'/">'.$action.'</a>';
                    }
                
                $result.='<td>'.implode(',&nbsp;',$acts).'</td>';
            }
            
            $result.='</tr>';
        }
        
        
        
        $result.='</table>';
        
        
        return $result;
    }
 
    function __construct()
    {
        return $this;
    }

}
?>
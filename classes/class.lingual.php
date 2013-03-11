<?
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */
 

/**
 * Lingual class
 * 
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @version 2010
 * @access public
 */
class Lingual
{
    public function days($days) {
        
        $to_out='';
        
        if ($days == 0) {
            return '<font color="red">сегодня!</font>';
        }
        
        if ($days == 1) {
            return '<font color="red">завтра!</font>';
        }        
        
        if ($days>0)
        {
            $to_out=$days;
            if (($days%10==1)&&($days%100!=11))
            {$to_out.=' день';}
            else
            if (($days%10==2)&&($days%100!=12))
            {$to_out.=' дня';}
            else
            if (($days%10==3)&&($days%100!=13))
            {$to_out.=' дня';}
            else
            if (($days%10==4)&&($days%100!=14))
            {$to_out.=' дня';}
            else
            {$to_out.=' дней';}
        } else $to_out = '<font color="red" style="text-decoration: line-through;">просрочено</font>';
        return $to_out;
        
    }
    
    public function tasks($days) {
        
        $to_out='';
        
        if ($days>0)
        {
            $to_out=$days;
            if (($days%10==1)&&($days%100!=11))
            {$to_out.=' задача';}
            else
            if (($days%10==2)&&($days%100!=12))
            {$to_out.=' задачи';}
            else
            if (($days%10==3)&&($days%100!=13))
            {$to_out.=' задачи';}
            else
            if (($days%10==4)&&($days%100!=14))
            {$to_out.=' задачи';}
            else
            {$to_out.=' задач';}
        } else $to_out = 'нет задач';
        return $to_out;
        
    }
    
    public function files($days) {
        
        $to_out='';
        
        if ($days>0)
        {
            $to_out=$days;
            if (($days%10==1)&&($days%100!=11))
            {$to_out.=' файл';}
            else
            if (($days%10==2)&&($days%100!=12))
            {$to_out.=' файла';}
            else
            if (($days%10==3)&&($days%100!=13))
            {$to_out.=' файла';}
            else
            if (($days%10==4)&&($days%100!=14))
            {$to_out.=' файла';}
            else
            {$to_out.=' файлов';}
        } else $to_out = '';
        return $to_out;
        
    }            
    
    public function daysAgo($date)
    {
        $time = is_numeric($date)?$date:strtotime($date);
        
        $diff=time()-$time;
        $days=($diff-($diff%86400))/86400;
        $diff=$diff-$days*86400;
        $hours=($diff-($diff%3600))/3600;
        $diff=$diff-$hours*3600;
        $mins=($diff-($diff%60))/60;
        $secs=$diff-$mins*60;
        $to_out='';
        if ($days>0)
        {
        $to_out=$days;
        if (($days%10==1)&&($days%100!=11))
        {$to_out.=' день ';}
        else
        if (($days%10==2)&&($days%100!=12))
        {$to_out.=' дня ';}
        else
        if (($days%10==3)&&($days%100!=13))
        {$to_out.=' дня ';}
        else
        if (($days%10==4)&&($days%100!=14))
        {$to_out.=' дня ';}
        else
        {$to_out.=' дней ';}
        }
        if ($hours>0)
        {
        $to_out.=$hours;
        if (($hours==1)||($hours==21))
        {$to_out.=' час ';}
        else
        if (($hours==2)||($hours==22))
        {$to_out.=' часа ';}
        else
        if (($hours==3)||($hours==23))
        {$to_out.=' часа ';}
        else
        if (($hours==4)||($hours==24))
        {$to_out.=' часа ';}
        else
        {$to_out.=' часов ';}
        
        }
        if ($mins>0)
        {
        $to_out.= $mins;
        if (($mins%10==1)&&($mins!=11))
        {$to_out.=' минуту ';}
        else
        if (($mins%10==2)&&($mins!=12))
        {$to_out.=' минуты ';}
        else
        if (($mins%10==3)&&($mins!=13))
        {$to_out.=' минуты ';}
        else
        if (($mins%10==4)&&($mins!=14))
        {$to_out.=' минуты ';}
        else
        {$to_out.=' минут ';}
        }
        
        if ($time<60)
        {
        $to_out.=$secs;
        if (($secs%10==1)&&($secs!=11))
        {$to_out.=' секунда';}
        else
        if (($secs%10==2)&&($secs!=12))
        {$to_out.=' секунды';}
        else
        if (($secs%10==3)&&($secs!=13))
        {$to_out.= ' секунды';}
        else
        if (($secs%10==4)&&($secs!=14))
        {$to_out.=' секунды';}
        else
        {$to_out.=' секунд';}
        
        }
        $to_out.= ($to_out!='')?' назад':' только что';        
        
        
        
        return $to_out;
    }
    
    public function comments($num)
    {
        $to_out = '';
        
        if ($num>0) {
            
            $to_out = $num;
        
            if (($num%10==1)&&($num!=11))
            {$to_out.=' комментарий';}
            else
            if (($num%10==2)&&($num!=12))
            {$to_out.=' комментария';}
            else
            if (($num%10==3)&&($num!=13))
            {$to_out.= ' комментария';}
            else
            if (($num%10==4)&&($num!=14))
            {$to_out.=' комментария';}
            else
            {$to_out.=' комментариев';}
            
            $to_out.= ' &rarr; ';
        
        }
        return $to_out;
    }
    
    public function roubles($num)
    {
        $to_out = '';
        
        if (($num%10==1)&&($num!=11))
        {$to_out.=' рубль';}
        else
        if (($num%10==2)&&($num!=12))
        {$to_out.=' рубля';}
        else
        if (($num%10==3)&&($num!=13))
        {$to_out.= ' рубля';}
        else
        if (($num%10==4)&&($num!=14))
        {$to_out.=' рубля';}
        else
        {$to_out.=' рублей';}
        
        
        return $to_out;
    }
    
    public function viewers($num)
    {
        $to_out = '';
        
        if (($num%10==1)&&($num!=11))
        {$to_out.=' зритель';}
        else
        if (($num%10==2)&&($num!=12))
        {$to_out.=' зрителя';}
        else
        if (($num%10==3)&&($num!=13))
        {$to_out.= ' зрителя';}
        else
        if (($num%10==4)&&($num!=14))
        {$to_out.=' зрителя';}
        else
        {$to_out.=' зрителей';}
        
        
        return $to_out;
    }         
    
    
    /**
     * Lingual::Lingual()
     * Constructor method - requires all needed 3rd party libs
     * 
     * @return true always true
     */
    function Lingual()
    {
    	  return $this;
    }
}
?>
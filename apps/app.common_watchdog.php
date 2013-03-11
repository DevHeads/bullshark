<?php
class CommonWatchdog
{
    
    public function log($msg,$object = null) //chainable
    {
        echo '['.sprintf('%01.4f',microtime(true)).'] '.$msg."\n";
        if (!is_null($object))
        {
            echo 'Object: '.var_export($object,true)."\n";
        }
        return $this;
    }
    
    public function notify($subj,$text)
    {
        $list = BS::getConfig('notifymail');
        if (!empty($list))
        {
            foreach ($list as $email)
            {
                BS::MQueue()->send($email,'['.BS::getConfig('site.domain').'] '.$subj,$text.'<hr />Автоматическая система предупреждения '.BS::version());
                $this->log('Notified '.$email.': '.$subj);
            }
        }        
    }
    
    public function notifySuccess($notifier,$text)
    {
        return $this->notify('Automatic action succeeded:'.$notifier,$text);        
    }
    
    public function notifyFail($notifier,$text)
    {
        return $this->notify('Automatic check failed:'.$notifier,$text);        
    }        
    
    public function run()
    {
        $this->log('Starting watchdog');
        
        foreach ($this->checks as $key=>$value)
        {
            if (method_exists($this,$key))
            {
                if (!$this->$key())
                {
                    $this->log('Executing anti-fail procedure: '.$value);
                    if (method_exists($this,$value))
                    {
                        $this->$value();
                    }
                    else
                    {
                        $this->log('Anti-fail procedure "'.$value.'" does not exist =(');
                    }
                }
            }
            else
            {
                $this->log('Checker method "'.$key.'" does not exist =(');
            }
        }
        
        $this->log('Successfully finished checks.');
    }
}
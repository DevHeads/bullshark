<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 * @global kernel $kernel;
*/

class inputGenerator
{
    private $state = array(); 
    
    public function inputGenerator($state)
    {
        $this->state = $state; 
    }
    
    public function render($name,$key,$desc)
    {
        if ($desc['type']=='text')
        {
            return '<tr><td><!--{T:'.$desc['name'].'}--></td><td><input type="text" class="middlefield" id="'.$name.'_'.$key.'"></td></tr>';
        }

        if ($desc['type']=='textarea')
        {
            return '<tr><td><!--{T:'.$desc['name'].'}--></td><td><textarea class="middlefield" id="'.$name.'_'.$key.'"></textarea></td></tr>';
        }
        
        if ($desc['type']=='list')
        {
            $list = isset($this->state->factory->config[$desc['list']])?$this->state->factory->config[$desc['list']]:false;
            
            if (!$list) die('Could not find list "'.$desc['list'].'"'); 
            
            $text = '<tr><td><!--{T:'.$desc['name'].'}--></td><td><select class="middlefield" id="'.$name.'_'.$key.'">';
            
            foreach($list as $item)
                $text .= '<option value="'.$item.'"><!--{T:'.$item.'}--></option>';
            
            $text .='</select></td></tr>';
            
            
            
            return $text;
        }

    }
    
}


class entityFactory
{
    public $config = array();
    
    private $state = array();
    
    public function entityFactory($state)
    {
        $this->state = $state;
        $this->config = include('config/entities.config.php');
    }
    
    public function create($name = false)
    {
        if (!$name)
        {
            debug_print_backtrace();
            die("No parameters set while creating entity");
        }
        
        if (!isset($this->config[$name]))
        {
            die("No entity with name $name is set");
        }
        
        $map = array();
        
        
        foreach ($this->config[$name] as $key=>$value)
        {
            $map[$key] = is_array($value)?(isset($value['default'])?$value['default']:false):$value;
        }
        
        $entity = new entity($this->state, $map,$name);
        return $entity;
    }
    
    
}

class entity
{
    private $options = array();
    private $name  = 'undefined';
    private $state = null; 
    
    function entity($state, $map = false, $name = false)
    {
        if ($map)  $this->options = $map;
        if ($name) $this->name = $name;
        
        $this->state = $state;
        $this->generator = new inputGenerator($this->state);
    }
    
    public function saveByUser($userId)
    {
        $set = new map_settings();
        foreach ($this->options as $key=>$value)
        {
            $set->insertItem(array('user'=>$userId,'entity'=>$this->name,'name'=>$key,'value'=>$value));    
        }
        unset($set);
    }
    
    public function getByUserAndName($userId,$name)
    {
        $set = new map_settings();
        
        $options = $set->getByFields(array('entity'=>$name,'user'=>$userId),array('id','name','value'));
        
        foreach ($options as $option) $this->options[$option['name']]=$option['value'];
        
        $this->name = $name; 
        
    }
    
    public function get($option)
    {
        return isset($this->options[$option])?$this->options[$option]:false;
    }
    
    public function set($option,$value)
    {
        $old = isset($this->options[$option])?$this->options[$option]:false;
        $this->options[$option] = $value;
        return $old;
    }
    
    public function renderInput($name, $key)
    {
        $desc = $this->state->factory->config[$name][$key];
        $item = $this->generator->render($name,$key,$desc);
        
        return $item; 
    }
    
    public function renderForm()
    {
        $form = '<table style="margin-top:20px;" cellspacing="10" id="form_'.$this->getName().'">';
        foreach($this->options as $key=>$value)
        {
            $form .= $this->renderInput($this->getName(),$key)."\n";
        }
        $form .='</table>';
        return $form;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
}

function lib_entity_main($state)
{
    $state->factory = new entityFactory($state);
}
function lib_entity_postexecute($state)
{
    
}
?>
<?php

/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
*/


/**
 * visual
 * ����� visual ��������� ���������� ������������, ������������
 * 
 * @author Charlie <charlie@chrl.ru> 
 * @access public
 */

class Visual
{
	public $template = array('name' => 'index.tpl', 'text' => '');
	public $p = array();
	public $collection = array();
    private $vars = array();
    private $config;
	
	public function visual()
	{
        $this->config = BS::getConfig('site');
        return $this;
	}
    
    public function setVars($vars)
    {
        foreach ($vars as $key=>$item)$this->vars[$key]=$item;
        return true;
    }
    
    public function setVar($name,$value)
    {
        $this->vars[$name]=$value;
        return true;
    }    
    
    public function setPage($name,$value)
    {
        $this->p[$name]=$value;
        return true;
    }
    
    public function getPage($name)
    {
        if (!isset($this->p[$name]))$this->p['name'] = '';
        return $this->p[$name];
    }
    
    public function __set($name,$value)
    {
        if (property_exists($this,$name))
        {
            $this->$name = $value;
        } else return $this->setPage($name,$value);
    }
    
    public function __get($name)
    {
        if (property_exists($this,$name))
        {
            return $this->$name;
        } else return $this->getPage($name);
    }
    
	private function fetchtemplate()
	{
 		if (!file_exists(TEMPLATEDIR .'/'. $this->template['name']))
        throw new Exception('Template doesn\'t exist: '.$this->template['name']);
        
		$this->template['text'] = file_get_contents(TEMPLATEDIR .'/'. $this->template['name']);
        
    	preg_match_all('/<!--\{([p]\:.+)\}-->/iU', $this->template['text'], $matches);
        
		for ($i = 0; $i < count($matches[0]); ++$i)
        {
            $this->template['text'] = str_replace($matches[0][$i],
                 $this->preloadtemplate(substr($matches[1][$i],2)),
                 $this->template['text']);
        }
        
    	preg_match_all('/<!--\{([p]\:.+)\}-->/iU', $this->template['text'], $matches);
        
		for ($i = 0; $i < count($matches[0]); ++$i)
        {
            $this->template['text'] = str_replace($matches[0][$i],
                 $this->preloadtemplate(substr($matches[1][$i],2)),
                 $this->template['text']);
        }        
                
	}
    
    function preloadtemplate($tpl)
    {
 		if (!file_exists(TEMPLATEDIR .'/'. $tpl.'.tpl'))
        die('<b>Fatal:</b> SubTemplate doesn\'t exist: '.$tpl);
		return file_get_contents(TEMPLATEDIR .'/'. $tpl.'.tpl');
    }
    
	function parsetemplate()
	{
		preg_match_all('/<!--\{([scd]\:.+)\}-->/iU', $this->template['text'], $matches);
		foreach ($matches[1] as $block) {
			$x = explode(':', $block);
			$src = $x[0];
			$name = $x[1];
			unset($x[0]);
			unset($x[1]);
			$arg = implode(':', $x);
			$this->collection[] = array('name' => $name, 'arg' => $arg, 'src' => strtolower($src));
		}
	}
    
    function pagifyText($pages,$text)
    {
        foreach ($pages as $name => $item) {
			$text = str_ireplace('<!--{L:' . $name . '}-->', $item, $text);
		}
        return $text;
    }
    
	function assembly()
	{
		
		foreach ($this->collection as $item) {
			$text = 'this block do not exist';
			if ($item['src'] == 'd') {
				$state = array('arg' => $item['arg'], 'v' => $this,
				'user'=>isset($_SESSION['user']) ? $_SESSION['user'] : array('id'=>0));
				$text = BS::runBlock($item['name'], $item['arg']);
			}
            
			if ($item['src'] == 'c') {
				$state = array('arg' => $item['arg'], 'v' => $this,
				'user'=>isset($_SESSION['user']) ? $_SESSION['user'] : array('id'=>0));
				$text = BS::Cache()->getBlockFromCache($item['name'], $item['arg']);
			}              
			$this->template['text'] = str_ireplace('<!--{' . $item['src'] . ':' . $item['name'] .
				(($item['arg'] != '') ? ':' . $item['arg'] : '') . '}-->', $text, $this->
				template['text']);
		}
		foreach ($this->p as $name => $item) {
			$this->template['text'] = str_ireplace('<!--{L:' . $name . '}-->',
             $item,$this->template['text']);
		}
		preg_match_all('/<!--\{([lcsd]\:.+)\}-->/iU', $this->template['text'], $matches);
		for ($i = 0; $i < count($matches[0]); ++$i)
			$this->template['text'] = str_replace($matches[0][$i], $matches[1][$i] .
				' is not defined', $this->template['text']);

	}
    function loadDict()
    {
        if (!empty($this->dict)) return true;
        
        $this->defaultTranslation = false;
        
        if (isset($_SESSION['translation']))
        {
            $this->template['translation'] = $_SESSION['translation'];
            if (BS::getConfig('site.defaulttranslation')!=$_SESSION['translation']) $this->defaultTranslation = false;
        }
        
        if (!isset($this->template['translation']))
        {
            $this->template['translation'] = BS::getConfig('site.defaulttranslation');
            $this->defaultTranslation = true;
        }
            
        if (file_exists(DICTDIR.'/t.'.$this->template['translation'].'.php'))
        {
            $this->dict = include(DICTDIR.'/t.'.$this->template['translation'].'.php');      
        }
        elseif (file_exists(BULLSHARKDIR.'/'.DICTDIR.'/t.'.$this->template['translation'].'.php'))
        {
            $this->dict = include(BULLSHARKDIR.'/'.DICTDIR.'/t.'.$this->template['translation'].'.php');
        }
        else throw new Exception('Translation '.$this->template['translation'].' doesn\'t exist');

    }
    function translate()
    {
    	preg_match_all('/<!--\{([t]\:.+)\}-->/iU', $this->template['text'], $matches);
		for ($i = 0; $i < count($matches[0]); ++$i)
        {
            $this->template['text'] = str_replace($matches[0][$i],
                 $this->translateItem(substr($matches[1][$i],2)),
                 $this->template['text']);
        }
			
    }
    function translateItem($item)
    {
        if (!isset($this->dict[$item]))
        {
            //Check if it exists in default translation
            if ($this->defaultTranslation == false)
            {
                if (file_exists(DICTDIR.'/t.'.BS::getConfig('site.defaulttranslation').'.php'))
                {
                    $default = include(DICTDIR.'/t.'.BS::getConfig('site.defaulttranslation').'.php');
                    
                    if (isset($default[$item]))
                    {
                        // ��������� � ������� �����, ������� ��������� �������� � �������
                        $result = BS::Translate()->t(
                                    $default[$item],
                                    BS::getConfig('translations.'.$this->template['translation'])
                        );
                        
                        $dict = file_get_contents(DICTDIR.'/t.'.$this->template['translation'].'.php');
                        $dict = str_replace("'%newitem%'=>'%newvalue%',","'".$item."'=>'".$result."',\n    '%newitem%'=>'%newvalue%',",$dict);
                        
                        file_put_contents(DICTDIR.'/t.'.$this->template['translation'].'.php',$dict);
                        
                        return $result;
                    }     
                }                
            }
            return $item;   
        }
        
         
        
        $text = $this->dict[$item];
        if (count($this->vars)>0)
        {
            preg_match_all('/%(.+)%/iU', $text, $matches);
            if (count($matches[1]>0))
                foreach ($matches[1] as $key=>$match)
                {
                    if(isset($this->vars[$match]))
                    {
                       $text = str_replace('%'.$match.'%',$this->vars[$match],$text); 
                    } else $text = str_replace('%'.$match.'%','',$text);
                }
        }
        return $text;
    }

    function flush()
    {
        echo $this->template['text'];
    }
    
    public function postexecute()
    {
        if (BS::getConfig('site.encoding'))
        {
        	header('Content-Type: text/html; charset='.BS::getConfig('site.encoding'));
        }
		$this->fetchtemplate();
        $this->parsetemplate();
        $this->assembly();
        $this->loadDict();
        $this->translate();
        $this->flush();
    }
}

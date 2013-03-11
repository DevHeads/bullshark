<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */

/**
 * DAO
 * 
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @version $Id$
 * @access public
 */
class DAO extends Database
{
    public $mappers = array();
    public $activeMapper = false;
    
    /**
     * DAO::__call()
     * Overload all methods of DAO, redirecting method calls to chosen activeMapper
     * 
     * @param string $name Name of called method
     * @param array $arg Arguments
     * @return mixed Mapper result return
     */
    public function __call($name,$arg)
    {
        if ($this->activeMapper)
        {
            return call_user_func_array( array( $this->mappers[$this->activeMapper], $name ), $arg);
        } 
        else throw new Exception('Mapper must be initialized before calls');
    }
    
    /**
     * DAO::DAO()
     * Singleton/Registry implementation, preloads mapper if not chosen, and choses active mapper before mapper call 
     * 
     * @param mixed $mapper Mapper name, false by default to enable class constructor
     * @return DAO Chainable object
     */
    public function DAO($mapper = false)
    {
        require_once(BULLSHARKDIR.'/mappers/map.everything.php');
        
        if ($mapper)
        {
            if (!isset($this->mappers[$mapper]))
            {
                if (file_exists('mappers/map.'.$mapper.'.php'))
                {
                    $path = 'mappers/map.'.$mapper.'.php';
                }
                elseif (file_exists(BULLSHARKDIR.'/mappers/map.'.$mapper.'.php'))
                {
                    $path = BULLSHARKDIR.'/mappers/map.'.$mapper.'.php';
                }
                else throw new Exception('Mapper '.$mapper.' not found');
                
                require_once($path);
                
                if (class_exists('map_'.$mapper))
                {
                    $mapperClass = 'map_'.$mapper;
                    $this->mappers[$mapper]= new $mapperClass();
                    
                } else throw new Exception('Mapper '.$mapper.' class not exists');
                
            }
            
            $this->activeMapper = $mapper;
            
        }
        
        return $this;
    }
}
?>
<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 * @global kernel $kernel;
*/

class Cache
{
    function cache()
    {
    }
    
    /**
     * Cache::getBlockFromCache()
     * Gets Block From Cache if not too old, or writes it to cache
     * 
     * @param mixed $block Name of the block
     * @param mixed $arg Arguments Passed
     * @return string $result Result of Block execution /cached or not/
     */
    public function getBlockFromCache($block,$arg)
    {
        if(file_exists(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache'))
        {
            if(time() - filemtime(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache')>CACHETIME)  // cache is too old
            {
                $result = BS::runBlock($block,$arg);
                file_put_contents(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache',$result);
                return $result;
            }
            else return file_get_contents(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache');
        }
        else
        {
            $result = BS::runBlock($block,$arg);
            file_put_contents(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache',$result);
            return $result;
        }
    }

    /**
     * Cache::clearCacheForBlock()
     * Clears cache file written for block
     * 
     * @param mixed $block Name of the block
     * @param mixed $arg Arguments Passed
     * @return string $result always true
     */
    public function clearCacheForBlock($block,$arg)
    {
        if(file_exists(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache'))
        {
            unlink(CACHEDIR.'/block.'.$block.'.'.md5($arg).'.cache');
        }
    }
    
    public function getEntityFromCache($entity,$id)
    {
        if(file_exists(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache'))
        {
            if(time() - filemtime(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache')>defined('SQLCACHETIME')?SQLCACHETIME:CACHETIME)  // cache is too old
            {
                $result = BS::DAO($entity)->getEntityById($id);
                file_put_contents(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache',serialize($result));
                return $result;
            }
            else return unserialize(file_get_contents(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache'));
        }
        else
        {
            $result = BS::DAO($entity)->getEntityById($id);
            file_put_contents(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache',serialize($result));
            return $result;
        }
    }
    
    public function clearEntityCache($entity,$id)
    {
        if(file_exists(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache'))
        {
            unlink(CACHEDIR.'/entity.'.$entity.'.'.md5($id).'.cache');
        }        
    }
	
	
         public function getTranslationFromCache($translation)
	{

        if(file_exists(CACHEDIR.'/translation.'.md5($translation).'.cache'))
        {
		return file_get_contents(CACHEDIR.'/translation.'.md5($translation).'.cache');
        }
        else
        {
            $result = BS::Ytranslate($translation);
            file_put_contents(CACHEDIR.'/translation.'.md5($translation).'.cache',$result);
            return $result;
        }
    		
	}
}
?>
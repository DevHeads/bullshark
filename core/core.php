<?php
/**
 * Framework core class
 * 
 * @package BullShark  
 * @author Charlie
 * @version 2010
 * @access public
 */
class BS
{
    private static $dmsg = array();                 // Array for debug messages storage
    protected static $version = 'BullShark v0.6';   // Class version
    public static $url = array();                   // Router uri
    public static $app = NULL;                     // Main framework application    
    private static $libs = array();                 // BullShark preloaded libs
    private static $depends = array();              // BullShark dependent libs
    public static $config = array();                // Configuration object
    
    public final function __construct() { throw new Exception("Do not instantiate static classes!"); }
    
    public static function __callStatic($name,$arg)
    {
        $name = strtolower($name);
        
        
        if(isset(self::$libs[$name]))
        {
            if (count($arg)==1)
            {
                return self::$libs[$name]->$name($arg[0]);
            } else return self::$libs[$name];
            
        }else return self::$libs[$name];
        
        
        return false;
    }
    
    static function start()
    {
        self::readConfig(CONFIG);                        // Read configuration

        self::initSession();                             // Start session
        self::loadDepend();                              // Load dependent libs        
        self::parseRoute();
        self::loadAppDepend();                              // Load dependent libs
        self::loadDepend();                              // Load dependent libs        
        self::debug('Running app: '.get_class(self::$app));
        self::runApp(self::$app);                        // Execute main application
        self::postExecute();                             // Post-execute terminating methods of dependent libs
        self::printDebug();                              // Output debug messages if needed
        return true;
    }

    /**
    * BS::version()
    * Returns system version
    * 
    * @return string Version of framework + server software
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    public static function version()
    {
        if (!isset($_SERVER['SERVER_SOFTWARE'])) $_SERVER['SERVER_SOFTWARE'] = `uname -a`;
        return self::$version . ' @ ' . $_SERVER['SERVER_SOFTWARE'];
    }


    /**
    * BS::debug()
    * Добавляет запись в буфер отладки
    * @param string/array $msg сообщение
    * @return int Количество записей в буфере отладки
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    public static function debug($msg)
    {
        if (is_array($msg))
        {
            $s = '/' . self::version() . '/ Array Iteration follows<br />';
            foreach ($msg as $key => $value)
            {
                $s .= $key . ' => ' . $value . '<br />';
            }

        } else
            self::$dmsg[] = '/' . self::version() . '/ ' . $msg;
        return count(self::$dmsg);
    }

    /**
    * BS::readConfig()
    * Читает, загружает, анализирует конфигурационный файл
    * 
    * @return boolean наличие конфигурации для сайта
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    public static function readConfig($conf)
    {
        
        
        if (file_exists($conf))
        {
                self::$config = require($conf);
                
                if (self::$config == 1) {
                    self::$config = $config;
                }
                
                if (isset(self::$config['extends']))
                {
                    foreach (self::$config['extends'] as $item)
                    {
                        $cnf = include($item);
                        self::$config = self::$config + $cnf;
                    }
                }                
                
                if (isset(self::$config['constants']))
                {
                    foreach (self::$config['constants'] as $key => $item)
                    {
                
                        define($key, $item);
                    }
                    unset(self::$config['constants']);
                }

                if (isset(self::$config['preload']))
                    foreach (self::$config['preload'] as $lib)
                        self::$depends[] = $lib;
        } else
            throw new Exception('No config file found at '.getcwd().'/'.$conf);
        return isset(self::$config['site']);
    }


    /**
    * BS::initSession()
    * Стартует сессию, или продляет если уже есть сессия
    * 
    * @return boolean always true
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */
    public static function initSession()
    {
        session_set_cookie_params(24*3600,'/'); //session lifetime in seconds prolong
        session_name(SID);
        session_start();
        return true;
    }

    /**
    * BS::parseRoute()
    * Разбирает URL, выполняет генерацию зависимостей по урлу, роутинг контроллеров
    * 
    * @return boolean always true
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */
    
    public static function parseRoute()
    {
        if(self::getConfig('site.cli')) // Проверим cli-версию
        {
            array_shift($_SERVER['argv']);
            self::$url = $_SERVER['argv'];

        }
        else    // Take from request
        {
            self::$url = isset($_GET['q'])?$_GET['q']:$_SERVER['REQUEST_URI'];
            self::$url = explode('/', trim(self::$url, '/'));
        }
        
        
        if (isset(self::$url[0]) && (self::$url[0] != ''))
        {
            $currentApp = (BS::getAlias(self::$url))?BS::getAlias(self::$url):self::$url[0];
            
            if (file_exists(APPDIR . '/app.' . $currentApp . '.php'))
            {
                self::$depends[] = 'app_' . $currentApp;
            } else
            if (file_exists(BULLSHARKDIR . '/'.APPDIR. '/app.' . $currentApp . '.php'))
            {
                self::$depends[] = 'app_' . $currentApp;
            } else self::$depends[] = 'app_404';
            
        } else
        {
            if (BS::getAlias(self::$config['site']['defaultroute']))
            {
                self::$depends[] = 'app_' . BS::getAlias(self::$config['site']['defaultroute']);
            }
            else self::$depends[] = 'app_' . self::$config['site']['defaultroute'];
        }
        return true;
    }
    
    public static function getAlias($name)
    {
        //flatten name
        if(!is_array($name))$name=array($name);
        
        if(false!==strpos($name[count($name)-1],'.')) {
            unset($name[count($name)-1]);
        }
        $name = implode('.',$name);
        
        
        if(BS::getConfig('alias.'.$name)) return BS::getConfig('alias.'.$name);
        
        
        if(isset(self::$libs['dao']))
        {
        
            $alias = BS::DAO('page')->getByFields(array('alias'=>$name),null,true);
            
            if ($alias) {
                if(false === strpos($name,'services'))
                return 'simple';
            }
            
            $alias = BS::DAO('category')->getByFields(array('alias'=>$name),null,true);
            
            
            
            if ($alias) {
                return 'category';
            }
            
            $alias = BS::DAO('object')->getByFields(array('alias'=>$name),null,true);
            
            if ($alias) {
                return 'viewobject';
            }                        
        }
        
        
        return false;
        
    }
    
    /**
    * BS::getURL()
    * Возвращает URL
    * 
    * @return boolean always true
    * @since 0.5
    * @author Charlie <charlie@chrl.ru>
    */
    
    public static function getURL()
    {
        return self::$url;
    }
    
    /**
    * Производит загрузку зависимостей
    * 
    * @return boolean всегда true
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    public static function loadDepend()
    {
        
        if (isset(self::$depends) && count(self::$depends))
        {

            foreach (self::$depends as $key => $item)
            {
                if (!isset(self::$libs[$item]))
                {
                    if (file_exists('classes/class.' . $item . '.php'))
                    {
                        require_once ('classes/class.' . $item . '.php');
                    } 
                    elseif (file_exists(BULLSHARKDIR.'/classes/class.' . $item . '.php'))
                    {
                        require_once (BULLSHARKDIR.'/classes/class.' . $item . '.php');
                    } 
                    else throw new Exception('Dependent Class not found: ' . $item);
                    
                    if (class_exists($item))
                    {
                        self::$libs[$item] = new $item();
                    }
                    else throw new Exception('Class '.$item.' not found, unless class storage file (class.'.self::$depends[$key].'.php) exists');
                    
                    
                    /**
                     *   @todo Убить тут не по ключу, а по самому значению. А возможно, и добавлять по значению.
                     */
                }
                unset(self::$depends[$key]); 
            }
        }
        return true;
    }

    /**
    * Производит загрузку зависимостей
    * 
    * @return boolean всегда true
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    public static function loadAppDepend()
    {
        foreach (self::$depends as $key => $item)
        {
            if (substr($item, 0, 4) == 'app_')
            {
                $item = substr($item, 4);
                
                if (file_exists(APPDIR . '/app.' . $item . '.php'))
                {
                    require_once (APPDIR . '/app.' . $item . '.php');    
                } 
                elseif (file_exists(BULLSHARKDIR .'/'. APPDIR. '/app.' . $item . '.php'))
                {
                    require_once (BULLSHARKDIR .'/'. APPDIR. '/app.' . $item . '.php');
                } else throw new Exception('Application app_'.$item.' not found!');
                
    
                $item = 'app_'.$item;
                if (class_exists($item))
                {
                    self::$app = new $item();
                    if (method_exists(self::$app,'init'))
                    {
                        self::$app->init();
                    }
                }
                else throw new Exception('Class '.$item.' not found, unless class storage file (app.'.substr(self::$depends[$key],4).'.php) exists');
                unset(self::$depends[$key]);
            }
        }
        
        if (isset(self::$app->depends) && count(self::$app->depends))
        {

            foreach (self::$app->depends as $key => $item)
            {
                if (!isset(self::$libs[$item]))
                {
                    if (file_exists('classes/class.' . $item . '.php'))
                    {
                        require_once ('classes/class.' . $item . '.php');
                    } 
                    elseif (file_exists(BULLSHARKDIR.'/classes/class.' . $item . '.php'))
                    {
                        require_once (BULLSHARKDIR.'/classes/class.' . $item . '.php');
                    } 
                    else throw new Exception('Dependent Class not found: ' . $item);
                    
                    if (class_exists($item))
                    {
                        self::$libs[$item] = new $item();
                    }
                    else throw new Exception('Class '.$item.' not found, unless class storage file (class.'.self::$depends[$key].'.php) exists');
                    
                    
                    /**
                     *   @todo Убить тут не по ключу, а по самому значению. А возможно, и добавлять по значению.
                     */
                }
                unset(self::$app->depends[$key]); 
            }
        }
        return true;
    }

    /**
    * Подгрузка и выполение функций, предназначенных для исполнения в конце ЖЦ
    * 
    * @return boolean Наличие подгружаемых функций
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    function postExecute()
    {
        if (isset(self::$config['postexecute']))
        {
            foreach (self::$config['postexecute'] as $lib)
            {
                if(isset(self::$libs[$lib]))
                {
                    if(method_exists(self::$libs[$lib],'postExecute'))
                    {
                        self::$libs[$lib]->postExecute();
                    } else throw new Exception('Library '.$lib.' is set to postexecute, but has no postExecute method');
                } else throw new Exception('Library '.$lib.' is set to postexecute, but not loaded as dependent');                
            }
            return true;
        } else
            return false;
    }

    /**
    * Выводит содержимое буфера отладки
    * 
    * @return boolean false / Количество строк
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */

    function printDebug()
    {
        if (count(self::$dmsg) && DEBUG)
        {
            echo '<hr />';
            foreach (self::$dmsg as $item)
                echo '<b>dBug:</b> ' . $item . '<br />';
            return count(self::$dmsg);
        } else
            return false;
    }

    
    /**
    * Возвращает Config
    * 
    * @param section Object $section
    * @return mixed Указанная секция конфига
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */
    
    public static function getConfig($section)
    {
        $section = explode('.',$section);
        
        $cursor = self::$config;
        
        foreach($section as $item)
        {
            
            
            if (isset($cursor[$item]))
            {
                $cursor = $cursor[$item];
            }
            else
            {
                return false;
            }
        }
        return $cursor;
    }

    /**
    * Выполняет приложение $app
    * 
    * @param application Object $app
    * @return mixed Результат операции
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */
    
    public static function runApp($app)
    {
        if (method_exists($app, 'run'))
            return $app->run();
        else
            throw new Exception('Method run does not exist within class '.get_class($app));
    }
    
    /**
    * Выполняет блок $block с аргументом $arg
    * 
    * @param application Object $block
    * @param arguments String $arg
    * @return mixed Результат операции
    * @since 0.1
    * @author Charlie <charlie@chrl.ru>
    */
    
    function runBlock($block,$arg)
    {
        if (file_exists(BLOCKDIR.'/block.'.$block.'.php'))
        {
            $path = BLOCKDIR.'/block.'.$block.'.php';
        }
        elseif (file_exists(BULLSHARKDIR.'/'.BLOCKDIR.'/block.'.$block.'.php'))
        {
            $path = BULLSHARKDIR.'/'.BLOCKDIR.'/block.'.$block.'.php';
        }
        else throw new Exception('Block '.$block.' not found');
        
        require_once ($path);
        if (class_exists('block'.ucfirst($block)))
        {
            $cn = 'block'.ucfirst($block);
            $obj = new $cn();
            if (method_exists($obj, 'run'))
                return $obj->run($this,$arg);
                else return false;
        } else throw new Exception('Class "'.'block'.ucfirst($block).'" is not defined!');
    }
}

?>
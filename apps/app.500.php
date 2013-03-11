<?php
/*
 Projekt: BullShark
 Author: Kirill S. Holodilin
 Date: 2009, May
*/

class app_500
{
    var $version='App_500 v.0.1';
    var $depends=array('lib_entity');
    public function init()
    {
        
    }
    public function run($state)
    {
        header("HTTP/1.1 500 Server Error");
		header("Status: 500 Server Error");
        header('Connection: close');
        
        
		$state->v->template['name']='error.tpl';
        $state->v->p['text']=isset($_GET['msg'])?htmlspecialchars($_GET['msg'],ENT_QUOTES):'Возникла непредвиденная ошибка сервера';
/*        
        $test = new entity( array( 
                                'test'=>'true',
                                'test2'=>'false',
                            ),
                            'testEntity'
                    ); 
*/                    
        //$test->saveByUser(10);
        
        $test = new entity();
        
        $test->getByUserAndName(10,'testEntity');
                    
        var_dump($test);
    }
}
?>
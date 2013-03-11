<?php
/**
 * Projekt: BullShark
 * Author: Kirill S. Holodilin
 * Date: 2009, May
 */

class app_formgen
{
    var $version='App_formgen v.0.1';
    var $depends=array('lib_entity');
    public function init()
    {
        
    }
    public function run($state)
    {
        
		$state->v->template['name']='test.tpl';
        
        $test = $state->factory->create('vacancy');
                    
        $state->v->p['text'] = $test->renderForm();
        //var_dump($test);
    }
}
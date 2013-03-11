<?php
/*
 Projekt: BullShark
 Author: Kirill S. Holodilin
 Date: 2009, May
*/
class catcher
{
    var $v = null;
    public function catcher($v)
    {
        $this->v = $v;
    } 
    public function is_email($string) {
      if (preg_match('/^[^0-9][a-zA-Z0-9_.]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$string)) {
        return true;
      } else {
        return false;
      }
    }
    
    
    
    public function catchRegform($data)
    {
        $return = array('result'=>'check');
        $fields = array();

        $user = new map_user();
        
        foreach ($data as $item)
        {
            $fields [$item->id]= $item->value;
        }
        
        //checks
        
        if ($user->getByFields(array('mail'=>$fields['form_mail'])))
        {
            $return['msg']='<!--{t:_user_exists}-->';
            $return['mark']='form_mail';
        }        
        
        
        if ($fields['form_pass1']!=$fields['form_pass2'])
        {
            $return['msg']='<!--{t:_pass_dont_match}-->';
            $return['mark']='form_pass2';
        }

        if (mb_strlen($fields['form_pass1'],'UTF-8')<4)
        {
            $return['msg']='<!--{t:_pass_too_short}-->';
            $return['mark']='form_pass1';
        }

        if (mb_strlen($fields['form_pass1'],'UTF-8')<4)
        {
            $return['msg']='<!--{t:_pass_too_short}-->';
            $return['mark']='form_pass1';
        }
        
        if ((mb_strlen($fields['form_name'],'UTF-8')<2)or(mb_strlen($fields['form_name'],'UTF-8')>15))
        {
            $return['msg']='<!--{t:_name_too_short_or_long}-->';
            $return['mark']='form_name';
        }        

        if ((mb_strlen($fields['form_secondname'],'UTF-8')<2)or(mb_strlen($fields['form_secondname'],'UTF-8')>15))
        {
            $return['msg']='<!--{t:_secondname_too_short_or_long}-->';
            $return['mark']='form_secondname';
        }        

        if (!$this->is_email($fields['form_mail']))
        {
            $return['msg']='<!--{t:_mail_not_valid}-->';
            $return['mark']='form_mail';
        }
        
        if (!isset($return['msg']))
        {
            
            $res = $user->insertItem(array(
                'mail'=>$fields['form_mail'],
                'pass'=>$fields['form_pass1'],
                'role'=>0
            )); 
            
            if ($res)
            {
                $link = md5(rand(1,1000).'__'.microtime().'check'.$res);
                
                $test = new entity(
                            $this->v->state,
                            array( 
                                'name'=>$fields['form_name'],
                                'secondName'=>$fields['form_secondname'],
                                'link'=>$link,
                                'regdate'=>time(),
                            ),
                            'profileSettings'
                    ); 
             
                $test->saveByUser($res);
                
                $letter = $this->v->preloadtemplate('letter');
                $letter = $this->v->pagifyText(array(
                    'name'=>$fields['form_name'],
                    'sitename'=>$this->v->state->config['site']['name'],
                    'verifyLink'=>'http://'.$this->v->state->config['site']['domain'].'/verify/'.$link.'/',
                    ),$letter);
                sendmail($fields['form_mail'],'Registration',$letter);
                
                $return['result']='ok';
                $return['uri']='/registersuccess/';
            }
            else
            {
                $return['result']='fail';
                $return['message']='Failed to register user';
            }
            
        }
        
        //return
        
        return $return;
    }
    
    public function catchLoginform($data)
    {
        $return = array('result'=>'check');
        $fields = array();
        
        foreach ($data as $item)
        {
            $fields [$item->id]= $item->value;
        }
        
        
        $user = new map_user();
        //checks
        
        if (!$this->is_email($fields['login_mail']))
        {
            $return['msg']='<!--{t:_mail_not_valid}-->';
            $return['mark']='login_mail';
        }
        
        if ($u = $user->getByFields(array('mail'=>$fields['login_mail'],'pass'=>$fields['login_pass']),null,true))
        {
            if ($u['role']==0)
            {
                $return['msg']='<!--{t:_verify_sent}-->';
                $return['mark']='login_mail';
            }
        }
        else
        {        
            $return['msg']='<!--{t:_login_not_correct}-->';
            $return['mark']='login_pass';
        }


        
        if (!isset($return['msg']))
        {
            
            $_SESSION['user'] = array('id'=>$u['id'],'mail'=>$u['mail']);
            session_write_close();
            
            $return['result']='ok';
            $return['uri']='/main/';
        }
        
        //return
        
        return $return;
    }
    
    
}
class app_catcher
{
    var $version='App_Catcher v.0.1';
    var $depends=array('lib_entity');
    public function init()
    {
        
    }
    
    public function run($state)
    {
		$state->v->template['name']='ajax.tpl';
        $state->v->p['text'] = '';
        
        $this->catcher = new catcher($state->v);
        if (isset($_POST['method'])&&($_POST['method']=='catch'))
        {
            $method = 'catch'.ucfirst($_POST['entity']);
            if (method_exists($this->catcher,$method))
            {
                $result = $this->catcher->$method(json_decode($_POST['data']));
                $state->v->p['text']=json_encode($result);   
            }
            else $state->v->p['text']=json_encode(array('result'=>'fail','method'=>$method));
        }
    }
}
?>
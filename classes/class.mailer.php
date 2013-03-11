<?
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 */
 

/**
 * Mailer class
 * 
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @version 2010
 * @access public
 */
class Mailer
{
      /**
       * Mailer::sendmail()
       * Sends mail using default settings
       * 
       * @param string $to Email of the adressee
       * @param string $subject Subject of the letter 
       * @param string $html Html text of the letter
       * @return Operation result
       */
      function sendmail($to,$subject,$html)
      {
      
    	    $smtp=new smtp_class;
    	    $smtp->host_name="smtp.gmail.com";       /* Change this variable to the address of the SMTP server to relay, like "smtp.myisp.com" */
    	    $smtp->host_port=465;                /* Change this variable to the port of the SMTP server to use, like 465 */
    	    $smtp->ssl=1;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
    	    $smtp->localhost="localhost";       /* Your computer address */
    	    $smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
    	    $smtp->timeout=100;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
    	    $smtp->data_timeout=0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
    					       Set to 0 to use the same defined in the timeout variable */
    	    $smtp->debug=1;                     /* Set to 1 to output the communication with the SMTP server */
    	    $smtp->html_debug=0;                /* Set to 1 to format the debug output as HTML */
    	    $smtp->pop3_auth_host="";           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
    	    $smtp->user=BS::getConfig('site.mailername');                     /* Set to the user name if the server requires authetication */
    	    $smtp->realm=BS::getConfig('site.mailerdomain');                    /* Set to the authetication realm, usually the authentication user e-mail domain */
    	    $smtp->password=BS::getConfig('site.mailerpass');                 /* Set to the authetication password */
    	    $smtp->workstation="";              /* Workstation name for NTLM authentication */
    	    $smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
    					       Leave it empty to make the class negotiate if necessary */
    
    
    
    	    $from=BS::getConfig('site.mailerfrom');
    	    $subject=trim($subject);
    	    
    	    if($smtp->SendMessage(
    		    $from,
    		    array(
    			$to
    		    ),
    		    array(
    			"From: ".BS::getConfig('site.mailerfromrealm')."<$from>",
    			"To: $to",
    			'MIME-Version: 1.0',	// Headers
    			'Content-type: text/html; charset=UTF-8',
    			'Content-transfer-encoding: base64',
    			'Subject: '.$subject,
    			"Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")
    		    ),
    		    base64_encode($html)))
    	    {
    			return true;
    	    }
    	    else
    		return false;
    }
    /**
     * Mailer::Mailer()
     * Constructor method - requires all needed 3rd party libs
     * 
     * @return this chainable
     */
    function Mailer()
    {
    	  require_once('mail/class.smtp.php');
    	  require_once('mail/class.sasl.php');
    	  return $this;
    }
}
?>
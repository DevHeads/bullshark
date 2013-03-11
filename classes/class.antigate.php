<?php
/**
 * @package BullShark
 * @author Charlie <charlie@chrl.ru>
 * @since 0.1
 * @global kernel $kernel;
*/

class Antigate
{
    protected $key='';

    public function __construct()
    {
        $this->key = BS::getConfig('site.antigate');
    }

    public function l($string)
    {
        echo $string;
        //file_put_contents('/tmp/antigate.log','['.  date('d.m.Y H:i:s').'] '.$string,FILE_APPEND);
    }

    public function recognize(
                    $filename,
                    $ext,
                    $is_verbose = true,
                    $sendhost = "antigate.com",
                    $rtimeout = 5,
                    $mtimeout = 120,
                    $is_phrase = 0,
                    $is_regsense = 0,
                    $is_numeric = 1,
                    $min_len = 0,
                    $max_len = 0,
                    $is_russian = 0)
    {
        $body=file_get_contents($filename);

        if ($ext=="jpg") $conttype="image/pjpeg";
        if ($ext=="gif") $conttype="image/gif";
        if ($ext=="png") $conttype="image/png";


        $boundary="---------FGf4Fh3fdjGQ148fdh";

        $content="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"method\"\r\n";
        $content.="\r\n";
        $content.="post\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"key\"\r\n";
        $content.="\r\n";
        $content.="{$this->key}\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"phrase\"\r\n";
        $content.="\r\n";
        $content.="$is_phrase\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"regsense\"\r\n";
        $content.="\r\n";
        $content.="$is_regsense\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"numeric\"\r\n";
        $content.="\r\n";
        $content.="$is_numeric\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"min_len\"\r\n";
        $content.="\r\n";
        $content.="$min_len\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"max_len\"\r\n";
        $content.="\r\n";
        $content.="$max_len\r\n";
        $content.="--$boundary\r\n";
        $content.="Content-Disposition: form-data; name=\"file\"; filename=\"capcha.$ext\"\r\n";
        $content.="Content-Type: $conttype\r\n";
        $content.="\r\n";
        $content.=$body."\r\n"; //тело файла
        $content.="--$boundary--";


        $poststr="POST http://$sendhost/in.php HTTP/1.0\r\n";
        $poststr.="Content-Type: multipart/form-data; boundary=$boundary\r\n";
        $poststr.="Host: $sendhost\r\n";
        $poststr.="Content-Length: ".strlen($content)."\r\n\r\n";
        $poststr.=$content;

       // echo $poststr;

        if ($is_verbose)$this->l("connecting $sendhost...");
        $fp=fsockopen($sendhost,80,$errno,$errstr,30);
        if ($fp!=false)
        {
            if ($is_verbose) $this->l( "OK\n");
            if ($is_verbose) $this->l( "sending request ".strlen($poststr)." bytes...");
            fputs($fp,$poststr);
            if ($is_verbose) $this->l( "OK\n");
            if ($is_verbose) $this->l( "getting response...");
            $resp="";
            while (!feof($fp)) $resp.=fgets($fp,1024);
            fclose($fp);
            $result=substr($resp,strpos($resp,"\r\n\r\n")+4);
            if ($is_verbose) $this->l( "OK\n");
        }
        else
        {
            if ($is_verbose) $this->l( "could not connect to anti-captcha\n");
            if ($is_verbose) $this->l( "socket error: $errno ( $errstr )\n");
            return false;
        }

        if (strpos($result, "ERROR")!==false or strpos($result, "<HTML>")!==false)
        {
            if ($is_verbose) $this->l( "server returned error: $result\n");
            return false;
        }
        else
        {
            $ex = explode("|", $result);
            $captcha_id = $ex[1];
            if ($is_verbose) $this->l("captcha sent, got captcha ID $captcha_id\n");
            $waittime = 0;
            if ($is_verbose) $this->l( "waiting for $rtimeout seconds\n");
            sleep($rtimeout);
            while(true)
            {
                $result = file_get_contents('http://antigate.com/res.php?key='.$this->key.'&action=get&id='.$captcha_id);
                if (strpos($result, 'ERROR')!==false)
                {
                    if ($is_verbose) $this->l( "server returned error: $result\n");
                    return false;
                }
                if ($result=="CAPCHA_NOT_READY")
                {
                    if ($is_verbose) $this->l( "captcha is not ready yet\n");
                    $waittime += $rtimeout;
                    if ($waittime>$mtimeout)
                    {
                            if ($is_verbose) $this->l( "timelimit ($mtimeout) hit\n");
                            break;
                    }
                            if ($is_verbose) $this->l( "waiting for $rtimeout seconds\n");
                    sleep($rtimeout);
                }
                else
                {
                    $ex = explode('|', $result);

                    $this->l($result);

                    if (trim($ex[0])=='OK') return trim($ex[1]);
                }
            }

            return false;
        }
    }
}
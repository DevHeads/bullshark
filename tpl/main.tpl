<!--{P:header}-->
<div style="position:absolute;width:100%;height:100%;left:0px;top:0px;z-index:999;background:black;" id="loader">
<table width="100%" height="100%">
<tr><td align="center">
    <span id="loaderText" style="display:none;">
        <!--{t:_loading_msg}-->
        <br /><br />
        <img src="/static/images/loading.gif" onload="$('#loaderText').fadeIn('slow');" />
    </span>
</td></tr>
</table>
</div>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
        <!--{P:logo}-->
        <table style="margin-top:20px;" id="loginform">
            <tr><td colspan="2"><input id="login_mail" class="bigfield" type="text" /></td></tr>
            <tr><td colspan="2"><input id="login_pass" class="bigfield" type="password" /></td></tr>
            <tr><td colspan="2">
            <div id="msg" style="font-size:16px;color:red;padding:20px;border:1px solid red;display:none;">

            </div>            
            </td></tr>
            <tr>
                <td><a href="#login" onclick="catcher('loginform');return false;"><!--{T:_enter}--></a> <!--{T:_or}--> <a href="/register/"><!--{t:_register_msg}--></a></td>
                <td align="right"><a href="/forgot/"><!--{t:_forgot_pass_msg}--></a></td>
            </tr>
        </table>

        </td>
    </tr>
</table>
<!--{P:footer}-->
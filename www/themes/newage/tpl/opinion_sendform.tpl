<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Enviar esta opinión a un amigo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

    <script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}prototype.js"></script>
    
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}article_printer.css"/>
    
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, noarchive, nofollow" /> 
</head>

<body>
<div id="container">
    
    <div class="logoXornalYBanner">        
        <div class="logoXornal">
            <img src="{$params.IMAGE_DIR}xornal-logo.jpg" alt="Xornal.com - Xornal de Galicia"
                 height="70" />
        </div>
    </div>    

    <div class="noticia">
        {if $smarty.request.action == 'sendform'}
        <h4>Enviar este artículo de opinión a un amigo</h4>
        <form method="POST" action="/opinion.php">
            <table width="100%" border="0" cellpadding="3" cellspacing="2" class="tabular-form">
            <tr> 
                <th>Nombre remitente:</th>
                <td><input name="name_sender" type="text" size="40" /></td>
            </tr>
            <tr> 
                <th>E-mail remitente:</th>
                <td><input name="sender" type="text" size="24" /></td>
            </tr>
            <tr> 
                <th>E-mail destinatario:</th>
                <td><input name="destination" type="text" size="24" /></td>
            </tr>
            <tr> 
                <th>Comentario:</th>
                <td>
                    <textarea name="body" rows="8" cols="50">{$opinion->title|clearslash}</textarea>
                </td>
            </tr>
            <tr> 
                <td colspan="2" align="center">
                    <input type="submit" value="Enviar" class="button" /> 
                </td>
            </tr>
            </table>
            
            <input type="hidden" name="opinion_id"     value="{$opinion->id}" />
            <input type="hidden" name="action" value="send" />
            <input type="hidden" name="token"  value="{$token}" /> 
        </form>
        {/if}
        
        {if $smarty.request.action == 'send'}
            <div class="message">
                <h4>{$message}</h4>
            </div>
            
            {literal}
            <script type="text/javascript">
            /* <![CDATA[ */
            window.setTimeout( function() {
                parent.myLightWindow.deactivate();
            }, 4000);
            /* ]]> */
            </script>
        {/literal}
        
        {/if}
    </div>
</div>

</body>
</html>

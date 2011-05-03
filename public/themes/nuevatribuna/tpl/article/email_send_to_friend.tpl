<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="language" content="es" />
    </head>
    <body>
        <div style="font-size: 11px; font-family: Arial;">
            <table border="0" cellpadding="0" cellspacing="0" width="765">
                <tbody>
                    <tr>
                        <td>
                            <a href="{$smarty.const.SITE_URL}" border="0" target="_blank">
                                <img border=0 src="{$params.IMAGE_DIR}logos/nuevatribuna-header.png" alt="{$smarty.const.SITE_FULLNAME}" />
                            </a>
<!--                            <div style="margin: 0px 0px 4px; padding-top: 10px; color:#980101; font-size: 18px; font-weight: bold; font-family: Arial;">
                                Alguien ha compartido un artículo contigo
                            </div> -->
                        <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="font-size:1.2em; color:#222">
                                <b>Hola {$destination}</b>
                                <br><br>
                                <b>{$mail->FromName} quiere compartir contigo la siguiente información de {$smarty.const.SITE_FULLNAME} </b>
                                <br><br>
                                <b> Comentario: {$body}</b>   
                                <br><br>{$message}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="margin:10px 0;">
                            <div style="margin: 0px 0px 0px; padding: 0px; font-family: Arial; font-size:23px; color:#333333; font-weight: normal;  border-top: 1px solid #ccc;">
                                <a href="{$smarty.const.SITE_URL}{$article->permalink}" target="_blank">
                                    <b>{$article->title}</b></div><br><div style="margin: 0px 0px 0px; padding: 0px; color:#666; font-size: 11px; font-weight: bold; text-align: left;">
                                </a>
                                <b>{$agency}</b> | {$date}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="margin: 0px; color:#333333; font-size: 12px; line-height: 15px; border-bottom: 1px solid #ccc; padding-bottom: 5px; ">
                                    {$summary}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="font-size:1.2em; color:#222; color:#014687; text-align:right; text-decoration: underline;  font-size: 12px; line-height: 15px;">
                                <a href="{$smarty.const.SITE_URL}{$article->permalink}" target="_blank">Ir al artículo completo</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
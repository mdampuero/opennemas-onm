<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="language" content="es" />
</head>
<div style="font-size: 11px; font-family: Arial;">
    <table border="0" cellpadding="0" cellspacing="0" width="765">
        <tbody>
            <tr>
                <td bgcolor="014687">
                    <a href="http://www.xornal.com/" border="0" target="_blank">
                        <img src="{$params.IMAGE_DIR}logos/nuevatribuna-header.png" alt="{$smarty.const.SITE_FULLNAME}" />
                    </a>
                    <br>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin: 0px 0px 4px; padding-top: 10px; color:#014687; font-size: 18px; font-weight: bold; font-family: Arial;">
                        ARTÍCULO RECOMENDADO
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Hola '.$_REQUEST['destination'].',</b>
                    <br>
                <b>'.$mail->FromName.' quiere compartir contigo la siguiente información: </b>
                <br>
                <br{$message}
                <br>
                <br>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="http://www.xornal.com/themes/xornal/images/fileteFondoNota.gif" height="1" width="1">
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin: 0px 0px 0px; padding: 0px; font-family: Arial; font-size:26px; color:#333333; font-weight: normal;  border-top: 1px solid #014687;">
                        <b>'.stripslashes($article->title).'</b></div><br><div style="margin: 0px 0px 0px; padding: 0px; color:#014687; font-size: 11px; font-weight: bold; text-align: left;">
                        <b>'.$agency.'</b> | '.$date.'</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin: 0px; color:#333333; font-size: 12px; line-height: 15px; border-bottom: 1px solid #014687; padding-bottom: 5px; ">
                            {$summary}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="color:#014687; text-align:right; text-decoration: underline;  font-size: 12px; line-height: 15px;">
                        <a href="'.$permalink.'" target="_blank">Ir al artículo completo</a>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
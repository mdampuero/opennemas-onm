{if $smarty.request.action=='send'}
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
{/if}
<div style="font-size: 12px; font-family: Arial;">
    <table border="0" cellpadding="0" cellspacing="0" width="765">
        <tbody>
            <tr>
                <td>
                    <a href="http://www.retrincos.info/" target="_blank">
                    {if $smarty.request.action=='send'}
                        <img src="cid:logo-cid" border="0" height="60" width="300" alt="" /></a>
                    {else}
                        <img src="{$URL_PUBLIC}/themes/{$smarty.const.THEME}/images/retrincos-big-logo.png" border="0" alt="Boletín de {$smarty.const.SITE_FULLNAME}" /></a>
                    {/if}
                    <br />
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin: 0px 0px 4px; padding-top: 10px; color:#354b1d; font-size: 18px; font-weight: bold; font-family: Arial;">
                        Noticias del {$current_date}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <br/>
                    Hola ###DESTINATARIO###,
                    <br/>
                    estas son las &uacute;ltimas noticias y articulos de opinions de hoy  te recordamos que puedes encontra estas y muchsa más visitando la web
                    {$smarty.const.SITE_FULLNAME} (<a href="{$URL_PUBLIC}">{$URL_PUBLIC}</a>):
                    <br /><br />
                </td>
            </tr>

            {if count($data->articles)>0}
                <tr>
                    <td style="font-family:Georgia; font-size:12px; color:#354b1d;background:#deeccf;padding:3px;border-bottom:1px solid #354b1d;">
                        <div style="margin-left: 5px">NOTICIAS</div>
                    </td>
                </tr>

                {section name=n loop=$data->articles}
                <tr>
                    <td style="font-size:15px;color:#333333;font-weight:normal;padding:5px;padding-left:10px;border-top:1px solid #a0a0a0;">
                        <a href="{$URL_PUBLIC}{$data->articles[n]->uri}" style="color: #354b1d">
                            <b>&middot; {$data->articles[n]->category_name}</b>: <span style="color:Black">{$data->articles[n]->title}</span></a>
                    </td>
                </tr>
                {/section}

                <tr>
                    <td>&nbsp;</td>
                </tr>
            {/if}

            {if count($data->opinions)>0}
                <tr>
                    <td style="font-family:Georgia; font-size:12px; color:#354b1d;background:#deeccf;padding:3px;border-bottom:1px solid #354b1d;">
                        <div style="margin-left:5px">ARTICULOS DE OPINI&Oacute;N</div>
                    </td>
                </tr>

                {section name=o loop=$data->opinions}
                <tr>
                    <td style="font-size:15px;color:#333333;font-weight:normal;padding:5px;padding-left:10px;border-top:1px solid #a0a0a0;">
                    <a href="{$URL_PUBLIC}{$data->opinions[o]->permalink}" style="color:#354b1d">
                        <b>&middot; {$data->opinions[o]->author}</b>: <span style="color:Black">{$data->opinions[o]->title}</span></a>
                </tr>
                {/section}

                <tr>
                    <td>&nbsp;</td>
                </tr>
            {/if}

            <tr>
                <td>&nbsp;</td>
            </tr>

            <tr>
                <td style="background:#deeccf;padding:3px;font-size:10px;border-bottom:1px solid #354b1d;">
                    <div style="margin-left: 10px"><a href="{$URL_PUBLIC}/conecta/boletin/">MODIFICAR / CANCELAR SUSCRIPCI&Oacute;N</a></div>
                </td>
            </tr>

            <tr>
                <td style=" font-size:12px; color:#333333; font-weight: normal; padding: 15px; padding-left: 10px; border-top: 1px solid #a0a0a0; background: #efefef;">
                Usted est&aacute; recibiendo este bolet&iacute;n tras solicitar este servicio a trav&eacute;s de la web de
                <a href="{$URL_PUBLIC}">{$smarty.const.SITE_FULLNAME} </a>. En cualquier momento puede modificar o cancelar su suscripci&oacute;n
                accediendo a  <a href="{$URL_PUBLIC}/conecta/boletin/"><b>esta direcci&oacute;n</b></a>.
                <br/><br/><br/>
                <div align="center"><b>{$smarty.const.SITE_FULLNAME}</b> <br/> {$smarty.const.SITE_DESCRIPTION}</div>

                <br/>
                </td>
            </tr>


        </tbody>
    </table>
</div>

{if $smarty.request.action=='send'}
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
{/if}
<div class="wrapper-content">
    <table border="0" cellpadding="0" cellspacing="0" width="765">
        <tbody>
            <tr>
                <td>
                    <a href="{$smarty.const.SITE_URL}" target="_blank">
                        <img src="{$smarty.const.SITE_URL}/themes/nuevatribuna/images/logos/nuevatribuna-header.png" border="0" alt="Boletín de {$smarty.const.SITE_FULLNAME}" /></a>
                    <br />
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin: 0px 0px 4px; padding-top: 10px; color:#980101; font-size: 12px; font-weight: bold; font-family: Arial;">
                        Noticias. {$current_date}.</div>
                </td>
            </tr>
            <tr>
                <td>
                    <br/>
                    Hola,
                    &eacute;stas son las &uacute;ltimas noticias y art&iacute;culos de opini&oacute;n de hoy. Te recordamos que puedes encontrar &eacute;stas noticias y muchas más visitando la web
                    de {$smarty.const.SITE_FULLNAME}
                    <br /><br />
                    Portadas Nueva Tribuna:<br />
                    <a href="{$smarty.const.SITE_URL}" style="color:#980101;">Inicio </a>|
                    {section loop=$inmenu_categorys name="c"}
                        {if $inmenu_categorys[c]->inmenu eq 1}
                            <a href="{$smarty.const.SITE_URL}seccion/{$inmenu_categorys[c]->name}" style="color:#980101;">
                                {if $inmenu_categorys[c]->name == 'opinion'}
                                    Opinión
                                {else}
                                    {$inmenu_categorys[c]->title}
                                {/if}
                            </a>
                            {if !$smarty.section.c.last} | {/if}
                        {/if}
                    {/section}
                    <br /><br />
                </td>
            </tr>

            {if count($data->articles)>0}
                <tr>
                    <td style="font-family:Arial; font-size:12px; color:#000000;background:#ddd;padding:3px;border-bottom:1px solid #000;">
                        <div style="margin-left: 5px"><strong>NOTICIAS</strong></div>
                    </td>
                </tr>
                {section name=n loop=$data->articles}
                <tr>
                    <td style="font-size:15px;color:#980101;font-weight:normal;padding:5px;padding-left:10px;border-top:1px solid #ddd;">
                        <a href="{$URL_PUBLIC}/
                           {generate_uri    content_type='articleNewsletter'
                                            id=$data->articles[n]->pk_content
                                            date=$data->articles[n]->date
                                            category_name=$data->articles[n]->cat
                                            title=''}/" style="color: #354b1d; text-decoration:none !important">
                            <span style="color:Black">{$data->articles[n]->title}</span></a>
                    </td>
                </tr>
                {/section}

                <tr>
                    <td>&nbsp;</td>
                </tr>
            {/if}

            {if count($data->opinions)>0}
                <tr>
                    <td style="font-family:Arial; font-size:12px; color:#000000;background:#ddd;padding:3px;border-bottom:1px solid #000;">
                        <div style="margin-left: 5px"><strong>ARTÍCULOS DE OPINIÓN</strong></div>
                    </td>
                </tr>
                {section name=o loop=$data->opinions}
                <tr>
                    <td style="font-size:15px;color:#333333;font-weight:normal;padding:5px;padding-left:10px;border-top:1px solid #ddd;">
                    <a href="{$URL_PUBLIC}/seccion/opinion/#{$data->opinions[o]->pk_content}" style="color:#354b1d; text-decoration:none !important">
                        <b style="color:#980101;">&middot; {$data->opinions[o]->author}</b>: <span style="color:Black">{$data->opinions[o]->title}</span></a>
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
                <td style="font-family:Arial; font-size:12px; color:#000000;background:#ddd;padding:3px;border-bottom:1px solid #354b1d;">
                    <div style="margin-left: 5px"><strong>MODIFICAR / CANCELAR SUSCRIPCI&Oacute;N</strong></div>
                </td>
            </tr>

            <tr>
                <td style=" font-size:12px; color:#333333; font-weight: normal; padding: 15px; padding-left: 10px; border-top: 1px solid #a0a0a0; background: #efefef;">
                Est&aacute; recibiendo este bolet&iacute;n tras solicitar este servicio a trav&eacute;s de la web de {$smarty.const.SITE_FULLNAME}. En cualquier momento puede modificar o cancelar su suscripci&oacute;n
                accediendo a  <a href="{$smarty.const.SITE_URL}newsletter/" style="text-decoration:none !important; color:#980101;"><b>esta direcci&oacute;n</b></a>.
                <br/><br/><br/>
                <div align="center"><a href="{$smarty.const.SITE_URL}" style="color:#980101;">{$smarty.const.SITE_FULLNAME}</a> <br/> {$smarty.const.SITE_DESCRIPTION}</div>

                <br/>
                </td>
            </tr>


        </tbody>
    </table>
</div>

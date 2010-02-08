{if $smarty.request.action=='send'}
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
{/if}
<div style="font-size: 11px; font-family: Arial;"> 

<table border="0" cellpadding="0" cellspacing="0" width="765"> 
<tbody>
    <tr> 
        <td bgcolor="#014687">                        
            <a href="http://www.xornal.com/" target="_blank">
                {if $smarty.request.action=='send'}
                    <img src="cid:logo-cid" border="0" height="60" width="300" alt="" /></a>
                {else}
                    <img src="{$URL_PUBLIC}/themes/xornal/images/xornal-boletin.jpg" border="0" alt="Boletín de Xornal de Galicia" /></a>
                {/if}                
            <br />
        </td>
    </tr>
    <tr>
        <td>
            <div style="margin: 0px 0px 4px; padding-top: 10px; color:#014687; font-size: 18px; font-weight: bold; font-family: Arial;">
                BOLET&Iacute;N {$current_date}</div>
        </td>
    </tr>
    <tr>
        <td>
            <br/>
            Hola ###DESTINATARIO###. Estas son las noticias destacadas publicadas hoy en
            <a href="http://www.xornal.com">Xornal de Galicia</a>:
            <br /><br />
        </td>
    </tr>

    {if count($data->articles)>0}
        <tr>
            <td style="background:#c0c0f0;padding:3px;font-size:10px;border-bottom:1px solid #505050;">
                <div style="margin-left: 10px">INFORMACI&Oacute;N</div>
            </td>
        </tr>
        
        {section name=n loop=$data->articles}
        <tr>
            <td style="font-size:15px;color:#333333;font-weight:normal;padding:5px;padding-left:10px;border-top:1px solid #a0a0a0;">                
                <a href="{$URL_PUBLIC}{$data->articles[n]->permalink}" style="color: #202050">
                    <b>&middot; {$data->articles[n]->category_name}</b>: {$data->articles[n]->title}</a>
            </td>
        </tr>
        {/section}    
        
        <tr>
            <td>&nbsp;</td>
        </tr>
    {/if}
    
    {if count($data->opinions)>0}    
        <tr>
            <td style="background:#c0c0f0;padding:3px;font-size:10px;border-bottom:1px solid #505050;">
                <div style="margin-left:10px">OPINI&Oacute;N</div>
            </td>
        </tr>
        
        {section name=o loop=$data->opinions}
        <tr>
            <td style="font-size:15px;color:#333333;font-weight:normal;padding:5px;padding-left:10px;border-top:1px solid #a0a0a0;">
            <a href="{$URL_PUBLIC}{$data->opinions[o]->permalink}" style="color:#202050">
                <b>&middot; {$data->opinions[o]->author}</b>: {$data->opinions[o]->title}</a>
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
        <td style="background:#c0c0f0;padding:3px;font-size:10px;border-bottom:1px solid #505050;">
            <div style="margin-left: 10px"><a href="{$URL_PUBLIC}/conecta/boletin/">MODIFICAR / CANCELAR SUSCRIPCI&Oacute;N</a></div>
        </td>
    </tr>
    
    <tr>
        <td style=" font-size:12px; color:#333333; font-weight: normal; padding: 15px; padding-left: 10px; border-top: 1px solid #a0a0a0; background: #d0d0d0;">
	Recibe el presente bolet&iacute;n tras solicitar este servicio a trav&eacute;s de la web de <a href="http://www.xornal.com"><b>XORNAL.COM</b></a>. Puede en todo momento modificar o cancelar su suscripci&oacute;n accediendo a  <a href="{$URL_PUBLIC}/conecta/boletin/"><b>esta direcci&oacute;n</b></a>.
        </td>
    </tr>
    
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td align="center">
            <b>Xornal de Galicia:</b> Informaci&oacute;n con criterio al servicio de Galicia
        </td>
    </tr>
	
</tbody>
</table>
</div>




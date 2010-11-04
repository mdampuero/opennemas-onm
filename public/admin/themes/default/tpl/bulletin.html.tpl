{* Cabecera boletín *}
<p align="left"><a name="arriba"></a></p>
<p align="left"><font color="#000066" face="Verdana, Arial, Helvetica, sans-serif" size="3">
    {if $ACTION=='send' || $ACTION=='step5'}
        {*<img src="cid:logo-cid" border="0" height="107" width="468" alt="">*}
        <img src="{$smarty.const.URL}../media/cronicas_de_la_emigracion.jpg" border="0" height="107" width="468" alt="">
    {else}
        <img src="{$smarty.const.URL}../media/cronicas_de_la_emigracion.jpg" border="0" height="107" width="468" alt="">
    {/if}
</font>
</p>
<p align="left">
    <font color="#ffffff" face="Verdana, Arial, Helvetica, sans-serif" size="3">
    <font color="#000066">Servicio digital de información y documentación para los españoles en el mundo<br />
    facilitado por el Grupo de Comunicación de Galicia en el Mundo S.L.</font></font>
</p>
<hr align="left" width="100%">
<p>&nbsp;</p>

{* Índice noticias *}
<p align="left">
    <strong><font style="font-size: 17pt;color:#000099;font-family: Arial">NOTICIAS</font></strong><br />
</p>
{section name=n loop=$data->news}
    {math x=$smarty.section.n.index y=1 equation="x+y" assign="indice"}
    <p align="left">
        {if !empty($data->news[n]->pk_content)}
            <a href="{$smarty.const.URL_PUBLIC}article.php?article_id={$data->news[n]->pk_content}&action=read&category={get_category id=$data->news[n]->pk_content}">
                <font color="#000066" face="Georgia" style="font-size: 13.5pt;color:#000066;">
		    {$data->news[n]->titulo|base64decode}</font></a>
        {else}
            <font color="#000066" face="Georgia" style="font-size: 13.5pt;color:#000066;">
                {$data->news[n]->titulo|base64decode}</font>
        {/if}<br />
        {if !empty($data->news[n]->agencia)}
	<font style="font-size: 8.5pt; color: #000066;text-transform: uppercase" face="Verdana">{$data->news[n]->agencia|base64decode}.</font>
	{/if}
	<font style="font-size: 8pt; color: #0084DC;font-weight: bold" face="Verdana">{$data->news[n]->subtitulo|base64decode}</font>
		<a style="text-decoration: none" href="{$smarty.const.URL_PUBLIC}article.php?article_id={$data->news[n]->pk_content}&amp;action=read&amp;category={get_category id=$data->news[n]->pk_content}"><font face="Verdana" style="font-size: 6pt; color: #000000;">(ir a información)</font></a>
    </p>
{/section}

<br />
<p style="margin-bottom: 12pt;"></p>
{* Cabecera Opinión *}
{if count($data->opinions) > 0}
<p align="left">
    <strong><font style="font-size: 17pt;color:#990000;font-family: Arial">OPINI&Oacute;N</font></strong><br />
</p>
{/if}

{* Opiniones *}
{section name=o loop=$data->opinions}
    {math x=$smarty.section.o.index y=1 equation="x+y" assign="indice"}
    <p align="left">
        {if !empty($data->opinions[o]->pk_content)}
            <a href="{$smarty.const.URL_PUBLIC}opinion.php?opinion_id={$data->opinions[o]->pk_content}&amp;action=read">
		<font face="Verdana" style="text-decoration: underline;font-style:italic;font-size: 13.5pt;color:#660000;">
                {$data->opinions[o]->titulo|base64decode}</font></a>
        {else}
            <font face="Verdana" style="text-decoration: underline;font-style:italic;font-size: 13.5pt;color:#660000;">
                {$data->opinions[o]->titulo|base64decode}</font>
        {/if}<br />
	<font style="font-size: 8pt; color: #ff0000;" face="Verdana">{$data->opinions[o]->agencia|base64decode}</font><br />
    </p>
{/section}

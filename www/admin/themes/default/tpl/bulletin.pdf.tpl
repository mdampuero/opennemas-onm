{* Cabecera boletín *}

<br />
<p align="left"><font color="#000066" face="Verdana, Arial, Helvetica, sans-serif" size="3">
    <img src="{$smarty.const.URL}../media/cronicas_de_la_emigracion.jpg" border="0" height="107" width="468" alt=""></font>
</p>
<p align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="#000066">Servicio digital de información y documentación para los españoles en el mundo facilitado por el Grupo de Comunicación de Galicia en el Mundo S.A.</font>
</p>
<br /><br /><br /><br />

{if count($data->news) > 0}
<p align="left"><strong><font color="#000066" face="Arial, Helvetica, sans-serif" size="5">NOTICIAS</font></strong></p>
<br /><br />
{/if}

{* Noticias completas *}
{section name=nn loop=$data->news}
    {math x=$smarty.section.nn.index y=1 equation="x+y" assign="indice"}
    <p align="left"><font color="#000066" face="Georgia, Times New Roman, Times, serif" size="+1">{if $data->news[nn]->pk_content}{$indice|string_format:"%02s"} {$data->news[nn]->titulo|base64decode}{else}{$indice|string_format:"%02s"} {$data->news[nn]->titulo|base64decode}{/if}</font><br /><font color="#336699" face="Verdana, Arial, Helvetica, sans-serif" size="1">{$data->news[nn]->agencia|base64decode}</font></p>
    <p align="left"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif">{$data->news[nn]->descripcion|base64decode}</font> &nbsp; {if !empty($data->news[nn]->pk_content)}<a href="{$smarty.const.URL_PUBLIC}article.php?article_id={$data->news[nn]->pk_content}&action=read&category={get_category id=$data->news[nn]->pk_content}">» Leer más</a>{/if}</p>

    <p>&nbsp;</p>
{/section}

<p>&nbsp;</p>

{if count($data->opinions) > 0}
<br newpage="newpage" />
<p align="left"><strong><font color="#990000" face="Arial, Helvetica, sans-serif" size="5">OPINIÓN</font></strong></p>
<br /><br />
{/if}

{* Opiniones completas *}
{section name=oo loop=$data->opinions}
    {math x=$smarty.section.oo.index y=1 equation="x+y" assign="indice"}
    <p align="left"><em><font color="#660000" face="Times New Roman, Times, serif" size="+1">{if $data->opinions[oo]->pk_content != 0}{$indice|string_format:"%02s"} {$data->opinions[oo]->titulo|base64decode}{else}{$indice|string_format:"%02s"} {$data->opinions[oo]->titulo|base64decode}{/if}</font></em><br /><font color="#ff0000" face="Verdana, Arial, Helvetica, sans-serif" size="1">{$data->opinions[oo]->agencia|base64decode}</font></p>
    <p align="left"><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif">{$data->opinions[oo]->descripcion|base64decode}</font> &nbsp; {if !empty($data->opinions[oo]->pk_content)}<a href="{$smarty.const.URL_PUBLIC}article.php?article_id={$data->opinions[oo]->pk_content}&action=read&category={get_category id=$data->opinions[oo]->pk_content}">» Leer más</a>{/if}</p>

    <p>&nbsp;</p>
{/section}
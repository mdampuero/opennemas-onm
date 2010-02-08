{include file="pc_header.tpl"}

{* LISTADO ******************************************************************* *}

<ul class="tabs">
    <li><a href="pc_hemeroteca.php?action=list&mytype=pc_photo&category=1" {if $mytype=='pc_photo'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Fotografias</a></li>
    <li><a href="pc_hemeroteca.php?action=list&mytype=pc_video&category=3" {if $mytype=='pc_video'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Videos</a></li>
    <li><a href="pc_hemeroteca.php?action=list&mytype=pc_letter&category=5" {if $mytype=='pc_letter'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Cartas</a></li>
    <li><a href="pc_hemeroteca.php?action=list&mytype=pc_opinion&category=6" {if $mytype=='pc_opinion'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Opinion</a></li>
    <li><a href="pc_hemeroteca.php?action=list&mytype=pc_poll&category=1" {if $mytype=='pc_poll'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Enquisas</a></li>
</ul>
<br /> <br />
<ul style="margin-left:30px;" class="tabs2">
    {section name=as loop=$allcategorys}
        <li>
            {assign var=ca value=`$allcategorys[as]->pk_content_category`}
            <a href="pc_hemeroteca.php?action=list&mytype={$mytype}&category={$ca}" {if $category==$ca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} {/if} >{$allcategorys[as]->title}</a>
        </li>
    {/section}
</ul> 
<br/><br/>

{include file="pc_botonera_up.tpl"}
 
{if !isset($smarty.post.action) || $smarty.post.action eq "list"}
    <div id="{$category}">
        <table border="0" style="width:99%;" cellspacing="10">
            <tr align='left'>
                <th nowrap>Plan Conecta:
                    {if $mytype=='pc_photo'} Fotografías {elseif $mytype=='pc_video'} Videos
                        {elseif $mytype=='pc_letter'} Cartas al director
                        {elseif $mytype=='pc_opinion'} Opinion
                        {elseif $mytype=='pc_poll'} Enquisas {/if}
                </th>
            </tr>
        </table>
        <table class="adminlist" >
            <tr>
                <th></th>
                <th class="title">Ver</th>
                <th class="title">Título</th>
                <th align="center">Fecha</th>
                <th class="title">Autor</th>
                <th class="title">IP</th>
                <th align="center">Publicar</th>
                <th align="center">Recuperar</th>
                <th align="center">Modificar</th>
                <th align="center">Eliminar</th>
            </tr>
            {section name=c loop=$contents}
		<tr {cycle values="class=row0,class=row1"} >	
                    <td style="text-align: left;font-size: 11px;width:2%;">
                    	<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$contents[c]->id}"  style="cursor:pointer;" >
                    </td>
                    <td style="padding:10px;font-size: 11px;width:40px;" >
                          {if $mytype=='pc_photo'}  <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'{$MEDIA_CONECTA_WEB}{$contents[c]->path_file}\' style=\'max-width:600px;\' > ', SHADOW, true, ABOVE, true, WIDTH, 600)" />
                                {elseif $mytype=='pc_video'}  <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'http://i4.ytimg.com/vi/{$contents[c]->code}/default.jpg\'> ', SHADOW, true, ABOVE, true,  WIDTH, 150)" />
                                {elseif $mytype=='pc_letter'} <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('{$contents[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|escape:'html'}', SHADOW, true, ABOVE, true,  WIDTH, 600)" />
                                {elseif $mytype=='pc_opinion'} <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('{$contents[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|escape:'html'}', SHADOW, true, ABOVE, true,  WIDTH, 600)" />
                                {elseif $mytype=='pc_poll'}
                          {/if}
                    </td>
                    <td style="padding:10px;font-size: 11px;width:30%;">
			{$contents[c]->title|clearslash}
                    </td>
                    <td style="padding:1px;width:10%;font-size: 11px;" align="center">
			{$contents[c]->created}
                    </td>
                    <td style="padding:10px;font-size: 11px;width:10%;">
                        {assign var='id_author' value=$contents[c]->fk_user}
                        {$conecta_users[$id_author]->nick}
                    </td>
                    <td style="padding:10px;font-size: 11px;width:10%;">
                            {$contents[c]->ip}
                    </td>
                    <td style="padding:10px;font-size: 11px;width:5%;" align="center">
                            {if $contents[c]->available == 1}
                                <a href="?id={$contents[c]->id}&amp;action=change_available&amp;status=0&amp;mytype={$mytype}&amp;category={$category}" title="Publicado">
                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                <a href="?id={$contents[c]->id}&amp;action=change_available&amp;status=1&amp;mytype={$mytype}&amp;category={$category}" title="Pendiente">
                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                    </td>
                    <td style="padding:1px;width:10%;font-size: 11px;" align="center">
                            <a href="?id={$contents[c]->id}&amp;action=change_status&amp;status=0&amp;mytype={$mytype}&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Recuperar">
                            <img src="{php}echo($this->image_dir);{/php}archive_no2.png" border="0" alt="Recuperar" /></a>
                    </td>
                    <td style="padding:10px;font-size: 11px;width:6%;" align="center">
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$contents[c]->id}');" title="Modificar">
                                    <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                    </td>
                    <td style="padding:10px;font-size: 11px;width:6%;" align="center">
                            <a href="#" onClick="javascript:confirmar(this, '{$contents[c]->id}');" title="Eliminar">
                                    <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                    </td>
		</tr>
		
            {sectionelse}
            <tr>
                    <td align="center" colspan=8><br><br><p><h2><b>Ningun elemento en hemeroteca</b></h2></p><br><br></td>
            </tr>
            {/section}
        </table>

        <table>
            <tr>
                <td  align="center">{$paginacion->links}</td>
            </tr>
        </table>
    </div>

{/if}


{include file="footer.tpl"}
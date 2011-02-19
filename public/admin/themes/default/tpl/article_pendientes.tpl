
{* LISTADO ******************************************************************* *}
{if !isset($smarty.post.action) || $smarty.post.action eq "list_pendientes"}
    <ul class="tabs2" style="margin-bottom: 28px;">
        <li>
            <a href="article.php?action=list_pendientes&category=todos" id="link_todos"  {if $category=='todos'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>TODOS</font></a>
        </li>
        {acl hasCategoryAccess=20}
        <li>
            <a href="article.php?action=list_pendientes&category=20" id='link_unknown' {if $category=='20'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>UNKNOWN</font></a>
        </li>
        {/acl}
         <script type="text/javascript">
                // <![CDATA[
                    {literal}
                          Event.observe($('link_todos'), 'mouseover', function(event) {
                             $('menu_subcats').setOpacity(0);
                             e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);

                            });
                             Event.observe($('link_hunknown'), 'mouseover', function(event) {
                                $('menu_subcats').setOpacity(0);
                                e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);
                            });
                    {/literal}
                // ]]>
            </script>
        {include file="menu_categorys.tpl" home="article.php?action=list_pendientes"}
    </ul>

    <br style="clear: both;" />

    {if $smarty.get.alert eq 'ok'}
        <script type="text/javascript" language="javascript">
            {literal}
                alert('{/literal}{$smarty.get.msg}{literal}');
            {/literal}
        </script>
    {/if}
    {include file="botonera_up.tpl"}


    <div id="{$category}">
        <table class="adminheading">
            <tr>
                <td><strong>Noticias Pendientes</strong><span style="font-size: 10px;"><em>(estos articulos <b>NO</b> est&aacute;n aceptadas por lo que no estar&aacute;n inclu&iacute;dos en el almac&eacute;n de noticias. Ac&eacute;ptelos para poder publicarlos)</em></span></td>
                <td align='right'>Ir a secci&oacute;n:
                    <select name="category" id="category" class="" onChange="javascript:location.href='article.php?action=list_pendientes&category='+this.options[this.selectedIndex].value;">
                        {if $category eq "todos"}
                           <option value="todos" selected="selected" name="{$allcategorys[as]->title}" >TODOS</option>
                        {else}
                           <option value="" selected="selected">Lista de Categorias</option>
                           <option value="todos" name="{$allcategorys[as]->title}" >TODOS</option>
                        {/if}
                        <option value="20" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >UNKNOWN</option>
                        <option value="opinion" {if $category eq 'opinion'}selected{/if} name="{$allcategorys[as]->title}" >OPINION</option>
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if $article->category eq $allcategorys[as]->pk_content_category}selected="selected"{/if} name="{$allcategorys[as]->title}">{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                                <option value="{$subcat[as][su]->pk_content_category}" {if $article->category  eq $subcat[as][su]->pk_content_category} selected="selected"{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                            {/section}
                        {/section}
                    </select>
                </td>
            </tr>
        </table>
        <table class="adminlist">
            <thead>
                <th style="width:50px;"></th>
                <th align="left" style="padding:5px;font-size: 11px;"><img src="themes/default/images/newsletter/editar.gif" border="0">Título</th>
                {if $category eq 'todos' || $category eq 'opinion'}
                    <th align="left" style="width:170px;"> <img src="themes/default/images/newsletter/editar.gif" border="0">Secci&oacute;n</th>
                    <th  align="left" style="width:70px;">Núm Pág</th>
                {/if}
                <th  align="left" style="width:230px;"><img src="themes/default/images/newsletter/editar.gif" border="0">Agencia/Autor</th>
                <th align="center" style="width:80px;">Creado</th>
                {*  <th align="center" style="padding:5px;font-size: 11px;width:50px;">Publisher</th>*}
                <th align="center" style="width:50px;">Last Editor</th>
                <th align="center" style="width:40px;">{if $category!=20}Aprobar{/if}</th>
                <th align="center" style="width:40px;">Editar</th>
                <th align="center" style="width:40px;">Eliminar</th>
            </thead>
            <input type="hidden"  name="user_name"  id="user_name" value="{$smarty.session.username}">
            {if $articles}
            {section name=c loop=$articles}
                <tr {cycle values="class=row1,class=row0"} />
                    <td>
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$articles[c]->id}"  style="cursor:pointer;" />
                        <input type="hidden"  name="permit_{$articles[c]->id}"  id="permit_{$articles[c]->id}"     {if $articles[c]->category !=20} value="1" {else} value="0" {/if}  />
                        {if isset($articles[c]->paper_page)} <img align="absmiddle" src="themes/default/images/xml_24.png" border="0" alt="De ImportXML" /> {/if}
                    </td>
                    <td style="padding:5px;font-size: 11px;">
                        <span style="cursor:pointer;" title="title" id="title_{$articles[c]->id}" name="{$articles[c]->id}" >{$articles[c]->title|clearslash} </span>
                        <script type="text/javascript">
                            // <![CDATA[
                                new Editable('title_{$articles[c]->id}','input');
                            // ]]>
                        </script>
                    </td>
                    {if $category eq 'todos'}
                        <td style="padding:5px;font-size: 11px;">
                              <span title="category" old_cat="{$articles[c]->category}" name="{$articles[c]->id}" id="cat_{$articles[c]->id}" {if $articles[c]->category eq 20} style="padding:4px;background-color:#FFA6A6;cursor:pointer;"{else} style="cursor:pointer;"{/if} > {$articles[c]->category_name|upper|clearslash}
                              </span>
                              <script type="text/javascript">
                                // <![CDATA[
                                      new Editable('cat_{$articles[c]->id}','select');
                                // ]]>
                              </script>
                        </td>
                        <td  style="padding:5px;font-size: 11px;">
                              {if $articles[c]->category neq 20}  {$articles[c]->paper_page} - (pos: {$articles[c]->position}) {/if}
                        </td>
                    {/if}
                    <td style="padding:5px;font-size: 11px;">
                        <span style="cursor:pointer;" title="agency" id="agency_{$articles[c]->id}" name="{$articles[c]->id}" >{$articles[c]->agency} </span>
                        <script type="text/javascript">
                            // <![CDATA[
                               new Editable('agency_{$articles[c]->id}','input');
                            // ]]>
                        </script>
                    </td>
                    <td align="center" style="padding:5px;font-size: 11px;">
                        {$articles[c]->created}
                    </td>
                    {*	<td align="center" style="padding:5px;font-size: 11px;">
                             {$art_publishers[c]}
                    </td> *}
                    <td align="center" style="padding:5px;font-size: 11px;">
                        <span style="cursor:pointer;" title="editor" id="editor_{$articles[c]->id}">{$art_editors[c]}</span>
                    </td>
                    <td style="padding:5px" align="center">
                        {if $category!=20 && $articles[c]->category !=20}
                            <a href="?id={$articles[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                        {/if}
                    </td>
                    <td style="padding:5px;" align="center">
                        <a style="cursor:pointer" onClick="javascript:enviar(this, '_self', 'read', '{$articles[c]->id}');" title="Modificar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
                    </td>
                    <td style="padding:5px;" align="center">
                        <a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
                            <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                    </td>
                </tr>
            {/section}
        {else}
            {if $category neq 'opinion'}
                <tr>
                    <td align="center" colspan="10" style="height:180px;" >Ninguna noticia pendiente de publicar.</td>
                </tr>
            {else}
                <tr>
                    <td align="center" colspan="10" style="height:180px;" >No hai ningún artículo pendiente de publicar.</td>
                </tr>
            {/if}
        {/if}
        {if $opinions}
            {if $category eq 'todos'}
                <tr>
                    <td align="left" colspan="4"><br><br><h2><b>Opiniones:</b></h2></td>
                </tr>
            {/if}
            {section name=c loop=$opinions}
                <tr {cycle values="class=row0,class=row1"}   >
                    <td style="font-size: 11px;">
                          <input type="checkbox" class="minput"  id="selected_opin_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;">
                          <input type="hidden"  name="permit_{$opinions[c]->id}"  id="permit_{$opinions[c]->id}" value="1"    />
                          {if isset($opinions[c]->paper_page)} <img align="absmiddle" src="themes/default/images/xml_24.png" border="0" alt="De ImportXML" /> {/if}
                    </td>
                    <td style="padding:4px;font-size: 11px;" onClick="javascript:document.getElementById('selected_opin_{$smarty.section.c.iteration}').click();">
                        <span style="cursor:pointer;"  title="opinion" id="op_{$opinions[c]->id}" name="{$opinions[c]->id}" >{$opinions[c]->title|clearslash} </span>
                        <script type="text/javascript">
                            // <![CDATA[
                                new Editable('op_{$opinions[c]->id}','input');
                            // ]]>
                        </script>
                    </td>
                  <td  style="padding:10px;font-size: 11px;">
                            OPINION {if $opinions[c]->type_opinion eq '0'} Autor{elseif $opinions[c]->type_opinion eq '0'}Director{else}Editorial{/if}
                    </td>
                    <td  style="padding:10px;font-size: 11px;">

                    </td>
                     <td  style="padding:10px;font-size: 11px;">
                           <span  title="author" old_author="{$opinions[c]->fk_author}" name="{$opinions[c]->id}" id="author_{$opinions[c]->id}"  {if $opin_names[c]} style="cursor:pointer;"> {$opin_names[c]}  {else} style="padding:4px;background-color:#FFA6A6;cursor:pointer;" >Unknown  {/if}
                              </span>
                  <script type="text/javascript">
                    // <![CDATA[
                            new Editable('author_{$opinions[c]->id}','select');
                    // ]]>
                    </script>
                    </td>
                    <td align="center" style="padding:10px;font-size: 11px;">
                             {$opinions[c]->created}
                    </td>
                 {*  <td align="center" style="padding:10px;font-size: 11px;">
                             {$opin_publishers[c]}
                    </td> *}
                    <td align="center" style="padding:10px;font-size: 11px;">
                        <span style="cursor:pointer;" title="editor" id="editor_{$opinions[c]->id}">{$opin_editors[c]}</span>
                    </td>

                    <td style="padding:4px;font-size: 11px;" align="center">
                            {if $opinions[c]->content_status == 1}
                                    <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
                                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                    <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
                                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                    </td>
                    <td style="padding:4px;font-size: 11px;;" align="center">
                            <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=read&amp;category={$category}" title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                    </td>
                    <td style="padding:4px;font-size: 11px;" align="center">
                            <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=yesdel&amp;category={$category}"  title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                    </td>
                </tr>
            {/section}
        {/if}

		<tfoot>
			<tr>
				<td colspan="10" class="pagination">
					{$pagination->links}
				</td>
			</tr>
		</tfoot>
        </table>

    </div>
{/if}

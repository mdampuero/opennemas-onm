{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}
    {script_tag src="/editables.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Pending articles{/t} :: {$datos_cat[0]->title}{if empty($datos_cat[0]->title)} {$category|upper} {/if}</h2></div>
        <ul class="old-button">
            {acl isAllowed="ARTICLE_DELETE"}
             <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />{t}Delete all{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="ARTICLE_AVAILABLE"}
            {if $category!=20}
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mavailable', 1);" onmouseover="return escape('<u>P</u>ublicar');" name="submit_mult" value="noFrontpage">
                    <img border="0" src="{$params.IMAGE_DIR}publish.gif" alt="noFrontpage"><br />{t}Publish{/t}
                </a>
            </li>
            {/if}
            {/acl}
            <li class="separator"></li>
            {acl isAllowed="ARTICLE_CREATE"}
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add">
                    <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New article{/t}
                </a>
            </li>
            {/acl}
        </ul>
    </div>
</div>-

<div class="wrapper-content">
        <div id="content-wrapper">

    <ul class="pills" style="margin-bottom:28px;">
        {acl hasCategoryAccess=0}
        <li>
            <a href="article.php?action=list_pendientes&amp;category=todos" id="link_todos"  {if $category=='todos'}class="active"{/if}>TODOS</font></a>
        </li>
        {/acl}
        {acl hasCategoryAccess=20}
        <li>
            <a href="article.php?action=list_pendientes&amp;category=20" id='link_unknown' {if $category=='20'} class="active"{/if}>UNKNOWN</font></a>
        </li>
        {/acl}
        <script type="text/javascript">
        // <![CDATA[
        Event.observe($('link_todos'), 'mouseover', function(event) {
            $('menu_subcats').setOpacity(0);
            e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
        });
        Event.observe($('link_hunknown'), 'mouseover', function(event) {
            $('menu_subcats').setOpacity(0);
            e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
        });
        // ]]>
        </script>
        {include file="menu_categories.tpl" home="article.php?action=list_pendientes"}
    </ul>
    <div id="{$category}">
        <table class="listing-table">
            <thead>
                <th style="width:15px;"><input type="checkbox" id="toggleallcheckbox"></th>
                <th class="left" >
                    <img src="themes/default/images/newsletter/editar.gif" border="0">
                    {t}Title{/t}
                </th>
                {if $category eq 'todos' || $category eq 'opinion'}
                    <th class="center" style="width:100px;">{t}Section{/t} <img src="themes/default/images/newsletter/editar.gif" border="0"></th>
                {/if}
                <th  class="center" style="width:100px;">{t}Author{/t} <img src="themes/default/images/newsletter/editar.gif" border="0"></th>
                <th class="center" style="width:100px;">{t}Created{/t}</th>
                <th class="center" style="width:80px;">{t}Last Editor{/t}</th>
                <th class="right" style="width:60px;">{t}Actions{/t}</th>
            </thead>
            <input type="hidden"  name="user_name"  id="user_name" value="{$smarty.session.username}">
            {if isset($articles)}
            {section name=c loop=$articles}
                <tr {cycle values="class=row1,class=row0"} />
                    <td>
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$articles[c]->id}"  style="cursor:pointer;" />
                        <input type="hidden"  name="permit_{$articles[c]->id}"  id="permit_{$articles[c]->id}"     {if $articles[c]->category !=20} value="1" {else} value="0" {/if}  />
                    </td>
                    <td>
                        {if isset($articles[c]->paper_page) && !empty($articles[c]->paper_page)} <img width="16px" align="absmiddle" src="themes/default/images/xml_24.png" border="0" alt="De ImportXML" /> {/if}
                            <span style="cursor:pointer;" title="title" id="title_{$articles[c]->id}" name="{$articles[c]->id}" >{$articles[c]->title|clearslash} </span>
                        <script type="text/javascript">
                            // <![CDATA[
                                new Editable('title_{$articles[c]->id}','input');
                            // ]]>
                        </script>
                    </td>
                    {if $category eq 'todos'}
                        <td class="center">
                              <span title="category" old_cat="{$articles[c]->category}" name="{$articles[c]->id}" id="cat_{$articles[c]->id}" {if $articles[c]->category eq 20} style="padding:4px;background-color:#FFA6A6;cursor:pointer;"{else} style="cursor:pointer;"{/if} >
                                {if $articles[c]->category_name == 'unknown'}
                                    {t}Unasigned{/t}
                                {else}
                                    {$articles[c]->category_name|upper|clearslash}
                                {/if}
                              </span>
                              <script type="text/javascript">
                                // <![CDATA[
                                      new Editable('cat_{$articles[c]->id}','select');
                                // ]]>
                              </script>
                        </td>
                    {/if}
                    <td >
                        <span style="cursor:pointer;" title="agency" id="agency_{$articles[c]->id}" name="{$articles[c]->id}" >{$articles[c]->agency} </span>
                        <script type="text/javascript">
                            // <![CDATA[
                               new Editable('agency_{$articles[c]->id}','input');
                            // ]]>
                        </script>
                    </td>
                    <td class="center">
                        {$articles[c]->created}
                    </td>
                    {*	<td class="center" >
                             {$art_publishers[c]}
                    </td> *}
                    <td class="center">
                        <span style="cursor:pointer;" title="editor" id="editor_{$articles[c]->id}">{$art_editors[c]}</span>
                    </td>
                    <td class="right">
                        <ul class="action-buttons">
                            {if $category!=20 && $articles[c]->category !=20}
                            <li>
                                <a href="?id={$articles[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                                </a>
                            </li>
                            {/if}
                            <li>
                                <a href="{$smarty.server.PHP_SELF}?action=read&id={$articles[c]->id}" title="{t}Edit{/t}">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="{t}Edit{/t}" /></a>
                            </li>
                            <li>
                                <a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            {sectionelse}
                <tr>
                    <td class="empty" colspan="10">{t}No pending articles to publish.{/t}</td>
                </tr>
            {/section}
            {else}
                {if $category neq 'opinion'}
                    <tr>
                        <td class="empty" colspan="10">{t}No pending opinion to publish.{/t}</td>
                    </tr>
                {else}
                    <tr>
                        <td class="empty" colspan="10">{t}No pending article to publish.{/t}</td>
                    </tr>
                {/if}
            {/if}


            <tfoot>
                <tr>
                    <td colspan="10" class="pagination">
                        {$pagination->links}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

        <br>

        {if $opinions && $category eq 'todos'}
        <table class="adminheading">
            <tr>
                <td><strong>{t}Opinions{/t}</strong></td>
            </tr>
        </table>
        <table class="listing-table">
            <thead>
                <th style="width:15px;"></th>
                <th  class="left" style="width:100px;">{t}Author{/t} <img src="themes/default/images/newsletter/editar.gif" border="0"></th>
                <th align="left" ><img src="themes/default/images/newsletter/editar.gif" border="0">Título</th>
                {if $category eq 'todos' || $category eq 'opinion'}
                    <th class="center" style="width:100px;">{t}Type{/t}</th>
                {/if}
                <th class="center" style="width:100px;">{t}Created{/t}</th>
                <th class="center" style="width:80px;">{t}Last Editor{/t}</th>
                <th class="right" style="width:60px;">{t}Actions{/t}</th>
            </thead>
            <tbody>
                {section name=c loop=$opinions}
                <tr {cycle values="class=row0,class=row1"}   >
                    <td class="left">
                          <input type="checkbox" class="minput"  id="selected_opin_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;">
                          <input type="hidden"  name="permit_{$opinions[c]->id}"  id="permit_{$opinions[c]->id}" value="1"    />
                    </td>
                    <td>
                           <span  title="author" old_author="{$opinions[c]->fk_author}" name="{$opinions[c]->id}" id="author_{$opinions[c]->id}"  {if $opin_names[c]} style="cursor:pointer;"> {$opin_names[c]}  {else} style="padding:4px;background-color:#FFA6A6;cursor:pointer;" >Unknown  {/if}</span>
                        <script type="text/javascript">
                        // <![CDATA[
                                new Editable('author_{$opinions[c]->id}','select');
                        // ]]>
                        </script>
                    </td>
                    <td  onClick="javascript:document.getElementById('selected_opin_{$smarty.section.c.iteration}').click();">
                        {if isset($opinions[c]->paper_page)} <img width="16px" align="absmiddle" src="themes/default/images/xml_24.png" border="0" alt="De ImportXML" /> {/if}
                        <span style="cursor:pointer;"  title="opinion" id="op_{$opinions[c]->id}" name="{$opinions[c]->id}" >{$opinions[c]->title|clearslash} </span>
                        <script type="text/javascript">
                            // <![CDATA[
                                new Editable('op_{$opinions[c]->id}','input');
                            // ]]>
                        </script>
                    </td>
                    <td class="center">
                        {if $opinions[c]->type_opinion eq '0'}Author opinion{elseif $opinions[c]->type_opinion eq '0'}Director opinion{else}Editorial opinion{/if}
                    </td>
                    <td class="center" >
                        {$opinions[c]->created}
                    </td>
                    {*  <td class="center" >
                        {$opin_publishers[c]}
                    </td> *}
                    <td class="center" >
                        <span style="cursor:pointer;" title="editor" id="editor_{$opinions[c]->id}">{$opin_editors[c]}</span>
                    </td>

                    <td  class="right">
                        <ul class="action-buttons">
                            <li>
                                {if $opinions[c]->content_status == 1}
                                <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                                </a>
                                {else}
                                <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                                </a>
                                {/if}
                            </li>
                            <li>
                                <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=read&amp;category={$category}" title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                </a>
                            </li>
                            <li>
                                 <a href="controllers/opinion/opinion.php?id={$opinions[c]->id}&amp;action=yesdel&amp;category={$category}"  title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            {/section}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan=7></td>
                </tr>
            </tfoot>
        </table>
    {/if}

    </div>

            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id|default:""}" />
        </div>
    </form>
</div>
{/block}

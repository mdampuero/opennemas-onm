{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {$titulo_barra} ::
                    {if $category eq 'home' ||  $category eq 'todos'} {$category|upper} {else} {$datos_cat[0]->title} {/if}
                </h2>
            </div>
            <ul class="old-button">
               <li>
                   <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                       <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />Eliminar
                   </a>
               </li>
               <li>
                   <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 2);" name="submit_mult" value="noFrontpage" title="Rechazar">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="Rechazar" alt="Rechazar" ><br />Rechazar
                   </a>
               </li>
               <li>
                   <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Publicar">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
                   </a>
               </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">


        <ul class="pills clearfix">
			<li>
				<a href="{$smarty.server.SCRIPT_NAME}?action=list&category=todos" id="link_todos"  {if $category=='todos'}class="active"{/if}>{t}ALL{/t}</a>
			</li>
			<li>
				<a href="{$smarty.server.SCRIPT_NAME}?action=list&category=home" id="link_home" {if $category=='home'}class="active"{/if}>{t}HOME{/t}</a>
			</li>
			<li>
			   <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=4" id="link_home" {if $category=='4'}class="active"{/if}>{t}OPINION{/t}</a>
			</li>
			<script type="text/javascript">
            //<![CDATA[
				Event.observe($('link_todos'), 'mouseover', function(event) {
					$('menu_subcats').setOpacity(0);
					e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
				});
				Event.observe($('link_home'), 'mouseover', function(event) {
					$('menu_subcats').setOpacity(0);
					e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
				});
			//]]>
            </script>
			{include file="menu_categories.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
		</ul>


		<div class="clearfix">
        {if $category neq "todos"}
            <ul id="tabs">
                <li>
					<a id="pending-tab" href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&comment_status=0">{t}Pending{/t}</a>
                </li>
                <li>
					<a id="published-tab" href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&comment_status=1">{t}Published{/t}</a>
                </li>
                <li>
					<a id="rejected-tab" href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&comment_status=2">{t}Rejected{/t}</a>
                </li>
            </ul>
        {/if}
        </div>

        <script type="text/javascript">
            document.observe('dom:loaded', function() {
                {if $comment_status==0}
                    $('pending-tab').setAttribute('class', 'active-tab');
                {elseif $comment_status==1}
                    $('published-tab').setAttribute('class', 'active-tab');
                    $('pending-tab').setAttribute('class', '');
                {elseif $comment_status==2}
                    $('rejected-tab').setAttribute('class', 'active-tab');
                    $('pending-tab').setAttribute('class', '');
                {/if}
            });
        </script>

		<div id="{$category}">

			<!--<table class="adminheading" style="border-top-left-radius:0px;">
				<tr>
                    {if $comment_status==0}
                        <th>{t}Comments pending for publishing{/t}</th>
                    {elseif $comment_status==1}
                        <th>{t}Comments already published{/t}</th>
                    {else}
                        <th>{t}Comments rejected{/t}</th>
                    {/if}
				</tr>
			</table>-->

			<table class="listing-table">
				<thead>
        			{if count($comments) > 0}
                    <tr>
                        <th  style='width:30px'>
                            <input type="checkbox" id="toggleallcheckbox">
                        </th>
                        <th  style='width:100px;'>{t}Author{/t}</th>
                        <th  style='width:200px;'>{t}Title{/t} - {t}Comment (50 chars){/t}</th>
                        <th style='width:200px;'>{t}Commented on{/t}</th>
                        <th  style='width:6%;' class="center">{t}IP{/t}</th>
                        {if $category eq 'todos' || $category eq 'home'}
                            <th class="center" style="width:5%;">{t}Category{/t}</th>
                        {/if}
                        <th  style='width:110px;' class="center">{t}Date{/t}</th>
                        <th style='width:20px;' class="center">{t}Votes{/t}</th>
                        <th style='width:40px;' class="center">{t}Actions{/t}</th>
				   </tr>
                   {else}
                   <tr>
                        <th>
                            &nbsp;
                        </th>
                   </tr>
    			   {/if}
                </thead>

				<tbody>
					<div class='fisgona' id='fisgona' name='fisgona'></div>
					{* Provisional - comentarios en encuestas en la solapa todos *}

					{section name=c loop=$comments|default:array()}
					<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
						<td >
							<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$comments[c]->id}"  style="cursor:pointer;" >
						</td>
						<td >
							{$comments[c]->author|strip_tags}
							{if preg_match('/@proxymail\.facebook\.com$/i', $comments[c]->email)}
								&lt;<span title="{$comments[c]->email}">{t}from facebook{/t}</span>&gt;
							{else}
								&lt;{$comments[c]->email}&gt;
							{/if}
						</td>
						<td onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
							{if $title}
								<strong>[{$comments[c]->title|strip_tags|clearslash|truncate:30:"..."}]</strong>
							{/if} {$comments[c]->body|strip_tags|clearslash|truncate:50}
						</td>
						 {assign var=type value=$articles[c]->content_type}
						<td >
							<strong>[{$content_types[$type]}]</strong>
							<a style="cursor:pointer;"  onClick="javascript:enviar(this, '_self', 'read', '{$comments[c]->pk_comment}');new Effect.BlindUp('edicion-contenido'); new Effect.BlindDown('article-info'); return false;">
							{$articles[c]->title|strip_tags|clearslash}
							</a>
						</td>
						<td class="center">
							{$comments[c]->ip}
						</td>
						{if $category eq 'todos' || $category eq 'home'}
						<td class="center">
							{$articles[c]->category_name} {if $articles[c]->content_type==4}Opini&oacute;n{/if}
						</td>
						{/if}
						<td class="center">
							{$comments[c]->created}
						</td>
						<td class="center">
							{$votes[c]->value_pos} /  {$votes[c]->value_pos}
						</td>
						<td class="center">
							<ul class="action-buttons">
								<li>
									{if $category eq 'todos' || $comments[c]->content_status eq 0}
										<a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Publicar">
												<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
										<a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Rechazar">
												<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
									{elseif $comments[c]->content_status eq 2}
										<a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Publicar">
											<img border="0" src="{$params.IMAGE_DIR}publish_g.png">
										</a>
									{else}
										<a class="publishing" href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Rechazar">
											<img border="0" src="{$params.IMAGE_DIR}publish_g.png">
										</a>
									{/if}
								</li>
								<li>
									<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$comments[c]->id}');" title="Modificar">
										<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
								</li>
								<li>
									<a href="#" onClick="javascript:confirmar(this, '{$comments[c]->id}');" title="Eliminar">
										<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
								</li>
							</ul>
						</td>
					</tr>

					{sectionelse}
					<tr>
						<td class="empty" colspan=10>
							{t}There is no comments here.{/t}
						</td>
					</tr>
					{/section}
				</tbody>
				<tfoot>
					<tr class="pagination">
						<td colspan="13">
                            {$paginacion->links|default:""}&nbsp;
                        </td>
					</tr>
				</tfoot>

			</table>
		</div>
    </div>

	<input type="hidden" id="action" name="action" value="" />
	<input type="hidden" name="id" id="id" value="{$id|default:""}" />

</form>
{/block}

{extends file="base/admin.tpl"}

{block name="admin_menu"}
<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{$titulo_barra}</h2></div>
        <ul class="old-button">
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mremove', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
					<img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />Eliminar todos
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'mremove', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
					<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />Eliminar
				</a>
			</li>
            <li class="separator"></li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'm_no_in_litter', 0);" name="submit_mult" value="Recuperar" title="Recuperar">
				    <img border="0" src="{$params.IMAGE_DIR}trash_no.png" title="Recuperar" alt="Recuperar"><br />Recuperar
				</a>
			</li>
		</ul>
	</div>
</div>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""} >
{block name="admin_menu"}{/block}
	<div class="wrapper-content">
		<ul class="pills clearfix">
			{*{section name=as loop=$types_content}
				<li>
					 {assign var=ca value=`$types_content[as]`}
					<a href="litter.php?action=list&mytype={$ca}" {if $mytype==$ca}class="active"{/if}>{$types_content[as]}</a>
				</li>
			{/section} *}
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=article" {if $mytype=='article'}class="active"{/if}>{t}Articles{/t}</a></li>
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=opinion" {if $mytype=='opinion'}class="active"{/if}>{t}Opinions{/t}</a></li>
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=advertisement" {if $mytype=='advertisement'}class="active"{/if}>{t}Ads{/t}</a></li>
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=comment" {if $mytype=='comment'}class="active"{/if}>{t}Coments{/t}</a></li>
			<!--<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=album" {if $mytype=='album'}class="active"{/if}>{t}Albums{/t}</a></li>-->
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=photo" {if $mytype=='photo'}class="active"{/if}>{t}Photographies{/t}</a></li>
			<!--<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=video" {if $mytype=='video'}class="active"{/if}>{t}Videos{/t}</a></li>-->
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=attachment" {if $mytype=='attachment'}class="active"{/if}>{t}Files{/t}</a></li>
		</ul>

        <table class="listing-table">

            <thead>
               <tr>
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th class='left'>{t}Title{/t}</th>
                    <th style="width:40px">{t}Section{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="center" style="width:110px;">{t}Date{/t}</th>
                    <th class="center" style="width:20px;">{t}Actions{/t}</th>
               </tr>
            </thead>

            <tbody>
                {section name=c loop=$litterelems}
                <tr>
                    <td >
                        <input type="checkbox" class="minput"  id="selected{$smarty.section.c.iteration}" name="selected_fld[]" value="{$litterelems[c]->id}"  style="cursor:pointer;" onClick="javascript:document.getElementById('selected{$smarty.section.c.iteration}').click();">
                    </td>
                    <td onClick="javascript:document.getElementById('selected{$smarty.section.c.iteration}').click();">
                        {$litterelems[c]->title|clearslash}
                    </td>
                    <td class="center">{$secciones[c]}</td>
                    <td class="center">{$litterelems[c]->views}</td>
                    <td class="center">{$litterelems[c]->created}</td>
                    <td class="center">
                        <ul class="action-buttons">
                            <li>
                               <a href="{$smarty.server.PHP_SELF}?id={$litterelems[c]->id}&amp;action=no_in_litter&amp;&amp;mytype={$mytype}&amp;page={$paginacion->_currentPage}" title="Recuperar">
                                   <img class="portada" src="{$params.IMAGE_DIR}trash_no.png" border="0" alt="{t}Restore{/t}" />
                               </a>
                            </li>
                            <li>
                                <a href="#" onClick="javascript:vaciar(this, '{$litterelems[c]->id}');" title="{t}Delete{/t}">
                                   <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                   </a>
                            </li>
                        </ul>
                    </td>
                </tr>
                {sectionelse}
                <tr >
                    <td class="empty"colspan=6>
                        {t}There is no elements in the trash{/t}
                    </td>
                </tr>
                {/section}
            </tbody>

            <tfoot>
                <tr class="pagination">
                    <td colspan="6">
                        {$paginacion->links}&nbsp;
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}

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
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'm_no_in_litter', 0);" name="submit_mult" value="Recuperar" title="Recuperar">
				    <img border="0" src="{$params.IMAGE_DIR}trash_no.png" title="Recuperar" alt="Recuperar"><br />Recuperar
				</a>
			</li>
			<li>
				<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
					<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
				</button>
			</li>
		</ul>
	</div>
</div>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""} >
{block name="admin_menu"}{/block}
	<div class="wrapper-content">
		<ul class="tabs2 clearfix">
			{*{section name=as loop=$types_content}
				<li>
					 {assign var=ca value=`$types_content[as]`}
					<a href="litter.php?action=list&mytype={$ca}" {if $mytype==$ca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{$types_content[as]}</a>
				</li>
			{/section} *}
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=article" {if $mytype=='article'} style="font-weight:bold; background-color:#BFD9BF" {/if}>{t}Articles{/t}</a></li>
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=opinion" {if $mytype=='opinion'} style="font-weight:bold; background-color:#BFD9BF" {/if}>{t}Opinions{/t}</a></li>
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=advertisement" {if $mytype=='advertisement'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}Ads{/t}</a></li>
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=comment" {if $mytype=='comment'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}Coments{/t}</a></li>
			<!--<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=album" {if $mytype=='album'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}Albums{/t}</a></li>-->
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=photo" {if $mytype=='photo'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}Photographies{/t}</a></li>
			<!--<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=video" {if $mytype=='video'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}Videos{/t}</a></li>-->
			<li><a href="{$smarty.server.PHP_SELF}?action=list&mytype=attachment" {if $mytype=='attachment'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}Files{/t}</a></li>
		</ul>

        <table class="listing-table">

            <thead>
               <tr>
                    <th style="width:2%;"><input type="checkbox" class="minput"></th>
                    <th style="width:75%;" align='left'>{t}Title{/t}</th>
                    <th style="width:5%;">{t}Section{/t}</th>
                    <th style="width:3%;">{t}Views{/t}</th>
                    <th style="width:8%;">{t}Date{/t}</th>
                    <th class="center" style="width:20px;">{t}Actions{/t}</th>
               </tr>
            </thead>

            <tbody>
                {section name=c loop=$litterelems}
                <tr {cycle values="class=row0,class=row1"} style="cursor:pointer;" >
                    <td style="text-align: left;width:20px;">
                        <input type="checkbox" class="minput"  id="selected{$smarty.section.c.iteration}" name="selected_fld[]" value="{$litterelems[c]->id}"  style="cursor:pointer;" onClick="javascript:document.getElementById('selected{$smarty.section.c.iteration}').click();">
                    </td>
                    <td style="text-align: left;width:75%;" onClick="javascript:document.getElementById('selected{$smarty.section.c.iteration}').click();">
                        {$litterelems[c]->title|clearslash}
                    </td>
                    <td style="text-align: center;width:5%;">{$secciones[c]}</td>
                    <td >{$litterelems[c]->views}</td>
                    <td >{$litterelems[c]->created}</td>
                    <td class="right">
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

{extends file="base/admin.tpl"}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
	<div class="top-action-bar">
		<div class="wrapper-content">
            <div class="title"><h2>{t}Opinion Manager :: Author list{/t}</div>
            <ul class="old-button">
                {acl isAllowed="AUTHOR_CREATE"}
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new&page=0" class="admin_add"  accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="{t}New author{/t}" alt="{t}New author{/t}"><br />{t}New author{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="OPINION_ADMIN"}
                <li >
                    <a href="opinion.php?action=list&desde=author" class="admin_add" name="submit_mult" value="Listado Opiniones" title="Listado Opiniones">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Listado Opiniones" alt="Listado Opiniones"><br />{t escape="off"}Go back{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
	</div>

    <div class="wrapper-content">

        <table class="adminheading">
            <tr align="right">
                <th>
                    Seleccione autor:
                    <select name="autores" id="autores" class="" onChange="window.location='{$smarty.server.SCRIPT_NAME}?action=read&id='+this.options[this.selectedIndex].value;">
                        <option> -- </option>
                        {section name=as loop=$authors_list}
                            <option value="{$authors_list[as]->pk_author}" >{$authors_list[as]->name}</option>
                        {/section}
                    </select>
                </th>
            </tr>
        </table>
        <table class="listing-table">
            <thead>
                <tr>
                    <th class="title">{t}Author name{/t}</th>
                    <th class="title">{t}Condition{/t}</th>
                    <th>{t}Blog name{/t}</th>
                    <th>{t}Blog url{/t}</th>
                    <th class="title" style="text-align:center">{t}Photos (#){/t}</th>
                    <th class="title">{t}Actions{/t}</th>
                </tr>
            </thead>
            {section name=c loop=$authors}
                <tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
                    <td style="padding:5px;">
                        {$authors[c]->name}&nbsp;&nbsp;{*if $authors[c]->fk_user != 0}(usuario){/if*}
                    </td>
                    <td>
                        {$authors[c]->condition}
                    </td>
                    <td>
                        {$authors[c]->politics}
                    </td>
                    <td>
                        {$authors[c]->gender}
                    </td>
                    <td style="text-align:center">
                        {$authors[c]->num_photos}
                    </td>
                    <td style="text-align:center;">
						<ul class="action-buttons">
                            {acl isAllowed="AUTHOR_UPDATE"}
							<li>
								<a href="{$smarty.server.PHP_SELF}?action=read&id={$authors[c]->pk_author}" title="Modificar">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" />
								</a>
							</li>
                            {/acl}
                            {acl isAllowed="AUTHOR_DELETE"}
							<li>
								<a href="#" onClick="javascript:confirmar(this, {$authors[c]->pk_author});" title="Eliminar">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" />
								</a>
							</li>
                            {/acl}
						</ul>
                    </td>
                </tr>

            {sectionelse}
                <tr>
                    <td align="center"><b>{t}There is no available authors{/t}</b></td>
                </tr>
            {/section}
            <tfoot>
                <tr class="pagination">
                    <td colspan="6" align="center">
                        {$paginacion->links}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div><!--fin wrapper-content-->
    <input type="hidden" id="action" name="action" value="">
    <input type="hidden" id="id" name="id" value={$id}>
</form>
{/block}

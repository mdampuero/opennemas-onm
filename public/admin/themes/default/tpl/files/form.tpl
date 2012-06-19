{extends file="base/admin.tpl"}


{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager ::{/t} {if $attaches}{t 1=$attaches->title}Editing file "%1"{/t}{else}{t}Creating new file{/t}{/if}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_files}" class="admin_add" value="Cancelar" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

	<form {if !is_null($attaches)} action="{url name=admin_files_update id=$attaches->id}" {else} action="{url name=admin_files_create}" enctype="multipart/form-data"{/if}" method="POST" name="formulario" id="formulario">

		<table class="adminform" width="700">
			<tbody>
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">{t}Title:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="title" name="title" title="Título de la noticia"
							value="{$attaches->title|clearslash}" class="required" size="100" onBlur="javascript:get_metadata(this.value);" />
						<input type="hidden" id="category" name="category" title="Fichero"
							value="{$attaches->category}" />
							<input type="hidden" id="fich" name="fich" title="Fichero"
							value="{$attaches->pk_attachment}" />

					</td>
				</tr>

                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">{t}Metadata:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input type="text" id="metadata" name="metadata" title="path"
                            value="{$attaches->metadata|clearslash}" class="required" size="100" />
                    </td>
                </tr>

                {if !is_null($attaches)}
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">{t}Path:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="path" name="path" title="path" readonly
							value="{$attaches->path|clearslash}" class="required" size="100" />
					</td>
				</tr>
                {else}
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">{t}Path:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input type="file" id="path" name="path" value="" class="required" />
                    </td>
                </tr>
                {/if}

                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">{t}Category:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <select name="category" id="category" class="validate-section">
                            <option value="20" data-name="{t}Unknown{/t}" {if !isset($category)}selected{/if}>{t}Unknown{/t}</option>
                            {section name=as loop=$allcategorys}
                                {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
                                {if (($category == $allcategorys[as]->pk_content_category))
                                || $attaches->category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                                {section name=su loop=$subcat[as]}
                                    {if $subcat[as][su]->internal_category eq 1}
                                        <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                                        {if $category eq $subcat[as][su]->pk_content_category || $attaches->category eq $subcat[as][su]->pk_content_category}selected{/if} >&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                    {/if}
                                {/section}
                                {/acl}
                            {/section}
                        </select>
                    </td>
                </tr>

				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">{t}Descripcion:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<textarea id="description" name="description" title="path"
							class="required">{$attaches->description|clearslash}</textarea>
					</td>
				</tr>
                {if !is_null($attaches)}
                <input type="hidden" name="id" id="id" value="{$attaches->id|default:""}" />
                {/if}

			</tbody>
		</table>

        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Save{/t}</button>
            </div>
        </div>
    </form>
</div>
{/block}

{extends file="base/admin.tpl"}


{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager ::{/t} {t 1=$attaches->title}Editing file "%1"{/t}</h2></div>
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

	<form action="{if !is_null($attaches)}{url name=admin_files_update id=$attaches->id}{else}{url name=admin_files_create}{/if}" method="POST" name="formulario" id="formulario">

		<table border="0" cellpadding="0" cellspacing="0" class="adminform" width="700">
			<tbody>
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">{t}Title:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="title" name="title" title="Título de la noticia"
							value="{$attaches->title|clearslash}" class="required" size="100" />
						<input type="hidden" id="category" name="category" title="Fichero"
							value="{$attaches->category}" />
							<input type="hidden" id="fich" name="fich" title="Fichero"
							value="{$attaches->pk_attachment}" />

					</td>
				</tr>
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">{t}Path:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="path" name="path" title="path" readonly
							value="{$attaches->path|clearslash}" class="required" size="100" />
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
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">{t}Descripcion:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<textarea id="description" name="description" title="path"
							class="required">{$attaches->description|clearslash}</textarea>
					</td>
				</tr>

			</tbody>
		</table>

        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Save{/t}</button>
            </div>
        </div>

        <input type="hidden" name="id" id="id" value="{$attaches->id|default:""}" />
    </form>
</div>
{/block}

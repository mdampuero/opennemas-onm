{extends file="base/admin.tpl"}


{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager :: General statistics{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=list" class="admin_add" value="Cancelar" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <th>&nbsp;</th>
            </tr>
        </table>
	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

		<table border="0" cellpadding="0" cellspacing="0" class="adminform" width="700">
			<tbody>
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">T&iacute;tulo:</label>
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
						<label for="title">Ruta:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="path" name="path" title="path" readonly
							value="{$attaches->path|clearslash}" class="required" size="100" />
					</td>
				</tr>

				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">Metadata:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="metadata" name="metadata" title="path"
							value="{$attaches->metadata|clearslash}" class="required" size="100" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="title">Descripcion:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="description" name="description" title="path"
							value="{$attaches->description|clearslash}" class="required" size="100" />
					</td>
				</tr>

			</tbody>
		</table>

        <div class="action-bar clearfix">
            <div class="right">
                <a href="#" class="onm-button red" onClick="javascript:enviar(this, '_self', 'update', '{$attaches->pk_attachment}');">{t}Save{/t}</a>
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </form>
</div>
{/block}

{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

<div id="menu-acciones-admin">
    <ul>
		<li>
			<a href="{$smarty.server.SCRIPT_NAME}?action=list" title="Cancelar">
				<img src="{$params.IMAGE_DIR}cancel.png" border="0" /><br />
                Cancelar</a>
		</li>

        <li>
			<a href="#config" onclick="$('formulario').submit();return false;" title="">
				<img src="{$params.IMAGE_DIR}save.gif" border="0" /><br />
                Guardar</a>
		</li>
	</ul>
</div>

<div>

    <table class="adminheading">
        <tr>
            <th nowrap>&nbsp;</th>
        </tr>
    </table>

    {if count($config)>0}
	<table id="tabla" name="tabla" class="tabla" cellpadding="2">
    <thead>
        <tr>
			<th width="300">Grupo cachés</th>
            <th align="center">Habilitar Caché</th>
            <th align="center">Tiempo Expiración</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$config key="k" item="v"}
        <tr bgcolor="{cycle values="#EEEEEE,#EEFFEE"}">
			<td>
				&nbsp;&nbsp;
                <img src="{$params.IMAGE_DIR}template_manager/{$groupIcon.$k}" border="0" title="Caché de opinión interior" />
                {$groupName.$k}&nbsp;
				<input type="hidden" name="group[]" value="{$k}" />
			</td>
            <td width="100" align="center">
                <input type="checkbox" name="caching[{$k}]" value="1" {if $v.caching}checked="checked"{/if}/>
            </td>

            <td width="180" align="right">
                <input type="text" size="12" name="cache_lifetime[{$k}]" value="{$v.cache_lifetime|default:300}" style="text-align: right;" /> <sub>segundos</sub>
            </td>
        </tr>
        {/foreach}
    </tbody>
    </table>
    {else}
        <h1>Ninguna configuración disponible</h1>
    {/if}

</div>

<input type="hidden" id="action" name="action" value="config" />
</form>

{/block}

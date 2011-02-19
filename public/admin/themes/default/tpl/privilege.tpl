{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
	 style="max-width:70% !important; margin: 0 auto; display:block;">

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

{include file="botonera_up.tpl"}

<table class="adminheading">
	<tr>
		<th nowrap></th>
	</tr>
</table>

<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
<tr>
<th class="title">{t}Privilege name{/t}</th>
<th class="title">{t}Module{/t}</th>
<th class="title">{t}Edit{/t}</th>
<th class="title">{t}Delete{/t}</th>
</tr>
{section name=c loop=$privileges}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td style="padding:10px;">
		{$privileges[c]->description}
	</td>
	<td style="padding:10px;">
		{$privileges[c]->module}
	</td>
	<td style="padding:10px;width:75px;">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$privileges[c]->id});" title="{t}Edit{/t}">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="{t}Edit{/t}" /></a>
	</td>
	<td style="padding:10px;width:75px;">
		<a href="#" onClick="javascript:confirmar(this, {$privileges[c]->id});" title="{t}Delete{/t}">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" alt="{t}Delete{/t}"/></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td colspan="4" align="center"><b>{t}There is no privileges added yet{/t}.</b></td>
</tr>
{/section}
{if count($privileges) gt 0}
<tr>
    <td colspan="4" align="center">{$paginacion->links}</td>
</tr>
{/if}
</table>
{/if}


{* FORMULARIO PARA ENGADIR UN permiso ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}

<style type="text/css">
{literal}
div.autocomplete {
    margin:0px;
    padding:0px;
    width:250px;
    background:#fff;
    border:1px solid #888;
    position:absolute;
}

div.autocomplete ul {
    margin:0px;
    padding:0px;
    list-style-type:none;
}

div.autocomplete ul li.selected {
    background-color:#ffb;
}

div.autocomplete ul li {
    margin:0;
    padding:2px;
    height:32px;
    display:block;
    list-style-type:none;
    cursor:pointer;
}
{/literal}
</style>

{include file="botonera_up.tpl"}

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>

{* Module *}
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="module">{t}Module{/t}</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="module" name="module" title="{t}Module{/t}" size="20" maxlength="40"
			value="{$privilege->module}" class="required" />
	</td>
</tr>

{* Name *}
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="description">{t}Name{/t}</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="name" name="name" title="Nombre" value="{$privilege->name}" class="required" />
        <sub>({t}recomended{/t}: MODULO_ACCION)</sub>
	</td>
</tr>

{* Description *}
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="description">{t}Description{/t}</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="description" name="description" title="{t}Description{/t}" size="80" maxlength="100"
			value="{$privilege->description}" class="required" />
	</td>
</tr>
</tbody>
</table>
</div>

<input type="hidden" id="id" name="id" value="{$privilege->id}" />


<script type="text/javascript">
/**
 *
 */
var PrivilegeHelper = Class.create({
    initialize: function(module, name, options) {
        this.module  = $(module);
        this.name    = $(name);
        this.modules = options.modules || [];

        //<div class="autocomplete" style="display:none"></div>
        divList = new Element('div', {class: 'autocomplete', style: {display: 'none'}});
        this.module.up().insert(divList, {position: 'after'});

        new Autocompleter.Local(this.module, divList, this.modules, {ignoreCase: true, partialChars: 3, partialSearch: false});

        this._addBehavior();
    },

    _addBehavior: function() {
        this.module.observe('keyup', this.updateSpanCallback.bind(this));
        this.module.observe('blur', this.updateSpanCallback.bind(this));
        this.module.observe('change', this.updateSpanCallback.bind(this));
    },

    updateSpanCallback: function() {
        // set to uppercase
        this.module.value = this.module.value.toUpperCase();
        if(/.+_/.test(this.name.value)) {
            this.name.value = this.module.value + '_' + this.name.value.replace(/[^_]+_(.*?)$/, '$1');
        } else {
            this.name.value = this.module.value + '_' + this.name.value;
        }

        this.name.value = this.name.value.replace(/_+/g, '_').toUpperCase();
    }
});

new PrivilegeHelper('module', 'name', { modules: {json_encode value=$modules} });
</script>
{/if}

<input type="hidden" id="action" name="action" value="" /><input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}

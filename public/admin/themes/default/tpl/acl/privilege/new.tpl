{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
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
table.adminlist td,
table.adminlist th {
    padding: 8px;
}
</style>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Priveleges manager :: Editing privilege{/t}</h2></div>
			<ul class="old-button">
				<li>
                {if isset($privilege->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$privilege->id}', 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.gif" title="{t}Save{/t}" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$privilege->id}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}validate.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list" onmouseover="return escape('<u>C</u>ancelar');" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" ><br />{t}Cancel{/t}
                    </a>
                </li>
			</ul>
		</div>
	</div>
    <div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <td>{t}Privilege information{/t}</td>
            </tr>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" class="adminlist" width="600">
        <tbody>

        {* Módulo *}
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                <label for="module">{t}Module{/t}</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                <input type="text" id="module" name="module" title="Módulo" size="20" maxlength="40"
                    value="{$privilege->module}" class="required" />
            </td>
        </tr>

        {* Name *}
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                <label for="description">{t}Name:{/t}</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                <input type="text" id="name" name="name" title="Nombre" value="{$privilege->name}" class="required" />
                <sub>{t}(recomendation: MODULE_ACTION){/t}</sub>
            </td>
        </tr>

        {* Descripcion *}
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                <label for="description">{t}Description{/t}</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                <input type="text" id="description" name="description" title="Descripci&oacute;n" size="80" maxlength="100"
                    value="{$privilege->description}" class="required" />
            </td>
        </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan=3></td>
            </tr>
        </tfoot>
        </table>
        </div>

        <script type="text/javascript">

        var PrivilegeHelper = Class.create({
            initialize: function(module, name, options) {
                this.module  = $(module);
                this.name    = $(name);
                this.modules = options.modules || [];

                //<div class="autocomplete" style="display:none"></div>
                divList = new Element('div', { class: 'autocomplete', style: { display: 'none' } });
                this.module.up().insert(divList, { position: 'after' });

                new Autocompleter.Local(this.module, divList, this.modules, { ignoreCase: true, partialChars: 3, partialSearch: false });

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


        <input type="hidden" id="action" name="action" value="" /><input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}

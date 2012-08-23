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
</style>
{/block}

{block name="footer-js" append}
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
{/block}

{block name="content"}
<form action="{if isset($privilege->id)}{url name="admin_acl_privileges_update" id=$privilege->id}{else}{url name="admin_acl_privileges_create"}{/if}" method="post">
    <div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Privileges manager{/t} :: {t}Editing privilege{/t}</h2></div>
			<ul class="old-button">
				<li>
                {if isset($privilege->id)}
                    <button type="submit" name="action" value="update">
                {else}
                    <button type="submit" name="action" value="create">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save{/t}" alt="{t}Save{/t}">
                        <br />{t}Save{/t}
                    </button>
                </li>
                <li>
                    <button type="submit" name="action" value="validate">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" >
                        <br />{t}Save and continue{/t}
                    </button>
                </li>
				<li class="separator"></li>
                <li>
                    <a href="{url name="admin_acl_privileges"}" title="{t}Go back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" >
                        <br />{t}Go back{/t}
                    </a>
                </li>
			</ul>
		</div>
	</div>
    <div class="wrapper-content">
        <table class="adminform">
			<tbody>
				<tr>
					<td colspan=2>&nbsp;</td>
				</tr>

				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="module">{t}Module{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="module" name="module" title="Módulo" size="20" maxlength="40"
							value="{$privilege->module|default:""}" class="required" />
					</td>
				</tr>

				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="description">{t}Name:{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="name" name="name" title="Nombre" value="{$privilege->name|default:""}" class="required" />
						<sub>{t}(recomendation: MODULE_ACTION){/t}</sub>
					</td>
				</tr>

				<tr>
					<td valign="top" align="right" style="padding:4px;" width="30%">
						<label for="description">{t}Description{/t}</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" width="70%">
						<input type="text" id="description" name="description" title="Descripci&oacute;n" size="80" maxlength="100"
							value="{$privilege->description|default:""}" class="required" />
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

        <input type="hidden" name="id" id="id" value="{$privilege->id|default:""}" />
    </div>
</form>
{/block}

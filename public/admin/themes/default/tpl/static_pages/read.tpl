{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Static Pages Manager{/t} :: {t}Editing page{/t}</h2></div>
        <form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
            <ul class="old-button">
            <li>
                <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', $('id').value, 'formulario');" value="Validar" title="Validar">
                    <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                </a>
            </li>
            <li>
                <a href="#" class="admin_add" onClick="javascript:enviar(this, '_self', 'save', $('id').value);" value="Guardar" title="{t}Save and exit{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" /><br />{t}Save and exit{/t}
                </a>
            </li>
            <li>
                <a href="?action=list" class="admin_add" value="Cancelar" title="{t}Cancel{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" /><br />{t}Go back{/t}
                </a>
            </li>
		</ul>
	</div>
</div>
<div class="wrapper-content">

    <table class="adminheading">
        <tr>
            <td>{t}Insert the static page information{/t}</td>
        </tr>
    </table>
    <table class="adminform">
        <tbody>
        <tr>
            <td colspan=2>
                <label for="name">{t}Title:{/t}</label>
                <input type="text" id="title" name="title" title="{t}Page title{/t}" value="{$page->title|default:""}"
                       class="required" size="60" maxlength="120" tabindex="1" />
            </td>
            <td valign="top" style="padding:4px;" rowspan="3">
                <div style="background-color:#F5F5F5;">
                    {acl isAllowed="STATIC_AVAILABLE"}
                    <div style="padding:10px 15px;">
                        <label for="available">{t}Published:{/t}</label>
                        <select name="available" id="available" class="required" tabindex="4">
                            <option value="1"{if isset($page->available) &&$page->available eq 1} selected="selected"{/if}>{t}Yes{/t}</option>
                            <option value="0"{if isset($page->available) && $page->available eq 0} selected="selected"{/if}>{t}No{/t}</option>
                        </select>
                    </div>
                    {/acl}
                    <div style="padding:10px 15px;">
                        <label for="available">{t}Description:{/t}</label> <br />
                        <textarea name="description" id="description" rows="4" cols="30" tabindex="5">{$page->description|default:""}</textarea>
                    </div>
                    <div style="padding:10px 15px;">
                        <label for="available">{t}Keywords:{/t}</label><small>{t}(separated with comas){/t}</small> <br />
                        <input type="text" name="metadata" id="metadata" size="40" tabindex="6" value="{$page->metadata|default:""}" />
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td style="padding:4px 0 4px 4px;">
                <label for="name">{t}Direction:{/t}</label><br>
                <span>{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH} </span>
                <input type="text" id="slug" name="slug" title="{t}Keywords{/t}" value="{$page->slug|default:""}"
                       class="required" size="56" maxlength="120" tabindex="2" />
                <span>.html</span>
            </td>
        </tr>

        <tr>
            <td valign="top" align="right" style="padding:4px;" colspan="2">
                <textarea name="body" id="body" tabindex="3" title="{t}Page content{/t}" style="width:100%; height:20em;">{$page->body|default:""}</textarea>
            </td>
        </tr>

        </tbody>
        <tfoot>
            <tr>
                <td colspan=3></td>
            </tr>
        </tfoot>
    </table>


        <input type="hidden" name="filter[title]" value="{$smarty.request.filter.title|default:""}" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

        </table>

        <input type="hidden" id="action" name="action" value="save" />
    </form>
</div>
{/block}

{block name="footer-js"}
{script_tag src="/tiny_mce/opennemas-config.js"}
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

OpenNeMas.tinyMceConfig.advanced.elements = "body";
tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
var previous = null;
var updateSlug = function() {
    var slugy = $('slug').value.strip();
    if(previous!=slugy) {

        new Ajax.Request('?action=build_slug', {
            method: 'post',
            postBody: 'slug=' + slugy + '&id=' + $('id').value + '&title=' + $('title').value,
            onSuccess: function(transport) {
                $('slug').value = transport.responseText;
                previous = $('slug').value;
            }
        });
    }
};

document.observe('dom:loaded', function() {
    $('title').observe('blur', function() {
        var slugy = $('slug').value.strip();
        if(slugy.length <= 0) {
            updateSlug();
        }
    });

    $('slug').observe('blur', updateSlug);

    $('metadata').observe('blur', function() {
        new Ajax.Request('?action=clean_metadata', {
            method: 'post',
            postBody: 'metadata=' + $('metadata').value,
            onSuccess: function(transport) {
                $('metadata').value = transport.responseText;
            }
        });
    });
});
/* ]]> */
</script>
{/block}

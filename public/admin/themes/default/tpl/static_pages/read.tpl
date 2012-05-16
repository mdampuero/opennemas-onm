{extends file="base/admin.tpl"}
{block name="header-css" append}
    <style type="text/css">
    label {
        display:block;
        color:#666;
        text-transform:uppercase;
        padding: 5px;
    }
    td {
        padding: 10px;
    }
    .utilities-conf label {
        text-transform:none;
    }
    </style>
{/block}
{block name="content"}
<form action="{if isset($page->id)}{url name=admin_staticpages_update id=$page->id}{else}{url name=admin_staticpages_create}{/if}" method="POST">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Static Pages Manager{/t} :: {t}Editing page{/t}</h2></div>
                <ul class="old-button">
                <li>
                    <button type="submit" name="action" value="validate" id="save-continue" title="Validar">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" title="{t}Save and continue{/t}" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                </li>
                <li>
                    <button type="submit" name="action" value="save" id="save-exit" title="{t}Save and exit{/t}">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}" /><br />{t}Save and exit{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_staticpages}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Cancel{/t}" /><br />{t}Go back{/t}
                    </a>
                </li>
    		</ul>
    	</div>
    </div>
    <div class="wrapper-content">
        <table class="adminform" style="padding:10px;">
            <tbody>
            <tr>
                <td>
                    <label for="title">{t}Title{/t}</label>
                    <input type="text" id="title" name="title" title="{t}Page title{/t}" value="{$page->title|default:""}"
                           class="required" style="width:99%" maxlength="120" tabindex="1" />
                </td>
                <td>
                    {acl isAllowed="STATIC_AVAILABLE"}
                    <div>
                        <label for="available">{t}Published{/t}</label>
                        <select name="available" id="available" class="required" tabindex="4">
                            <option value="1"{if isset($page->available) && $page->available eq 1} selected="selected"{/if}>{t}Yes{/t}</option>
                            <option value="0"{if isset($page->available) && $page->available eq 0} selected="selected"{/if}>{t}No{/t}</option>
                        </select>
                    </div>
                    {/acl}
                </td>
            </tr>

            <tr>
                <td>
                    <label for="slug">{t}Direction:{/t}</label>
                    <span>{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH} </span>
                    <input type="text" id="slug" name="slug" title="{t}Keywords{/t}" value="{$page->slug|default:""}"
                           class="required" style="width:60%;display:inline;" maxlength="120" tabindex="2" />
                    <span>.html</span>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <label for="metadata">{t}Keywords{/t} <small>{t}(separated with comas){/t}</small> </label>
                    <input type="text" name="metadata" id="metadata" style="width:99%" tabindex="6" value="{$page->metadata|default:""}" />
                </td>
                <td>

                </td>

            </tr>

            <tr>
                <td>
                    <label for="description">{t}Description:{/t}</label> <br />
                    <textarea name="description" id="description" rows="4" cols="30" style="width:99%"  tabindex="5">{$page->description|default:""}</textarea>
                </td>
                <td>

                </td>
            </tr>

            <tr>
                <td>
                    <label for="body">{t}Body{/t}</label>
                    <textarea name="body" id="body" tabindex="3" title="{t}Page content{/t}" style="width:100%; height:20em;">{$page->body|default:""}</textarea>
                </td>
                <td>

                </td>
            </tr>

            </tbody>
        </table>
    </div>


    <input type="hidden" name="filter[title]" value="{$smarty.request.filter.title|default:""}" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}

{block name="footer-js"}
{script_tag src="/tiny_mce/opennemas-config.js"}
<script type="text/javascript">
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

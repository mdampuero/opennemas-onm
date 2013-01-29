{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if isset($page->id)}{url name=admin_staticpages_update id=$page->id}{else}{url name=admin_staticpages_create}{/if}" method="POST" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($page->id)}{t}Creating static page{/t}{else}{t}Editing page{/t}{/if}</h2></div>
                <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1" id="save-continue" title="Validar">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save{/t}" alt="{t}Save{/t}" ><br />{t}Save{/t}
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
        {render_messages}
        <div class="form-horizontal panel">
            <div class="control-group">
                <label for="name" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$page->title|default:""}"
                           maxlength="120" tabindex="1" required="required"  class="input-xlarge"/>
                </div>
            </div>

            <div class="control-group">
                <label for="slug" class="control-label">{t}URL{/t}</label>
                <div class="controls">
                    <input type="text" id="slug" name="slug" value="{$page->slug|default:""}"
                           maxlength="120" tabindex="2" required="required"  class="input-xxlarge"/>
                    <span class="help-block">{t}The slug component in the url{/t}: {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/slug.html
                </div>
            </div>

            {acl isAllowed="STATIC_AVAILABLE"}
            <div class="control-group">
                <label for="available" class="control-label">{t}Published{/t}</label>
                <div class="controls">
                    <select name="available" id="available" tabindex="3">
                        <option value="1"{if isset($page->available) && $page->available eq 1} selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="0"{if isset($page->available) && $page->available eq 0} selected="selected"{/if}>{t}No{/t}</option>
                    </select>
                </div>
            </div>
            {/acl}

            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <input type="text" name="description" id="description" rows="4" tabindex="5" class="input-xxlarge" value="{$page->description|default:""}">
                </div>
            </div>

            <div class="control-group">
                <label for="body" class="control-label">{t}Body{/t}</label>
                <div class="controls">
                    <textarea name="body" id="body" rows="5" required="required" tabindex="5" style="height:20em;" class="input-xxxlarge">{$page->body|default:""}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Metadata{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" value="{$page->metadata|default:""}"
                           tabindex="6" required="required"  class="input-xxlarge"/>
                </div>
            </div>
        </div>
    </div>


    <input type="hidden" name="filter[title]" value="{$smarty.request.filter.title|default:""}" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}

{block name="footer-js" append}

{script_tag src="/tiny_mce/opennemas-config.js"}
<script type="text/javascript">
/* <![CDATA[ */
tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
OpenNeMas.tinyMceConfig.advanced.elements = "body";
tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );

jQuery(document).ready(function($){

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    var previous = null;

    jQuery("#title").on('blur', '', function(e){
        var slugy = jQuery.trim(jQuery('#slug').attr('value'));
        if ((slugy.length <= 0) && (previous!=slugy)) {

            jQuery.ajax({
                url:  "{url name=admin_staticpages_build_slug id=$page->id|default:0}",
                type: "POST",
                data: { action:"buildSlug", id:'{$page->id|default:0}', slug:slugy, title:jQuery('#title').attr('value') },
                success: function(data){
                    jQuery('#slug').attr('value', data);
                    previous = jQuery('#slug').value;
                }
            });
        }
    });

    jQuery("#metadata").on('change', '', function(e){
        jQuery.ajax({
            url:  "{url name=admin_staticpages_clean_metadata id=$page->id|default:0}",
            type: "POST",
            data: { action:"cleanMetadata", id:'{$page->id|default:0}', metadata:jQuery('#metadata').attr('value') },
            success: function(data){
                jQuery('#slug').attr('value', data);
                previous = jQuery('#slug').attr('value');
            }
        });
    });
});
/* ]]> */
</script>
{/block}

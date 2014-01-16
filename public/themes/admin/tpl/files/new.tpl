{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#title').on('change', function(e, ui) {
                fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
            });
            $('#formulario').onmValidate({
                'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
            });
        });

    </script>
{/block}

{block name="content"}

<form action="{if !is_null($attaches)}{url name=admin_files_update id=$attaches->id}{else}{url name=admin_files_create}{/if}"
    enctype="multipart/form-data" method="POST" name="formulario" id="formulario" />

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if $attaches}{t}Editing file{/t}{else}{t}Creating file{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button  type="submit" >
                        <img src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
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
    </div>

    <div class="wrapper-content panel">
        <div class="form-horizontal">
            <div class="control-group">
                <label for="" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title" name="title" value="{$attaches->title|clearslash}"
                        class="input-xlarge" required="required">
                </div>
            </div>

            <div class="control-group">
                <label for="metadata" class="control-label">{t}Metadata{/t}</label>
                <div class="controls">
                    <input type="text" id="metadata" name="metadata" value="{$attaches->metadata|clearslash}" class="input-xlarge" required="required">
                </div>
            </div>

            <div class="control-group">
                <label for="description" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea id="description" name="description" class="input-xlarge" required="required"
                                    class="required">{$attaches->description|clearslash}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="path" class="control-label">{t}Path{/t}</label>
                <div class="controls">
                    {if !is_null($attaches)}
                    <a href="{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches->path}">{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches->path}</a>
                    <input type="hidden" id="path" name="path" value="{$attaches->path|clearslash}" class="input-xlarge" required="required" readonly="readonly">
                    {else}
                    <input type="file" id="path" name="path" value="" required="required" />
                    {/if}
                </div>
            </div>

            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    {include file="common/selector_categories.tpl" name="category" item=$attaches}
                </div>
            </div>
        </div>

        {if !is_null($attaches->id)}
        <input type="hidden" id="id" name="id"  value="{$attaches->id|default:""}" />
        <input type="hidden" id="fich" name="fich" value="{$attaches->pk_attachment}" />
        {/if}
        <input type="hidden" name="page" id="page" value="{$page|default:"1"}" />
    </div>
</form>
{/block}

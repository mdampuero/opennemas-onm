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
            <div class="title"><h2>{t}Files manager ::{/t} {if $attaches}{t 1=$attaches->title}Editing file "%1"{/t}{else}{t}Creating new file{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button  type="submit" >
                        <img src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li>
                    <a href="{url name=admin_files}" class="admin_add" value="Cancelar" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content panel">

        {render_messages}

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
                    <input type="text" id="path" name="path" value="{$attaches->path|clearslash}" class="input-xlarge" required="required" readonly="readonly">
                    {else}
                    <input type="file" id="path" name="path" value="" required="required" />
                    {/if}
                </div>
            </div>

            <div class="control-group">
                <label for="category" class="control-label">{t}Category{/t}</label>
                <div class="controls">
                    <select name="category" id="category" required="required">
                        <option value="20" data-name="{t}Unknown{/t}" {if !isset($category)}selected{/if}>{t}Unknown{/t}</option>
                        {section name=as loop=$allcategorys}
                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                            <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
                            {if (($category == $allcategorys[as]->pk_content_category))
                            || $attaches->category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                                {if $subcat[as][su]->internal_category eq 1}
                                    <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                                    {if $category eq $subcat[as][su]->pk_content_category || $attaches->category eq $subcat[as][su]->pk_content_category}selected{/if} >&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                {/if}
                            {/section}
                            {/acl}
                        {/section}
                    </select>
                </div>
            </div>
        </div>

        {if !is_null($attaches)}
        <input type="hidden" name="id" id="id" value="{$attaches->id|default:""}" />
        <input type="hidden" id="category" name="category" title="Fichero"
            value="{$attaches->category}" />
        <input type="hidden" id="fich" name="fich" title="Fichero"
            value="{$attaches->pk_attachment}" />
        {/if}
        <input type="hidden" name="page" id="page" value="{$page|default:"1"}" />
    </div>
</form>
{/block}

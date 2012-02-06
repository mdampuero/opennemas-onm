{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css" media="screen">
    label {
        display:block;
        color:#666;
        text-transform:uppercase;
    }
</style>
{/block}

{block name="header-js" append}
    {script_tag src="/utilsalbum.js" language="javascript"}
{/block}

{block name="footer-js" append}
    {script_tag src="/cropper.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
    <script>
    jQuery(document).ready(function($){
        $("#form-validate-button, #form-send-button").on("click", function(event) {

            var frontpage_image =  $(".album-frontpage-image");
            var album_images =  $("#list-of-images .image");
            if (frontpage_image.val() == "") {
                $("#modal-edit-album-errors").modal('show');
                $("#album-contents").tabs('selected',0);
                return false;
            }
            if (album_images.length < 1) {
                $("#modal-edit-album-errors").modal('show');
                $("#album-contents").tabs('selected',1);
                return false;
            };
            return true;
        })
    });
    </script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="album-edit-form" {$formAttrs}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Album manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating Album{/t}{else}{t}Editing Album{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    {acl isAllowed="ALBUM_CREATE"}
                    <button type="submit" name="action" value="validate"  id="form-validate-button">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </button>
                    {/acl}
                </li>
                <li>
                    {if isset($album->id)}
                        {acl isAllowed="ALBUM_UPDATE"}
                        <button type="submit" name="action" value="update" id="form-send-button">
                            <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y continuar" alt="{t}Save{/t}" ><br />{t}Save{/t}
                        </button>
                        {/acl}
                    {else}
                        {acl isAllowed="ALBUM_CREATE"}
                        <button type="submit" name="action" value="create" id="form-send-button">
                            <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />{t}Save{/t}
                        </button>
                        {/acl}
                    {/if}
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list&amp;category={$smarty.request.category}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <div class="album-edit-form panel">
            <table class="album-information">
                <tbody>
                    <tr>
                        <td>
                            <label for="title">{t}Title:{/t}</label>
                            <input type="text" id="title" name="title" title={t}"Album"{/t}
                                value="{$album->title|clearslash|escape:"html"}"
                                class="required"
                                onBlur="javascript:get_metadata(this.value);"
                                style="width:98%;" />
                        </td>
                        <td rowspan="4" style="width:200px">
                            <label for="title">{t}Available:{/t}</label>
                            <input type="checkbox" value="{$album->available}" id="available" name="available">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:inline-block;width:30%;">
                                <label for="category">{t}Category{/t}</label>
                                <select name="category" id="category" style="width:98%">
                                    {section name=as loop=$allcategorys}
                                    {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                    <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                    {/acl}
                                    {section name=su loop=$subcat[as]}
                                        {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                                        <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                        {/acl}
                                    {/section}
                                    {/section}
                                </select>
                            </div>
                            <div style="display:inline-block;width:69%;">
                                <label for="agency">{t}Agency{/t}</label>
                                <input type="text" id="agency" name="agency" style="width:98%"
                                    value="{$album->agency|clearslash|escape:"html"}" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="description">{t}Description{/t}</label>
                            <textarea name="description" id="description" style="width:98%;" >{t 1=$album->description|clearslash|escape:"html"}%1{/t}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="metadata">{t}Keywords:{/t}<small> ({t}Separated by coma{/t})</small></label>
                            <input type="text" id="metadata" name="metadata" style="width:98%"
                                   class="required" title={t}"Metadata"{/t} value="{$album->metadata}" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="album-images">
                {include file="album/partials/_images.tpl"}
            </div><!-- /album-images -->
        </div>

        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
</form>
{include file="album/modals/_edit_album_error.tpl"}
{/block}

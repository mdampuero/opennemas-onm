{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
div.book-preview {
    background: none repeat scroll 0 0 #DDDDDD;
    border-radius: 5px 5px 5px 5px;
    padding: 10px;
    width: 160px;
}
input[type="text"].required {
    width: 90%;
}

</style>
{/block}

{block name="footer-js" append}
<script>
    jQuery('#starttime').datepicker({
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd'
    });
    jQuery('#title').on('change', function(e, ui) {
        fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });
</script>
{/block}

{block name="content"}
<form action="{if isset($book)}{url name=admin_books_update id=$book->id}{else}{url name=admin_books_create}{/if}"
    method="POST" name="formulario" id="formulario" enctype="multipart/form-data">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($book->id)}{t}Creating Book{/t}{else}{t}Editing Book{/t}{/if}</h2></div>
            <ul class="old-button">
                {if isset($book->id)}
                    {acl isAllowed="BOOK_UPDATE"}
                    <li>
                        <button href="{url name=admin_books_update id=$book->id}" name="continue" value="1">
                            <img src="{$params.IMAGE_DIR}save.png"><br />{t}Save{/t}
                        </button>
                    </li>
                    {/acl}
                {else}
                    {acl isAllowed="BOOK_CREATE"}
                    <li>
                        <button href="{url name=admin_books_create}" name="continue" value="1">
                             <img src="{$params.IMAGE_DIR}save.png"><br />{t}Save{/t}
                        </button>
                    </li>
                    {/acl}
                {/if}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_books category=$category|default:""}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <table class="adminform">
        <tbody>
            <tr>
                <td rowspan="11" style="padding:10px;width:160px;">
                    {if (!empty($book->id))}
                    <div class="book-preview">
                        <label for="title">{t}Preview:{/t}</label><br>
                            <img src="{$smarty.const.INSTANCE_MEDIA}/books/{$book->file_img}" style=" width:164px;" alt="{$book->file_img}" />
                    </div>
                    {/if}
                </td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <label for="title">{t}Title:{/t}</label>
                </td>
                <td>
                    <input type="text" id="title" name="title" title={t}"Album"{/t}
                        value="{$book->title|clearslash|escape:"html"}" />
                </td>
            </tr>
            <tr>
                <td style="padding:4px;">
                    <label for="title">Tapa libro:(jpg)</label>
                </td>
                <td>
                    {if (isset($book->file_img) && !empty($book->file_img) )}
                         <input name="file_img_name" type="text" readonly="readonly" value="{$book->file_img|default:''}"/>
                     {/if}
                      <input name="file_img" type="file"/>
                </td>
            </tr>
                <tr>
                    <td>
                        <label for="title">{t}Author{/t}:</label>
                    </td>
                    <td>
                        <input type="text" id="author" name="author" title="{t}author{/t}"
                            size="30" value="{$book->author|clearslash|escape:"html"}" />
                    </td>
                </tr>
                 <tr>
                    <td>
                        <label for="title">{t}Date{/t}:</label>
                    </td>
                    <td>
                        <input type="text" id="starttime" name="starttime" title="{t}Date{/t}"
                            size="60" value="{$book->starttime|clearslash|date_format:"Y-m-d"}" />
                    </td>
                </tr>
                 <tr>
                    <td>
                        <label for="title">{t}Editorial{/t}:</label>
                    </td>
                    <td>
                        <input type="text" id="editorial" name="editorial" title="{t}editorial{/t}"
                            size="60" value="{$book->editorial|clearslash|escape:"html"}" />
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px;">
                        <label for="title">Descripci&oacute;n:</label>
                    </td>
                    <td>
                        <textarea name="description" id="description"  title="description" style="width:90%; height:10em;">{t 1=$book->description|clearslash}%1{/t}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="title">Secci&oacute;n:</label>
                    </td>
                    <td>
                        <select name="category" id="category"  >
                            {section name=as loop=$allcategorys}
                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t 1=$allcategorys[as]->title}%1{/t}</option>
                                {section name=su loop=$subcat[as]}
                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}</option>
                                {/section}
                            {/section}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <label for="title"> {t}Available:{/t} </label>
                    </td>
                    <td valign="top">
                            <select name="available" id="available"
                                class="required" {acl isNotAllowed="BOOK_AVAILABLE"} disabled="disabled" {/acl}>
                                <option value="0" {if $book->available eq 0} selected {/if}>{t}No{/t}</option>
                                <option value="1" {if !isset($book) || $book->available eq 1} selected {/if}>{t}Yes{/t}</option>

                            </select>
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px;">
                        <label for="metadata">{t}Keywords:{/t}</label>
                    </td>
                    <td>
                        <input type="text" id="metadata" name="metadata" size="60"
                           class="required" title="Metadata" value="{$book->metadata}" />
                        <br><label align='right'><sub>{t}Separated by coma{/t}</sub></label>
                    </td>
                </tr>
            </tbody>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$book->id|default:""}" />
    </div>
</form>
{capture assign="language"}{setting name=site_language}{/capture}
{assign var="lang" value=$language|truncate:2:""}
{if !empty($lang)}
    {assign var="js" value="/jquery/jquery_i18n/jquery.ui.datepicker-"|cat:$lang|cat:".js"}
    {script_tag language="javascript" src=$js}
    <script>
    jQuery(document).ready(function() {
        jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "{$lang}" ] );
    });
    </script>
{/if}
{/block}

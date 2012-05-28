{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsBook.js"}

{/block}


{block name="footer-js" append}
<script>

jQuery('#starttime').datepicker({
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd'
    });

</script>

{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" enctype="multipart/form-data" {$formAttrs}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Book manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating Book{/t}{else}{t}Editing Book{/t}{/if}</h2></div>
            <ul class="old-button">
                {acl isAllowed="BOOK_CREATE"}
                <li>
                    <a class="admin_add" onClick="enviar(this, '_self', 'validate', '{$book->id}');" title="{t}Save and continue{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Save and continue{/t}" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                {/acl}
                <li>
                    {if isset($book->id)}
                        {acl isAllowed="BOOK_UPDATE"}
                            <a onClick="javascript:enviar(this, '_self', 'update', '{$book->id}');">
                        {/acl}
                    {else}
                        {acl isAllowed="BOOK_CREATE"}
                            <a onClick="javascript:enviar(this, '_self', 'create', '0');">
                        {/acl}
                    {/if}
                        <img src="{$params.IMAGE_DIR}save.png" alt="Guardar y salir"><br />{t}Save{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=list&amp;category={$smarty.request.category}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <table class="adminheading">
            <tr>
                <td>
                    {t}Enter book information{/t}
                </td>
            </tr>
        </table>

        <table class="adminform">
            <tbody>
                <tr>
                    <td>
                        <label for="title">{t}Title:{/t}</label>
                    </td>
                    <td>
                        <input type="text" id="title" name="title" title={t}"Album"{/t}
                            size="60" value="{$book->title|clearslash|escape:"html"}"
                            class="required" onBlur="javascript:get_metadata(this.value);" />
                    </td>
                    <td rowspan="2">
                        <table style='background-color:#F5F5F5; padding:18px;'>
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
                                <td valign="top"  align="right">
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
                        </table>
                    </td>
                </tr>

            <tr>
                <td style="padding:4px;">
                    <label for="title">Archivo:(pdf)</label>
                </td>
                <td>
                    {if (isset($book->file_name) && !empty($book->file_name) )}
                         <input name="file_name" type="text" readonly="readonly" value="{$book->file_name}"/>
                    {/if}
                    <input name="file" type="file"/>
                </td>
            </tr>
            <tr>
                <td style="padding:4px;">
                    <label for="title">Tapa libro:(jpg)</label>
                </td>
                <td>
                    {if (isset($book->file_img) && !empty($book->file_img) )}
                         <input name="file_img" type="text" readonly="readonly" value="{$book->file_img|default:''}"/>
                     {/if}
                      <input name="file_img" type="file"/>
                </td>
                <td rowspan="5" style="padding:6px;">
                    {if (!empty($book->id))}
                    <label for="title">Preview:</label><br>
                    <a href="{$smarty.const.INSTANCE_MEDIA}/books/{$book->file_name}" target="_blank">
                        <img src="{$smarty.const.INSTANCE_MEDIA}/books/{$book->file_img}" style=" width: 164px;" />
                    </a>
                    {/if}
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
                        <textarea name="description" id="description"  title="description" style="width:98%; height:10em;">{t 1=$book->description|clearslash|escape:"html"}%1{/t}</textarea>
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
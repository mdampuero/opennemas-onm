{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
function toggleAdvanced() {
    var results = $$('tr.advanced');
    results.each(function(elem){
        elem.toggleClassName('nodisplay');
    });
}
</script>
{/block}

{block name="header-css" append}
<style type="text/css">
.nodisplay {
    display:none;
}

table th, table label {
    color: #888;
    text-shadow: white 0 1px 0;
    font-size: 13px;
}
th {
    vertical-align: top;
    text-align: left;
    padding: 10px;
    width: 200px;
    font-size: 13px;
}
label{
    font-weight:normal;
}
.panel {
    background:White;
}
fieldset {
    border:none;
    border-top:1px solid #ccc;
}
legend {
    color:#666;
    text-transform:uppercase;
    font-size:13px;
    padding:0 10px;
}

.awesome {
    border:0;
}
.panel {
    margin:0;
}
.default-value {
    display:inline;
    color:#666;
    margin-left:10px;
    vertical-align:middle
}
input[type="text"],
textarea{
    max-height:80%
}
</style>
{/block}


{block name="footer-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="GET">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Image manager{/t}:: {if $action eq 'search'} {t}Search{/t} {elseif $action eq 'searchResult'} {t}Search result{/t} {else} {t}Information{/t} {/if} </h2></div>
            <ul class="old-button">
                <li>
                    <a class="admin_add" href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}" onmouseover="return escape('Listado de Categorias');" name="submit_mult" value="Listado de Categorias">
                        <img border="0" style="width:50px;"  src="{$params.IMAGE_DIR}previous.png" alt="Información"><br />{t}Go back{/t}
                    </a>
                </li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=statistics">
                        {t}Statistics{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {if !is_null($smarty.get.string_search)}
            <div class="notice">
                <strong>{t}Searching images with the next criteria: {/t}</strong>
                {if isset($search_string)}search string "{$search_string}"{/if}
                {if !empty($search_criteria['category'])}{t 1=$datos_cat[0]->title}, in category "%1"{/t}{/if}
                {if !empty($search_criteria['maxWidth'])}{t 1=$search_criteria['maxWidth']}, max width of %1 px{/t}{/if}
                {if !empty($search_criteria['minWidth'])}{t 1=$search_criteria['minWidth']}, min width of %1 px{/t}{/if}
                {if !empty($search_criteria['maxHeight'])}{t 1=$search_criteria['maxHeight']}, max height of %1 px{/t}{/if}
                {if !empty($search_criteria['minHeight'])}{t 1=$search_criteria['minHeight']}, min height of %1 px{/t}{/if}
                {if !empty($search_criteria['maxWeight'])}{t 1=$search_criteria['maxWeight']}, max weight of %1 bytes{/t}{/if}
                {if !empty($search_criteria['minWeight'])}{t 1=$search_criteria['minWeight']}, min weight of %1 bytes{/t}{/if}
                {if !empty($search_criteria['type'])}{t 1=$search_criteria['type']}, type of "%1"{/t}{/if}
                {if !empty($search_criteria['color'])}{t 1=$search_criteria['color']}, color "%1"{/t}{/if}
                {if !empty($search_criteria['author'])}{t 1=$search_criteria['author']}, created by "%1"{/t}{/if}
                {if !empty($search_criteria['starttime'])}{t 1=$search_criteria['starttime']}, created after "%1"{/t}{/if}
                {if !empty($search_criteria['endtime'])}{t 1=$search_criteria['endtime']}, created before "%1"{/t}{/if}
            </div>
            {include file="image/_partials/media-browser.tpl"}
        {else}
        <table class="adminheading">
            <tr>
                <td>
                    {t}Fill the form for searching an image{/t}
                </td>
            </tr>
        </table>
        <table class="adminform" >
            <tbody >
                <tr>
                    <th><label for="string_search">{t}Image name{/t}</label></td>
                    <td>
                        <input type="text" id="string_search" name="string_search" size="60" value="{$smarty.request.string_search}" />
                    </td>
                </tr>
                <tr>
                    <th><label for="category">{t}Category{/t}</label></td>
                    <td>
                        <select name="category">
                            <option value="all" {if $photo1->color eq "all"}selected{/if}>{t}All{/t}</option>
                            <option value="2" {if $category eq "2"} selected {/if}>{t}Advertisement{/t}</option>
                            {section name=as loop=$allcategorys}
                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}">{$allcategorys[as]->title}</option>
                                {section name=su loop=$subcat[as]}
                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category  eq $subcat[as][su]->pk_content_category} selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                               {/section}
                            {/section}
                        </select>
                     </td>
                </tr>

                <tr>
                    <td colspan=2>
                    <a href="javascript:toggleAdvanced();" id="show-advanced">
                        {t escape=off}Show advanced search &darr;{/t}
                    </a>
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="max_width">{t}Max width:{/t} </label>
                    </th>
                    <td>
                        <input type="text" id="max_width" name="max_width" /> px.
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="min_width">{t}Min width:{/t}</label>
                    </th>
                    <td>
                        <input type="text" id="min_width" name="min_width" /> px.
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="max_height">{t}Max height:{/t}</label>
                    </th>
                    <td>
                        <input type="text" id="max_height" name="max_height" /> px.
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="min_height">{t}Min height:{/t}</label>
                    </th>
                    <td>
                        <input type="text" id="min_height" name="min_height" /> px.
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="max_weight">{t}Max weight:{/t}</label>
                    </th>
                    <td>
                        <input type="text" id="max_weight" name="max_weight" />  Kb
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="min_weight">{t}Min weight:{/t}</label>
                    </th>
                    <td>
                        <input type="text" id="min_weight" name="min_weight" size="18" />  Kb
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="type">{t}Type:{/t}</label>
                    </th>
                    <td>
                        <select name="type" id="type" />
                            <option value="" selected >{t}-- All --{/t}</option>
                            <option value="jpg" >jpg</option>
                            <option value="gif" >gif</option>
                            <option value="png" >png</option>
                            <option value="svg" >svg</option>
                            <option value="swf" >swf</option>
                            <option value="otros" >{t}Others{/t}</option>
                        </select>
                     </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="color">{t}Color:{/t}</label>
                    </th>
                    <td>
                        <select name="color" id="color" />
                             <option value="" selected>{t} - All types - {/t}</option>
                            <option value="BN" >{t}Black and white{/t}</option>
                            <option value="color" >{t}Color{/t}</option>
                        </select>
                     </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="author">{t}Author:{/t}</label>
                    </th>
                    <td>
                        <input type="text" id="author" name="author"
                            value='{$photo1->author_name|clearslash|escape:'html'}' size="15" />
                    </td>
                </tr>
                <tr class="advanced nodisplay">
                    <th>
                        <label for="starttime">{t}Date period:{/t}</label>
                    </th>
                    <td>
                        {t}From:{/t}
                        <input type="text" size="18" id="starttime" name="starttime" value="" />
                        {t}To:{/t}
                        <input type="text" size="18" id="endtime" name="endtime" value="" />
                     </td>
                </tr>
                </div>
            </tbody>
        </table>
        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Search{/t}</button>
            </div>
        </div>
        {/if}

        <input type="hidden" id="action" name="action" value="search" />
    </div>
</form>

{/block}

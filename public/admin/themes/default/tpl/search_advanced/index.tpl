{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
#search-form button {
    width:100%;
}
#search_string{
    width:195px;
    margin-bottom:5px;
}
#content_types {
    min-height:150px;
}
#search-form {
    height:100%
}
span.highlighted {
    color:Red
}
</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Global search{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="{url name=admin_search}" method="GET">
        <div class="wrapper-content">
            <div class="search clearfix">
                <div class="search-results">
                    <div id="search-results">
                        {if !is_null($search_string)}
                            {include file="search_advanced/partials/_list.tpl"}
                        {else}
                        <div class="empty">
                            <p>
                                <img src="{$params.IMAGE_DIR}/search/search-images.png">
                            </p>
                            {t escape="off"}Please fill the form for searching contents{/t}
                        </div><!-- / -->
                        {/if}
                    </div><!-- /search-results -->
                </div><!-- /search -->
                <div class="search-form">
                    <div>

                        <label for="string_search">{t}Content name{/t}</label>
                        <input type="search" id="search_string" name="search_string" title="stringSearch" value="{$search_string}"
                                style="width:95%"/>
                        <button type="submit" class="onm-button red submit" style="width:100%"><i class="icon-search icon-white"></i></button>

                        <br>

                        <div class="search-bar-title">{t}Content type{/t}</div>
                        <select name="content_types[]" id="content_types" multiple>
                        {foreach name=contentTypes item=type from=$arrayTypes}
                            {if (!empty($string_search) && ($type['pk_content_type'] == 1 || $type['pk_content_type'] == 4))
                                || (in_array($type['name'], $content_types_selected))}
                            <option value="{$type['name']}" selected="selected"/>{$type['name']|ucfirst}</option>
                            {else}
                            <option value="{$type['name']}" />{$type['name']|ucfirst}</option>
                            {/if}
                        {/foreach}
                        </select>
                    </div><!-- /search-form -->
                </div>
            </div><!-- /search -->
        </div>
    </form>
</div>
{/block}

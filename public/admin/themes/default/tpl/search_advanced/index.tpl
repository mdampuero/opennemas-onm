{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css" media="screen">
#stringSearch {
    width:80%;
}
label {
    font-weight:normal;
}
.content-type-picker {
    display:inline-block;
    padding:3px 0;
    min-width:90px;
    margin-right:5px;
}
</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Advanced search{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
        <div class="wrapper-content">
            <div class="search clearfix">
                <div class="search-results">
                    <div id="search-results">

                        {if !is_null($smarty.get.stringSearch)}
                            {include file="search_advanced/partials/_list.tpl"}
                        {else}

                            <div class="empty">
                                <p>
                                    <img src="{$params.IMAGE_DIR}/search/search-images.png">
                                </p>
                                {t escape="off"}Please fill the form of<br> the side to search contents{/t}
                            </div><!-- / -->
                        {/if}
                    </div><!-- /search-results -->
                </div><!-- /search -->
                <div class="search-form">
                    <div>
                        <label for="string_search">
                            {t}Content name{/t}
                            <input type="search" id="stringSearch" name="stringSearch" title="stringSearch" value="{$search_string}"
                                class="input-medium search-query" /> &nbsp;

                        </label>

                        <div class="search-bar-title">{t}Content type{/t}</div>

                        {foreach name=contentTypes item=type from=$arrayTypes}
                        <div class="content-type-picker type-picker">
                            {if $type[0] == 1 || $type[0] == 4}
                            <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" checked="checked"/>
                            <label for="{$type[1]}">{$type[2]|mb_capitalize}</label>
                            {else}
                            <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" />
                            <label for="{$type[1]}">{$type[2]|mb_capitalize}</label>
                            {/if}
                        </div>

                        {/foreach}
                        <br>
                        <button type="submit" class="btn btn-danger">{t}Search content{/t}</button>
                    </div><!-- /search-form -->
                </div>
            </div><!-- /search -->
        </div>
    </form>
</div>
{/block}

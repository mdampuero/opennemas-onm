{extends file="base/admin.tpl"}
{block name="footer-js" append}
<script>
    jQuery(document).ready(function($){
        $('#importer-element-info').tabs();
    });
</script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EuropaPress importer{/t} :: {t}Article information{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=import&amp;id={$element->xmlFile}" title="{t}Import{/t}">
                <img src="{$params.IMAGE_DIR}archive_no.png" alt="{t}Import{/t}" ><br />{t}Import{/t}
                </a>
            </li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=list" title="{t}Go back to list{/t}">
                <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    <div id="importer-element-info"  class="tabs">
        <ul>
            <li><a href="#basic" title="{t}Basic information{/t}">{t}Basic information{/t}</a></li>
            <li><a href="#more" title="{t}More information{/t}">{t}More information{/t}</a></li>
        </ul><!-- -->
        <div id="basic">
            <h2 style="margin:0;">{$element->title}</h2>
            <p>
                <strong>{t}Priority:{/t}</strong> {$element->priority}
            </p>

            <p>
                <strong>{t}Date:{/t}</strong> {$element->created_time->format("H:i:s d-m-Y")}
            </p>
            {if $element->pretitle}
            <p>
                <strong>{t}Pretitle:{/t}</strong> <br/>
                {$element->pretitle}
            </p>
            {/if}
            {if $element->summary}
            <p>
                <strong>{t}Summary:{/t}</strong> <br/>
                {$element->summary}
            </p>
            {/if}
            <p>
                <strong>{t}Body:{/t}</strong>
                {$element->body}
            </p>
        </div>
        <div id="more">
            {if count($element->photos) > 0}
            <p>
                <strong>{t}Photos:{/t}</strong> <br/>
                <ul>
                {foreach from=$element->photos item=photo}
                    <li>{$photo}</li>
                {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->people) > 0}
            <p>
                <strong>{t}People:{/t}</strong> <br/>
                <ul>
                {foreach from=$element->people item=person}
                    <li>{$person}</li>
                {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->place) > 0}
            <p>
                <strong>{t}Place:{/t}</strong> <br/>
                <ul id="id">
                    {foreach from=$element->place item=value key=key}
                        <li>{$key} -{$value}</li>
                    {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->associatedDocs) > 0}
            <p>
                <strong>{t}Associated Docs:{/t}</strong> <br/>
                <ul>
                {foreach from=$element->associatedDocs item=doc}
                    <li>{$doc}</li>
                {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->categories) > 0}
            <p>
                <strong>{t}Categories:{/t}</strong> <br/>
                <ul>
                {foreach from=$element->categories item=cat}
                    <li>{$cat}</li>
                {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->dataCastID) > 0}
            <p>
                <strong>{t}Level:{/t}</strong> <br/>
                <ul>
                {foreach from=$element->dataCastID item=dataCastID}
                    <li>{$dataCastID}</li>
                {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->level) > 0}
            <p>
                <strong>{t}Level:{/t}</strong> <br/>
                <ul>
                {foreach from=$element->level item=level}
                    <li>{$level}</li>
                {/foreach}
                </ul>
            </p>
            {/if}
            {if count($element->redactor) > 0}
            <p>
                <strong>{t}Redactors:{/t}</strong> {$element->redactor|implode:", "}
            </p>
            {/if}
        </div>
    </div><!-- /importer-element-info -->
</div>
{/block}

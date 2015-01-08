{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css" media="screen">
    .photo {
        display: inline-block;
    }
        .photo img {
            float: left;
            width: 220px;
            margin-right: 10px;
        }
</style>
{/block}

{block name="footer-js" append}
<script>
    jQuery(document).ready(function($){
        $('#importer-element-info').tabs();
    });
</script>
{/block}

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}News Agency{/t}
                    </h4>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="top-action-bar clearfix">
    <div class="content">
        <ul class="old-button">
            <li>
                <a href="{url name=admin_news_agency_pickcategory source_id=$element->source_id id=$element->xml_file}" title="{t}Import{/t}">
                <img src="{$params.IMAGE_DIR}archive_no.png" alt="{t}Import{/t}" ><br />{t}Import{/t}
                </a>
            </li>
            <li>
                <a href="{url name=admin_news_agency}" class="admin_add" title="{t}Go back to list{/t}">
                <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="content">

    {render_messages}

    {if $imported}
    <div class="already_imported">
        {t}This article was imported before. Take care of it.{/t}
    </div><!-- / -->
    {/if}
    <div id="importer-element-info" class="tabs">
        <ul>
            <li><a href="#basic">{t}Basic information{/t}</a></li>
            {if count($element->photos) > 0}<li><a href="#photos">{t}Photos{/t}</a></li>{/if}
            {if count($element->videos) > 0}<li><a href="#videos">{t}Videos{/t}</a></li>{/if}
            {if (count($element->files) > 0) || (count($element->moddocs) > 0)}
            <li><a href="#other-attachments">{t}Other attachments{/t}</a></li>
            {/if}
        </ul><!-- / -->
        <div id="basic">
            <fieldset>
            {if count($element->photos) > 0}
            <div style="float:left; margin-right: 20px">
                {foreach from=$element->photos item=photo}
                <div class="photo" style="width:220px;display:block;">
                    <img src="{url name=admin_news_agency_showattachment source_id=$element->source_id id=$element->id attachment_id=$photo->id index=$photo@index}" alt="{$photo->title}" class="thumbnail">
                    <div>
                        <p>{$photo->title}</p>
                    </div>
                </div>
                {/foreach}
            </div>
            {/if}
                <fieldset>
                    {if $element->pretitle}
                    <p>
                        <strong>{t}Pretitle:{/t}</strong>
                        {$element->pretitle}
                    </p>
                    {/if}
                    <p>
                        <h4>{$element->title}</h4>
                    </p>

                    <p>
                        <strong>{t}Priority:{/t}</strong>
                        {$element->priority}
                    </p>

                    <p>
                        <strong>{t}Date:{/t}</strong> {date_format date=$element->created_time}
                    </p>
                    {if $element->summary}
                        <strong>{t}Summary:{/t}</strong> <br/>
                        {$element->summary}
                    {/if}

                    <p>{$element->body|html_entity_decode}</p>
                </fieldset>
            </fieldset>
        </div>
        {if count($element->photos) > 0}
        <div id="photos" class="clearfix">
            {foreach from=$element->photos item=photo}
            <div class="photo">
                <img src="{url name=admin_news_agency_showattachment source_id=$element->source_id id=$element->id attachment_id=$photo->id index=$photo@index}" alt="{$photo->title}" class="thumbnail">
                <div>
                    <strong>{t}Description{/t}:</strong>
                    <p>{$photo->title}</p>
                </div>
            </div>
            {/foreach}
        </div><!-- /photos -->
        {/if}
        {if count($element->videos) > 0}
        <div id="videos">
            <ul>
            {foreach from=$element->videos item=video}
                <li>{$video->title} ({$video->file_type})</li>
            {/foreach}
            </ul>
        </div><!-- /videos -->
        {/if}
        {if (count($element->files) > 0) || (count($element->moddocs) > 0)}
        <div id="other-attachments">

            {if count($element->files) > 0}
            <strong>{t}Files:{/t}</strong> <br/>
            <ul>
            {foreach from=$element->files item=doc}
                <li>{$file}</li>
            {/foreach}
            </ul>
            {/if}

            {if count($element->moddocs) > 0}
            <strong>{t}Documentary modules:{/t}</strong> <br/>
            <ul>
            {foreach from=$element->moddocs item=doc}
                <li>{$doc}</li>
            {/foreach}
            </ul>
            {/if}

        </div><!-- /other-attachments -->
        {/if}

    </div><!-- /importer-element-info -->
</div>
{/block}

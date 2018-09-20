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

{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/788682-opennemas-agencias-de-noticias" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                          <i class="fa fa-question"></i>
                        </a>
                        {t}News Agency{/t}
                    </h4>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li>
                        <a href="{url name=backend_news_agency}" class="btn btn-link" title="{t}Go back to list{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li>
                        <span class="h-seperate"></span>
                    </li>
                    <li>
                        <a class="btn btn-primary" href="{url name=backend_news_agency_pickcategory source_id=$element->source_id id=$element->xml_file}" title="{t}Import{/t}" id="import_button">
                        <span class="fa fa-cloud-download"></span> <span class="hidden-xs">{t}Import{/t}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="grid simple">
        <div class="grid-body">
            <div class="row">

                {if count($element->photos) > 0}
                <div class="col-md-3">
                        {foreach from=$element->photos item=photo}
                        <div class="photo" style="width:220px;display:block;">
                            <img src="{url name=backend_news_agency_showattachment source_id=$element->source_id id=$element->id attachment_id=$photo->id index=$photo@index}" alt="{$photo->title}" class="thumbnail">
                            <small>
                                <p>{$photo->title}</p>
                            </small>
                        </div>
                        {/foreach}
                </div>
                {/if}
                <div class="{if count($element->photos) > 0}col-md-9{else}col-md-12{/if}">
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
                        <strong>{t}Priority:{/t} </strong>
                        <!--{t}Priority{/t}-->
                        {if $element->priority == 1}<span class="badge badge-important">{t}Urgent{/t}</span>{/if}
                        {if $element->priority == 2}<span class="badge badge-warning">{t}Important{/t}</span>{/if}
                        {if $element->priority == 3}<span class="badge badge-info">{t}Normal{/t}</span>{/if}
                        {if $element->priority < 1 || $element->priority > 3}<span class="badge">{t}Basic{/t}</span>{/if}
                    </p>

                    <p>
                        <strong>{t}Date:{/t}</strong> {date_format date=$element->created_time}
                    </p>
                    {if $element->summary}
                        <strong>{t}Summary:{/t}</strong> <br/>
                        {$element->summary}
                    {/if}

                    <p>{$element->body|html_entity_decode}</p>
                </div>

                <div class="col-md-12">
                    <h4>{t}Attachments{/t}</h4>
                    <p>{t}This agency element has some attachments that could be imported with it.{/t}</p>
                    {if count($element->photos) > 0}
                    <div id="photos" class="clearfix">
                        <h5>{t}Photos{/t}</h5>
                        {foreach from=$element->photos item=photo}
                        <div class="photo">
                            <img src="{url name=backend_news_agency_showattachment source_id=$element->source_id id=$element->id attachment_id=$photo->id index=$photo@index}" alt="{$photo->title}" class="thumbnail" style="width:150px">
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
                        <h5>{t}Videos{/t}</h5>
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
                        <h5>{t}Files:{/t}</h5>
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
                </div>

            </div>
        </div>
    </div>
</div>
{/block}

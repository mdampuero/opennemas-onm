23{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
<style type="text/css">
    #newsletter-content-provider, #newsletter-contents {
        display:inline-block;
        width: 49%;
        vertical-align: top;
    }

    .toolbar {
        margin-bottom:5px;
    }
    .related-content-provider {
        width:100%;
    }
    #newsletter-content-provider .toolbar{
        text-align:right
    }
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/onm/jquery.content-provider.js"}
{script_tag src="/jquery-onm/jquery.newsletter.js"}

{/block}

{block name="content"}

<form action="{url name=admin_newsletter}" method="POST" name="newsletterForm" id="newsletterForm">

    <div id="buttons-contents" class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Newsletter management{/t}</h2>
            </div>

            <ul class="old-button">
                <li>
                    <button type="submit" title="{t}Next{/t}" id="next-button">
                        <img src="{$params.IMAGE_DIR}arrow_next.png" alt="{t}Next{/t}" /><br />
                        {t}Next step{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletter_config}" class="admin_add" title="{t}Config newsletter module{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
                    </a>
                </li>

                <li >
                    <a href="{url name=admin_newsletter_subscriptors}" class="admin_add" id="submit_mult" title="{t}Subscriptors{/t}">
                        <img src="{$params.IMAGE_DIR}authors.png" title="{t}Subscriptors{/t}" alt="{t}Subscriptors{/t}"><br />{t}Subscriptors{/t}
                    </a>
                </li>
            </ul>

        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="alert alert-info">{t}Drag elements from the right column to include them into the newsletter{/t}</div>

        <div class="newsletter-contents">



            <div id="newsletter-contents">
                <div class="btn-group toolbar">
                    <button id="button-add-container" class="btn">
                        <span class="icon-plus"></span>{t}Add Container{/t}
                    </button>
                    <button class="btn" title="{t}Clean containers{/t}" id="clean-button">
                        <i class="icon-trash"></i>{t}Clean contents{/t}
                    </button>
                    {if count($savedNewsletters) > 0}
                    <div id="savedNewsletter" class="input-append">
                        <select name="saved_newsletters" id="saved_newsletters">
                            <option value="0">{t}Load old newsletter contents{/t}</option>
                            {foreach from=$savedNewsletters item=newsletter}
                                <option value="{$newsletter->id}">{$newsletter->created|date_format:"%d/%m/%Y - %H:%M:%S"} </option>
                            {/foreach}
                        </select>
                        <button class="btn" id="load-saved">{t}Load{/t}</button>
                    </div>
                    {/if}
                </div>
                <div id="newsletter-container" class="column-receiver">

                    {if empty($newsletterContent)}
                        <div class="container-receiver active"  data-title="En Portada" data-id="1" >
                            <div class="container-label"><span>{t}In Frontpage{/t}</span>
                                <div class="container-buttons btn-group">
                                        <i class="icon-chevron-down"></i>
                                        <i class="icon-pencil"></i>
                                        <i class="icon-trash"></i>
                                        <i class="icon-clean"></i>
                                </div>
                            </div>
                            <ul class="content-receiver">
                            </ul>
                        </div>
                    {else}
                        {section name=c loop=$newsletterContent}
                            {assign var='contents' value=$newsletterContent[c]->items}
                            {if !empty($contents)}
                            <div class="container-receiver {if $smarty.section.c.first} active{/if}"
                                data-title="{$newsletterContent[c]->title}" data-id="{$newsletterContent[c]->id}">
                                <div class="container-label"><span>{$newsletterContent[c]->title}</span>
                                    <div class="container-buttons btn-group">
                                            <i class="icon-chevron-down"></i>
                                            <i class="icon-pencil"></i>
                                            <i class="icon-trash"></i>
                                            <i class="icon-clean"></i>
                                    </div>
                                </div>
                                <ul class="content-receiver">
                                    {section name=d loop=$contents}
                                        {if !empty($contents[d]->title)}
                                        <li  data-id="{$contents[d]->id}"
                                            {if $contents[d]->content_type eq 'label'} class="container-label" {/if}
                                            data-title="{$contents[d]->title}" data-type="{$contents[d]->content_type}" >
                                             {$contents[d]->type} {$contents[d]->title}
                                             <span class="btn"><i class="icon-trash"></i></span>
                                        </li>
                                        {/if}
                                    {/section}
                                </ul>
                            </div>
                            {/if}
                        {/section}
                    {/if}

                </div>
            </div>
            <div id="newsletter-content-provider">
                <div class="btn-group toolbar">
                    <a id="button-check-all" href="#" class="btn"  title="{t}Check All{/t}">
                        <i class="icon-check"></i>{t}Check All{/t}
                    </a>
                    <a class="btn" id="add-selected" href="#"  title="{t}Add Selected items{/t}" >
                        <i class="icon-plus"></i>{t}Add selected contents{/t}
                    </a>
                </div>
                {include file="newsletter/_partials/container_contents.tpl"}
            </div>

            <textarea style="display:none;" id="newsletterContent" name="newsletterContent" style="width:90%"></textarea>
        </div>
    </div>

</form>
{include file="newsletter/modals/_add_container_label.tpl"}
{include file="newsletter/modals/_update_container_label.tpl"}
{include file="newsletter/modals/_activate_container_alert.tpl"}
{/block}


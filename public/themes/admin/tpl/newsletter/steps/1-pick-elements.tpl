{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_newsletter_save_contents}" method="POST" name="newsletterForm" id="newsletter-pick-elements-form">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Newsletters{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
                <li class="quicklinks hidden-xs">
                  <h5>{t}Pick contents{/t}</h5>
                </li>
            </ul>
           <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_newsletters}" class="btn btn-link" title="{t}Go back to list{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks btn-group">
                        <button class="btn btn-primary" type="submit" title="{t}Next{/t}" id="next-button">
                            <span class="hidden-xs">{t}Next{/t}</span>
                            <span class="fa fa-chevron-right"></span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="content newsletter-manager">

    {render_messages}

    <div class="grid simple">
        <div class="grid-body">
            <div class="form-group">
                <label for="name" class="form-label">{t}Email subject{/t}</label>
                <div class="controls">
                    <input type="text" name="title" id="title" value="{$newsletter->title|default:$name}" required class="form-control"/>
                </div>
            </div>
        </div>
    </div>

    <div class="newsletter-contents">

        <div class="grid simple" id="newsletter-contents">
            <div class="grid-title">
                <div>{t}Drag elements from the right column to include them into the newsletter{/t}</div>
            </div>
            <div class="grid-body">
                <div id="newsletter-container" class="column-receiver col-md-6">
                    <div class="btn-group toolbar">
                        <button id="button-add-container" class="btn">
                            <span class="icon-plus"></span> {t}Add Container{/t}
                        </button>
                        <button class="btn" title="{t}Clean containers{/t}" id="clean-button">
                            <i class="icon-remove"></i> {t}Clean contents{/t}
                        </button>
                    </div>

                    {if empty($newsletterContent)}
                        <div class="container-receiver active"  data-title="En Portada" data-id="1" >
                            <div class="container-label">
                                <span>{t}In Frontpage{/t}</span>
                                <div class="container-buttons">
                                    <i class="fa fa-chevron-up"></i>
                                    <i class="fa fa-pencil"></i>
                                    <i class="fa fa-remove"></i>
                                    <i class="fa fa-trash-o"></i>
                                </div>
                            </div>
                            <ul class="content-receiver">
                            </ul>
                        </div>
                    {else}
                        {section name=c loop=$newsletterContent}
                            {assign var='contents' value=$newsletterContent[c]->items}
                            <div class="container-receiver {if $smarty.section.c.first} active{/if}"
                                data-title="{$newsletterContent[c]->title|clearslash|clean_for_html_attributes}" data-id="{$newsletterContent[c]->id}">
                                <div class="container-label"><span>{$newsletterContent[c]->title|clearslash}</span>
                                    <div class="container-buttons btn-group">
                                        {if $smarty.section.c.first || count($contents) > 0}
                                            <i class="icon-chevron-up"></i>
                                        {else}
                                            <i class="icon-chevron-down"></i>
                                        {/if}
                                        <i class="fa fa-pencil"></i>
                                        <i class="icon-remove"></i>
                                        <i class="icon-trash"></i>
                                    </div>
                                </div>
                                <ul class="content-receiver"
                                {if $smarty.section.c.first || count($contents) > 0}style="display:block;"{/if}>
                                    {section name=d loop=$contents}
                                        {if !empty($contents[d]->title)}
                                        <li  data-id="{$contents[d]->id}"
                                            class="content"
                                            {if $contents[d]->content_type eq 'label'} class="container-label" {/if}
                                            data-title="{$contents[d]->title|clearslash|clean_for_html_attributes}" data-type="{$contents[d]->content_type}" >
                                            {$contents[d]->type} {$contents[d]->title|clean_for_html_attributes}
                                            <span class="icon"><i class="icon-trash"></i></span>
                                        </li>
                                        {/if}
                                    {/section}
                                </ul>
                            </div>
                        {/section}
                    {/if}

                </div>

                <div id="newsletter-content-provider" class="col-md-6">
                    <div class="btn-group toolbar">
                        <a id="button-check-all" href="#" class="btn"  title="{t}Check All{/t}">
                            <i class="icon-check"></i> {t}Check All{/t}
                        </a>
                        <a class="btn" id="add-selected" href="#"  title="{t}Add Selected items{/t}" >
                            <i class="icon-plus"></i> {t}Add selected contents{/t}
                        </a>
                    </div>
                    {include file="newsletter/_partials/container_contents.tpl"}
                </div>
            </div>
        </div>
    </div>


        <div class="newsletter-contents">




            <input type="hidden" id="content_ids" name="content_ids">
            <input type="hidden" name="id" value="{$newsletter->pk_newsletter}">
        </div>
    </div>
</div>
</form>
{/block}

{block name="footer-js" append}
    {script_tag src="/onm/newsletter.js"}
    {script_tag src="/onm/content-provider.js"}
    {script_tag src="/jquery-onm/jquery.onmvalidate.js"}
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#newsletter-pick-elements-form').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });
    {if $with_html}
    var has_contents = true;
    {else}
    var has_contents = false;
    {/if}
    </script>

    {include file="newsletter/modals/_add_container_label.tpl"}
    {include file="newsletter/modals/_update_container_label.tpl"}
    {include file="newsletter/modals/_activate_container_alert.tpl"}
    {include file="newsletter/modals/_back_contents_accept.tpl"}
{/block}

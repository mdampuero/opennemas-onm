{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
#newsletter-content-provider, #newsletter-contents {
    display:inline-block;
    width: 49%;
    vertical-align: top;
}
.content-receiver {
    border:1px solid #ccc;
    border-top:0;
}
.container-label { position:relative; }
.container-buttons {
    position:absolute;
    top:-1px;
    right:10px;
    display:inline-block;
}
.container-buttons i {
    margin-top:0;
    margin-left:3px;
}
.toolbar {
    margin-bottom:5px;
}
.related-content-provider {
    width:100%;
}
#newsletter-content-provider {
    width:50%;
}
#newsletter-content-provider .toolbar{
    text-align:right
}
.contents ul {
    margin:0 !important;
    width:100%;
}
.contents ul li {
    margin:4px 0;
}
.placeholder-element {
    min-height:24px !important;
    background:#efefef !important;
    border:1px dashed Gray !important;
}
</style>
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
{/block}

{block name="content"}

<form action="{url name=admin_newsletter_save_contents}" method="POST" name="newsletterForm" id="newsletter-pick-elements-form">

    <div id="buttons-contents" class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Newsletter{/t} :: {t}Newsletter contents{/t}</h2>
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
                    <a href="{url name=admin_newsletters}" class="admin_add" title="{t}Back to list{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="" /><br />
                        {t}Back to list{/t}
                    </a>
                </li>
            </ul>

        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="panel">
            <div class="control-group">
                <label for="name" class="control-label">{t}Email subject{/t}</label>
                <div class="controls">
                    <input type="text" name="title" id="title" style="width:80%" value="{$newsletter->title|default:$name}" required class="input-xlarge"/>
                </div>
            </div>

            <div>{t}Drag elements from the right column to include them into the newsletter{/t}</div>

            <div class="newsletter-contents">

                <div id="newsletter-contents">
                    <div class="btn-group toolbar">
                        <button id="button-add-container" class="btn">
                            <span class="icon-plus"></span> {t}Add Container{/t}
                        </button>
                        <button class="btn" title="{t}Clean containers{/t}" id="clean-button">
                            <i class="icon-remove"></i> {t}Clean contents{/t}
                        </button>
                    </div>
                    <div id="newsletter-container" class="column-receiver">

                        {if empty($newsletterContent)}
                            <div class="container-receiver active"  data-title="En Portada" data-id="1" >
                                <div class="container-label">
                                    <span>{t}In Frontpage{/t}</span>
                                    <div class="container-buttons">
                                        <i class="icon-chevron-up"></i>
                                        <i class="icon-pencil"></i>
                                        <i class="icon-remove"></i>
                                        <i class="icon-trash"></i>
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
                                            <i class="icon-pencil"></i>
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
                </div>
                <div id="newsletter-content-provider">
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

                <input type="hidden" id="content_ids" name="content_ids">
                <input type="hidden" name="id" value="{$newsletter->pk_newsletter}">
            </div>
        </div>
    </div>

</form>
{include file="newsletter/modals/_add_container_label.tpl"}
{include file="newsletter/modals/_update_container_label.tpl"}
{include file="newsletter/modals/_activate_container_alert.tpl"}
{include file="newsletter/modals/_back_contents_accept.tpl"}
{/block}


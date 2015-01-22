{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
#htmlContent {
    border:1px solid #ccc;
    padding:10px;
    background-color: white;
    max-width:100%;
    display:block;
}
#html-content-textarea {
    display:block;
    width:100%;
    display:none;
    min-height:500px;
}
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/onm/newsletter.js"}
<script type="text/javascript">
$(document).data('saved', true);
var  newsletter_urls = {
    save_contents : '{url name=admin_newsletter_save_html id=$newsletter->id}'
}
</script>

{include file="newsletter/modals/_save_changes_alert.tpl"}
{/block}

{block name="content"}

<form action="{url name=admin_newsletter_save_html id=$newsletter->id}" method="POST" id="newsletter-preview-form">
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
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{t}Creating{/t} :: {t}Preview{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_newsletters}" class="admin_add" title="{t}Go back to list{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks btn-group">
                        <a href="{url name=admin_newsletter_show_contents id=$newsletter->id}" class="btn btn-primary" title="{t}Previous{/t}" id="prev-button">
                            <span class="fa fa-chevron-left"></span>
                            {t}Previous step{/t}
                        </a>
                        <a href="{url name=admin_newsletter_pick_recipients id=$newsletter->id}" class="btn btn-primary" title="{t}Next{/t}" id="next-button">
                            {t}Next step{/t}
                            <span class="fa fa-chevron-right"></span>
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
            <div class="form-group">
                <label for="name" class="form-label">{t}Email subject{/t}</label>
                <div class="controls">
                    <input type="text" name="subject" id="title" value="{$newsletter->title}" required class="form-control"/>
                </div>
            </div>
        </div>
    </div>

    <div class="grid simple">
        <div class="grid-title">
            <h4>{t}Preview{/t}</h4>
            <div id="buttons-preview" class="pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="#" title="{t}Edit{/t}" id="edit-button" class="btn btn-mini btn-default">
                            <span class="fa fa-pencil"></span>
                            {t}Edit{/t}
                        </a>
                    </li>
                    <li id="li-save-button" style="display:none;"  class="quicklinks">
                        <a id="save-button" href="#" class="admin_add" title="{t}Save changes{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}save.png" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="grid-body">
            <div class="control-group">
                <label for="htmlContent" class="control-label"></label>
                <div class="controls">
                    <div id="col-md-9">{$newsletter->html}</div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}

{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .video-type-selector {
        list-style:none;
        margin:40px auto;
        width:50%;
    }
    .video-type-selector li {
        border-radius:3px;
        border:1px solid #ccc;
        background:#eee;
        margin:5px;
    }
    .video-type-selector li:hover {
        background:#dedede;
    }
    .video-type-selector li img {
        vertical-align:middle;
        margin-right:10px;
    }
    .video-type-selector li a {
        padding:10px;
        display:block;
        width:100%:
        height:100%;
        color:#666;
        font-size:1.2em;
        margin-left:10px;
        vertical-align:middle;
    }
    .video-type-selector li a:hover {
        text-decoration:none;
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
                        {t}Videos{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{t}Pick type{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name="admin_videos"}" class="btn btn-link">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="content">

    {render_messages}

    <div class="grid simple">
        <div class="grid-body">
            <h5>{t}Pick the method to add the video{/t}</h5>

            <ul class="video-type-selector">
                {is_module_activated name="VIDEO_LOCAL_MANAGER"}
                <li class="file">
                    <a href="{url name=admin_videos_create type=file category=$category}" class="clearfix">
                        <img src="{$params.IMAGE_DIR}video/video-file-source.png" alt="" />
                        {t}Upload a file from my computer{/t}
                    </a>
                </li>
                {/is_module_activated}
                <li class="web">
                    <a href="{url name=admin_videos_create type="web-source" category=$category}" class="clearfix">
                        <img src="{$params.IMAGE_DIR}video/video-web-source.png" alt="" />
                        {t}Link video from other web video services{/t}
                    </a>
                </li>
                <li class="web">
                    <a href="{url name=admin_videos_create type="script" category=$category}" class="clearfix">
                        <img src="{$params.IMAGE_DIR}video/script-code-48.png" alt="" />
                        {t}Use HTML code{/t}
                    </a>
                </li>
                <li class="web">
                    <a href="{url name=admin_videos_create type="external" category=$category}" class="clearfix">
                        <img src="{$params.IMAGE_DIR}video.png" alt="" />
                        {t}Use file video URLs (External HTML5/FLV){/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>
{/block}

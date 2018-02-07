{extends file="base/admin.tpl"}

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


    <div class="grid simple">
        <div class="grid-body">
            <h5>{t}Pick the method to add the video{/t}</h5>

            <ul class="video-type-selector">
                <li class="web">
                    <a href="{url name=admin_videos_create type='web-source'}" class="clearfix btn btn-white">
                        <i class="fa fa-vimeo fa-lg"></i>
                        <i class="fa fa-youtube fa-lg"></i>
                        <div class="p-t-10">
                          {t}Link video from other web video services{/t}
                        </div>
                    </a>
                </li>
                <li class="web">
                    <a href="{url name=admin_videos_create type=script}" class="clearfix btn btn-white">
                        <i class="fa fa-file-code-o fa-3x"></i>
                        <div class="p-t-10">
                          {t}Use HTML code{/t}
                        </div>
                    </a>
                </li>
                <li class="web">
                    <a href="{url name=admin_videos_create type=external}" class="clearfix btn btn-white">
                        <i class="fa fa-film fa-3x"></i>
                        <div class="p-t-10">
                          {t}Use file video URLs (External HTML5/FLV){/t}
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>
{/block}

{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script>
    $('body').on('click', '.dismiss', function(e, ui) {
        e.preventDefault();
        $.ajax('{url name="admin_acl_user_set_meta"}?initial_tour_done=1');
        $(this).closest('.well').slideUp('fast');
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
                        {t}Welcome to Opennemas{/t}
                    </h4>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="content welcome-page">
    {render_messages}

    <div class="span12">
        {if !$initial_tour_done}
        <div class="well">
            <h4>{t escape=off}<strong>Are you new in Opennemas?</strong> If you need some help getting started to create awesome content, check out our online user documentation.{/t}</h4>
            <div class="pull-right buttons">
                <a href="http://help.opennemas.com/knowledgebase/articles/221740-primeros-pasos-en-opennemas" target="_blank" class="btn">{t}Get started{/t}</a>
                <a href="#dismiss" class="dismiss"> {t}or dismiss{/t}</a>
            </div>
        </div>
        {/if}
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="grid simple add-contents">
                <div class="grid-title">
                    <h4>{t}Add contents to your site{/t}</h4>
                </div>
                <div class="grid-body">
                    <ul class="actions">
                        {is_module_activated name="ARTICLE_MANAGER"}
                        {acl isAllowed="ARTICLE_CREATE"}
                        <li class="col-xs-6 col-sm-4 col-md-3">
                            <a href="{url name=admin_article_create}" title="{t}New article{/t}" class="thumbnail">
                                <i class="fa fa-file-o fa-3x"></i>{t}New article{/t}
                            </a>
                        </li>
                        {/acl}
                        {/is_module_activated}
                        {is_module_activated name="OPINION_MANAGER"}
                        {acl isAllowed="OPINION_CREATE"}
                        <li class="col-xs-6 col-sm-4 col-md-3">
                            <a href="{url name=admin_opinion_create}" title="{t}New opinion{/t}" class="thumbnail">
                                <i class="fa fa-comment-o fa-3x"></i>{t}New opinion{/t}
                            </a>
                        </li>
                        {/acl}
                        {/is_module_activated}
                        {is_module_activated name="IMAGE_MANAGER"}
                        {acl isAllowed="PHOTO_CREATE"}
                        <li class="col-xs-6 col-sm-4 col-md-3">
                            <a href="{url name=admin_image}" title="{t}Media manager{/t}" class="thumbnail">
                                <i class="fa fa-image fa-3x"></i>{t}Upload images{/t}
                            </a>
                        </li>
                        {/acl}
                        {/is_module_activated}

                        {is_module_activated name="ALBUM_MANAGER"}
                        {acl isAllowed="ALBUM_CREATE"}
                        <li class="col-xs-6 col-sm-4 col-md-3">
                            <a href="{url name=admin_album_create}" title="{t}Media manager{/t}" class="thumbnail">
                                <i class="fa fa-stack-overflow fa-3x"></i>{t}New Album{/t}
                            </a>
                        </li>
                        {/acl}
                        {/is_module_activated}

                        {is_module_activated name="VIDEO_MANAGER"}
                        {acl isAllowed="VIDEO_CREATE"}
                        <li class="col-xs-6 col-sm-4 col-md-3">
                            <a href="{url name=admin_videos_create}" title="{t}Media manager{/t}" class="thumbnail">
                                <i class="fa fa-video-camera fa-3x"></i>{t}Upload video{/t}
                            </a>
                        </li>
                        {/acl}
                        {/is_module_activated}

                        {is_module_activated name="STATIC_PAGES_MANAGER"}
                        {acl isAllowed="STATIC_PAGE_CREATE"}
                        <li class="col-xs-6 col-sm-4 col-md-3">
                            <a href="{url name=admin_staticpages_create}" title="{t}Media manager{/t}" class="thumbnail">
                                <i class="fa fa-file-text-o fa-3x"></i>{t}Static page{/t}
                            </a>
                        </li>
                        {/acl}
                        {/is_module_activated}
                    </ul>
                </div>
            </div>

            <div class="grid simple merchant">
                <div class="grid-title">
                    <h4>{t}Want more features?{/t}</h4>
                </div>
                <div class="grid-body">
                    <p>{t}We have two ways to add functionality to you site.{/t}</p>

                    <div class="row">
                        <div class="col-sm-6 plans">
                            <a href="http://help.opennemas.com/knowledgebase/articles/221745-precios-de-opennemas-packs" class="thumbnail" target="_blank">
                                <i class="fa fa-dropbox fa-3x"></i>
                                <div class="title">{t}Plans{/t}</div>
                                <div class="description">{t}Bundles multiple functionality in a reduced price{/t}</div>
                            </a>
                        </div>
                        <div class="col-sm-6 modules">
                            <a href="http://help.opennemas.com/knowledgebase/articles/222016-precios-de-opennemas-m%C3%B3dulos" class="thumbnail" target="_blank">
                                <i class="fa fa-archive fa-3x"></i>
                                <div class="title">{t}Modules{/t}</div>
                                <div class="description">{t}Adds an specific feature in your site{/t}</div>
                            </a>
                        </div>
                    </div>

                    {t}If you have special needs or want some advice extending your site{/t}
                    <a href="javascript:UserVoice.showPopupWidget();" class="btn btn-large contact">{t}Contact us{/t}</a>
                    <i class="fa fa-shopping-cart background-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="grid simple help">
                <div class="grid-title">
                    <h4><i class="fa fa-youtube-play"></i>{t}Need Help?{/t}</h4>
                </div>
                <div class="grid-body">
                    <p>{t}We have created a lot of videos that will teach you to perform easy tasks and advanced tasks.{/t}</p>

                    <div id="myCarousel" class="carousel slide clearfix" data-interval="">
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                            {foreach $youtube_videos  as $videoId}
                            <div class="{if $videoId@iteration == 1}active{/if} item">
                                <div class="video-container">
                                    <iframe width="420" height="315" src="//www.youtube.com/embed/{$videoId}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                            {/foreach}
                        </div>
                        <!-- Carousel nav -->
                        <a class="carousel-control left btn" href="#myCarousel" data-slide="prev"><i class="fa fa-angle-left"></i></a>
                        <a class="carousel-control right btn" href="#myCarousel" data-slide="next"><i class="fa fa-angle-right"></i></a>
                    </div>

                    <p>{t escape=off 1="http://www.youtube.com/user/OpennemasPublishing"}See more help videos in <a href="%1">our YouTube channel</a> or subscribe to it.{/t}</p>
                    <script src="https://apis.google.com/js/plusone.js"></script>
                    <div class="g-ytsubscribe" data-channel="OpennemasPublishing" data-layout="default"></div>

                    <hr>
                    <p>{t}If you prefer you can read our online documentation or if you have any doubt ask us.{/t}</p>

                    <ul>
                        <li><a href="http://help.opennemas.com/knowledgebase">{t}Online documentation{/t}</a></li>
                        <li><a href="javascript:UserVoice.showPopupWidget();">{t}Contact support{/t}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
{include file="welcome/modals/policies.tpl"}
{/block}

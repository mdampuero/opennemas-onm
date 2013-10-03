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
<div class="welcome-page">
    <div class="wrapper-content ">
        {render_messages}
        <div class="brand-link">
            {t}Welcome to Opennemas{/t}
        </div>

        <div class="row-fluid">
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
            <div class="row-fluid">
                <div class="span6">
                    <div class="panel">
                        <h3 class="title">{t}Add content to your site{/t}</h3>
                        <div class="content">
                            <ul class="actions">
                                {is_module_activated name="ARTICLE_MANAGER"}
                                {acl isAllowed="ARTICLE_CREATE"}
                                <li>
                                    <a href="{url name=admin_article_create}" title="{t}New article{/t}" class="thumbnail">
                                        <i class="icon icon-file-alt"></i>{t}New article{/t}
                                    </a>
                                </li>
                                {/acl}
                                {/is_module_activated}
                                {is_module_activated name="OPINION_MANAGER"}
                                {acl isAllowed="OPINION_CREATE"}
                                <li>
                                    <a href="{url name=admin_opinion_create}" title="{t}New opinion{/t}" class="thumbnail">
                                        <i class="icon icon-comment-alt"></i>{t}New opinion{/t}
                                    </a>
                                </li>
                                {/acl}
                                {/is_module_activated}
                                {is_module_activated name="IMAGE_MANAGER"}
                                {acl isAllowed="IMAGE_CREATE"}
                                <li>
                                    <a href="{url name=admin_image_new}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-picture"></i>{t}Upload images{/t}
                                    </a>
                                </li>
                                {/acl}
                                {/is_module_activated}

                                {is_module_activated name="ALBUM_MANAGER"}
                                {acl isAllowed="ALBUM_CREATE"}
                                <li>
                                    <a href="{url name=admin_album_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-stackexchange"></i>{t}New Album{/t}
                                    </a>
                                </li>
                                {/acl}
                                {/is_module_activated}

                                {is_module_activated name="VIDEO_MANAGER"}
                                {acl isAllowed="VIDEO_CREATE"}
                                <li>
                                    <a href="{url name=admin_videos_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-facetime-video"></i>{t}Upload video{/t}
                                    </a>
                                </li>
                                {/acl}
                                {/is_module_activated}

                                {is_module_activated name="STATIC_PAGES_MANAGER"}
                                {acl isAllowed="STATIC_CREATE"}
                                <li>
                                    <a href="{url name=admin_staticpages_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-file-text-alt"></i>{t}Static page{/t}
                                    </a>
                                </li>
                                {/acl}
                                {/is_module_activated}
                            </ul>
                        </div>
                    </div>

                    <div class="panel merchant">
                        <h3 class="title">{t}Upgrade your site{/t}</h3>
                        <div class="content">
                            <p>{t}We have two ways to add functionality to you site.{/t}</p>

                            <div class="row-fluid">
                                <a href="http://help.opennemas.com/knowledgebase/articles/221745-precios-de-opennemas-packs" class="thumbnail plans span6" target="_blank">
                                    <i class="icon icon-dropbox"></i>
                                    <div class="title">{t}Plans{/t}</div>
                                    <div class="description">{t}Bundles multiple functionality in a reduced price{/t}</div>
                                </a>
                                <a href="http://help.opennemas.com/knowledgebase/articles/222016-precios-de-opennemas-m%C3%B3dulos" class="thumbnail modules span6" target="_blank">
                                    <i class="icon icon-archive"></i>
                                    <div class="title">{t}Modules{/t}</div>
                                    <div class="description">{t}Adds an specific feature in your site{/t}</div>
                                </a>
                            </div>

                            {t}If you have special needs or want some advice extending your site{/t}
                            <a href="javascript:UserVoice.showPopupWidget();" class="btn btn-large contact">{t}Contact us{/t}</a>
                            <i class="icon icon-shopping-cart background-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="span6">

                    <div class="panel help">
                        <h3 class="title"><i class="icon icon-youtube-play"></i>{t}Need Help?{/t}</h3>
                        <div class="content">
                            <p>{t}We have created a lot of videos that will teach you to perform from easy tasks to the advanced ones.{/t}</p>

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
                                <a class="carousel-control left btn" href="#myCarousel" data-slide="prev"><i class="icon icon-angle-left"></i></a>
                                <a class="carousel-control right btn" href="#myCarousel" data-slide="next"><i class="icon icon-angle-right"></i></a>
                            </div>

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

        <hr>

    </div>
</div>
{include file="welcome/modals/policies.tpl"}
{/block}

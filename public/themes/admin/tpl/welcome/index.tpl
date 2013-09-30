{extends file="base/admin.tpl"}


{block name="footer-js" append}
<script>
    $('body').on('click', '.dismiss', function(e, ui) {
        e.preventDefault();
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
                <div class="well">
                    <h4>{t escape=off}<strong>Are you new in Opennemas?</strong> If you need some help getting started to create awesome content, check out our online user documentation.{/t}</h4>
                    <div class="pull-right buttons">
                        <a href="http://help.opennemas.com/knowledgebase/articles/221740-primeros-pasos-en-opennemas" target="_blank" class="btn">{t}Get started{/t}</a>
                        <a href="#dismiss" class="dismiss"> {t}or dismiss{/t}</a>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="panel">
                        <h3 class="title">{t}Add content to your site{/t}</h3>
                        <div class="content">
                            <ul class="actions">
                                <li>
                                    <a href="{url name=admin_article_create}" title="{t}New article{/t}" class="thumbnail">
                                        <i class="icon icon-file-alt"></i>{t}New article{/t}
                                    </a>
                                </li>
                                <li>
                                    <a href="{url name=admin_opinion_create}" title="{t}New opinion{/t}" class="thumbnail">
                                        <i class="icon icon-comment-alt"></i>{t}New opinion{/t}
                                    </a>
                                </li>
                                <li>
                                    <a href="{url name=admin_image_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-picture"></i>{t}Upload images{/t}
                                    </a>
                                </li>

                                <li>
                                    <a href="{url name=admin_album_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-stackexchange"></i>{t}New Album{/t}
                                    </a>
                                </li>

                                <li>
                                    <a href="{url name=admin_videos_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-facetime-video"></i>{t}Upload video{/t}
                                    </a>
                                </li>

                                <li>
                                    <a href="{url name=admin_staticpages_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-file-text-alt"></i>{t}Static page{/t}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel merchant">
                        <h3 class="title">{t}Do you want to extend your site?{/t}</h3>
                        <div class="content">
                            <p>{t}We have a lot of modules that add functionality to you site.{/t}</p>
                            <ul>
                                {foreach $modules as $module}
                                <li>
                                    <a href="#" title="{t}New article{/t}">
                                        <strong>{$module}</strong>
                                        <small>Module explanation</small>
                                    </a>
                                </li>
                                {/foreach}
                            </ul>
                            {t}If you are interested in one of this modules. Contact with us by using the Help -> Contact us link in the bar above.{/t}
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
                                <a class="carousel-custom-control pull-left btn" href="#myCarousel" data-slide="prev"><i class="icon icon-angle-left"></i></a>
                                <a class="carousel-custom-control pull-right btn" href="#myCarousel" data-slide="next"><i class="icon icon-angle-right"></i></a>
                            </div>

                            <hr>
                            <p>{t}If you prefer you can read our online documentation or if you have any doubt ask us.{/t}</p>

                            <ul>
                                <li><a href="http://help.opennemas.com/knowledgebase">{t}Knownledge base{/t}</a></li>
                                <li><a href="javascript:UserVoice.showPopupWidget();" class="support-button">{t}Contact support{/t}</a></li>
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

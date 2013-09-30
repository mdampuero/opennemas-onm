{extends file="base/admin.tpl"}

{block name="content"}
<!-- <div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t 1="OpenNemas"}Welcome to %1{/t}</h2></div>

        <div class="buttons" style="display:none">
            <a href="{url name=admin_images}" class="button" title="{t}Go to multimedia manager{/t}">
                {t}Media manager{/t}
            </a>
            <a href="{url name=admin_opinion_create}" class="button" title="{t}Create new opinion{/t}">
                {t}New opinion{/t}
            </a>
            <a href="{url name=admin_article_create}" class="button" title="{t}Create new article{/t}">
                <span class="icon home">{t}New article{/t}</span>
            </a>
        </div>
    </div>
</div> -->
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
                        <h3 class="title">{t}Add content{/t}</h3>
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
                                    <a href="{url name=admin_video_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-facetime-video"></i>{t}Upload video{/t}
                                    </a>
                                </li>

                                <li>
                                    <a href="{url name=admin_static_page_create}" title="{t}Media manager{/t}" class="thumbnail">
                                        <i class="icon icon-file-text-alt"></i>{t}Static page{/t}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel">
                        <h3 class="title">{t}Discover new modules for your site{/t}</h3>
                        <div class="content">
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
                            If you are interested in one of this modules. Contact with us by using the Help -> Contact us link in the bar above.
                        </div>
                    </div>
                </div>
                <div class="span6">

                    <div class="panel help">
                        <h3 class="title"><i class="icon icon-youtube-play"></i>{t}Latest videotutorials{/t}</h3>
                        <div class="content">
                            <p>Check our latest video tutorials</p>

                            <div id="myCarousel" class="carousel slide" data-interval="">
                                <!-- Carousel items -->
                                <div class="carousel-inner">
                                    <div class="active item">
                                        <div class="video-container">
                                            <iframe width="420" height="315" src="//www.youtube.com/embed/39TM-pMZUFw" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="video-container">
                                            <iframe width="420" height="315" src="//www.youtube.com/embed/39TM-pMZUFw" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="video-container">
                                            <iframe width="420" height="315" src="//www.youtube.com/embed/39TM-pMZUFw" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>
                                <!-- Carousel nav -->
                                <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                                <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
                            </div>

                            <hr>
                            <p>Or check our available online help:</p>

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

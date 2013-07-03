{extends file="base/admin.tpl"}

{block name="prototype"}{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t 1="OpenNemas"}Welcome to %1{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_images}" class="admin_add"
                   title="{t}Media manager{/t}">
                    <img src="{$params.IMAGE_DIR}/icons.png" title="" alt="" />
                    <br />{t}Media manager{/t}
                </a>
            </li>
            <li>
                <a href="{url name=admin_opinion_create}" class="admin_add"
                   title="{t}New opinion{/t}">
                    <img src="{$params.IMAGE_DIR}opinion.png" title="" alt="" />
                    <br />{t}New opinion{/t}
                </a>
            </li>
            <li>
                <a href="{url name=admin_article_create}" class="admin_add"
                   title="{t}New article{/t}">
                        <img src="{$params.IMAGE_DIR}/article_add.png" title="" alt="" />
                    <br />{t}New article{/t}
                </a>
            </li>
        </ul>
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
</div>
<div class="wrapper-content welcome-page">
    {render_messages}
    <div class="hero-unit">
        <h1>{t}Welcome to your OpenNemas site!{/t}</h1>
        <p class="lead">{t escape=off 1="http://help.opennemas.com/knowledgebase/articles/220778-primeros-pasos-en-opennemas"}If you need help getting started, check out our documentation on <a href="%1">First Steps with OpenNemas</a>.
           If you’d rather dive right in, here are a few things most people do first when they set up
           a new OpenNemas site.{/t}
        </p>
        <p><a class="onm-button blue">{t}Learn more{/t}</a></p>
    </div>
    <div class="welcome-panel-column-container">
        <div class="welcome-panel-column">
            <h4><span class="icon16 icon-cog"></span> {t}Basic Settings{/t}</h4>
            <p>{t}Here are a few easy things you can do to get your feet wet. Make sure to click Save on each Settings screen.{/t}</p>
            <ul>
            <li><a href="{url name=admin_system_settings}">{t}Change your site name and preferences{/t}</a></li>
            <li><a href="{url name=admin_system_settings}#misc">{t}Select your tagline and time zone{/t}</a></li>
            <li><a href="{url name=admin_acl_user_show id=$smarty.session.userid}">{t}Fill in your profile{/t}</a></li>
            </ul>
        </div>
        <div class="welcome-panel-column">
            <h4><span class="icon16 icon-th-large"></span> {t}Add your own Content{/t}</h4>
            <p>{t escape=off}Check out the <a href="#">sample page</a> &amp; <a href="#">post</a> editors to see how it all works, then delete the default content and write your own!{/t}</p>
            <ul>
            <li>{t escape=off 1={url name=admin_staticpages_create} 2={url name=admin_article_create}}Create a <a href="%1">new page</a> and <a href="%2">article</a>{/t}</li>
            </ul>
        </div>
        <div class="welcome-panel-column welcome-panel-last">
            <h4><span class="icon16 icon-book"></span>{t}Customize Your Site{/t}</h4>
            <p>{t}Do you want more information check out our documentation.{/t}</p>
            <ul>
                <li><a href="{url name=admin_system_settings}">{t}Set a background color{/t}</a></li>
                <li><a href="{url name=admin_system_settings}">{t}Select a new header image{/t}</a></li>
            </ul>
        </div>
    </div>
    <hr>

</div>
{/block}

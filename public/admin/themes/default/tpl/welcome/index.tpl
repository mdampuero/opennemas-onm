{extends file="base/admin.tpl"}

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
        <p class="lead">{t escape=off}If you need help getting started, check out our documentation on <a href="#">First Steps with OpenNemas</a>.
           If you’d rather dive right in, here are a few things most people do first when they set up
           a new OpenNemas site.{/t}
        </p>
        <p><a class="onm-button blue">Learn more</a></p>
    </div>
    <div class="welcome-panel-column-container">
        <div class="welcome-panel-column">
            <h4><span class="icon16 icon-cog"></span> {t}Basic Settings{/t}</h4>
            <p>Here are a few easy things you can do to get your feet wet. Make sure to click Save on each Settings screen.</p>
            <ul>
            <li><a href="{url name=admin_system_settings}">{t}Change your site name and preferences{/t}</a></li>
            <li><a href="{url name=admin_system_settings}#misc">{t}Select your tagline and time zone{/t}</a></li>
            <li><a href="{url name=admin_acl_user_show id=$smarty.session.userid}">{t}Fill in your profile{/t}</a></li>
            </ul>
        </div>
        <div class="welcome-panel-column">
            <h4><span class="icon16 icon-th-large"></span> {t}Add your own Content{/t}</h4>
            <p>Check out the <a href="#">sample page</a> &amp; <a href="#">post</a> editors to see how it all works, then delete the default content and write your own!</p>
            <ul>
            <li>View the <a href="{url name=admin_staticpages_create}">sample page</a> and <a href="#">post</a></li>
            <li>Delete the <a href="{url name=admin_staticpages}">sample page</a> and <a href="#">post</a></li>
            <li><a href="#">Create an About Me page</a></li>
            <li><a href="#">Write your first article</a></li>
            </ul>
        </div>
        <div class="welcome-panel-column welcome-panel-last">
            <h4><span class="icon16 icon-book"></span> Customize Your Site</h4>
            <p>{t}Do you want more information check out our documentation.{/t}</p>
            <ul>
                <li><a href="#">Set a background color</a></li>
                <li><a href="#">Select a new header image</a></li>
                <li><a href="#">Add some widgets</a></li>
            </ul>
        </div>
    </div>
    <hr>

</div>
{/block}

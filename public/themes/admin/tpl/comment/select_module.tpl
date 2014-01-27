{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .comment-type-selector {
        list-style:none;
        margin:40px auto;
        width:50%;
    }
    .comment-type-selector li {
        border-radius:3px;
        border:1px solid #ccc;
        background:#eee;
        margin:20px;
    }
    .comment-type-selector li:hover {
        background:#dedede;
    }
    .comment-type-selector li i,
    .comment-type-selector li img {
        vertical-align:middle;
        margin-right:10px;
    }
    .comment-type-selector li img {
        width: 45px;
    }
    .comment-type-selector li i.icon {
        font-size: 3em;

    }
    .comment-type-selector li a {
        padding:10px;
        display:block;
        width:100%;
        height:100%;
        color:#666;
        font-size:1.2em;
        margin-left:10px;
        vertical-align:middle;
    }
    .comment-type-selector li a:hover {
        text-decoration:none;
    }
    .icon-facebook-sign { color:#354c8c; }
    .icon-comment { color:#222; }
</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Comments{/t}</h2></div>
    </div>
</div>

<div class="wrapper-content">

    {render_messages}

    <h4><div class="step-number">1</div> {t}Pick the method to manage comments:{/t}</h4>

    <ul class="comment-type-selector">
        <li>
            <a href="{url name=admin_comments_select type=onm}" class="clearfix">
                <i class="icon icon-comment"></i>
                {t}Opennemas{/t}
            </a>
        </li>
        <li>
            <a href="{url name=admin_comments_select type=disqus}" class="clearfix">
                <img src="{$params.IMAGE_DIR}/disqus-icon.png" alt="Disqus" />
                {t}Disqus{/t}
            </a>
        </li>
        <li>
            <a href="{url name=admin_comments_select type=facebook}" class="clearfix">
                <i class="icon icon-facebook-sign"></i>
                {t}Facebook{/t}
            </a>
        </li>
    </ul>

</div>
{/block}

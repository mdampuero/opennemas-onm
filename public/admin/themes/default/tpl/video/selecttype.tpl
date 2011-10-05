{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .video-type-selector {
        list-style:none;
        margin:40px auto;
        width:50%;
    }
    .video-type-selector li {
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
<form action="#" method="post" name="formulario" id="formulario">

<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Video manager{/t} :: {t}Which type of video do you want to add?{/t}</h2></div>
	</div>
</div>

    <div class="wrapper-content">
        <ul class="video-type-selector">
            <li class="file">
                <a href="#" class="clearfix">
                    <img src="{$params.IMAGE_DIR}video/video-file-source.png" alt="" />
                    {t}Upload file from my computer{/t}
                </a>
            </li>
            <li class="web">
                <a href="{$smarty.server.PHP_SELF}?action=new&type=web-source&category={$category}" class="clearfix">
                    <img src="{$params.IMAGE_DIR}video/video-web-source.png" alt="" />
                    Link video from other web video services<br>
                </a>
            </li>
        </ul>
        
	</div>
</form>
{/block}

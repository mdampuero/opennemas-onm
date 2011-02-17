{extends file="base/admin.tpl"}

{block name="header-css" append}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}utilities.css"/>
<style type="text/css">
    .gf-title, .gf-title:hover{
        font-size:1.1em;
        margin-bottom:10px;
        color: #0B55C4;
    }
    .gf-title:hover {
        text-decoration:underline;
    }
    .gf-snippet {
        margin:5px;
        margin-bottom:15px;
    }
    .gf-author {
        margin-left:5px;
    }
    .gf-author,
    .gf-relativePublishedDate {
        font-size:.9em;
        color:#888
    }
    .gf-relativePublishedDate {
        display:block;
    }
</style>
{/block}

{block name="footer-js" append}
<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAAm85YhpjwWOAjVRurtFoZeBTmeauUFXdDTHxXlqQ2gYMcEYi9-xS0s4NcIHse4XpBCrOhkmD7LoZW6A"></script>
<script type="text/javascript" src="{$params.JS_DIR}/jquery/jquery.min.js"></script>
<script type="text/javascript" src="{$params.JS_DIR}feed/feed.js"></script>

<script type="text/javascript">
$.noConflict();
{if $feeds neq null}
{section name="feed" loop=$feeds}
    jQuery(document).ready(function() {
        jQuery("#feed-{$smarty.section.feed.index}").gFeed ({
            url: '{$feeds[feed].url}',
            max: 4
        });
    });
{/section}

{/if}
</script>
{/block}

{block name="content"}
<div id="menu-acciones-admin" style="width:70%;margin:0 auto;">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Welcome to OpenNemas{/t}</h2>
    </div>
    <ul>
        <li>
            <a href="mediamanager.php?category=GLOBAL" class="admin_add"
               title="{t}Gestor de imagenes{/t}">
                <img border="0" src="{$params.IMAGE_DIR}/icons.png" title="" alt="" />
                <br />{t}Gestor de imagenes{/t}
            </a>
        </li>
        <li>
            <a href="controllers/opinion/opinion.php?action=new" class="admin_add"
               title="{t}Nueva opinion{/t}">
                <img border="0" src="{$params.IMAGE_DIR}opinion.png" title="" alt="" />
                <br />{t}Crear opinion{/t}
            </a>
        </li>
        <li>
            <a href="article.php?action=new" class="admin_add"
               title="{t}Nueva noticia{/t}">
                <img border="0" src="{$params.IMAGE_DIR}/article_add.gif" title="" alt="" />
                <br />{t}Crear noticia{/t}
            </a>
        </li>
    </ul>
</div>
<br/>
<table class="adminheading" style="width:70%;margin:0 auto;">
    <tbody>
        <tr>
            <th>{t}Ahora mismo en otros diarios digitales...{/t}</th>
        </tr>
    </tbody>
</table>

<table border="0" cellpadding="4" cellspacing="0" class="adminlist" style="width:70%;margin:0 auto;">

    <tbody>
    {if $feeds neq null}
            <tr style=" display:block; padding:10px !important;">

        {section name="feed" loop=$feeds}
            <td style="vertical-align:top">
                <h3>Noticias en {$feeds[feed].name}:</h3>
                <div id="feed-{$smarty.section.feed.index}"></div>
            </td>
        {sectionelse}
            <td>No tiene configurado ningún RSS</td>
        {/section}
                </tr>

    {/if}
    </tbody>

    <tfoot>
        <tr>
            <td colspan="5" align="center">
                &nbsp;
            </td>
        </tr>
    </tfoot>
</table>
{/block}

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
            max: 3
        });
    });
{/section}

{/if}
</script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t 1="OpenNemas"}Welcome to %1{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="controllers/mediamanager/mediamanager.php" class="admin_add"
                   title="{t}Media manager{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}/icons.png" title="" alt="" />
                    <br />{t}Media manager{/t}
                </a>
            </li>
            <li>
                <a href="controllers/opinion/opinion.php?action=new" class="admin_add"
                   title="{t}New opinion{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}opinion.png" title="" alt="" />
                    <br />{t}New opinion{/t}
                </a>
            </li>
            <li>
                <a href="article.php?action=new" class="admin_add"
                   title="{t}New article{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}/article_add.png" title="" alt="" />
                    <br />{t}New article{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    {if isset($smarty.session.messages)
        && !empty($smarty.session.messages)}
        {messageboard type="inline"}
    {else}
        {messageboard type="growl"}
    {/if}
    <br/>
    <table class="adminheading">
        <tbody>
            <tr>
                <th>{t}News in other online newspapers...{/t}</th>
            </tr>
        </tbody>
    </table>

    <table border="0" cellpadding="4" cellspacing="0" class="adminlist" >

        <tbody>
        {if $feeds neq null}
            <tr style=" display:block; padding:10px !important;">

            {section name="feed" loop=$feeds|default:array()}
                <td style="vertical-align:top">
                    <h3>{t 1=$feeds[feed].name}News from "%1"{/t}</h3>
                    <div id="feed-{$smarty.section.feed.index}"></div>
                </td>
            {sectionelse}
                <td>{t}You don't have RSS configured to show here{/t}</td>
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

</div>
{/block}

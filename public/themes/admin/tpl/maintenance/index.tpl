{extends file="base/admin.tpl"}

{block name="title"}<title>{t}Instance not activated - Opennemas{/t}</title>{/block}

{block name="content"}
<br>
<br>
    <div class="code"><i class="fa fa-lg fa-cogs"></i></div>
    <div class="desc">{t}We are improving our platform.{/t}</div>
    <div class="explanation">{t}We are doing some maintenance in the Opennemas platform, please wait a few minutes.{/t}</div>
{/block}

 {block name="header_links"}{/block}
 {block name="global-js"}{/block}
 {block name="comments"}{/block}

{block name="quick-create"}{/block}
{block name="sidebar"}{/block}

{block name="header-css" append}
<style>
.code {
  font-size:5em;
}
.view {
  margin-bottom:0px;
  margin:100px auto;
  text-align:center;
  color:#1b1e24;
}
.desc {
font-size:3em;
font-weight: bold;
}
.server-sidebar .page-content {
  padding-left:0px !important;
}
</style>
{/block}

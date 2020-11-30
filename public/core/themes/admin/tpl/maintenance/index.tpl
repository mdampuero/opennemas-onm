{extends file="base/admin.tpl"}

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

{block name="header"}{/block}
{block name="sidebar"}{/block}

{block name="content"}
  <div class="code"><i class="fa fa-lg fa-cogs"></i></div>
  <div class="desc">{t}We are improving our platform.{/t}</div>
  <div class="explanation">{t}We are doing some maintenance in the Opennemas platform, please wait a few minutes.{/t}</div>
{/block}

{block name="global-js"}{/block}

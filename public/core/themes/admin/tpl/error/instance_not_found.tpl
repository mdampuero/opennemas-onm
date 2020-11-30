{extends file="error/404.tpl"}

{block name="page_container"}
  <div class="error-container">
    <div class="error-main">
      <div class="error-description">{t 1=$host escape=off}<a href="http://www.opennemas.com">%1</a> doesn’t exist.{/t}</div>
      <div class="error-description-mini">{t 1=$host escape=off}Do you want to register <a href="http://www.opennemas.com">%1?</a>{/t}</div>
    </div>
  </div>
{/block}

 {block name="header_links"}{/block}
 {block name="global-js"}{/block}
 {block name="comments"}{/block}

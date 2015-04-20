{extends file="error/404.tpl"}

{block name="page_container"}
  <div class="error-container">
    <div class="error-main">
      <div class="error-description">{t 1=$server->get('SERVER_NAME')}%1 doesn’t exist.{/t}</div>
      <div class="error-description-mini">{t 1=$server->get('SERVER_NAME')}Do you want to register %1?{/t}</div>
    </div>
  </div>
{/block}

 {block name="header_links"}{/block}
 {block name="global-js"}{/block}
 {block name="comments"}{/block}

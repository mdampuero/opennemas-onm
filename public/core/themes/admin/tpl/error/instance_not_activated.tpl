{extends file="error/instance_not_found.tpl"}

{block name="title"}<title>{t 1=$host}%1 not activated - Opennemas{/t}</title>{/block}

{block name="page_container"}
  <div class="error-container">
    <div class="error-main">
      <div class="error-description">{t 1=$host}%1 temporary deactivated.{/t}</div>
      <div class="error-description-mini">This newspaper is temporary deactivated, please come back soon.</div>
    </div>
  </div>
{/block}

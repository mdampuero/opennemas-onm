{extends file="error/instance_not_found.tpl"}

{block name="title"}<title>{t 1=$server->get('SERVER_NAME')}'%1' not activated - Opennemas{/t}</title>{/block}

{block name="content"}
    <div class="desc">{t 1=$server->get('SERVER_NAME')}%1 temporary deactivated.{/t}</div>
    <div class="explanation">This newspaper is temporary deactivated, please come back soon.</div>
{/block}

{extends file="error/instance_not_found.tpl"}

{block name="title"}<title>{t}Instance not activated - Opennemas{/t}</title>{/block}

{block name="content"}
    <div class="code"><i class="icon-warning-sign"></i></div>
    <div class="desc">{t}Online newspaper not activated.{/t}</div>
    <div class="buttons">
        <a href="http://www.opennemas.com" class="btn btn-primary btn-large">{t}Contact with support.{/t}</a>
    </div>
{/block}

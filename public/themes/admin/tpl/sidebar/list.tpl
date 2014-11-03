{extends file="base/admin.tpl"}

{block name="header-js" append}
    {include file="common/angular_includes.tpl"}
{/block}

{block name="content"}
<form action="{url name=admin_widgets}" method="GET" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Sidebars{/t}</h2>
            </div>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <div>
            Nothing here for now
        </div>
    </div>
</form>
{/block}

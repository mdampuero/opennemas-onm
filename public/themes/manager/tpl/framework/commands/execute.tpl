{extends file="base/base.tpl"}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Application commands{/t}</h2>
        </div>
        <div class="old-buttons pull-right">
            <a href="{url name=manager_framework_commands}">
                <img border="0" src="{$params.COMMON_ASSET_DIR}images/previous.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" /><br />
                Go back
            </a>
        </div>
    </div>
</div>
<div class="wrapper-content">
    <pre>{$output}</pre>
</div>
{/block}

{extends file="base/base.tpl"}

{block name="content"}
<div class="clearfix"></div>
<div class="content">
    <div class="page-title">
        <h2>{t}Application command output{/t}</h2>
    </div>
    <div class="wrapper-table-block">
        <h4 class="command-name"><strong>Command name:</strong> {$name}</h4>

        <pre class="command-output">{$output}</pre>

        <a href="{url name=manager_framework_commands}" class="btn">
            Go back
        </a>
    </div>
</div>
{/block}

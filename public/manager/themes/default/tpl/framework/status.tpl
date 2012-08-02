{extends file="base/base.tpl"}

{block name="content"}
<div class="top-action-bar">
    <div class="wrapper-content">
        <div class="title">
            <h2>{t}Framework status{/t}</h2>
        </div>
    </div>
</div>
<div class="wrapper-content">
    <pre>{$status}</pre>
</div>
{/block}

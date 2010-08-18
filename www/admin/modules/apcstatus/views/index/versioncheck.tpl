{extends file='layout.tpl'}

{block name='body-content'}


{*include file="menu.tpl"*}

<div class="info">
    <h2>{t}APC Version Information{/t}</h2>
    {if $error_from_feed}
        <div>{t}Unable to fetch version information.{/t}</div>
    {else}
        {if $version_compare eq 1}
            <div class="ok">{t}You are running the latest version of APC ({$apc_version}){/t}</div>
        {else}
            <div class="failed">{t}You are running an older version of APC ({$apc_version}), 
                newer version {$version_match[1]} is available at <a href="http://pecl.php.net/package/APC{$version_match[1]}">
                http://pecl.php.net/package/APC/{$version_match[1]}{/t}</a>
            </div>
        {/if}
    {/if}
    <h3>{t}Change Log:{/t}</h3><hr/>
    <div>{$description_new_versions}</div>
</div>

{/block}
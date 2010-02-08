{if $smarty.request.action == 'getOpinion' || $smarty.request.action == 'getArticle'}{$message}{/if}

{if $smarty.request.action == 'searchNew'}
    {section loop=$articles name=a}
        <li onclick="javascript:transferData(this);"
            data="{ldelim}'pk_content': {$articles[a]->id}, 'title': '{$articles[a]->title|clearslash|strip_tags|base64encode}', 'subtitle': '{$articles[a]->subtitle|clearslash|strip_tags|base64encode}', 'summary': '{$articles[a]->summary|clearslash|strip_tags|base64encode}'{rdelim}">
                {$articles[a]->title}</li>
    {sectionelse}
        <li>Sin resultados</li>
    {/section}
{/if}

{if $smarty.request.action == 'searchArchive'}
    {section loop=$archives name=a}
        <li onclick="javascript:transferDataA(this);"
            data="{$archives[a]->data|base64encode}">
                {$archives[a]->created}</li>
    {sectionelse}
        <li>Sin resultados</li>
    {/section}
{/if}
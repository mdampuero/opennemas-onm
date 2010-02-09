{if count($terms)>0}
    <ul>
    {section name="s" loop=$terms}
        <li>            
            <a href="?action=read&id={$terms[s]->id}" title="{$terms[s]->tipo}: {$terms[s]->value|escape:"html"}">
                {$terms[s]->pclave}</a>            
        </li>        
    {/section}
    </ul>
{/if}
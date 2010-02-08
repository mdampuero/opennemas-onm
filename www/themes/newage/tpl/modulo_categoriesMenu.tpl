{foreach key=k item=v from=$categories[$posmenu].subcategories}    
    <div class="elemMenuBarraFecha elemMenuBarraFechaSec">        
        {if $subcategory_name eq $k}
            <a style="text-decoration:underline;" href="/seccion/{$categories[$posmenu].name}/{$k}/">{$v}</a>
        {else}
            <a href="/seccion/{$categories[$posmenu].name}/{$k}/">{$v}</a>
        {/if}
    </div>
    <div class="separadorElemMenuBarraFecha separadorElemMenuBarraFechaSec"></div>
{/foreach}
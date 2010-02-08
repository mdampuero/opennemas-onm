<div class="containerActualidad">
    {include file="modulo_actualidadvideos.tpl"}
    {if #container_noticias_gente# == '0'}
        {include file="index_gente.tpl"}
    {/if}
    {if #container_noticias_fotos# == '0'}
        {include file="modulo_actualidadfotos.tpl"}
    {/if}
</div>
<div class="header">
    <div class="logoXornalYBanner">        
        <div class="logoXornal"><a href="/"><img src="{$params.IMAGE_DIR}xornal-logo.jpg" alt="" /></a></div>
        {* renderbanner banner=$banner3 photo=$photo3 cssclass="zonaBannerYMenuInferior" width="468" height="60" *}
        {insert name="renderbanner" type=3 cssclass="zonaBannerYMenuInferior" width="610" height="70"}
    </div>
    
    {include file="modulo_sections_menu.tpl"}
    {include file="modulo_zonaHoraBusqueda.tpl"}
    
    {if $category_name == 'home'}
        {include file="modulo_carousel.tpl"}
    {/if}      
</div>
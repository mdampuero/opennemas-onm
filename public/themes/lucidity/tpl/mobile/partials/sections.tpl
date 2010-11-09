
<ul id="navtabs" class="clearfix">
    <div class="centered">
    <li><a href="{$smarty.const.BASE_PATH}/" {if $section eq "home"}id="current"{/if}>portada</a></li>
        <li><a href="{$smarty.const.BASE_PATH}/ultimas-noticias/" id="{if $section eq "ultimas"}current{/if}">&Uacute;ltimas</a></li>
        <li><a href="{$smarty.const.BASE_PATH}/opinions/" id="{if $section eq 'opinion'}current{/if}">opinion</a></li>
        {section name="ed" loop=$mobileCategories}
        <li><a href="{$smarty.const.BASE_PATH}/seccion/{$mobileCategories[ed]->name}/" class={$mobileCategories[ed]->name}" id="{if $category_name eq $mobileCategories[ed]->name}current{/if}"
               {if strtolower( $mobileCategories[ed]->name ) eq $section} id="current"{/if}>{$mobileCategories[ed]->name}</a></li>
        {/section}
    </div>
</ul>

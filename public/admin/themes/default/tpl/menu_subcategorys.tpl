<ul style="list-style:none;">
{section name=as loop=$allcategorys}
    {section name=su loop=$subcat[as]}
        {* if $smarty.session.isAdmin || is_array($smarty.session.accesscategories) && in_array($allcategorys[as]->pk_content_category, $smarty.session.accesscategories) *}
        {assign var=subca value=$subcat[as][su]->pk_content_category}
        {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
            {if (preg_match('/video\.php/',$home)) || (!preg_match('/video\.php/',$home) && ($subcat[as][su]->internal_category neq '5'))}
            {if (preg_match('/album\.php/',$home)) || (!preg_match('/album\.php/',$home) && ($subcat[as][su]->internal_category neq '3'))}
                {if $allcategorys[as]->pk_content_category eq $category}
                     <li>
                        <a  href="{$home}&category={$subcat[as][su]->pk_content_category}" class="links {if $category==$subca}active {/if}" >
                         <span style="color:#222 ;margin-left: 12px;margin-right: 12px;">
                           {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0} <img src="{$params.IMAGE_DIR}publish_r.png" style="width:10px;"/>{/if}{$subcat[as][su]->title}
                           </span></a>
                     </li>
                {else}
                    {if $subcat[as][su]->fk_content_category eq $datos_cat[0]->fk_content_category}
                         {assign var=subca value=$subcat[as][su]->pk_content_category}
                         <li>
                             <a class="links" href="{$home}&category={$subcat[as][su]->pk_content_category}" class="links {if $category==$subca}active {/if}" >
                             <span style="color:#222 ;margin-left: 12px;margin-right: 12px;">
                             {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0} <img src="{$params.IMAGE_DIR}publish_r.png" style="width:10px;"/>{/if}{$subcat[as][su]->title}
                             </span></a>
                         </li>
                    {/if}
                {/if}
            {/if}
            {/if}
        {/acl}
        {* /if *} {* Si é unha categoría accesible *}
    {/section}
{/section}
</ul>

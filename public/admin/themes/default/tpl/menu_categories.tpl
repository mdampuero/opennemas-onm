{section name=as loop=$allcategorys}
{acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
    {assign var=ca value=$allcategorys[as]->pk_content_category}
    {if (preg_match('/video\.php/',$home)) || (!preg_match('/video\.php/',$home) && ($allcategorys[as]->internal_category neq '9'))}
    
    <li class="{if count($subcat[as]) > 0}with-subcategories{/if} {if $category==$ca} active {elseif $ca eq $datos_cat[0]->fk_content_category}active{/if}">
        <a  {if $home} href="{$home}&category={$ca}" {/if} id="link_{$ca}"  class="links {if $category==$ca} active {else}{if $ca eq $datos_cat[0]->fk_content_category}active {/if}{/if}" >
        {if $allcategorys[as]->inmenu eq 0}<img src="{$params.IMAGE_DIR}publish_r.png" style="width:10px;height:10px;"/>{/if}
        {$allcategorys[as]->title}
        {if $allcategorys[as]->internal_category eq 7}
             <img src="{$params.IMAGE_DIR}album.png" border="0"  style="height:11px; " alt="Sección de Album" />
        {elseif $allcategorys[as]->internal_category eq 9}
             <img src="{$params.IMAGE_DIR}video.png" border="0"  style="height:11px; " alt="Sección de Videos" />
        {/if}
        </a>

        <ul>
            {section name=su loop=$subcat[as]}
            {assign var=subca value=$subcat[as][su]->pk_content_category}
            {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                {if $allcategorys[as]->pk_content_category eq $category}
                <li>
                    <a href="{$home}&category={$subcat[as][su]->pk_content_category}" class="links {if $category==$subca}active {else}{if $subca eq $datos_cat[0]->fk_content_category}active{/if} {/if}" >
                        {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                        <img src="{$params.IMAGE_DIR}publish_r.png" style="width:10px;"/>
                        {/if}
                        {$subcat[as][su]->title}
                    </a>
                </li>
                {else}
                    {assign var=subca value=$subcat[as][su]->pk_content_category}
                    <li>
                        <a  href="{$home}&category={$subcat[as][su]->pk_content_category}" class="links {if $category==$subca}active {else}{if $subca eq $datos_cat[0]->fk_content_category}active{/if} {/if}" >
                            {if $subcat[as][su]->inmenu eq 0 || $allcategorys[as]->inmenu eq 0}
                            <img src="{$params.IMAGE_DIR}publish_r.png" style="width:10px;"/>
                            {/if}
                            {$subcat[as][su]->title}
                        </a>
                    </li>
                {/if}
            {/acl}
            {/section}
        </ul>
    </li>
    {/if}
     
{/acl}
{/section}

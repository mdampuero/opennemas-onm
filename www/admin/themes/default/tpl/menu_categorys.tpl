    {section name=as loop=$allcategorys}
        {* if $smarty.session.isAdmin || is_array($smarty.session.accesscategories) && in_array($allcategorys[as]->pk_content_category, $smarty.session.accesscategories) *}
        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
            <li>
                {assign var=ca value=`$allcategorys[as]->pk_content_category`}
                <a class="links" {if $home} href="{$home}&category={$ca}#" {else} id="link_{$ca}" {/if} {if $category==$ca } style="color:#000000; font-weight:bold; background-color:#BFD9BF;cursor:pointer;" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF;cursor:pointer;" {else} style="cursor:pointer;" {/if}{/if} >{$allcategorys[as]->title}</a>
            </li>
            {if !$home}
            <script type="text/javascript">
                // <![CDATA[
                    {literal}           
                            Event.observe($('link_{/literal}{$ca}{literal}'), 'click', function(event) { 
                                reload_div_menu({/literal}{$ca}{literal});
                                get_frontpage_articles({/literal}{$ca}{literal});
                             change_style_link('link_{/literal}{$ca}{literal}');
                            });
                    {/literal}
                // ]]>
            </script>
            {/if}
            
        {/acl}
        {* /if *}        
    {/section}
    {section name=as loop=$allcategorys} 
        {section name=su loop=$subcat[as]}
            {* if $smarty.session.isAdmin || is_array($smarty.session.accesscategories) && in_array($allcategorys[as]->pk_content_category, $smarty.session.accesscategories) *}
            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                  {if $allcategorys[as]->pk_content_category eq $category }
                      {assign var=subca value=`$subcat[as][su]->pk_content_category`}
                      <li>
                          <a class="links" {if $home} href="{$home}&category={$subcat[as][su]->pk_content_category}#" {else} id="link_{$subca}" style="cursor:pointer;" {/if} {if $category==$subca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} >
                          <span style="color:#222 ;margin-left: 12px;margin-right: 12px;">{$subcat[as][su]->title}</span></a>
                      </li>
                       {if !$home}
                        <script type="text/javascript">
                            // <![CDATA[
                                {literal}
                                        Event.observe($('link_{/literal}{$subca}{literal}'), 'click', function(event) {
                                           // reload_div_menu({/literal}{$subca}{literal});
                                            get_frontpage_articles({/literal}{$subca}{literal});
                                           change_style_link('link_{/literal}{$subca}{literal}');
                                        });
                                {/literal}
                            // ]]>
                        </script>
                        {/if}
                  {else}
                      {if $subcat[as][su]->fk_content_category eq $datos_cat[0]->fk_content_category}
                          {assign var=subca value=`$subcat[as][su]->pk_content_category`}
                          <li>
                              <a class="links" {if $home} href="{$home}&category={$subcat[as][su]->pk_content_category}#" {else} id="link_{$subca}" style="cursor:pointer;" {/if}  {if $category==$subca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} >
                              <span style="color:#222 ;margin-left: 12px;margin-right: 12px;">{$subcat[as][su]->title}</span></a>
                          </li>
                           {if !$home}
                            <script type="text/javascript">
                                // <![CDATA[
                                    {literal}
                                            Event.observe($('link_{/literal}{$subca}{literal}'), 'click', function(event) {
                                                //reload_div_menu({/literal}{$subca}{literal});
                                                get_frontpage_articles({/literal}{$subca}{literal});
                                               change_style_link('link_{/literal}{$subca}{literal}');
                                            });
                                    {/literal}
                                // ]]>
                            </script>
                            {/if}
                       {/if}
                  {/if}
            {/acl}
            {* /if *} {* Si é unha categoría accesible *}
        {/section}
    {/section}

{* PHP que utiliza
	  
	  
	  $cc = new ContentCategoryManager();
		  // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
	  //Mirar categorias y se recorre para sacar subcategorias.
	  $allcategorys = $cc->find('inmenu=1 AND fk_content_category=0', 'ORDER BY posmenu');	
	  $i=0;
	  foreach( $allcategorys as $prima) {				
		  $subcat[$i]=$cc->find(' inmenu=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');					
		    $i++;
	  }

	  $tpl->assign('subcat', $subcat);
	  // FIXME: Set pagination
	  $tpl->assign('allcategorys', $allcategorys);
		  $datos_cat = $cc->find('pk_content_category='.$_GET['category'], NULL);	
	  $tpl->assign('datos_cat', $datos_cat);
  

*}


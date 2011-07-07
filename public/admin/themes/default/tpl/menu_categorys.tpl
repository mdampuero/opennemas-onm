{section name=as loop=$allcategorys}
     {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
          {assign var=ca value=$allcategorys[as]->pk_content_category}
          {* Cuando se hace reload menú que no pinte las que son de la seccion *}
          {if (preg_match('/video\.php/',$home)) || (!preg_match('/video\.php/',$home) && ($allcategorys[as]->internal_category neq '9'))}
               {if (preg_match('/album\.php/', $home)) || (!preg_match('/album\.php/', $home) && ($allcategorys[as]->internal_category neq '7'))}
               <li>
                    <a class="links" {if $home} href="{$home}&category={$ca}" {/if} id="link_{$ca}"   {if $category==$ca} style="color:#000000; font-weight:bold; background-color:#BFD9BF;cursor:pointer;" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF;cursor:pointer;" {else} style="cursor:pointer;" {/if}{/if} >
                    {if $allcategorys[as]->inmenu eq 0} <img src="{$params.IMAGE_DIR}publish_r.png" style="width:10px;height:10px;"/>{/if}
                    {$allcategorys[as]->title}
                    {if $allcategorys[as]->internal_category eq 7}
                         <img src="{$params.IMAGE_DIR}album.png" border="0"  style="height:11px; " alt="Sección de Album" />
                    {elseif $allcategorys[as]->internal_category eq 9}
                         <img src="{$params.IMAGE_DIR}video.png" border="0"  style="height:11px; " alt="Sección de Videos" />
                    {/if}
                    </a>
               </li>
             {/if}
          {/if}
          <script type="text/javascript">
          // <![CDATA[
          {if !$home}
               Event.observe($('link_{$ca}'), 'click', function(event) {
                   reload_div_menu({$ca});
                   get_frontpage_articles({$ca});
                   change_style_link('link_{$ca}');
               });
          {/if}
               var e;
               Event.observe($('link_{$ca}'), 'mouseover', function(event) {
                  clearTimeout(e);
                  $('menu_subcats').setOpacity(1);
                  new Effect.Opacity('menu_subcats', { from: 0.0, to: 1.0, duration: 0.5 });
                  show_subcat('{$ca}','{$home|urlencode}');
         
                });
                Event.observe($('link_{$ca}'), 'mouseout', function(event) {
                    e = setTimeout("show_subcat('{$category}','{$home|urlencode}');",4000);
                });
                
                // ]]>
        </script>
     {/acl}
{/section}
<br style="clear:both;"> <br />
<div id='menu_subcats' style="width:90%;">
    {include file="menu_subcategorys.tpl"}
</div>

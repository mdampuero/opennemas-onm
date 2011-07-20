
<ul id='thelist'  class="gallery_list clearfix" style="width:100%; margin:0; padding:0">
   {assign var=num value='1'}
   {section name=n loop=$videos}

   {if $videos[n]->content_status eq 1}
       <li style="display:inline-block;">
           <div>
               <a>
                  
                   {assign var='information' value=$videos[n]->information|unserialize}
                   
                    <img class="video"  width="67" id="draggable_video{$num}"
                         src="{$information['thumbnail']}"
                         name="{$videos[n]->pk_video}" alt="{$videos[n]->title}"
                         qlicon="{$videos[n]->videoid}"                         
                         de:created="{$videos[n]->created}"
                         de:description="{$videos[n]->description|clearslash|escape:'html'}"
                         de:tags="{$videos[n]->metadata}"
                         title="{$videos[n]->title}" />

                </a>
           </div>
           <script type="text/javascript">
               new Draggable('draggable_video{$num}', {literal}{ revert:true, scroll: window, ghosting:true}{/literal}  );
           </script>
       </li>
       {assign var=num value=$num+1}
   {/if}
   {/section}
</ul>
{if $videoPager}
    <div class="pagination" align="center" style="clear:both; width:100%"> {$videoPager} </div>
{/if}
 
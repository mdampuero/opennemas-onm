{extends file="mobile/mobile_layout.tpl"}

{block name="content"}
<div id="content">

       <div id="infoblock">
           <h2>{$article->title|clearslash}</h2>
           <p class="subtitle">
                {$article->summary|clearslash:'<p><br><strong><em><b><i>'}
           </p>
       </div>

       <div class="post">
       {if isset($photo)}
            <img src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/images/{$photo}" alt="" {*imageattrs image=$photos*}/>
        {/if}
       <p>{$article->body|clearslash:'<p><br><strong><em><b><i>'}</p>
       </div>
    </div>
    <div id="related-posts">
      {if count($related) > 0}<h3>Artículos relacionados</h3>{/if}
      <ul>
       {section name="art" loop=$related}
              {if count($related) > 0}
                     {include      file="mobile/partials/element_list.tpl"
                                   article=$article}
              {/if}
	   {/section}
       </ul>
    </div>

</div>

{/block}

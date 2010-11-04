{include file="mobile/header.tpl"}


<div id="content">

       <div id="infoblock">
           <h2>{$article->title|clearslash}</h2>
           <p class="subtitle">
                {$article->summary|clearslash:'<p><br><strong><em><b><i>'}
<!--                <strong>autor</strong> | {humandate article=$article created=$article->created updated=$article->changed}-->
           </p>
       </div>
       
       <div class="post">
       {if isset($photo)}
            <img src="{$smarty.const.MOBILE_MEDIA_PATH}{$photo}" alt="" {*imageattrs image=$photos*}/>
        {/if}
       <p>{$article->body|clearslash:'<p><br><strong><em><b><i>'}</p>
       </div>
    </div>
    {$ccm->get_title($section)}
    <div id="related-posts">
      <h3>Artículos relacionados</h3>
      <ul>
           <li class="clearfix">
               <a href=""><img src="" alt="" /></a>
               <span class="date">{humandate article=$article created=$article->created updated=$article->changed}</span>
           </li>
       </ul>
    </div>
        
</div>
    
{include file="mobile/footer.tpl"}

<li class="post">
      <a href="{$smarty.const.BASE_PATH}{$article->permalink}">
        {if isset($photos_articles[$id])}
         <img width="60" height="60" alt="{$photos->title}"
              src="{$smarty.const.MOBILE_MEDIA_PATH}/images/{$photos_articles[$id]}">
        {/if}
         <span class="content">
            <span class="category">{$ccm->get_title($article->category_name)}</span>
            <span class="title">{$article->title|clearslash}</span>
            <span class="metadata">{humandate article=$article created=$article->created updated=$article->changed} | 0 comentarios</span>
         </span>
      </a>
   </li>


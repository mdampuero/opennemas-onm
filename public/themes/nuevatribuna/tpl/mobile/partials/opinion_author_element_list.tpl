{if !empty($op_colaborador)}
<li class="post">
      <a href="{$smarty.const.BASE_PATH}/{generate_uri content_type="opinions"
                                                        id=$op_colaborador.id
                                                        date=$op_colaborador.created
                                                        title=$op_colaborador.title
                                                        category_name=$op_colaborador.author_name_slug}">
          
        {if isset($photos.$id)}
         <img width="60" height="60" alt="{$photos.title}"
              src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/{$photos.$id}">
        {/if}
         <span class="content">
            <span class="category">{$op_colaborador.name}</span>
            <span class="title">{$op_colaborador.title|clearslash}</span>
            <span class="metadata">
                {$op_colaborador->name} |
                {humandate article=$op_colaborador created=$op_colaborador.created updated=$op_colaborador.changed}
            </span>
         </span>
      </a>
   </li>

{else}
<li class="post">
      <a href="{$smarty.const.BASE_PATH}{$article->permalink}">
        {if isset($photos.$id)}
         <img width="60" height="60" alt="{$photos.title}"
              src="{$smarty.const.MEDIA_URL}/{$smarty.const.MEDIA_DIR}/images/{$photos.$id}">
        {/if}
         <span class="content">
            <span class="category">{$article->name}</span>
            <span class="title">{$article->title|clearslash}</span>
            <span class="metadata">
                {$article->name} |
                {humandate article=$article created=$article->created updated=$article->changed}
            </span>
         </span>
      </a>
   </li>

{/if}

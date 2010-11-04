{if !empty($op_colaborador)}

<li class="post">
      <a href="{$smarty.const.BASE_PATH}{$op_colaborador.permalink}">
        {if isset($photos.$id)}
         <img width="60" height="60" alt="{$photos.title}"
              src="{$smarty.const.MOBILE_MEDIA_PATH}{$photos.$id}">
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
              src="{$smarty.const.MOBILE_MEDIA_PATH}{$photos.$id}">
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
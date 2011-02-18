{if !empty($suggested)}
   <div class="machine-related-contents">
      <div class="title">Quizás también le interese:</div>
      <ul>
         {section name=r loop=$suggested}
         {if $suggested[r].pk_content neq $article->pk_article}
         <li><a href="{$suggested[r].uri}">{$suggested[r].title|clearslash}</a></li>
         {/if}
         {/section}
      </ul>
   </div>
{/if}

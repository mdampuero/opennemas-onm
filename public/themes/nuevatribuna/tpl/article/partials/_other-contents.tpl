{if !empty($suggested)}
   <div class="machine-related-contents clearfix">
      <div>
         <div class="title">Quizás también le interese:</div>
         {if $opinion}
              <ul>
                {section name=r loop=$suggested}
                    {if $suggested[r].pk_content neq $opinion->pk_opinion}
                    <li><a href="{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                                                                            id=$suggested[r].pk_content
                                                                            date=$suggested[r].created
                                                                            title=$suggested[r].title
                                                                            category_name="opinion"}">{$suggested[r].title|clearslash}</a></li>
                    {/if}
                {/section}
             </ul>
             
         {else}
            <ul>
                {section name=r loop=$suggested}
                    {if $suggested[r].pk_content neq $article->pk_article}
                    <li><a href="{$smarty.const.SITE_URL}{generate_uri   content_type="article"
                                                                            id=$suggested[r].pk_content
                                                                            date=$suggested[r].created
                                                                            title=$suggested[r].title
                                                                            category_name=$article->category_name}">{$suggested[r].title|clearslash}</a></li>
                    {/if}
                {/section}
             </ul>
         {/if}
      </div>
   </div>
{/if}

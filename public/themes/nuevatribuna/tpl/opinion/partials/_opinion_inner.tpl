<div class="in-big-title span-24">

    <div class="span-3">
        {if $opinion->type_opinion neq 1 and $opinion->path_img}
            <a class="opinion-author" href="/opinions_autor/{$opinion->fk_author}/{$opinion->name|clearslash}.html">
                <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$opinion->path_img}" width="110" alt="{$opinion->name}"/>
            </a>
        {/if}
    </div>

    <h1 class="span-20 last">{$opinion->title|clearslash}</h1>


    {* 0 - autor, 1 - editorial, 2 - director *}
    <div class="info-author span-13">
        escrito por
          {if $opinion->type_opinion eq 0}
        <a class="opinion-author-name" href="/opinions_autor/{$opinion->fk_author}/{$opinion->name|clearslash}.html">{$opinion->name}</a>
        (<span class="opinion-author-condition">{$opinion->condition|clearslash|truncate:34:"...":"true"}</span>),
          {elseif $opinion->type_opinion eq 2}
        <a class="opinion-author-name" href="/opinions_autor/2/Director.html">El director</a>,
          {else}
        <a class="opinion-author-name" href="/opinions_autor/1/Editorial.html">La editorial</a>,
          {/if}
        <span class="publish-date">{articledate article=$opinion updated=$opinion->changed nohour='true'}</span>
    </div>

</div><!-- fin lastest-news -->

<div class="span-24">
    <div class="layout-column first-column span-16">
	<div class="border-dotted">
	    <div class="span-16 toolbar">
		{include file="utilities/widget_ratings.tpl"}
		{include file="utilities/widget_utilities.tpl" long="true"}
	    </div><!--fin toolbar -->
	    <div class="content-article">
		  {if !empty($relationed)}
		      <div class="related-news-embebed span-5">
			 <p class="title">Noticias relacionadas:</p>
			 <ul>
			    {section name=r loop=$relationed}
				{if $relationed[r]->pk_article neq  $article->pk_article}
				   {renderTypeRelated content=$relationed[r]}
				{/if}
			    {/section}
			</ul>
		     </div>
		{/if}
		<div>{$opinion->body|clearslash}</div>
	    </div><!-- /content-article -->
	    <div class="span-16 toolbar">
		{include file="utilities/widget_ratings.tpl"}
		{include file="utilities/widget_utilities.tpl" long="true"}
	    </div><!--fin toolbar -->
	    <hr class="new-separator"/>
	    <div class="more-news-bottom-article">
		{if !empty($suggested)}
		    <p class="title">Si le interesó este artículo, eche un vistazo a estes:</p>
		     <ul>
			{section name=r loop=$suggested}
			     {if $suggested[r].pk_content neq $opinion->pk_content}
			       <li><a href="{$suggested[r].uri}">{$suggested[r].title|clearslash}</a></li>
			    {/if}
			{/section}
		    </ul>
		{/if}
	    </div><!--fin more-news-bottom-article -->
	   {include file="module_comments.tpl" content=$contentId nocache}
	</div>
    </div>

    {include file="opinion/opinion_inner_last_column.tpl"}

</div>

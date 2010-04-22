 {*
    OpenNeMas project

    @theme      Lucidity
*}

 <div class="span-7 layout-column last widget-lastest-tab">
    <div class="title">       
    </div>
    <div id="tabs2" class="content">
            <ul>
                <li><a href="#tab-more-views"><span>+Visto</span></a></li>
                <li><a href="#tab-more-comments"><span>+Comentado</span></a></li>

            </ul>
            <div id="tab-more-views">
               {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                {section name=a loop=$articles_viewed}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                             {renderTypeRelated content=$articles_viewed[a]}
                           {* <a href="{$articles_viewed[a]->permalink}" title="{$articles_viewed[a]->metadata}">
                                {$articles_viewed[a]->title|clearslash}
                            </a> *}
                        </div>
                    </div>
                {/section}
            </div>
            <div id="tab-more-comments">
                {*<div class="explanation">Noticias, vídeos y comentarios</div> *}
                {foreach item=article from=$articles_comments}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                            <a href="{$article.permalink}" title="{$article.title}">
                                {$article.title|clearslash}
                            </a>
                        </div>
                    </div>
                {/foreach}

                {*

                 {section name=a loop=$articles_comments}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                            <a href="{$articles_comments[a]->permalink}" title="{$articles_comments[a]->metadata}">
                                {$articles_comments[a]->title|clearslash}
                            </a>
                        </div>
                    </div>
                {/section}

                    *}
            </div>

        </div>
 </div>
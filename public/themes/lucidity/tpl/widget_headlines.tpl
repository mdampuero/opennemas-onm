 {*
    OpenNeMas project

    @theme      Lucidity
*}

 <div class="span-8 layout-column last widget-lastest-tab">
    <div class="title">
        Temas calientes...
    </div>
    <div id="tabs2" class="content">
            <ul>
                <li><a href="#tab-more-views"><span>+Visto</span></a></li>
                <li><a href="#tab-more-voted"><span>+Votado</span></a></li>
                <li><a href="#tab-more-comments"><span>+Comentado</span></a></li>

            </ul>
            <div id="tab-more-views">
               {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                {section name=a loop=$articles_viewed max=6}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                             {renderTypeRelated content=$articles_viewed[a]}
                        </div>
                    </div>
                {/section}
            </div>
            <div id="tab-more-comments">
                {*<div class="explanation">Noticias, vídeos y comentarios</div> *}
                {section name=a loop=$articles_comments max=6}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                             {renderTypeRelated content=$articles_comments[a]}
                        </div>
                    </div>
                {/section}

            </div>
             <div id="tab-more-voted">
               {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                {section name=a loop=$articles_voted max=6}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                             {renderTypeRelated content=$articles_voted[a]}
                           {* <a href="{$articles_viewed[a]->permalink}" title="{$articles_viewed[a]->metadata}">
                                {$articles_viewed[a]->title|clearslash}
                            </a> *}
                        </div>
                    </div>
                {/section}
            </div>

        </div>
 </div>
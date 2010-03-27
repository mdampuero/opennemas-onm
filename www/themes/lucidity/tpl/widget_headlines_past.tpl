 {*
    OpenNeMas project

    @theme      Lucidity
*}

  <div class="span-8 layout-column last widget-lastest-tab">
        <div class="title">
            Lo último en {$category_data.title|default:'Portada'|capitalize}
        </div>
        <div id="tabs" class="content" style="width:290px;">
                <ul>
                    <li><a href="#tab-last-day"><span>Último día</span></a></li>
                    <li><a href="#tab-last-3-days"><span>Últimos 3 días</span></a></li>
                    <li><a href="#tab-last-week"><span>Última semana</span></a></li>
                </ul>
                <div id="tab-last-day">
                    {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                    {section name=a loop=$articles_24h}
                        <div class="tab-lastest clearfix">
                            <div class="tab-lastest-title">
                                <a href="{$articles_24h[a]->permalink}" title="{$articles_24h[a]->metadata}">
                                    {$articles_24h[a]->title|clearslash}
                                </a>
                            </div>
                        </div>
                    {/section}

                </div>
                <div id="tab-last-3-days">
                    {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                     {section name=a loop=$articles_3day}
                        <div class="tab-lastest clearfix">
                            <div class="tab-lastest-title">
                                <a href="{$articles_3day[a]->permalink}" title="{$articles_3day[a]->metadata}">
                                    {$articles_3day[a]->title|clearslash}
                                </a>
                            </div>
                        </div>
                    {/section}
                </div>
                <div id="tab-last-week">
                    {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                    {section name=a loop=$articles_1sem}
                        <div class="tab-lastest clearfix">
                            <div class="tab-lastest-title">
                                <a href="{$articles_1sem[a]->permalink}" title="{$articles_1sem[a]->metadata}">
                                    {$articles_1sem[a]->title|clearslash}
                                </a>
                            </div>
                        </div>
                    {/section}
                </div>
        </div>
    </div>
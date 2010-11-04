 {*
    OpenNeMas project

    @theme      Lucidity
*}
{if !empty($polls)}
 <div class="layout-column last widget-lastest-tab">
    <div class="title">
        Otras Encuestas...
    </div>
    <div id="tabs2" class="content">            
            <div id="tab-more-views">
               {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
                {section name=a loop=$polls max=6}
                    <div class="tab-lastest clearfix">
                        <div class="tab-lastest-title">
                             <a href="{$polls[a]->permalink}"> {$polls[a]->title}</a>
                        </div>
                    </div>
                {/section}
            </div>

        </div>
 </div>
 {/if}
 {*
    OpenNeMas project

    @theme      Lucidity
*}

  <div id="widget-lastest-tab" class="layout-column last widget-lastest-tab">
        <div class="title">
            Lo último en {$actual_category_title|default:'Portada'|capitalize}
        </div>
        <div id="tabs" class="content">
                <ul>
                    <li><a href="#tab-last-day"><span>Último día</span></a></li>
                    <li><a href="#tab-last-3-days"><span>Últimos 3 días</span></a></li>
                    <li><a href="#tab-last-week"><span>Última semana</span></a></li>
                </ul>
                <div id="tab-last-day">
                    {section name=a loop=$articles_24h}
                        <div class="tab-lastest clearfix">
                            <div class="tab-lastest-title">
                                {renderTypeRelated content=$articles_24h[a]}
                            </div>
                        </div>
                    {/section}
                </div>
                <div id="tab-last-3-days">
                     {section name=a loop=$articles_3day}
                        <div class="tab-lastest clearfix">
                            <div class="tab-lastest-title">
                                 {renderTypeRelated content=$articles_3day[a]}
                            </div>
                        </div>
                    {/section}
                </div>
                <div id="tab-last-week">
                    {section name=a loop=$articles_1sem}
                        <div class="tab-lastest clearfix">
                            <div class="tab-lastest-title">
                                 {renderTypeRelated content=$articles_1sem[a]}
                            </div>
                        </div>
                    {/section}
                </div>
        </div>
    </div>
<script type="text/javascript">
jQuery(document).ready(function(){
   $('#widget-lastest-tab').tabs();
});
</script>
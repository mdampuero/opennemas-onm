<div class="widget-past-headlines-most-viewed widget-past-headlines-most-viewed-wrapper widget clearfix">
    <div class="clearfix">

        <div id="tabs" class="content clearfix widget-{$rnd_number}">
                <div class="title">
                    Lo último en {$actual_category_name|default:'Portada'}
                </div>
                <ul>
                    <li><a href="#tab-last-day"><span>24hs</span></a></li>
                    <li><a href="#tab-last-3-days"><span>3 días</span></a></li>
                    <li><a href="#tab-last-week"><span>1 semana</span></a></li>
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
</div>
<script defer="defer" type="text/javascript">
jQuery(document).ready(function(){
   $('div.widget-{$rnd_number}').tabs();
   $('div.widget-{$rnd_number} .initially-hidden').css('display','block');
});
</script>
{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="widget-most-seeing-voted-commented-content widget-most-seeing-voted-commented-content-wrapper clearfix">

    <div id="widget-most-seeing-voted-commented-content-tab" class="content clearfix widget-{$rnd_number}">

        <ul class="tab-selector">
            <li><a href="#tab-more-views"><span>+ visto</span></a></li>
            <li><a href="#tab-more-voted"><span>+ votado</span></a></li>
            <li><a href="#tab-more-comments"><span>+ comentado</span></a></li>
        </ul>
        
        <div id="tab-more-views">
            {foreach from=$articlesMostViewed item=article}
                <div class="tab-lastest clearfix">
                    <div class="tab-lastest-title">
                        {renderTypeRelated content=$article}
                    </div>
                </div>
            {/foreach}
        </div>
        <div id="tab-more-voted">
            {foreach from=$articlesMostVoted item=article}
                <div class="tab-lastest clearfix">
                    <div class="tab-lastest-title">
                        {renderTypeRelated content=$article}
                    </div>
                </div>
           {/foreach}
        </div>
        <div id="tab-more-comments">
            {foreach from=$articlesMostCommented item=article}
                <div class="tab-lastest clearfix">
                    <div class="tab-lastest-title">
                        {renderTypeRelated content=$article}
                    </div>
                </div>
            {/foreach}
        </div>

    </div>
</div>
<script defer="defer" type="text/javascript">
jQuery(document).ready(function(){
    $('div.widget-{$rnd_number}').tabs();
    $('div.widget-{$rnd_number} .initially-hidden').css('display','block');
});
</script>
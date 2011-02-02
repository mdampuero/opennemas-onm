{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="widget-most-seeing-voted-commented-content widget-most-seeing-voted-commented-content-wrapper clearfix">

   <div id="widget-most-seeing-voted-commented-content-tab" class="content clearfix widget-{$rnd_number}">
      <ul class="tab-selector">
         <li><a href="#tab-more-views"><span>Lo + Visto</span></a></li>
         <li><a href="#tab-more-voted"><span>Lo + Votado</span></a></li>
         <li><a href="#tab-more-comments"><span>Lo + Comentado</span></a></li>
      </ul>
      <div id="tab-more-views" class="clearfix">
        {* <div class="explanation">Noticias, vídeos y comentarios</div> *}
         {section name=a loop=$articles_viewed max=6}
             <div class="tab-lastest clearfix">
                 <div class="tab-lastest-title">
                      {renderTypeRelated content=$articles_viewed[a]}
                 </div>
             </div>
         {/section}
      </div>
      <div id="tab-more-comments" class="clearfix initially-hidden">
         {*<div class="explanation">Noticias, vídeos y comentarios</div> *}
         {section name=a loop=$articles_comments max=6}
             <div class="tab-lastest clearfix">
                 <div class="tab-lastest-title">
                      {renderTypeRelated content=$articles_comments[a]}
                 </div>
             </div>
         {/section}
      
      </div>
      <div id="tab-more-voted" class="clearfix initially-hidden">
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
<script defer="defer" type="text/javascript">
jQuery(document).ready(function(){
   $('div.widget-{$rnd_number}').tabs();
   $('div.widget-{$rnd_number} .initially-hidden').css('display','block');
});
</script>
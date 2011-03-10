<div class="logos-and-info">

   <div id="info-top" class="clearfix span-24 last">
      <div class="current-hour">
         {insert name="time"}
      </div>
      {*include file="internal_widgets/widget_weather.tpl"*}
   </div>
   
   <div id="logo-and-pages" class="clearfix span-24 last">

       <div id="logo-image" class="span-16">
         {*if ($category_name eq 'home')*}
             <h1><a href="{$smarty.const.BASE_URL}" class="big-text-logo" title="{$smarty.const.SITE_TITLE}"><img src="{$params.IMAGE_DIR}logos/nuevatribuna-header.png" alt="nuevatribuna.es" /></a></h1>
         {*else*}
            {*<a href="{$smarty.const.BASE_URL}"><img class="transparent-logo" alt="{$smarty.const.SITE_TITLE}" src="{$params.IMAGE_DIR}logos/nuevatribuna-header.png" ></a>*}
         {*/if*}
       </div>
   
       <div class="info-pages">
         <div class="block-pages">
            <img src="{$params.IMAGE_DIR}logos/nuevatribuna-square.png" alt="{$smarty.const.SITE_TITLE}" />
            <ul class="pages">
               <li><a href="http://tribuna.local/estaticas/contacto.html">Contacto</a> |</li>
               <li><a href="http://tribuna.local/estaticas/subscripcion.html">Boletín Diario</a></li>
            </ul>
         </div>
         <div class="colofon">
            Información y análisis para una ciudadanía comprometida
         </div>
       </div>
   
   </div>
</div>

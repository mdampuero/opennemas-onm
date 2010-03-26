{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="layout-column last-column last span-8">
    <div class="border-dotted">

       {include file="widget_ad_column.tpl" type='103'}
         
       {include file="widget_column_video_viewer.tpl" video=$videoInt}

       {* <div class="nw-big">
            <img class="nw-image" src="images/news/temporal.png" alt=""/>
            <div class="nw-category-name">TIEMPO</div>
            <h3 class="nw-title"><a href="#">Un gran vendabal entra por Galicia y aplaca las ciudades</a></h3>
            <p class="nw-subtitle">Investigadores apuntan a la desaparición de la especie humana en 1 semana</p>
            <div class="more-resources">
                <ul>
                    <li class="res-file"><a href="#">El viaje al sol, un sueño olvidado (PDF)</a></li>
                    <li class="res-image"><a href="#">Fototeca: El viaje al sol, un sueño olvidado</a></li>
                    <li class="res-link"><a href="#">Los grandes pelígros de la humanidad</a></li>
                    <li class="res-video"><a href="#">Descubre el sistema planetario en este vídeo</a></li>
                </ul>
            </div>
        </div><!-- fin nw-big -->
   *}

        <hr class="new-separator"/>

        <div class="news-highliter">
           <h3>Destacadas en {$category_data.title|capitalize}</h3>
            {include file="frontpage_article.tpl" item=$other_news[0]}
            {include file="frontpage_article.tpl" item=$other_news[1]}
         {*  <div class="nw-big">
                <h3 class="nw-title"><a href="#">En un sólo día se registran tantas explosiones solares como en todo el año 2009</a></h3>
                <p class="nw-subtitle">Investigadores apuntan a la desaparición de la especie humana en 1 semana</p>
            </div>
           <div class="nw-big">
                <h3 class="nw-title"><a href="#">En un sólo día se registran tantas explosiones solares como en todo el año 2009</a></h3>
                <p class="nw-subtitle">Investigadores apuntan a la desaparición de la especie humana en 1 semana</p>
            </div>
            *}

        </div>
    </div>
    {include file="widget_headlines_past.tpl"}
      {include file="widget_ad_column.tpl" type='104'}

    <hr class="new-separator">



</div>
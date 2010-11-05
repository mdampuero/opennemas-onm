{*
    OpenNeMas project

    @theme      Lucidity
*}

<div class="lastest-news span-24clearfix">
    <span class="span-2"><strong>Últimas noticias</strong>: </span>
    <ul  class="span-20 last slide_cicle">
        {section name=exp loop=$articles_home_express}
            <li class="teaser" id="teaser-{$smarty.section.exp.iteration}">
                {$articles_home_express[exp]->created|date_format:"%H:%M"}
                <a href="{$articles_home_express[exp]->permalink}">{$articles_home_express[exp]->title|clearslash}</a>
            </li>
        {/section}
    </ul>
</div>


{*
<div class="lastest-news clearfix">
    <ul style="margin:0; padding:0">
        <li><strong>Últimas noticias</strong>:</li>
      <li id="teaser-0" style="margin:0; padding:0">
          Titular de noticia relacionada con las últimas declaraciones del gobierno.
      </li>
      <li id="teaser-1" style="margin:0; padding:0; display:none">
          Y esta es la respuesta de la oposición al titular de noticia relacionada con las últimas declaraciones del gobierno.
      </li>
    </ul>
</div>
*}

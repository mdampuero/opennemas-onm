{*
    OpenNeMas project

    @theme      Lucidity
*}
 <div id="tabs2" class="span-8 layout-column last widget-lastest-tab">
     <ul>
            <li><a href="#tab-new"><span>Últimos</span></a></li>
            <li><a href="#tab-viewed"><span>+ Vistos</span></a></li>
            <li><a href="#tab-related"><span>+ Votados</span></a></li>
             <li><a href="#tab-commented"><span>+ Comentados</span></a></li>
          
    </ul>

    <div id="tab-new">
        {section name=i loop=$last_gallerys}
             <div class="ui-tabs-panel clearfix">
                <a class="video-link" title="{$last_gallerys[i]->title|clearslash|escape:'html'}" href="{$last_gallerys[i]->permalink}">
                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$last_gallerys[i]->cover}" alt="{$last_gallerys[i]->title|clearslash|escape:'html'}" title="{$last_gallerys[i]->title|clearslash|escape:'html'}" />
                </a>
                <div class="tab-thumb-video-shortitle">{$last_gallerys[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                     <a href="{$last_gallerys[i]->permalink}" title="{$last_gallerys[i]->title|clearslash|escape:'html'}">{$last_gallerys[i]->title|clearslash|escape:'html'}</a>
                 </div>
            </div>
        {/section}
    </div>

    <div id="tab-viewed">
        {section name=i loop=$gallerys_viewed}
             <div class="ui-tabs-panel clearfix">
                <a class="video-link" title="{$gallerys_viewed[i]->title|clearslash|escape:'html'}" href="{$gallerys_viewed[i]->permalink}">
                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$gallerys_viewed[i]->cover}" alt="{$gallerys_viewed[i]->title|clearslash|escape:'html'}" title="{$gallerys_viewed[i]->title|clearslash|escape:'html'}" />
                </a>
                <div class="tab-thumb-video-shortitle">{$gallerys_viewed[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                     <a href="{$gallerys_viewed[i]->permalink}" title="{$gallerys_viewed[i]->title|clearslash|escape:'html'}">{$gallerys_viewed[i]->title|clearslash|escape:'html'}</a>
                 </div>
            </div>
        {/section}
    </div>

    <div id="tab-related">
        {section name=i loop=$gallerys_voted}
             <div class="ui-tabs-panel clearfix">
                <a class="video-link" title="{$gallerys_voted[i]->title|clearslash|escape:'html'}" href="{$gallerys_voted[i]->permalink}">
                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$gallerys_voted[i]->cover}" alt="{$gallerys_voted[i]->title|clearslash|escape:'html'}" title="{$gallerys_voted[i]->title|clearslash|escape:'html'}" />
                </a>
                <div class="tab-thumb-video-shortitle">{$gallerys_voted[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                     <a href="{$gallerys_voted[i]->permalink}" title="{$gallerys_voted[i]->title|clearslash|escape:'html'}">{$gallerys_voted[i]->title|clearslash|escape:'html'}</a>
                 </div>
            </div>
        {/section}
    </div>

     <div id="tab-commented">
        {section name=i loop=$gallerys_comments}
             <div class="ui-tabs-panel clearfix">
                <a class="video-link" title="{$gallerys_comments[i]->title|clearslash|escape:'html'}" href="{$gallerys_comments[i]->permalink}">
                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$gallerys_comments[i]->cover}" alt="{$gallerys_comments[i]->title|clearslash|escape:'html'}" title="{$gallerys_comments[i]->title|clearslash|escape:'html'}" />
                </a>
                <div class="tab-thumb-video-shortitle">{$gallerys_comments[i]->category_title}</div>
                <div class="tab-thumb-video-title">
                     <a href="{$gallerys_comments[i]->permalink}" title="{$gallerys_comments[i]->title|clearslash|escape:'html'}">{$gallerys_comments[i]->title|clearslash|escape:'html'}</a>
                 </div>
            </div>
        {/section}
    </div>
</div>
 
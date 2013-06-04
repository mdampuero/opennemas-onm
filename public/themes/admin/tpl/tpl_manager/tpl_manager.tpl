{extends file="base/admin.tpl"}

{block name="footer-js" append}
<style type="text/css">
    .expired { color:#DA4F49; font-weight:bold; }
</style>
{/block}
{block name="footer-js" append}
<script>
    jQuery(function($){
        $('#caches').on('click','.delete-cache-button', function(e, ui) {
            e.preventDefault();
            var urlDelete = $(this).attr('href');
            var element = $(this);
            $.ajax({
                url: urlDelete,
                context: document.body,
                success: function() {
                    element.closest('.cache-element')
                        .animate({ 'backgroundColor':'#fb6c6c !important' },300)
                        .animate({ 'opacity': 0, 'height': 0 }, 600, function() {
                            $(this).remove();
                    });
                }
            });
        });
    });
</script>
{/block}


{block name="content"}
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Cache Manager{/t}</h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit" title="{t}Delete cache{/t}" id="delete-caches" class="delChecked">
                        <img src="{$params.IMAGE_DIR}template_manager/delete48x48.png" />
                        <br />
                        {t}Delete{/t}
                    </button>
                </li>
                <li>
                    <a title="{t}Get updated cache list{/t}" href="{url name=admin_tpl_manager}">
                        <img alt="{t}Refresh list{/t}" src="{$params.IMAGE_DIR}/template_manager/refresh48x48.png"><br>
                        {t}Refresh list{/t}
                    </a>
                </li>

                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_tpl_manager_config}" title="{t}Configurar cachés{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" />
                        <br />
                        {t}Settings{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        {render_messages}
        <div class="table-info clearfix">
            <form action="{url name=admin_tpl_manager}" name="search">
            <div>
                <div class="right form-inline">
                    <label>
                        {t}Show{/t}
                        <input type="text" name="items_page" id="items_page" value="{$itemsperpage}"
                            size="3" maxlength="3" style="width:30px !important; text-align:right; margin-top:-2px; padding:2px 5px" />
                        {t}items/page with type{/t}
                    </label>

                    <select name="type" id="type">
                        <option value="" {if isset($smarty.request.type) && $smarty.request.type eq ''}selected="selected"{/if}>{t}All types{/t}</option>
                        <option value="frontpages" {if isset($smarty.request.type) && ($smarty.request.type eq 'frontpages')}selected="selected"{/if}>{t}Frontpages{/t}</option>
                        <option value="articles" {if isset($smarty.request.type) && $smarty.request.type eq 'articles'}selected="selected"{/if}>{t}Article: inner{/t}</option>
                        <option value="mobilepages" {if isset($smarty.request.type) && $smarty.request.type eq 'mobilepages'}selected="selected"{/if}>{t}Mobile: frontpages{/t}</option>
                        <option value="rss" {if isset($smarty.request.type) && $smarty.request.type eq 'rss'}selected="selected"{/if}>{t}RSS{/t}</option>
                        <option value="frontpage-opinions" {if isset($smarty.request.type) && $smarty.request.type eq 'frontpage-opinions'}selected="selected"{/if}>{t}Opinion: Authors{/t}</option>
                        <option value="opinions" {if isset($smarty.request.type) && $smarty.request.type eq 'opinions'}selected="selected"{/if}>{t}Opinion: inner{/t}</option>
                        <option value="video-frontpage" {if isset($smarty.request.type) && $smarty.request.type eq 'video-frontpage'}selected="selected"{/if}>{t}Video: frontpage{/t}</option>
                        <option value="video-inner" {if isset($smarty.request.type) && $smarty.request.type eq 'video-inner'}selected="selected"{/if}>{t}Video: inner{/t}</option>
                        <option value="gallery-frontpage" {if isset($smarty.request.type) && $smarty.request.type eq 'gallery-frontpage'}selected="selected"{/if}>{t}Album: frontpage{/t}</option>
                        <option value="gallery-inner" {if isset($smarty.request.type) && $smarty.request.type eq 'gallery-inner'}selected="selected"{/if}>{t}Album: inner{/t}</option>
                        <option value="poll-frontpage" {if isset($smarty.request.type) && $smarty.request.type eq 'poll-frontpage'}selected="selected"{/if}>{t}Poll: frontpage{/t}</option>
                        <option value="poll-inner" {if isset($smarty.request.type) && $smarty.request.type eq 'poll-inner'}selected="selected"{/if}>{t}Poll: inner{/t}</option>

                    </select>
                    {t}and from{/t}
                    <select name="section" id="section">
                        <option value="">{t}All sections{/t}</option>
                        {html_options options=$sections selected=$smarty.request.section|default:""}
                    </select>

                    <button type="submit" class="btn"><i class="icon-refresh"></i>
                        {t}Update list{/t}
                    </button>
                </div>
            </div>
            </form>
        </div>
    <form  action="{url name=admin_tpl_manager}" method="POST" id="tplform">
        <div id="caches">
            {if count($caches)>0}
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th  style="width:10px;">
                            <input type="checkbox" class="toggleallcheckbox" value="" />
                        </th>
                        <th class="left">{t}Resource{/t}</th>
                        <th class="left" scope=col style="width:30px;">{t}Category{/t}</th>
                        <!-- <th class="left" scope=col style="width:120px;">{t}Created in{/t}</th> -->
                        <th class="left" scope=col style="width:100px;">{t}Valid until{/t}</th>
                        <th class="left" scope=col style="width:40px;">{t}Size{/t}</th>
                        <th class="center" scope=col style="width:40px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name="c" loop=$caches}
                    {* dont show frontpage parts*}
                    {if $caches[c].resource neq 0 && $caches[c].template eq 'frontpage'}
                     {else}
                    <tr class="cache-element">
                        <td>
                            <input type="checkbox" name="selected[]" value="{$smarty.section.c.index}"  class="minput"/>
                            <input type="hidden"   name="cacheid[]"  value="{$caches[c].category}|{$caches[c].resource}" />
                            <input type="hidden"   name="tpl[]"      value="{$caches[c].template}.tpl" />
                        </td>
                        <td class="left">
                            {assign var="resource" value=$caches[c].resource}

                        {* Inner Article *}
                        {if isset($titles.$resource) && ($caches[c].template == 'article')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/article16x16.png" title="{t}Article inner cache file{/t}" />
                            {* Video *}
                        {elseif ($caches[c].template == 'video_inner') ||
                             ($caches[c].template == 'video_frontpage') ||
                             ($caches[c].template == 'video_main_frontpage')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" title="{t}Video inner cache file{/t}" />
                            {* Gallery frontpage *}
                        {elseif ($caches[c].template == 'album_frontpage') ||
                            ($caches[c].template == 'album')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" title="{t}Album cache file{/t}" />
                            {* Opinion author index*}
                        {elseif ($caches[c].template == 'opinion_author_index') ||
                            ($caches[c].template == 'opinion_frontpage') ||
                            (isset($titles.$resource) && ($caches[c].template == 'opinion'))}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/opinion16x16.png" title="{t}Opinion author index cache file{/t}" />
                            {* Frontpage mobile *}
                        {elseif ($caches[c].template == 'mobile-article-inner')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/phone16x16.png" title="{t}Mobile article inner cache file{/t}" />
                            {* Frontpage mobile *}
                        {elseif not isset($titles.$resource) && not isset($authors.$resource)
                            && ($caches[c].template == 'frontpage-mobile')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/phone16x16.png" title="{t}Mobile frontpage cache file{/t}" />
                            {* Frontpages *}
                        {elseif ($caches[c].template == 'frontpage')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/home16x16.png" title="{t}Category frontpage cache file{/t}" />
                            {* Polls *}
                        {elseif ($caches[c].template == 'poll') ||
                             ($caches[c].template == 'graphic_poll') ||
                             ($caches[c].template == 'poll_frontpage')}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/polls.png" title="{t}Poll cache file{/t}" />
                            {* RSS *}
                        {elseif $resource eq "RSS"}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/rss16x16.png" title="{t}RSS cache file{/t}" />
                            {* Other kind of resources *}
                        {elseif isset($authors.$resource)}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/rss16x16.png" title="{t}RSS Opinion author cache file {/t}" />
                            {elseif not isset($titles.$resource) && not isset($authors.$resource)}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/home16x16.png" title="{t}Section Frontpage cache file{/t}" />
                            {/if}
                            {assign var="resource" value=$caches[c].resource}
                        {* Inner Article *}
                        {if isset($titles.$resource) && ($caches[c].template == 'article')}
                            <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}"  target="_blank">Inner Article: {$titles.$resource|clearslash}</a>
                            <input type="hidden" name="uris[]" value="{$contentUris.$resource}" />
                            {* Frontpage mobile *}
                        {elseif ($caches[c].template == 'mobile-article-inner')}
                            <a href="{$smarty.const.SITE_URL}mobile/seccion/{$caches[c].category}/"  target="_blank">Frontpage mobile: {$titles.$resource}</a>
                            <input type="hidden" name="uris[]" value="mobile/seccion/{$caches[c].category}/" />
                            {* Video inner *}
                        {elseif isset($titles.$resource) && ($caches[c].template == 'video_inner')}
                            <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}" target="_blank">Video inner: {$titles.$resource|clearslash}</a>
                            <input type="hidden" name="uris[]" value="{$contentUris.$resource|htmlentities}" />
                            {* Video frontpage *}
                        {elseif ($caches[c].template == 'video_frontpage')}
                            <a href="{$smarty.const.SITE_URL}video/{$caches[c].category}/" target="_blank">{t 1=$caches[c].category}Video: frontpage %1{/t}</a>
                            <input type="hidden" name="uris[]" value="video/{$caches[c].category}/" />
                            {elseif ($caches[c].template == 'video_main_frontpage')}
                            <a href="{$smarty.const.SITE_URL}video/" target="_blank">{t}Video: main frontpage{/t}</a>
                            <input type="hidden" name="uris[]" value="video/" />
                            {* Opinion inner *}
                        {elseif isset($titles.$resource) && ($caches[c].template == 'opinion')}
                            {assign var="resName" value='RSS'|cat:$resource}
                            <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}"  target="_blank">Opinion: {$titles.$resource|clearslash}</a>
                            <input type="hidden" name="uris[]" value="{$contentUris.$resource}" />
                            {elseif ($caches[c].template == 'opinion_frontpage')}
                            <a href="{$smarty.const.SITE_URL}opinion/" target="_blank">{t 1=$caches[c].category}Frontpage %1{/t}</a>
                            <input type="hidden" name="uris[]" value="opinion/" />
                            {* Opinion author index*}
                        {elseif ($caches[c].template == 'opinion_author_index')}
                        {assign var="authorid" value=$caches[c].category|string_format:"%d"}
                            {capture "uriAuthor"}{generate_uri content_type="opinion_author_frontpage"
                                        title=$allAuthors.$authorid
                                        id=$authorid}{/capture}
                            <a href="{$smarty.const.SITE_URL}{$smarty.capture.uriAuthor|trim}" target="_blank">
                                {t 1=$allAuthors.$authorid}Opinion of Author "%1"{/t}</a>
                            <input type="hidden" name="uris[]" value="{$smarty.capture.uriAuthor|trim}" />
                            {* Gallery frontpage *}
                        {elseif ($caches[c].template == 'album_frontpage')}
                            <a href="{$smarty.const.SITE_URL}album/{$caches[c].category}/" target="_blank">
                                {if $caches[c].category neq 'home'}{t 1=$caches[c].category}Album %1{/t} {else} {t}Album Frontpage{/t} {/if}
                            </a>
                            <input type="hidden"  name="uris[]" value="album/{$caches[c].category}/" />
                            {* Gallery inner *}
                        {elseif isset($titles.$resource) && ($caches[c].template == 'album')}
                            <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}" target="_blank">{$titles.$resource|clearslash}</a>
                            <input type="hidden" name="uris[]" value="{$contentUris.$resource}" />
                            {* Polls *}
                        {elseif ($caches[c].template == 'poll_frontpage')}
                            <a href="{$smarty.const.SITE_URL}encuesta/{$caches[c].category}"
                                 target="_blank">
                                {if $caches[c].category neq 'home'}{t 1=$caches[c].category}Poll %1{/t} {else} {t}Poll: frontpage{/t} {/if}
                            </a>
                            <input type="hidden" name="uris[]" value="encuesta/{$caches[c].category}" />
                            {elseif isset($titles.$resource) && (($caches[c].template == 'poll') || ($caches[c].template == 'graphic_poll'))}
                            <a href="{$smarty.const.SITE_URL}{$contentUris.$resource}"
                                 target="_blank">{$titles.$resource|clearslash}</a>
                            <input type="hidden" name="uris[]" value="{$contentUris.$resource}" />
                            {* RSS opinion *}
                        {elseif isset($authors.$resource)}
                            <a href="{$smarty.const.SITE_URL}rss/opinion/{$resource|replace:"RSS":""}/"  target="_blank">{$authors.$resource|clearslash}</a>
                            <input type="hidden" name="uris[]" value="rss/opinion/{$resource|replace:"RSS":""}/" />
                            {* RSS *}
                        {elseif $resource eq "RSS"}
                            <a href="{if $caches[c].category != 'home'}{$smarty.const.SITE_URL}rss/{$caches[c].category}/{else}{$smarty.const.SITE_URL}rss/{/if}" target="_blank"> <strong>{t}RSS:{/t}</strong>
                                {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}
                            </a>
                            <input type="hidden" name="uris[]" value="rss/{$caches[c].category}/" />
                            {* Frontpage mobile *}
                        {elseif not isset($titles.$resource) && not isset($authors.$resource) && ($caches[c].template == 'frontpage-mobile')}
                            <a href="{$smarty.const.SITE_URL}mobile/seccion/{$caches[c].category}/"  target="_blank">
                                {t}Mobile frontpage: {/t}{$ccm->get_title($caches[c].category)|clearslash|default:"Portada"}
                            </a>
                            <input type="hidden" name="uris[]" value="mobile/seccion/{$caches[c].category}/" />
                            {* Frontpages *}
                        {elseif ($caches[c].template == 'frontpage')}
                            {if $caches[c].resource eq 0}
                            <a href="{$smarty.const.SITE_URL}seccion/{$caches[c].category}/"  target="_blank">
                                {t}Frontpage: {/t}{$ccm->get_title($caches[c].category)|clearslash|default:$caches[c].category}
                            </a>
                            {/if}
                            <input type="hidden" name="uris[]" value="seccion/{$caches[c].category}/" />
                            {* Other kind of resources *}
                        {elseif not isset($titles.$resource) && not isset($authors.$resource)}
                            <a href="{$smarty.const.SITE_URL}seccion/{$caches[c].category}/{$caches[c].resource}"  target="_blank">
                                {if $caches[c].resource gt 0}
                                {$ccm->get_title($caches[c].category)|clearslash|default:$caches[c].category} {t 1=$caches[c].resource}(Page %1){/t}
                            </a>
                            {else}
                                {$ccm->get_title($caches[c].category)|clearslash|default:$caches[c].category}
                            </a>
                            {/if}
                        <input type="hidden" name="uris[]" value="seccion/{$caches[c].category}/{$caches[c].resource}" />
                        {/if}
                    </td>

                    <td class="left">
                        {$ccm->get_title($caches[c].category)|clearslash|capitalize|default:$caches[c].category|capitalize}
                    </td>

                    <!-- <td class="left">{$caches[c].created|date_format:"%H:%M:%S %d/%m/%Y"}</td> -->

                    <td class="left">
                        <div class="valid-until-date {if $caches[c].expires < $smarty.now}expired{else}valid{/if} nowrap">
                            {$caches[c].expires|date_format:"%H:%M:%S %d/%m/%Y"}
                        </div>
                    </td>
                    <td class="center nowrap">{$caches[c].size} KB</td>
                    <td class="right nowrap">
                        <a class="btn btn-danger delete-cache-button" href="{url name=admin_tpl_manager_delete cacheid=$caches[c].cache_id  tpl=$caches[c].tpl}"
                            title="{t}Delete cache file{/t}"><i class="icon-trash icon-white"></i></a>
                    </td>
                </tr>
                {/if}
                {/section}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="8" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        {else}
        <div style="border:1px solid #ccc; padding:10px; font-size:1.2em; text-align:center">
            <p>
                {t escape="no"}Ohh, there is <strong>no cache file</strong>
                in the system.{/t}
            </p>
            <p>
                {t escape="no" 1=$smarty.const.SITE_URL}Visit some pages in
                <a href="%1" title="Visit your site">your site</a>
                {/t}
            </p>
        </div>
        {/if}
    </div>
    <input type="hidden" id="page" name="page" value="{$smarty.request.page|default:'1'}" />
    <input type="hidden" id="action" name="action" value="" />
</form>
</div>
{include file="tpl_manager/modals/_modalBatchDelete.tpl"}
{include file="tpl_manager/modals/_modalAccept.tpl"}
{/block}
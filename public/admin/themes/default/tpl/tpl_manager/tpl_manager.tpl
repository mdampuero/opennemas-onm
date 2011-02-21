{extends file="base/admin.tpl"}

{block name="footer-js"}
    <script type="text/javascript">
    var previousValue = null;
    document.observe('dom:loaded', function() {
        if( $('tabla') ) {
            $('tabla').select('tbody input[type=text]').each(function(item) {
                item.observe('change', function() {
                    this.up(2).select('input[type=checkbox]')[0].
                        setAttribute('checked', 'checked');
                });

                new Control.DatePicker(item,{
                    icon: './themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    onSelect: function(fecha, instance) {
                        instance.element.up(2).select('input[type=checkbox]')[0].
                            setAttribute('checked', 'checked');
                    },
                    onHover: function(fecha, instance) {
                        instance.element.up(2).select('input[type=checkbox]')[0].
                            setAttribute('checked', 'checked');
                    }
                });
            });
        }
    });

    function sendForm(actionValue) {
        // FIXME: chequeos de seguridad
        $('action').value = actionValue;
        $('formulario').submit();
    }

    function selectAll(indicator, checkboxes) {
        for(var i=0; i<checkboxes.length; i++) {
            if(indicator) {
                checkboxes[i].setAttribute('checked', 'checked');
                checkboxes[i].selected = true;
            } else {
                checkboxes[i].removeAttribute('checked');
                checkboxes[i].selected = false;
            }
        }
    }
    </script>
{/block}


{block name="content"}

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST"
	 style="max-width:70% !important; margin: 10px auto; display:block;">
	<div id="menu-acciones-admin">
		 <div style="float:left; margin:8px;"><h2>{t}Cache Manager{/t}</h2></div>
		<ul>
			<li>
				<a href="#delete" onclick="if(confirm('{t}Are you sure that you want to delete this selected cache files?{/t}')){ sendForm('delete'); }return false;" title="{t}Delete cache{/t}">
					<img src="{$params.IMAGE_DIR}template_manager/delete48x48.png" border="0" /><br />
					{t}Delete{/t}
				</a>
			</li>

			<li>
				<a href="#refresh" rel="refresh" onclick="sendForm('refresh');return false;"
				  title="{t}Delete and generates a new cache with updated data. BE AWARE: If you apply this action to multiple files you could slow down the system.{/t}">
					<img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" border="0" /><br />
					{t}Regenerate{/t}
				</a>
			</li>

			<li>
				<a href="#update" onclick="sendForm('update');return false;"
				  title="{t}This changes the expire date but maintains the cache file contents{/t}">
					<img src="{$params.IMAGE_DIR}template_manager/update48x48.png" border="0" /><br />
					{t}Change expiration{/t}
				</a>
			</li>

			<li>
				<a href="{$smarty.server.SCRIPT_NAME}?action=config" title="{t}Configurar cachés{/t}">
					<img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" border="0" /><br />
					{t}Settings{/t}
				</a>
			</li>
		</ul>
	</div>


	<div>

    <table class="adminheading">
        <tr>
            <td align="right">

                <label>
                    {t}Show{/t}
                    <input type="text" name="items_page" id="items_page" value="{$smarty.request.items_page}"
                        size="2" maxlength="2" style="text-align:right; margin-top:-2px; padding:0 5px" />
                    {t}items/page with type{/t}
                </label>

                <select name="type" id="type">
                    <option value="" {if $smarty.request.type eq ''}selected="selected"{/if}>{t}All types{/t}</option>
                    <option value="frontpages" {if $smarty.request.type eq 'frontpages'}selected="selected"{/if}>{t}Frontpages{/t}</option>
                    <option value="articles" {if $smarty.request.type eq 'articles'}selected="selected"{/if}>{t}Inner notice{/t}</option>
                    <option value="mobilepages" {if $smarty.request.type eq 'mobilepages'}selected="selected"{/if}>{t}Mobile frontpages{/t}</option>
                    <option value="rss" {if $smarty.request.type eq 'rss'}selected="selected"{/if}>{t}RSS pages{/t}</option>
					<option value="frontpage-opinions" {if $smarty.request.type eq 'frontpage-opinions'}selected="selected"{/if}>{t}Frontpage opinion{/t}</option>
					<option value="opinions" {if $smarty.request.type eq 'opinions'}selected="selected"{/if}>{t}Inner opinion{/t}</option>
					<option value="video-frontpage" {if $smarty.request.type eq 'video-frontpage'}selected="selected"{/if}>{t}Video frontpage{/t}</option>
					<option value="video-inner" {if $smarty.request.type eq 'video-inner'}selected="selected"{/if}>{t}Video inner{/t}</option>
					<option value="gallery-frontpage" {if $smarty.request.type eq 'video-frontpage'}selected="selected"{/if}>{t}Video frontpage{/t}</option>
					<option value="gallery-inner" {if $smarty.request.type eq 'video-inner'}selected="selected"{/if}>{t}Video inner{/t}</option>
                </select>

                {t}and from{/t}

                <select name="section" id="section">
                    <option value="">{t}All sections{/t}</option>
                    {html_options options=$sections selected=$smarty.request.section}
                </select>

                <button onclick="javascript:paginate(1);return false;">
                    <img src="{$params.IMAGE_DIR}template_manager/reload16x16.png" border="0" align="absmiddle" width="10" />
                    {t}Update list{/t}
                </button>

            </td>
        </tr>
    </table>

    {if count($caches)>0}
    <table id="tabla" name="tabla" width="100%" class="adminlist">
        <thead>
            <tr align="left">
                <th><input type="checkbox" value="" onclick="selectAll(this.checked, $('tabla').select('tbody input[type=checkbox]'));" /></th>
                <th>{t}Resource{/t}</th>
                <th>{t}Category{/t}</th>
                <th>{t}Created in{/t}</th>
                <th>{t}Valid until{/t}</th>
                <th>{t}File size{/t}</th>
                <th>{t}Actions{/t}</th>
            </tr>
        </thead>
	    <tbody>
        {section name="c" loop=$caches}
			<tr bgcolor="{cycle values="#EEEEEE,#FFFFFF"}">
			   <td width="16">
					<input type="checkbox" name="selected[]" value="{$smarty.section.c.index}" />
					<input type="hidden"   name="cacheid[]"  value="{$caches[c].category}|{$caches[c].resource}" />
					<input type="hidden"   name="tpl[]"      value="{$caches[c].template}.tpl" />
   			   </td>
               <td>
					{assign var="resource" value=$caches[c].resource}
					{* Inner Article *}
					{if isset($titles.$resource) && ($caches[c].template == 'article')}
                    <img src="{$params.IMAGE_DIR}template_manager/article16x16.png" border="0" title="{t}Inner article cache file{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/article.php?article_id={$caches[c].resource}&action=read&category_name={$caches[c].category}" target="_blank">
                        <strong>{t}Article{/t}:</strong> {$titles.$resource|clearslash}
					</a>

					{* Video inner *}
					{elseif isset($titles.$resource) && ($caches[c].template == 'video_inner')}
                    <img src="{$params.IMAGE_DIR}template_manager/video16x16.png" border="0" title="{t}Inner video cache file{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/videos.php?id={$caches[c].resource}&action=inner" target="_blank">
						 <strong>{t}Video Inner:{/t}</strong> {$titles.$resource|clearslash}
					</a>

					{* Video frontpage *}
					{elseif ($caches[c].template == 'video_frontpage')}
                    <img src="{$params.IMAGE_DIR}template_manager/video16x16.png" border="0" title="{t}Video inner cache file{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/videos.php?category_name={$caches[c].category}&action=list" target="_blank">
                        <strong>{t}Video Frontpage:{/t}</strong> {$caches[c].category}</a>

					{* Opinion inner *}
					{elseif isset($titles.$resource) && ($caches[c].template == 'opinion')}
                    <img src="{$params.IMAGE_DIR}template_manager/opinion16x16.png" border="0" title="{t}Opinion inner article file{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/opinion.php?category_name=opinion&opinion_id={$caches[c].resource}&action=read" target="_blank">
                        <strong>{t}Opinion inner:{/t}</strong> {$titles.$resource|clearslash}
					</a>

					{* Gallery frontpage *}
					{elseif isset($titles.$resource) && ($caches[c].template == 'gallery-frontpage')}
                    <img src="{$params.IMAGE_DIR}template_manager/gallery16x16.png" border="0" title="{t}Frontpage article caché file{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/opinion.php?category_name=opinion&opinion_id={$caches[c].resource}&action=read" target="_blank">
                        <strong>{t}Opinion inner:{/t}</strong> {$titles.$resource|clearslash}
					</a>

					{* Gallery inner *}
					{elseif isset($titles.$resource) && ($caches[c].template == 'gallery-inner')}
                    <img src="{$params.IMAGE_DIR}template_manager/gallery16x16.png" border="0" title="{t}Caché de gallery interior{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/opinion.php?category_name=opinion&opinion_id={$caches[c].resource}&action=read"
                         target="_blank">
                        <strong>{t}Opinion inner:{/t}</strong> {$titles.$resource|clearslash}
					</a>

					{* RSS opinion *}
					{elseif isset($authors.$resource)}
                    <img src="{$params.IMAGE_DIR}template_manager/rss16x16.png" border="0" title="{t}RSS Opinion author cache file {/t}" />
                    <a href="{$smarty.const.SITE_URL}rss/opinion/{$resource|replace:"RSS":""}/"  target="_blank">
                        <strong>{t}RSS:{/t}</strong> {$authors.$resource|clearslash}
					</a>

					{* Opinion author index*}
					{elseif ($caches[c].template == 'opinion_author_index')}
                    <img src="{$params.IMAGE_DIR}template_manager/opinion16x16.png" border="0" title="{t}RSS frontpage author of opinion{/t}" />
                    <a href="{$smarty.const.SITE_URL}controllers/opinion_index.php?category_name=opinion&opinion_id={$caches[c].resource}&action=read" target="_blank">
                        <strong>{t}Opinion Author Index:{/t}</strong> {t 1=$caches[c].resource}Author ID %1{/t}
					</a>

					{* RSS *}
					{elseif $resource eq "RSS"}
			   	    <img src="{$params.IMAGE_DIR}template_manager/rss16x16.png" border="0" title="{t}Caché RSS{/t}" />

                    <a href="{if $caches[c].category != 'home'}{$smarty.const.SITE_URL}rss/{$caches[c].category}/{else}{$smarty.const.SITE_URL}rss/"{/if}" target="_blank">
						<strong>{t}RSS:{/t}</strong> {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}
					</a>

					{* Frontpage mobile *}
					{elseif not isset($titles.$resource) && not isset($authors.$resource) && ($caches[c].template == 'frontpage-mobile')}
                    <img src="{$params.IMAGE_DIR}template_manager/phone16x16.png" border="0" title="{t}Caché de portadas versión móvil{/t}" />
                    <a href="{$smarty.const.SITE_URL}mobile/seccion/{$caches[c].category}/"  target="_blank">
                        <strong>{t}Mobile Frontpage:{/t}</strong> {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}
					</a>

					{* Frontpages *}
					{elseif ($caches[c].template == 'frontpage')}
                    <img src="{$params.IMAGE_DIR}template_manager/home16x16.png" border="0" title="{t}Caché de portadas sección{/t}" />
                    <a href="{$smarty.const.SITE_URL}seccion/{$caches[c].category}/{$caches[c].resource}"  target="_blank">
					<strong>Frontpage:</strong> {if $caches[c].resource gt 0}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"HOME"} (Pág. {$caches[c].resource})</a>
                    {else}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"HOME"}</a>
                    {/if}

					{* Other kind of resources *}
					{elseif not isset($titles.$resource) && not isset($authors.$resource)}
                    <img src="{$params.IMAGE_DIR}template_manager/home16x16.png" border="0" title="{t}Caché de portadas sección{/t}" />
                    <a href="{$smarty.const.SITE_URL}seccion/{$caches[c].category}/{$caches[c].resource}"  target="_blank">
					{if $caches[c].resource gt 0}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"} {t}(Pág.{/t} {$caches[c].resource})</a>
                    {else}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}</a>
                    {/if}


				{/if}
            </td>
			<td width="100">
                {$ccm->get_title($caches[c].category)|clearslash|default:"HOME"}
            </td>

			<td width="125" align="center">
                {$caches[c].created|date_format:"%H:%M:%S %d/%m/%Y"}
            </td>

			<td width="190" align="center">
                <div>
					{if $caches[c].expires < $smarty.now}
						 <img style="margin:7px 4px; float:left" src="{$params.IMAGE_DIR}template_manager/outtime16x16.png" border="0" alt="La caché ya expiró" style="float: left; margin-right: 4px;" />
					 {else}
						 <img style="margin:7px 4px; float:left" src="{$params.IMAGE_DIR}template_manager/ok16x16.png" border="0" alt="Caché activa"  style="float: left; margin-right: 4px;" />
					 {/if}
					 <input type="text" name="expires[]" value="{$caches[c].expires|date_format:"%H:%M %d/%m/%Y"}"
						maxlength="20" style="width: 130px; display:inline"/>
				</div>
            </td>
            <td width="70" align="center">
                {$caches[c].size/1024|string_format:"%d"} KB
            </td>
			<td width="20" align="center">
               <a href="?action=refresh&amp;cacheid={$caches[c].category}|{$caches[c].resource}&amp;tpl={$caches[c].template}.tpl&{$paramsUri}"
                   title="{t}Regenerate cache file{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/refresh16x16.png" border="0" alt="" />
			   </a>&nbsp;
			   <a href="?action=delete&amp;cacheid={$caches[c].category}|{$caches[c].resource}&amp;tpl={$caches[c].template}.tpl&{$paramsUri}"
                    title="{t}Delete cache file{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/delete16x16.png" border="0" alt="" />
			   </a>
            </td>
        </tr>
        {/section}
		</tbody>

		<tfoot>
			<tr>
				<td colspan="7" class="pagination">
					<script language="javascript" type="text/javascript">
					// <![CDATA[
					function paginate(page) {
						$('page').value = page;
						$('formulario').submit();
					}
					// ]]>
					</script>
					{$pager->links} &nbsp;
				</td>
			</tr>
		</tfoot>
    </table>
    {else}
	 <div style="border:1px solid #ccc; padding:10px; font-size:1.2em; text-align:center">
		  <p>{t escape="no"}Ohh, there is <strong>no cache file</strong> in the system.{/t}</p>
		  <p>{t escape="no" 1=$smarty.const.SITE_URL}Visit some pages in <a href="%1" title="Visit your site">your site</a>{/t}</p>
	 </div>
    {/if}

</div>

<input type="hidden" id="page"   name="page"   value="{$smarty.request.page|default:'1'}" />
<input type="hidden" id="action" name="action" value="" />
</form>
{/block}

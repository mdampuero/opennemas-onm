{extends file="base/admin.tpl"}

{block name="header-js" append}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}AdPosition.js"></script>
{/block}
{block name="footer-js" append}
      <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>
{/block}
{block name="header-css" append}
<style type="text/css">
    .inputExtension {
        top:5px !important;
    }
    object {
        z-index:0;
    }
    .datepickerControl {
        z-index:99;
    }
    table.adminlist img {
        height:auto !important;
        max-height:400px;
    }
</style>
{/block}

{block name="content" append}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Ad manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating banner{/t}{else}{t}Editing banner{/t}{/if}</h2></div>
		<ul class="old-button">
			<li>
			{if isset($advertisement->id)}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$advertisement->id}', 'formulario');">
			{else}
			   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
			{/if}
				<img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="Guardar y salir"><br />{t}Save and exit{/t}
				</a>
			</li>
			<li class="separator"></li>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action={$_REQUEST['desde']}&category={$_REQUEST['category']|default:0}&page={$_GET['page']|default:0}" value="Cancelar" title="Cancelar">
					<img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="wrapper-content">

    {if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

        <table class="adminheading">
            <tr>
                <td>{t escape="off"}Please, fill the form with the ad description and push <strong>Save and Exit </strong> when you've finished.{/t}</td>
            </tr>
        </table>
        <table class="adminform">
            <tbody>

                <tr>
                    <td valign="top" align="right" width=10%>
                        <label for="title">{t}Name{/t}</label>
                    </td>
                    <td valign="top">
                        <input  type="text" id="title" name="title" title="Publicidad"
								tabindex=1
                                value="{$advertisement->title|clearslash|escape:"html"}"
                                class="required"
                                style="width:90%"
                                onBlur="javascript:get_metadata(this.value);"/>
                    </td>

                    <td rowspan="4" valign="top">
                        <div style="background-color: #F5F5F5; padding:9px; min-width:300px;">
                            <table width="100%" border="0">
                                <tr>
                                    <td valign="top" align="right">
                                        <label for="available">{t}Published:{/t}</label>
                                    </td>
                                    <td>
                                        <select name="available" id="available"
                                            {acl isNotAllowed="ADVERTISEMENT_AVAILABLE"} disabled="disabled" {/acl} >
                                            <option value="1" {if $advertisement->available == 1}selected="selected"{/if}>Si</option>
                                            <option value="0" {if $advertisement->available == 0}selected="selected"{/if}>No</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td valign="top" align="right">
                                        <label for="overlap">{t}Hide Flash events:{/t}</label>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="overlap" id="overlap" value="1" {if $advertisement->overlap == 1}checked="checked"{/if} />
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="middle" align="right">
                                        <label>{t}Periodicity:{/t}</label>
                                    </td>
                                    <td align="left">
                                        <input type="radio" id="non" name="type_medida" value="NULL"
                                            {if !isset($advertisement->type_medida) || $advertisement->type_medida == 'NULL'} checked="checked"{/if} onClick="permanencia(this);"/>
                                        <label>{t}Undefined{/t}</label>
                                        <br>
                                        <span id="div_permanencia" style="display:{if $advertisement->with_script==1}none{/if};">
                                            <input type="radio" id="clic" name="type_medida" value="CLIC"
                                                {if $advertisement->type_medida == 'CLIC'}checked="checked"{/if} onClick="permanencia(this);"/>
                                            <label>Nº Clicks</label>
                                            <br>
                                            <input id="view" type="radio" name="type_medida" value="VIEW"
                                                {if $advertisement->type_medida == 'VIEW'}checked="checked"{/if} onClick="permanencia(this);" />
                                            <label>Nº Visitas</label>
                                        </span>
                                        <br>
                                        <input type="radio" id="fecha" name="type_medida" value="DATE"
                                            {if $advertisement->type_medida == 'DATE'}checked="checked"{/if} onClick="permanencia(this);" />
                                        <label>Por Fechas</label>
                                    </td>
                                </tr>

                                <tr>
                                    <td valign="top" colspan="2">

                                        <div id="porfecha" style="{if $advertisement->type_medida neq 'DATE'} display:none{else}display:block{/if};">
                                            <table width="95%">
                                            <tr>
                                                <td valign="top" align="right" >
                                                    <label for="starttime">{t}Publication start time:{/t}</label>
                                                </td>
                                                <td>
                                                    <input type="text" id="starttime" style="width:100%" name="starttime" title="Fecha inicio publicacion"
                                                        value="{if $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />

                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" align="right">
                                                    <label for="endtime">{t}Publication end time:{/t}</label>
                                                </td>
                                                <td>
                                                    <input type="text" id="endtime" style="width:100%" name="endtime" title="Fecha fin publicacion"
                                                        value="{if $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />

                                                </td>
                                            </tr>
                                            </table>
                                        </div>

                                        <div id="porclic" style="width:95%;display:{if $advertisement->type_medida!='CLIC'}none{/if};">
                                            <table width="95%">
                                                <tr>
                                                    <td valign="top" align="right" style="padding:4px;" width="40%">
                                                        <label for="title">{t}# of clicks:{/t}</label>
                                                    </td>
                                                    <td style="padding:4px;" nowrap="nowrap" width="60%">
                                                        <input type="text" id="num_clic" name="num_clic" title="Numero de clic"
                                                            value="{$advertisement->num_clic}" />
                                                        {if $smarty.request.action eq "read"}
                                                            {if $advertisement->type_medida == 'CLIC'}
                                                                Actuales: {$advertisement->num_clic_count}
                                                            {/if}
                                                            <input type="hidden" id="num_clic_count" name="num_clic_count" title="Numero de clic"
                                                                value="{$advertisement->num_clic_count}" />
                                                        {/if}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div id="porview" style="width:95%;display:{if $advertisement->type_medida!='VIEW'}none{/if};">
                                            <table width="95%">
                                                <tr>
                                                    <td valign="top" align="right" style="padding:4px;" width="40%">
                                                        <label for="title">{t}Visualization count:{/t}</label>
                                                    </td>
                                                    <td style="padding:4px;" nowrap="nowrap" width="60%">
                                                        <input type="text" id="num_view" name="num_view" title="Numero de visionados"
                                                            value="{$advertisement->num_view}" />
                                                        {if $smarty.request.action eq "read"}
                                                            {if $advertisement->type_medida == 'VIEW'}
                                                                Actuales: {$advertisement->views}
                                                            {/if}
                                                        {/if}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                    <td>

                                </tr>

                                <tr>
                                    <td valign="top" colspan="2">
                                        <div id="timeout_container" style="margin-top: 10px; border-top: 1px solid #CCC;width:95%;display:{if $advertisement->type_advertisement!=50}none{/if};">

                                        <label for="timeout">{t}Time scheduling:{/t}</label>

                                        <table width="95%">
                                            <tr>
                                                <td width="40%">&nbsp;</td>
                                                <td style="padding:4px;" nowrap="nowrap" width="60%">
                                                    <input type="text" id="timeout" name="timeout" size="2" title="Segundos antes de desaparecer"
                                                        value="{$advertisement->timeout|default:"4"}" style="text-align: right;" />
                                                    {t escape="off"}seconds. <sub>( -1 no desaparece )</sub>{/t}
                                                </td>
                                            </tr>
                                        </table>

                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </div>

                        {if $smarty.request.action eq "read"}
                            <input type="hidden" id="num_clic_count" name="num_clic_count" title="Numero de clic"
                                value="{$advertisement->num_clic_count}" />
                        {/if}

                    </td>

                </tr>

				<tr>
					<td valign="top" align="right">
						<label for="metadata">{t}Keywords:{/t}</label><br />
						<sub>{t}Separated by commas{/t}</sub>
					</td>
					<td>
						<textarea id="metadata" name="metadata" style="width:90%" tabindex=2
						   title="Metadatos" value="">{$advertisement->metadata|strip}</textarea>
					</td>
				</tr>

                <tr>
                    <td valign="top" align="right" >
                        <div id="div_url1" style="display:{if $advertisement->with_script==1}none{/if};">
                            <label for="title">{t}URL:{/t}</label>
                        </div>
                    </td>
                    <td valign="top">
                        <div id="div_url2" style="display:{if $advertisement->with_script==1}none{/if};">
                            <input type="text" id="url" name="url" class="validate-url" title="Direccion web Publicidad"
                                style="width:90%" value="{$advertisement->url|default:"http://"}" tabindex=3 />
                        </div>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right">
                        <label for="category">{t}Section{/t}</label>
                    </td>
                    <td valign="top" style="height:20px;padding:4px;">
                        <select name="category[]" id="category" class="required" multiple tabindex=4>
                        {if $smarty.request.action eq "read"}
                            <option value="0" {if in_array(0,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Frontpage{/t}</option>
                            <option value="4" {if in_array(4,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Opinion{/t}</option>
                            <option value="3" {if in_array(3,$advertisement->fk_content_categories)}selected="selected"{/if}>{t}Gallery{/t}</option>
                            {section name=as loop=$allcategorys}
                                <option value="{$allcategorys[as]->pk_content_category}"
                                    {if in_array($allcategorys[as]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                                    {$allcategorys[as]->title}
                                </option>
                                {section name=su loop=$subcat[as]}
                                    <option value="{$subcat[as][su]->pk_content_category}"
                                        {if in_array($allcategorys[as]->pk_content_category,$advertisement->fk_content_categories)}selected="selected"{/if}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                                    </option>
                                {/section}
                            {/section}
                        {else}
                            <option value="0" {if $category == 0}selected="selected"{/if}>{t}Frontpage{/t}</option>
                            {is_module_activated name="OPINION_MANAGER"}
                            <option value="4" {if $category == 4}selected="selected"{/if}>{t}Opinion{/t}</option>
                            {/is_module_activated}
                            {is_module_activated name="ALBUM_MANAGER"}
                            <option value="3" {if $category eq 3}selected="selected"{/if}>{t}Gallery{/t}</option>
                            {/is_module_activated}

                            {section name=as loop=$allcategorys}
                                <option value="{$allcategorys[as]->pk_content_category}"
                                    {if $category eq $allcategorys[as]->pk_content_category}selected="selected"{/if}>
                                    {$allcategorys[as]->title}
                                </option>
                                {section name=su loop=$subcat[as]}
                                    <option value="{$subcat[as][su]->pk_content_category}"
                                        {if $category eq $subcat[as][su]->pk_content_category}selected="selected"{/if}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                                    </option>
                                {/section}
                            {/section}
                        {/if}

                        </select>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right" style="height:20px;padding:4px;" nowrap="nowrap">
                        <label for="with_script">{t}Ad with JavaScript:{/t}</label>
                    </td>
                    <td valign="top" style="height:20px;padding:4px;">
                        <input type="checkbox" id="with_script" name="with_script" value="1" tabindex=5
                            {if $advertisement->with_script == 1}checked="checked"{/if} onClick="with_without_script(this);" />
                        <div id="div_script" style="display:{if $advertisement->with_script!=1}none{/if}; text-align: right;">
                            <textarea name="script" id="script" class="validate-script" title="script de publicidad" style="width:100%; height:8em;">{$advertisement->script|default:'&lt;script type="text/javascript"&gt;/* Código javascript */&lt;/script&gt;'}</textarea>
                            <br />

                            <label>Recortes de código Geoip:
                                <span id="geoip_select"></span>
                            </label>

                            <script type="text/javascript" src="{$params.JS_DIR}GeoipHelper.js?cacheburst=1258404035" charset="utf-8"></script>
                            <script type="text/javascript">
                                new GeoipHelper('geoip_select', 'script');
                            </script>

                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <button onclick="testScript(this.form);return false;">{t}Test Javascript code{/t}</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:4px;" colspan="3">
                        {include file="advertisement/partials/advertisement_images.tpl"}
                    </td>
                </tr>

                <tr>
                    <td valign="top" style="padding:4px;" colspan="3">
                        <label for="title"><h2>{t}Ad position:{/t}</h2></label>

                        <ul id="tabs">
                            <li><a href="#publi-portada">{t}Frontpage{/t}</a></li>
                            <li><a href="#publi-interior">{t}Inner article{/t}</a></li>
                            {is_module_activated name="VIDEO_MANAGER"}
                            <li><a href="#publi-video">{t}Video frontpage{/t}</a></li>
                            <li><a href="#publi-video-interior">{t}Inner video{/t}</a></li>
                            {/is_module_activated}
                            {is_module_activated name="OPINION_MANAGER"}
                            <li><a href="#publi-opinion">{t}Opinion frontpage{/t}</a></li>
                            <li><a href="#publi-opinion-interior">{t}Inner opinion{/t}</a></li>
                            {/is_module_activated}
                            {is_module_activated name="ALBUM_MANAGER"}
                            <li><a href="#publi-gallery">{t}Galleries{/t}</a></li>
                            <li><a href="#publi-gallery-inner">{t}Gallery Inner{/t}</a></li>
                            {/is_module_activated}
                        </ul>

                        <div id="publi-portada" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions.tpl"}
                        </div>

                        <div id="publi-interior" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_interior.tpl"}
                        </div>
                        {is_module_activated name="OPINION_MANAGER"}
                        <div id="publi-opinion" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_opinion.tpl"}
                        </div>
                        <div id="publi-opinion-interior" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_opinion_interior.tpl"}
                        </div>
                        {/is_module_activated}
                        {is_module_activated name="VIDEO_MANAGER"}
                        <div id="publi-video" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_video.tpl"}
                        </div>
                        <div id="publi-video-interior" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_video_interior.tpl"}
                        </div>
                        {/is_module_activated}
                        {is_module_activated name="ALBUM_MANAGER"}
                        <div id="publi-gallery" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_gallery.tpl"}
                        </div>
                        <div id="publi-gallery-inner" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_gallery_inner.tpl"}
                        </div>
                        {/is_module_activated}

                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="pagination">
                    <td colspan=3></td>
                </tr>
            </tfoot>
        </table>

        <style type="text/css">
            #tabs a {
                background-color: #F5F5F5;
            }

            #tabs a.active-tab {
                background-color: #EEE;
                font-weight: bold;
            }
            table.adminlist img {
                height:auto !important;
            }

        </style>

        <script language="javascript" type="text/javascript">
            function testScript(frm)  {
                frm.action.value = 'test_script';
                frm.target = 'test_script'; // abrir noutra ventá
                frm.submit();

                frm.target = ''; // cambiar o target para que siga facendo peticións na mesma ventá
                frm.action.value = '';
            }
        </script>

        <script type="text/javascript">
            /* <![CDATA[ */
            // Add exhibit method to Fabtabs to can select a tab by id
            Fabtabs.addMethods( {
                exhibit: function(id) {
                    elm = this.element.select("a[href$="+id+"]")[0];
                    this.show( elm );
                    this.menu.without(elm).each(this.hide.bind(this));

                    if(['publi-opinion', 'publi-opinion-interior'].indexOf(id)!=-1) {
                        this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', { display: ''});
                        this.changePropertyTabs('a[href$=publi-video],a[href$=publi-video-interior]', { display: 'none'});
                        this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', { display: 'none'});
                        this.changePropertyTabs('a[href$=publi-gallery], a[href$=publi-gallery-inner]', { display: 'none' });

                     } else {
                        this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', { display: '' });
                        this.changePropertyTabs('a[href$=publi-video],a[href$=publi-video-interior]', { display: '' });
                        this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', { display: 'none' });
                        this.changePropertyTabs('a[href$=publi-gallery],a[href$=publi-gallery-inner]', { display: '' });
                    }


                },

                changePropertyTabs: function(tabSelector, style) {
                    this.element.select(tabSelector).each(function(item) {
                        item.setStyle(style);
                    });
                },

                activate :  function(ev) {
                    var elm = Event.findElement(ev, "a");
                    var id = elm.getAttribute('href').gsub(/#(.*?)$/, '#{1}');

                    Event.stop(ev);

                    this.show(elm);
                    this.menu.without(elm).each(this.hide.bind(this));

                    if( $F('category') == '4' && (['publi-opinion', 'publi-opinion-interior'].indexOf(id)!=-1)) {
                        this.show(elm);
                        this.menu.without(elm).each(this.hide.bind(this));
                    }

                    if( $F('category') != '4' && (['publi-opinion', 'publi-opinion-interior'].indexOf(id)==-1)) {
                        this.show(elm);
                        this.menu.without(elm).each(this.hide.bind(this));
                    }
                }
            });

            $('category').observe('change', function() {
                if(this.value == '4') {
                    $fabtabs.exhibit('publi-opinion');
                } else if(this.value == '3') {
                    $fabtabs.exhibit('publi-gallery');
                } else {
                    $fabtabs.exhibit('publi-portada');
                }
            });

            document.observe('dom:loaded', function() {
                {if $category ne '4'}
                    {if $advertisement->type_advertisement lt 100}
                        $fabtabs.exhibit('publi-portada');
                    {else}
                        $fabtabs.exhibit('publi-interior');
                    {/if}
                {else}
                    {if $advertisement->type_advertisement ne 101 && $advertisement->type_advertisement ne 5}
                        $fabtabs.exhibit('publi-opinion');
                    {else}
                        $fabtabs.exhibit('publi-opinion-interior');
                    {/if}
                {/if}
            });

        </script>

        <script type="text/javascript" language="javascript">
            if($('starttime')) {
                new Control.DatePicker($('starttime'), {
                    icon: '{$smarty.const.SITE_URL_ADMIN}/themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
                });

                new Control.DatePicker($('endtime'), {
                    icon: '{$smarty.const.SITE_URL_ADMIN}/themes/default/images/template_manager/update16x16.png',
                    locale: 'es_ES',
                    timePicker: true,
                    timePickerAdjacent: true,
                    dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
                });
            }
        </script>

        <input type="hidden" name="filter[type_advertisement]" value="{$smarty.request.filter.type_advertisement}" />
        <input type="hidden" name="filter[available]" value="{$smarty.request.filter.available}" />
        <input type="hidden" name="filter[type]" value="{$smarty.request.filter.type}" />
    {/if}

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
</div>
{/block}

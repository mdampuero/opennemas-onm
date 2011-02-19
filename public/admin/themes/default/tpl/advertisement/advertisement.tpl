{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}AdPosition.js"></script>
{/block}


{block name="content"}

<form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >
    <div id="content-wrapper" style="width:80% !important; margin:0 auto;">

{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
    {include file="advertisement/list.tpl"}
{/if}

{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}
<script language="javascript" type="text/javascript">
function testScript(frm)  {
    frm.action.value = 'test_script';
    frm.target = 'test_script'; // abrir noutra ventá
    frm.submit();

    frm.target = ''; // cambiar o target para que siga facendo peticións na mesma ventá
    frm.action.value = '';
}
</script>

{include file="botonera_up.tpl"}

<table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" width="800">
<tbody>

<tr>
	<td valign="top" align="right" style="height:20px;padding:4px;" width="30%">
		<label for="title">{t}Name:{/t}</label>
	</td>
	<td valign="top" style="height: 20px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title={t}"Publicity"{/t}
			size="80" value="{$advertisement->title|clearslash|escape:"html"}" class="required" onBlur="javascript:get_metadata(this.value);"/>
		{* <input type="hidden" id="category" name="category" title="Publicidad" value="advertisement" /> *}
	</td>

    {* begin: PANEL PROPIEDADES *}
    <td rowspan="5" valign="top">
        <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
        <div style="background-color: #F5F5F5; padding: 18px 9px; width: 400px;">
            <table width="100%" border="0">
                <tr>
                    <td valign="top" align="right"><label for="available">Publicado:</label></td>
                    <td>
                        <select name="available" id="available">
                            <option value="1" {if $advertisement->available == 1}selected="selected"{/if}>Si</option>
                            <option value="0" {if $advertisement->available == 0}selected="selected"{/if}>No</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right">
                        <label for="metadata">{t}Keywords:{/t}</label><br />
                        <sub>{t}Separate by coma{/t}</sub>
                    </td>
                    <td>
                        <textarea id="metadata" name="metadata" cols="20" rows="2" style="width: 100%;"
                           title="Metadatos" value="">{$advertisement->metadata|strip}</textarea>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right">
                        <label for="overlap">{t}Hide Flash events{/t}</label>
                    </td>
                    <td>
                        <input type="checkbox" name="overlap" id="overlap" value="1" {if $advertisement->overlap == 1}checked="checked"{/if} />
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="border-bottom: 1px solid #CCC;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" align="left">
                        <label>{t}Periodicity:{/t}</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <label>
                            {t}Undefined{/t}<input type="radio" id="non" name="type_medida" value="NULL"
                            {if !isset($advertisement->type_medida) || $advertisement->type_medida == 'NULL'} checked="checked"{/if} onClick="permanencia(this);"/>
                        </label>
                        <span id="div_permanencia" style="display:{if $advertisement->with_script==1}none{/if};">
                            <label>{t}Nº Clicks{/t}<input type="radio" id="clic" name="type_medida" value="CLIC"
                                {if $advertisement->type_medida == 'CLIC'}checked="checked"{/if} onClick="permanencia(this);"/></label>
                            <label>{t}Nº Visits{/t}<input id="view" type="radio" name="type_medida" value="VIEW"
                                {if $advertisement->type_medida == 'VIEW'}checked="checked"{/if} onClick="permanencia(this);" /></label>
                        </span>

                        <label>
                            {t}By date{/t}<input type="radio" id="fecha" name="type_medida" value="DATE"
                                {if $advertisement->type_medida == 'DATE'}checked="checked"{/if} onClick="permanencia(this);" />
                        </label>
                    </td>
                </tr>

                <tr>
                    <td valign="top" colspan="2">

                        <div id="porfecha" style="width:95%;display:{if $advertisement->type_medida!='DATE'}none{/if};">
                            <table width="95%">
                            <tr>
                                <td valign="top" align="right" style="padding:4px;" width="40%">
                                    <label for="starttime">{t}Start time publication:{/t}</label>
                                </td>
                                <td style="padding:4px;" nowrap="nowrap" width="60%">
                                    <input type="text" id="starttime" name="starttime" size="16" title={t}"Start time publication"{/t}
                                        value="{if $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />

                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="right" style="padding:4px;" width="40%">
                                    <label for="endtime">{t}End time publication:{/t}</label>
                                </td>
                                <td style="padding:4px;" nowrap="nowrap" width="60%">
                                    <input type="text" id="endtime" name="endtime" size="16" title={t}"End time publication"{/t}
                                        value="{if $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />

                                </td>
                            </tr>
                            </table>
                        </div>

                        <div id="porclic" style="width:95%;display:{if $advertisement->type_medida!='CLIC'}none{/if};">
                            <table width="95%">
                                <tr>
                                    <td valign="top" align="right" style="padding:4px;" width="40%">
                                        <label for="title">{t}Click number:{/t}</label>
                                    </td>
                                    <td style="padding:4px;" nowrap="nowrap" width="60%">
                                        <input type="text" id="num_clic" name="num_clic" title={t}"Click number"{/t}
                                            value="{$advertisement->num_clic}" />
										{if $smarty.request.action eq "read"}
                                            {if $advertisement->type_medida == 'CLIC'}
                                                Actuales: {$advertisement->num_clic_count}
                                            {/if}
                                            <input type="hidden" id="num_clic_count" name="num_clic_count" title={t}"Click number"{/t}
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
                                        <label for="title">{t}Views number{/t}</label>
                                    </td>
                                    <td style="padding:4px;" nowrap="nowrap" width="60%">
                                        <input type="text" id="num_view" name="num_view" title={t}Views number{/t}
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

                        <label for="timeout">{t}Timer{/t}</label>

                        <table width="95%">
                        <tr>
                            <td width="40%">&nbsp;</td>
                            <td style="padding:4px;" nowrap="nowrap" width="60%">
                                <input type="text" id="timeout" name="timeout" size="2" title={t}"Seconds before disappear"{/t}
                                    value="{$advertisement->timeout|default:"4"}" style="text-align: right;" />
                                {t}seconds.{/t} <sub>{t}( -1 don't disappear ){/t}</sub>
                            </td>
                        </tr>
                        </table>

                        </div>
                    </td>
                </tr>

            </table>
        </div>
        <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>

        {if $smarty.request.action eq "read"}
            <input type="hidden" id="num_clic_count" name="num_clic_count" title={t}"Click number"{/t}
                value="{$advertisement->num_clic_count}" />
        {/if}

    </td>
    {* end: PANEL PROPIEDADES *}
</tr>

<tr>
	<td valign="top" align="right" style="height:20px;padding:4px;" width="30%">
        <div id="div_url1" style="display:{if $advertisement->with_script==1}none{/if};">
            <label for="title">{t}URL:{/t}</label>
        </div>
	</td>
	<td valign="top" style="height:20px;padding:4px;" nowrap="nowrap" width="70%">
        <div id="div_url2" style="display:{if $advertisement->with_script==1}none{/if};">
            <input type="text" id="url" name="url" class="validate-url" title={t}"Web advertisement direction"{/t}
                size="80" value="{$advertisement->url|default:"http://"}" />
        </div>
	</td>
</tr>

<tr>
    <td valign="top" align="right" style="height:20px;padding:4px;">
        <label for="category">{t}Section{/t}</label>
    </td>
    <td valign="top" style="height:20px;padding:4px;">
        <select name="category" id="category" class="required">
            <option value="0">{t}HOME{/t}</option>
            <option value="4" {if $category eq 4}selected="selected"{/if}>{t}Opinion{/t}</option>
            <option value="3" {if $category eq 3}selected="selected"{/if}>{t}Gallery{/t}</option>
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
        </select>
    </td>
</tr>

<tr>
	<td valign="top" align="right" style="height:20px;padding:4px;" nowrap="nowrap">
		<label for="with_script">{t}JavaScript with advertisement{/t}</label>
    </td>
    <td valign="top" style="height:20px;padding:4px;">
        <input type="checkbox" id="with_script" name="with_script" value="1"
            {if $advertisement->with_script == 1}checked="checked"{/if} onClick="with_without_script(this);" />
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
        <div id="div_script" style="display:{if $advertisement->with_script!=1}none{/if}; text-align: right;">
            <textarea name="script" id="script" class="validate-script" title={t}"Advertisement script"{/t} style="width:100%; height:8em;">{$advertisement->script|default:'&lt;script type="text/javascript"&gt;/* Código javascript */&lt;/script&gt;'}</textarea>
            <br />

            <label>{t}Geoip code{/t}
                <span id="geoip_select"></span>
            </label>

            <script type="text/javascript" src="{$params.JS_DIR}GeoipHelper.js?cacheburst=1258404035" charset="utf-8"></script>
            <script defer="defer" type="text/javascript">

            new GeoipHelper('geoip_select', 'script');

            </script>

            &nbsp;&nbsp;&nbsp;&nbsp;
            <button onclick="testScript(this.form);return false;">{t}Test JS code{/t}</button>
		</div>
	</td>
</tr>

{* Selector de imágenes *}
{include file="advertisement/advertisement_images.tpl"}

<tr>
	<td valign="top" style="padding:4px;padding-top: 40px;" colspan="2">
		<label for="title">{t}Advertisement type{/t} </label>

        <ul id="tabs">
            <li><a href="#publi-portada">{t}Frontpage{/t}</a></li>
            <li><a href="#publi-interior">{t}Inner article{/t}</a></li>
            <li><a href="#publi-video">{t}Video Frontpage{/t}</a></li>
            <li><a href="#publi-video-interior">{t}Inner video{/t}</a></li>
            <li><a href="#publi-opinion">{t}Opinion Frontpage{/t}</a></li>
            <li><a href="#publi-opinion-interior">{t}Inner opinion{/t}</a></li>
            <li><a href="#publi-gallery">Gallery</a></li>
        </ul>

        <div id="publi-portada" class="panel-ads">
            {include file="advertisement/advertisement_positions.tpl"}
        </div>

        <div id="publi-interior" class="panel-ads">
            {include file="advertisement/advertisement_positions_interior.tpl"}
        </div>

        <div id="publi-opinion" class="panel-ads">
            {include file="advertisement/advertisement_positions_opinion.tpl"}
        </div>

        <div id="publi-opinion-interior" class="panel-ads">
            {include file="advertisement/advertisement_positions_opinion_interior.tpl"}
        </div>
        <div id="publi-video" class="panel-ads">
            {include file="advertisement/advertisement_positions_video.tpl"}
        </div>

        <div id="publi-video-interior" class="panel-ads">
            {include file="advertisement/advertisement_positions_video_interior.tpl"}
        </div>
        <div id="publi-gallery" class="panel-ads">
            {include file="advertisement/advertisement_positions_gallery.tpl"}
        </div>

	</td>
    <td>&nbsp;</td>
</tr>
</tbody>
</table>

{* if $smarty.request.action eq "read" *}
<script defer="defer" type="text/javascript">
/* <![CDATA[ */

// Add exhibit method to Fabtabs to can select a tab by id
Fabtabs.addMethods({
    exhibit: function(id) {
        elm = this.element.select("a[href$="+id+"]")[0];
        this.show( elm );
        this.menu.without(elm).each(this.hide.bind(this));

        if(['publi-opinion', 'publi-opinion-interior'].indexOf(id)!=-1) {
            this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', { display: '' });
            this.changePropertyTabs('a[href$=publi-video],a[href$=publi-video-interior]', { display: 'none' });
            this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', { display: 'none' });
            this.changePropertyTabs('a[href$=publi-gallery]', { display: 'none' });
        } else if(['publi-gallery'].indexOf(id) != -1) {
            this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', { display: 'none' });
            this.changePropertyTabs('a[href$=publi-video],a[href$=publi-video-interior]', { display: 'none'});
            this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', { display: 'none' });
            this.changePropertyTabs('a[href$=publi-gallery]', { display: '' });

         } else {
            this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', { display: '' });
            this.changePropertyTabs('a[href$=publi-video],a[href$=publi-video-interior]', { display: '' });
            this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', { display: 'none' });
            this.changePropertyTabs('a[href$=publi-gallery]', { display: 'none' });
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


document.observe('dom:loaded', function() {ldelim}
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
{rdelim});




/* ]]> */
</script>
{* /if *}

{* FIXME: replace by DatePicker}
{dhtml_calendar inputField="starttime" button="triggerstart"  ifFormat="%Y-%m-%d %H:%M:%S" firstDay=1 align="CR"}
{dhtml_calendar inputField="endtime" button="triggerend"  ifFormat="%Y-%m-%d %H:%M:%S" firstDay=1 align="CR"}
*}
<script defer="defer" type="text/javascript" language="javascript">



if($('starttime')) {
    new Control.DatePicker($('starttime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });

    new Control.DatePicker($('endtime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
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


    {* ******************************************************************************* *}
    {* Vista para a proba do script de publicidade, esta tpl abrirase nunha nova ventá *}
    {if isset($smarty.request.action) && $smarty.request.action eq "test_script"}
        <h1>Prueba script publicidad</h1>
        <div style="text-align:center; border: 2px dashed #CCC;">
            {$script}
        </div>

        <div align="right" style="margin: 10px 10px;">
            <a href="#" style="color: #666; font-size: large;"
                onclick="window.close();" title="Cerrar ventana">[Cerrar]</a>
        </div>
    {/if}


    {* ******************************************************************************* *}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
    </div><!--fin content-wrapper-->
</form>
{/block}

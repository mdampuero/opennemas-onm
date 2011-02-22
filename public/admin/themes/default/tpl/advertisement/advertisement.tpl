{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    table.adminlist img {
        height:auto !important;
    }
</style>
{/block}


{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}AdPosition.js"></script>
{/block}

{block name="footer-js" append}
    <script language="javascript" type="text/javascript">
        function testScript(frm)  {
            frm.action.value = 'test_script';
            frm.target = 'test_script'; // abrir noutra ventá
            frm.submit();

            frm.target = ''; // cambiar o target para que siga facendo peticións na mesma ventá
            frm.action.value = '';
        }
    </script>
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
{/block}


{block name="content"}
<div class="wrapper-content">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >



        <div id="menu-acciones-admin" class="clearfix">
            <div style="float: left; margin-left: 10px; margin-top: 10px;">
                <h2>
                    {if $smarty.request.action eq "new"}
                        {t}Creating new Ad{/t}
                    {elseif $smarty.request.action eq "read"}
                        {t}Editing Ad{/t}
                    {/if}
                </h2>
            </div>
            <ul>
                 <li>
                    <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/advertisement/advertisement.php?action=list&category={php} echo $_REQUEST['category']; {/php}&page={php} echo $_GET['page']; {/php}" class="admin_add"  value="Cancelar" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />{t}Cancel{/t}
                    </a>
                </li>
                <li>
                {if isset($advertisement->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$advertisement->id}', 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save and exit{/t}
                    </a>
                </li>
            </ul>
        </div>

        <table class="adminheading">
            <tbody>
                <tr>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <table class="adminlist" class="fuente_cuerpo">
            <tbody>

                <tr>
                    <td align="right">
                        <label for="title">{t}Name:{/t}</label>
                    </td>
                    <td>
                        <input type="text" id="title" name="title" title="{t}Publicity{/t}"
                            size="80" value="{$advertisement->title|clearslash|escape:"html"}"
                            class="required"
                            onBlur="javascript:get_metadata(this.value);"/>
                    </td>

                    {* begin: PANEL PROPIEDADES *}
                    <td rowspan=5>
                        <div>
                            <table width="100%" border="0">
                                <tr>
                                    <td align="right">
                                        <label for="available">{t}Published:{/t}</label>
                                    </td>
                                    <td>
                                        <select name="available" id="available">
                                            <option value="1" {if $advertisement->available == 1}selected="selected"{/if}>{t}Yes{/t}</option>
                                            <option value="0" {if $advertisement->available == 0}selected="selected"{/if}>{t}No{/t}</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">
                                        <label for="metadata">{t}Keywords:{/t}</label>
                                        <sub>{t}(Separated by comas){/t}</sub>
                                    </td>
                                    <td>
                                        <textarea id="metadata" name="metadata"
                                           title="{t}Metadata{/t}" value="">{$advertisement->metadata|strip}</textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td valign="middle" align="right">
                                        <label for="overlap">{t}Hide Flash events{/t}</label>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="overlap" id="overlap" value="1" {if $advertisement->overlap == 1}checked="checked"{/if} />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <label>{t}Periodicity:{/t}</label>
                                    </td>
                                    <td>
                                        <ul id="div_permanencia" style="list-style:none; {if $advertisement->with_script==1}display:none{/if};">
                                            <li>
                                                <input type="radio" id="non" name="type_medida" value="NULL"
                                                {if !isset($advertisement->type_medida) || $advertisement->type_medida == 'NULL'} checked="checked"{/if} onClick="permanencia(this);"/>
                                                <label for="type_media">{t}Undefined{/t}</label>
                                            </li>
                                            <li>
                                                <input type="radio" id="clic" name="type_medida" value="CLIC"
                                                    {if $advertisement->type_medida == 'CLIC'}checked="checked"{/if} onClick="permanencia(this);"/>
                                                <label for="type_medida">{t}# Clics{/t}</label>
                                            </li>
                                            <li>
                                                <input id="view" type="radio" name="type_medida" value="VIEW"
                                                    {if $advertisement->type_medida == 'VIEW'}checked="checked"{/if} onClick="permanencia(this);" />
                                                <label for="type_medida">{t}Nº Visits{/t}</label>
                                            </li>
                                            <li>
                                                <input type="radio" id="fecha" name="type_medida" value="DATE"
                                                    {if $advertisement->type_medida == 'DATE'}checked="checked"{/if} onClick="permanencia(this);" />
                                                <label for="type_medida">{t}By date{/t}</label>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>

                                <tr id="porfecha" style="{if $advertisement->type_medida!='DATE'}display:none{/if};">
                                    <td align="right"></td>
                                    <td>
                                        <label for="starttime">{t}Start time publication:{/t}</label></td>
                                        <input type="text" id="starttime" name="starttime" title="{t}Start time publication{/t}"
                                            value="{if $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />


                                        <label for="endtime">{t}End time publication:{/t}</label>
                                        <input type="text" id="endtime" name="endtime" size="16" title="{t}End time publication{/t}"
                                            value="{if $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                                    </td>
                                </tr>


                                <tr id="porClic" style="{if $advertisement->type_medida!='CLIC'}display:none{/if};">
                                    <td>
                                        <label for="title">{t}Click number:{/t}</label>
                                    </td>
                                    <td>
                                        <input type="text" id="num_clic" name="num_clic" title={t}"Number of clicks"{/t}
                                            value="{$advertisement->num_clic}" />
                                        {if $smarty.request.action eq "read"}
                                            {if $advertisement->type_medida == 'CLIC'}
                                                Actuales: {$advertisement->num_clic_count}
                                            {/if}
                                            <input type="hidden" id="num_clic_count" name="num_clic_count" title={t}"Number of clics"{/t}
                                                value="{$advertisement->num_clic_count}" />
                                        {/if}
                                    </td>
                                </tr>
                                <tr id="porview" style="{if $advertisement->type_medida!='VIEW'}display:none{/if};">
                                    <td>
                                        <label for="title">{t}Views number{/t}</label>
                                    </td>
                                    <td>
                                        <input type="text" id="num_view" name="num_view" title="{t}Views number{/t}"
                                            value="{$advertisement->num_view}" />
                                        {if $smarty.request.action eq "read"}
                                            {if $advertisement->type_medida == 'VIEW'}
                                                {t}Currents:{/t} {$advertisement->views}
                                            {/if}
                                        {/if}
                                    </td>
                                </tr>

                                <tr id="timeout_container" style="{if $advertisement->type_advertisement!=50}display:none{/if};">
                                    <td>&nbsp;</td>
                                    <td>

                                        <label for="timeout">{t}Timer{/t}</label>

                                        <input type="text" id="timeout" name="timeout" size="2" title={t}"Seconds before disappear"{/t}
                                            value="{$advertisement->timeout|default:"4"}" style="text-align: right;" />
                                        {t}seconds.{/t} <sub>( -1 {t}don't disappear{/t} )</sub>

                                    </td>
                                </tr>

                            </table>
                        </div>

                        {if $smarty.request.action eq "read"}
                            <input type="hidden" id="num_clic_count" name="num_clic_count" title="{t}Click number{/t}"
                                value="{$advertisement->num_clic_count}" />
                        {/if}

                    </td>
                    {* end: PANEL PROPIEDADES *}

                </tr>

                <tr>
                    <td align="right">
                        <div id="div_url1" style="display:{if $advertisement->with_script==1}none{/if};">
                            <label for="title">{t}URL:{/t}</label>
                        </div>
                    </td>
                    <td >
                        <div id="div_url2" style="display:{if $advertisement->with_script==1}none{/if};">
                            <input type="text" id="url" name="url" class="validate-url" title="{t}Web advertisement direction{/t}"
                                value="{$advertisement->url|default:"http://"}" />
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="right">
                        <label for="category">{t}Section{/t}</label>
                    </td>
                    <td>
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
                    <td align="right">
                        <label for="with_script">{t}Advertisement with JavaScript{/t}</label>
                    </td>
                    <td>
                        <input type="checkbox" id="with_script" name="with_script" value="1"
                            {if $advertisement->with_script == 1}checked="checked"{/if} onClick="with_without_script(this);" />
                    </td>
                </tr>

                <tr>
                    <td align="right">
                        &nbsp;
                    </td>
                    <td>
                        <div id="div_script" style="display:{if $advertisement->with_script!=1}none{/if};">
                            <textarea name="script" id="script" class="validate-script" title="{t}Advertisement script{/t}" style="width:80%; height:100px">{$advertisement->script|default:'&lt;script type="text/javascript"&gt;/* {t}Place here your JavaScript code{/t} */&lt;/script&gt;'}</textarea>
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
                {include file="advertisement/partials/advertisement_images.tpl"}

                <tr>
                    <td colspan="3">
                        <label for="title">{t}Advertisement type{/t} </label>

                        <ul id="tabs">
                            <li><a href="#publi-portada">{t}Frontpage{/t}</a></li>
                            <li><a href="#publi-interior">{t}Inner article{/t}</a></li>
                            <li><a href="#publi-video">{t}Frontpage Video{/t}</a></li>
                            <li><a href="#publi-video-interior">{t}Video Inner{/t}</a></li>
                            <li><a href="#publi-opinion">{t}Frontpage Opinion{/t}</a></li>
                            <li><a href="#publi-opinion-interior">{t}Opinion Inner{/t}</a></li>
                            <li><a href="#publi-gallery">{t}Gallery{/t}</a></li>
                        </ul>

                        <div id="publi-portada" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions.tpl"}
                        </div>

                        <div id="publi-interior" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_interior.tpl"}
                        </div>

                        <div id="publi-opinion" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_opinion.tpl"}
                        </div>

                        <div id="publi-opinion-interior" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_opinion_interior.tpl"}
                        </div>
                        <div id="publi-video" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_video.tpl"}
                        </div>

                        <div id="publi-video-interior" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_video_interior.tpl"}
                        </div>
                        <div id="publi-gallery" class="panel-ads">
                            {include file="advertisement/partials/advertisement_positions_gallery.tpl"}
                        </div>

                    </td>
                </tr>
                <tfoot>
                    <tr class="pagination">
                        <td colspan=3></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>



        <input type="hidden" name="filter[type_advertisement]" value="{$smarty.request.filter.type_advertisement}" />
        <input type="hidden" name="filter[available]" value="{$smarty.request.filter.available}" />
        <input type="hidden" name="filter[type]" value="{$smarty.request.filter.type}" />

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />
        </div><!--fin content-wrapper-->
    </form>
</div>
{/block}

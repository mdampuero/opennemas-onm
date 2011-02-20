<br />
<style type="text/css">
    table.adminlist img {
        height:auto;
    }
</style>

<table class="adminheading">
    <tr>
        <td align="right"></td>
    </tr>
</table>
<table class="adminlist" border='0'>
    <tr>
        <td align='left'>
            <img src="{$MEDIA_IMG_URL}{$photo1->path_file}{$photo1->name}" id="change1" border="0"
                style="padding-left:20px;max-width:340px;max-height:380px;" ]
                onunload="GUnload()" />
        </td>
        <td align='left'>

            <p><strong>{t}File:{/t}</strong> {$photo1->name}</p>

            <p><strong>{t}Resolution:{/t}</strong> {$photo1->width} x {$photo1->height} (px)</p>

            <p><strong>{t}Size:{/t}</strong> {$photo1->size} Kb<br></p>

            <p>
                <label for="author_name[{$photo1->id}]">Autor:</label>
                <input type="text" id="author_name[{$photo1->id}]" name="author_name[{$photo1->id}]"
                value='{$photo1->author_name|clearslash|escape:'html'}' size="15"  title="{t}Author{/t}" />
            </p>

            <p>
                <label>{t}Color:{/t}</label>
                <select name="color[{$photo1->id}]" id="color[{$photo1->id}]" />
                    <option value="{t}B/N{/t}" {if $photo1->color eq 'BN'} selected {/if}>{t}B/W{/t}</option>
                    <option value="{t}color{/t}" {if $photo1->color eq 'color'} selected {/if}>{t}Color{/t}</option>
                </select>
            </p>

            <p>
                <label>{t}Date:{/t}</label>
                <input type="text" size="18" id="fecha[{$photo1->id}]" name="fecha[{$photo1->id}]"
                    value="{$photo1->date|date_format:"%Y-%m-%d %H:%M:%S"}"  title="{t}Date:{/t}" />
            </p>

            <p>
                <label for="title">{t}Keywords:{/t}</label>
                <textarea id="metadata[{$photo1->id}]" name="metadata[{$photo1->id}]"
                    title="Metadatos" cols="20" rows="2"  style="width:99%" >{$photo1->metadata|strip}</textarea>
            </p>
            <p>
                <label>{t}Description:{/t}</label> <br /><textarea id="description[{$photo1->id}]" name="description[{$photo1->id}]"
                    title="descripcion" cols="20" rows="2" style="width:99%">{$photo1->description|clearslash|escape:'html'}</textarea>
            </p>

            <input type="hidden" name="resolution[{$photo1->id}]" value="{$photo1->resolution}" />
            <input type="hidden" name="title" value="{$photo1->name}" />

         </td>
         <td>

            <div style="padding:40px;">
                <script src="http://maps.google.com/maps?file=api&v=2&sensor=true&key={$smarty.const.GOOGLE_MAPS_API_KEY}" type="text/javascript"></script>

                <p>
                  <input type="text" value="{$photo1->address}"  id="address[{$photo1->id}]" name="address[{$photo1->id}]" size="20">
                  <input type="button" value="IR" onClick="showAddress(); return false;" />
                </p>

                <div id="map_canvas[{$photo1->id}]" style="width:200px; height:200px"></div>

                <script defer="defer" type="text/javascript" charset="utf-8">
                    var map = new GMap2(document.getElementById("map_canvas[{$photo1->id}]"));
                    map.setCenter(new GLatLng(42.339806,-7.866068), 13);
                    function initialize() {

                    }

                    function showAddress() {
                         var address = document.getElementById('address[{$photo1->id}]').value;

                         if(address){
                             var geocoder = new GClientGeocoder();
                             if (geocoder) {
                                   geocoder.getLatLng(address, function(point) {
                                   if (!point) {
                                     alert("{t}We can't geolocalize that direction{/t}" + address);
                                   } else {
                                     map.setCenter(point, 13);
                                     var marker = new GMarker(point);
                                     map.addOverlay(marker);
                                     marker.openInfoWindowHtml(address);
                                   }
                                 });
                            }
                        }
                     }
                </script>
            </div>

         </td>
    </tr>
    <tr>
        <td align='right' colspan="2">
            {if $display eq 'none'}<a href="#" onclick="new Effect.toggle($('div_datos_{$photo1->id}'),'blind')">
            <strong>{t}+INFO:{/t} </strong></a>
            {/if}

            <div id="div_datos_{$photo1->id}">

                {if $photo1->myiptc}
                    <div id="iptc" >
                      <table>
                          <tr>
                            <th colspan="2"><h3>{t}IPTC Data:{/t}</h3></th>
                          </tr>
                          {foreach item="val" key="caption" from=$photo1->myiptc}
                              {if $val}
                                     <tr>
                                        <td><strong>{$caption}</strong> </td>
                                        <td style="padding: 2px;">{$val}</td>
                                     </tr>
                              {/if}
                          {/foreach}
                      </table>
                      <br />
                    </div>
                {else}
                    <div>
                        <strong>{t}No available IPTC data.{/t}</strong>
                    </div>
                {/if}

                <br>

                {if is_null($photo1->exif) neq true}
                    <div id="exif">
                        <table>
                            <tr>
                                <th colspan="2"><h3>{t}EXIF Data:{/t}</h3></th>
                            </tr>
                            {foreach item="value" key="name" from=$photo1->exif}
                                <tr>
                                    <td colspan="2"><strong>{$name} </strong> </td>
                                </tr>
                                {foreach item="dato" key="d" from=$value}
                                <tr>
                                    <td><strong>{$d}</strong></td>
                                    <td style="padding: 2px;">{$dato}</td>
                                </tr>
                                {/foreach}
                            {/foreach}
                        </table>
                    </div>
                {else}
                    <div>
                        <strong>{t}No available EXIF data.{/t}</strong>
                    </div>
                {/if}
            </div>
        </td>
    </tr>
    <tfoot>
        <tr class="pagination">
            <td  colspan=3></td>
        </tr>
    </tfoot>
</table>


<script defer="defer" type="text/javascript" language="javascript">
    if($('fecha[{$photo1->id}]')) {
        new Control.DatePicker($('fecha[{$photo1->id}]'), {
            icon: './themes/default/images/template_manager/update16x16.png',
            locale: 'es_ES',
            timePicker: true,
            timePickerAdjacent: true,
            dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
        });
    }
</script>

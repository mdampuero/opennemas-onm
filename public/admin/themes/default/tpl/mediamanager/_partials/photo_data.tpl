<table class="adminheading">
    <tr>
        <td><strong>{t}Photo information{/t}</strong></td>
    </tr>
</table>
<table class="adminform photoform" border='0'>
    <tr>
        <td>
            <table style="width:100%">
                <tr>
                    <td style="vertical-align:top; padding:20px 5px;">
                        <div class="image-preview">
                            <img src="{$MEDIA_IMG_URL}{$photo1->path_file}{$photo1->name}" id="change1" border="0"
                                onunload="GUnload()" />
                        </div>
                    </td>
                    <td style="padding:20px 5px; width:380px;">
                        <div class="photo-static-info">
                            <p><label>{t}File:{/t}</label> {$photo1->name}</p>
                            <p><label>{t}Resolution:{/t}</label> {$photo1->width} x {$photo1->height} (px)</p>
                            <p><label>{t}Size:{/t}</label> {$photo1->size} Kb<br></p>
                        </div>

                        <p>
                            <div style="display:inline-block;">
                                <label for="author_name[{$photo1->id}]">Autor:</label><br>

                                <input type="text" id="author_name[{$photo1->id}]" name="author_name[{$photo1->id}]"
                                value='{$photo1->author_name|clearslash|escape:'html'}' size="23"  title="{t}Author{/t}" />
                            </div>
                            <div style="display:inline-block">
                                <label for="fecha[{$photo1->id}]">{t}Date:{/t}</label><br>

                                <input type="text" size="22" id="fecha[{$photo1->id}]" name="fecha[{$photo1->id}]"
                                    value="{$photo1->date|date_format:"%Y-%m-%d %H:%M:%S"}"  title="{t}Date:{/t}" />
                            </div>
                        </p>


                        <p>
                            <label>{t}Color:{/t}</label>
                            <select name="color[{$photo1->id}]" id="color[{$photo1->id}]" />
                                <option value="{t}B/W{/t}" {if $photo1->color eq 'BN'} selected {/if}>{t}B/W{/t}</option>
                                <option value="{t}Color{/t}" {if $photo1->color eq 'color'} selected {/if}>{t}Color{/t}</option>
                            </select>
                        </p>

                        <p>
                            <label for="title">{t}Keywords:{/t}</label>
                            <textarea id="metadata[{$photo1->id}]" name="metadata[{$photo1->id}]"
                                title="Metadatos"  rows="2"  style="width:99%" >{$photo1->metadata|strip}</textarea>
                        </p>
                        <p>
                            <label>{t}Description:{/t}</label> <br /><textarea id="description[{$photo1->id}]" name="description[{$photo1->id}]"
                                title="descripcion" rows="2" style="width:99%">{$photo1->description|clearslash|escape:'html'}</textarea>
                        </p>

                        <input type="hidden" name="resolution[{$photo1->id}]" value="{$photo1->resolution}" />
                        <input type="hidden" name="title" value="{$photo1->name}" />

                    </td>
                    <td style="width:300px;vertical-align:top; padding:20px;">
                        <div class="help-block ">
                            <div class="title"><h4>{t}HELP{/t}</h4></div>
                            <div class="content">{t escape=off}Complete all the photo information for helping OpenNeMas to make better search results{/t}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="width:100%">
                <tr>
                    <td style="vertical-align:top; padding:10px">
                            {if $photo1->myiptc}
                                <div id="iptc" class="photo-static-info">
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
                                <div id="iptc" class="photo-static-info">
                                    <strong>{t}No available IPTC data.{/t}</strong>
                                </div>
                            {/if}
                        </div>
                    </td>
                    <td style="vertical-align:top; padding:10px">
                        {if is_null($photo1->exif) neq true}
                            <div id="exif" class="photo-static-info">
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
                            <div id="exif" class="photo-static-info">
                                <strong>{t}No available EXIF data.{/t}</strong>
                            </div>
                        {/if}
                    </td>
                    <td style="vertical-align:top; padding:10px; width:40%">
                        <label for="address[{$photo1->id}]">Geolocalization</label>
                        <script src="http://maps.google.com/maps?file=api&v=2&sensor=true&key={setting google_maps_api_key}" type="text/javascript"></script>

                        <p>
                          <input type="text" value="{$photo1->address}"  id="address[{$photo1->id}]" name="address[{$photo1->id}]" size="20">
                          <input type="button" value="IR" onClick="showAddress(); return false;" />
                        </p>

                        <div id="map_canvas[{$photo1->id}]" style="width:100%; height:200px"></div>

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
                        <br>

                        <div class="help-block ">
                            <div class="title"><h4>{t}Geolocalization{/t}</h4></div>
                            <div class="content">{t escape=off}Help OpenNeMas to get all the photos geolocalized. In the future you will enjoy geolocalized search results.{/t}</div>
                        </div>
                    </td>
                </tr>
            </table>
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

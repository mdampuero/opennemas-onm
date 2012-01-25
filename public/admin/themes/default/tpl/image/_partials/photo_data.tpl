<table class="listing-table nofill withborder photoform" border='0'>
    <thead>
        <tr>
            <th colspan=3>{t 1=$photo->name}Photo information "%1"{/t}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="vertical-align:top">
                <div class="image-preview">
                {if preg_match('/^swf$/i', $photo->type_img)}
                    <object>
                        <param name="wmode" value="transparent" value="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}" />
                        <embed wmode="transparent" src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"></embed>
                    </object>
                {else}
                    <img src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}" id="change1" border="0" onunload="GUnload()" />
                {/if}
                </div>
            </td>
            <td style="padding:10px 5px 0 10px; width:380px;">
                <div class="photo-static-info"> 
                    <p><label>{t}Name:{/t}</label> {$photo->title}</p>
                    <p><label>{t}File:{/t}</label> {$photo->name}</p>
                    <p><label>{t}Resolution:{/t}</label> {$photo->width} x {$photo->height} (px)</p>
                    <p><label>{t}Size:{/t}</label> {$photo->size} Kb<br></p>
                </div>
                <p>
                    <label>{t}Description:{/t}</label> <br /><textarea class="required" id="description[{$photo->id}]" name="description[{$photo->id}]"
                        title="descripcion" rows="2" style="width:96%"
                        onBlur="javascript:get_metadata_imagen(this.value,'{$photo->id}');">{$photo->description|clearslash|escape:'html'}</textarea>
                </p>
                <p>
                    <label for="title">{t}Keywords:{/t}</label>
                    <textarea id="metadata[{$photo->id}]" name="metadata[{$photo->id}]"
                        title="Metadatos"  rows="2"  style="width:96%" >{$photo->metadata|strip}</textarea>
                </p>

                <p>
                    <div style="display:inline-block; width:45%; margin-right:20px;">
                        <label for="author_name[{$photo->id}]">Autor:</label><br>

                        <input type="text" id="author_name[{$photo->id}]" name="author_name[{$photo->id}]"
                            value='{$photo->author_name|clearslash|escape:'html'}' />
                    </div>
                    <div style="display:inline-block; width:48%">
                        <label for="date[{$photo->id}]">{t}Date:{/t}</label><br>

                        <input type="text" id="date[{$photo->id}]" name="date[{$photo->id}]"
                            value="{$photo->date|date_format:"%Y-%m-%d %H:%M:%S"}" />
                    </div>
                </p>


                <p>
                    <label>{t}Color:{/t}</label>
                    <select name="color[{$photo->id}]" id="color[{$photo->id}]" />
                        <option value="{t}Color{/t}" {if $photo->color eq 'color'} selected {/if}>{t}Color{/t}</option>
                        <option value="{t}B/W{/t}" {if $photo->color eq 'BN'} selected {/if}>{t}B/W{/t}</option>
                    </select>
                </p>

                <input type="hidden" name="resolution[{$photo->id}]" value="{$photo->resolution}" />
                <input type="hidden" name="title[{$photo->id}]" value="{$photo->name}" />
                <input type="hidden" name="category[{$photo->id}]" value="{$photo->category}" />

            </td>
            <td style="width:300px;vertical-align:top; padding:10px 10px 0 10px;">
                <div class="photo-geolocation">
                {if {setting name=google_maps_api_key} != ''}
                    <label for="address[{$photo->id}]">Geolocalization</label>

                    <p style="text-align:center">
                      <input type="text" value="{$photo->address}"  id="address[{$photo->id}]" name="address[{$photo->id}]" size="30">
                      <input class="onm-button blue" type="button" value="IR" onClick="showAddress(); return false;" />
                    </p>

                    <div class="photo-geolocation-canvas" id="map_canvas[{$photo->id}]" style="height:200px"></div>
                    <script src="http://maps.google.com/maps?file=api&sensor=true&key={setting name=google_maps_api_key}" type="text/javascript"></script>
                    <script defer="defer" type="text/javascript" charset="utf-8">
                        var map = new GMap2(document.getElementById("map_canvas[{$photo->id}]"));
                        map.setCenter(new GLatLng(42.339806,-7.866068), 13);
                        function initialize() {

                        }

                        function showAddress() {
                             var address = document.getElementById('address[{$photo->id}]').value;

                             if(address){
                                 var geocoder = new GClientGeocoder();
                                 if (geocoder) {
                                       geocoder.getLatLng(address, function(point) {
                                       if (!point) {
                                         alert("{t}We can't geolocalize that direction{/t}" + address);
                                       } else {
                                         map.setCenter(point, 7);
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
                {else}
                    <input type="hidden" value="{$photo->address}"  id="address[{$photo->id}]" name="address[{$photo->id}]" size="30">
                    <div class="help-block ">
                        <div class="title"><h4>{t}Set your Google maps key{/t}</h4></div>
                        <div class="content">{t escape=off}Help OpenNeMas to get all the photos geolocalized. For this you have to configure your Google Maps API key from
                        the <a href="/admin/controllers/system_settings/system_settings.php?action=list#external" title="Go to the system settings dialog">system settings dialog</a>{/t}</div>
                    </div>
                {/if}
                <div class="help-block ">
                    <div class="title"><h4>{t}Help{/t}</h4></div>
                    <div class="content">{t escape=off}Complete all the photo information for helping OpenNeMas to make better search results{/t}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan=3>
                <table style="width:100%">
                    <tr>
                        <td style="vertical-align:top; padding:10px;">
                            <h3>{t}EXIF Data:{/t}</h3>
                            {if is_null($photo->exif) neq true}
                            <div id="exif" class="photo-static-info" style="max-height:300px; overflow-y:scroll">
                                <table>
                                    <tbody>
                                    {foreach item="value" key="name" from=$photo->exif}
                                        {foreach item="dato" key="d" from=$value}
                                        <tr>
                                            <td><strong>{$d}</strong></td>
                                            <td>{$dato}</td>
                                        </tr>
                                        {/foreach}
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                            {else}
                            <div id="exif" class="photo-static-info">
                                <strong>{t}No available EXIF data.{/t}</strong>
                            </div>
                            {/if}
                        </td>
                        <td style="vertical-align:top; padding:10px">
                            <h3>{t}IPTC Data:{/t}</h3>
                            {if isset($photo->myiptc)}
                            <div id="iptc" class="photo-static-info"  style="max-height:300px; overflow-y:scroll">
                                <table>
                                    <tbody>
                                    {foreach item="val" key="caption" from=$photo->myiptc}
                                        {if $val}
                                        <tr>
                                            <td><strong>{$caption}</strong> </td>
                                            <td style="padding: 2px;">{$val}</td>
                                        </tr>
                                        {/if}
                                    {/foreach}
                                    </tbody>
                                </table>
                              <br />
                            </div>
                            {else}
                            <div id="iptc" class="photo-static-info">
                                <strong>{t}No available IPTC data.{/t}</strong>
                            </div>
                            {/if}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="pagination">
            <td  colspan=3></td>
        </tr>
    </tfoot>
</table>


<script defer="defer" type="text/javascript" language="javascript">
    if($('date[{$photo->id}]')) {
        new Control.DatePicker($('date[{$photo->id}]'), {
            icon: './../../themes/default/images/template_manager/update16x16.png',
            locale: 'es_ES',
            timePicker: true,
            timePickerAdjacent: true,
            dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
        });
    }
</script>

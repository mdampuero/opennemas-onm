<div id="photo-{$photo->id}" class="photo-edit tabs">
    <ul>
        <li><a href="#basic-{$photo->id}" title="">{t}Basic information{/t}</a></li>
        <li><a href="#geolocation-{$photo->id}" title="">{t}Geolocation{/t}</a></li>
        <li><a href="#additional-info-{$photo->id}" title="">{t escape=off}EXIF &amp; IPTC{/t}</a></li>
    </ul><!-- / -->
    <div id="basic-{$photo->id}">
        <div class="image-preview">
            {if preg_match('/^swf$/i', $photo->type_img)}
                <object width="" height="">
                    <param name="wmode" value="transparent"
                           value="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"/>
                    <embed wmode="transparent"
                           src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"></embed>
                </object>
                <!-- <img style="width:16px;height:16px;border:none;"  src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" /> -->
            {else}
                <img src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}" onunload="GUnload()" />
            {/if}
        </div>

        <div class="photo-basic-information">
            <div class="photo-static-info">
                <div><label>{t}Original filename:{/t}</label> {$photo->title}</div>
                <div><label>{t}File:{/t}</label> {$photo->name}</div>
                <div><label>{t}Resolution:{/t}</label> {$photo->width} x {$photo->height} (px)</div>
                <div><label>{t}Size:{/t}</label> {$photo->size} Kb</div>
                <div>
                    <label>{t}URL:{/t}</label>
                    <a href="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"
                       title="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"
                       target="_blank">
                       {$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}
                    </a>
                </div>
            </div>
            <div>
                <label>{t}Description:{/t}</label> <br />
                <textarea class="required" id="description[{$photo->id}]" name="description[{$photo->id}]"
                    title="descripcion" rows="2" style="width:96%"
                    onBlur="javascript:get_metadata_imagen(this.value,'{$photo->id}');">{$photo->description|clearslash|escape:'html'}</textarea>
                <label for="title">{t}Keywords:{/t}</label>
                <textarea id="metadata[{$photo->id}]" name="metadata[{$photo->id}]"
                    title="Metadatos"  rows="2"  style="width:96%" >{$photo->metadata|strip}</textarea>

                <div style="display:inline-block; width:35%; margin-right:20px;">
                    <label for="author_name[{$photo->id}]">Autor:</label><br>

                    <input type="text" id="author_name[{$photo->id}]" name="author_name[{$photo->id}]"
                        value='{$photo->author_name|clearslash|escape:'html'}' />
                </div>
                <div style="display:inline-block; width:35%">
                    <label for="date-{$photo->id}">{t}Date:{/t}</label><br>

                    <input type="text" id="date-{$photo->id}" name="date[{$photo->id}]"
                        value="{$photo->date|date_format:"%Y-%m-%d %H:%M:%S"}" />
                </div>

                <div style="display:inline-block; width:25%">
                    <label>{t}Color:{/t}</label><br/>
                    <select name="color[{$photo->id}]" id="color[{$photo->id}]" style="width:80px;height:28px;margin-left:20px;"/>
                        <option value="{t}color{/t}" {if $photo->color eq 'color'} selected {/if}>{t}Color{/t}</option>
                        <option value="{t}bw{/t}" {if $photo->color eq 'bw'} selected {/if}>{t}B/W{/t}</option>
                    </select>
                </div><!-- / -->
            </div>
        </div><!-- /basic -->
    </div>
    <div id="geolocation-{$photo->id}">
        {if {setting name=google_maps_api_key} != ''}
            <label for="address[{$photo->id}]">Geolocalization</label>

            <input type="text" value="{$photo->address}"  id="address[{$photo->id}]" name="address[{$photo->id}]" size="30">
            <input class="onm-button blue" type="button" value="IR" onClick="showAddress(); return false;" />

            <div class="photo-geolocation-canvas" id="map_canvas[{$photo->id}]" style="height:200px"></div>

            <div class="onm-help-block ">
                <div class="title"><h4>{t}Geolocalization{/t}</h4></div>
                <div class="content">{t escape=off}Help OpenNeMas to get all the photos geolocalized. In the future you will enjoy geolocalized search results.{/t}</div>
            </div>
        {else}
            <input type="hidden" value="{$photo->address}"  id="address[{$photo->id}]" name="address[{$photo->id}]" size="30">
            <div class="onm-help-block ">
                <div class="title"><h4>{t}Set your Google maps key{/t}</h4></div>
                <div class="content">{t escape=off}Help OpenNeMas to get all the photos geolocalized. For this you have to configure your Google Maps API key from
                the <a href="{url name=admin_system_settings}#external" title="Go to the system settings dialog">system settings dialog</a>{/t}</div>
            </div>
        {/if}
    </div><!-- /geolocation -->

    <div id="additional-info-{$photo->id}">
        <div style="display:inline-block; width:49%; margin-right:10px">
            <h3>{t}EXIF Data:{/t}</h3>
            {if is_null($photo->exif) neq true}
            <div id="exif" class="photo-static-info" style="max-height:400px; overflow-y:scroll">
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
        </div>

        <div style="display:inline-block; width:49%;">
            <h3>{t}IPTC Data:{/t}</h3>
            {if isset($photo->myiptc)}
            <div id="iptc" class="photo-static-info"  style="max-height:400px; overflow-y:scroll">
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
        </div><!-- / -->
    </div><!-- /additional-info -->

    <input type="hidden" name="resolution[{$photo->id}]" value="{$photo->resolution}" />
    <input type="hidden" name="title[{$photo->id}]" value="{$photo->name}" />
    <input type="hidden" name="category[{$photo->id}]" value="{$photo->category}" />

</div><!-- /photo-{$rnf} -->
<hr>
<script defer="defer">
    jQuery(document).ready(function($){
        $('#photo-{$photo->id}').tabs();
            jQuery('#date-{$photo->id}').datetimepicker({
            hourGrid: 4,
            showAnim: "fadeIn",
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            minuteGrid: 10
        });

        jQuery('#ui-datepicker-div').css('clip', 'auto');
    });
    {if {setting name=google_maps_api_key} != ''}
    var map = new GMap2(document.getElementById("map_canvas[{$photo->id}]"));
    map.setCenter(new GLatLng(42.339806,-7.866068), 13);
    function initialize() { }

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
    {/if}
</script>

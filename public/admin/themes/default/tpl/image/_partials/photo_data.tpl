<div id="photo-{$photo->id}" class="form-vertical photo-edit tabs">
    <ul>
        <li><a href="#basic-{$photo->id}" title="">{t}Basic information{/t}</a></li>
        <li><a href="#geolocation-{$photo->id}" title="">{t}Geolocation{/t}</a></li>
        <li><a href="#additional-info-{$photo->id}" title="">{t escape=off}EXIF &amp; IPTC{/t}</a></li>
    </ul><!-- / -->
    <div id="basic-{$photo->id}" class="clearfix">
        <div style="width:330px; display:inline-block;" class="pull-left clearfix">
            <div class="thumbnail">
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

            <br>

            <div class="well well-small">
                <div><strong>{t}Original filename:{/t}</strong> {$photo->title}</div>
                <div><strong>{t}File:{/t}</strong> <a href="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"
                       title="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"
                       target="_blank">{$photo->name}</a></div>
                <div><strong>{t}Resolution:{/t}</strong> {$photo->width} x {$photo->height} (px)</div>
                <div><strong>{t}Size:{/t}</strong> {$photo->size} Kb</div>
            </div>
        </div>

        <div class="photo-basic-information">

            <div>
                <div class="control-group">
                    <label for="description-{$photo->id}" class="control-label">{t}Description{/t}</label>
                    <div class="controls">
                        <textarea required="required" id="description-{$photo->id}" name="description[{$photo->id}]"  class="input-xxlarge"
                            rows="2">{$photo->description|clearslash|escape:'html'}</textarea>
                    </div>
                    <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('#description-{$photo->id}').on('change', function(e, ui) {
                            fill_tags(jQuery('#description-{$photo->id}').val(),'#metadata-{$photo->id}', '{url name=admin_utils_calculate_tags}');
                        });
                    });
                    </script>
                </div>
                <div class="control-group">
                    <label for="metadata-{$photo->id}" class="control-label">{t}Keywords{/t}</label>
                    <div class="controls">
                        <textarea id="metadata-{$photo->id}" name="metadata[{$photo->id}]" rows="2"  class="input-xxlarge" required="required">{$photo->metadata|strip}</textarea>
                        <div class="help-block">{t}Used for searches and automated suggestions.{/t}</div>
                    </div>
                </div>

                <div class="control-group">
                    <label for="author_name[{$photo->id}]" class="control-label">{t}Copyright{/t}</label>
                    <div class="controls">
                        <input type="text" id="author_name[{$photo->id}]" name="author_name[{$photo->id}]"
                            value='{$photo->author_name|clearslash|escape:'html'}' required="required"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="date-{$photo->id}" class="control-label">{t}Date{/t}</label>
                    <div class="controls">
                        <input type="text" id="date-{$photo->id}" name="date[{$photo->id}]"
                            value='{$photo->date|date_format:"%Y-%m-%d %H:%M:%S"}'/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="color[{$photo->id}]" class="control-label">{t}Color{/t}</label>
                    <div class="controls">
                        <select name="color[{$photo->id}]" id="color[{$photo->id}]" style="width:80px;height:28px;margin-left:20px;"/>
                            <option value="{t}color{/t}" {if $photo->color eq 'color'} selected {/if}>{t}Color{/t}</option>
                            <option value="{t}bw{/t}" {if $photo->color eq 'bw'} selected {/if}>{t}B/W{/t}</option>
                        </select>
                    </div>
                </div>
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
            <h5>{t}EXIF Data:{/t}</h5>
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
            <h5>{t}IPTC Data:{/t}</h5>
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

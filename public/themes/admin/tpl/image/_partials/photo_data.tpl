<div id="photo-{$photo->id}" class="form-vertical photo-edit tabs">
    <ul>
        <li><a href="#basic-{$photo->id}" title="">{t}Basic information{/t}</a></li>
        <li><a href="#geolocation-{$photo->id}" title="" class="has-map">{t}Geolocation{/t}</a></li>
    </ul><!-- / -->
    <div id="basic-{$photo->id}" class="clearfix">
        <div style="width:330px; display:inline-block;" class="pull-left clearfix">
            <div class="thumbnail">
                {if preg_match('/^swf$/i', $photo->type_img)}
                    <object width="" height="">
                        <param name="wmode" value="window"
                               value="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"/>
                        <embed wmode="window"
                               src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"></embed>
                    </object>
                    <!-- <img style="width:16px;height:16px;border:none;"  src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" /> -->
                {else}
                    {dynamic_image src="{$photo->path_file}{$photo->name}" transform="thumbnail,330,330" onunload="GUnload()"}
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
                            rows="2">{$photo->description|clearslash}</textarea>
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
                            value='{$photo->author_name|clearslash}'/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="date-{$photo->id}" class="control-label">{t}Date{/t}</label>
                    <div class="controls">
                        <input type="text" id="date-{$photo->id}" name="date[{$photo->id}]"
                            value='{$photo->date|date_format:"%Y-%m-%d %H:%M:%S"}'/>
                    </div>
                </div>
            </div>
            <div class="iptc-exif">
                <h5 class="toggler" title=""><i class="icon-plus"></i> {t escape=off}View advanced data{/t}</h5>
                <div class="info">
                    {if is_null($photo->exif) neq true}
                    <h6>{t}EXIF Data{/t}</h6>
                    <div id="exif" class="photo-static-info">
                        {foreach $photo->exif as $name => $value}
                            {foreach $value as $d => $dato}
                                <p>
                                    <strong>{$d}</strong> : {$dato}
                                </p>
                            {/foreach}
                        {/foreach}
                    </div>
                    {else}
                    <div id="exif" class="photo-static-info">
                        {t}No available EXIF data.{/t}
                    </div>
                    {/if}

                    {if isset($photo->myiptc)}
                    <h6>{t}IPTC Data{/t}</h6>
                    <div id="iptc" class="photo-static-info">
                        {foreach $photo->myiptc as $name => $value}
                        {if $name}
                            <p>
                                <strong>{$name}</strong> : {$value}
                            </p>
                        {/if}
                        {/foreach}
                      <br />
                    </div>
                    {else}
                    <div id="iptc" class="photo-static-info">
                        {t}No available IPTC data.{/t}
                    </div>
                    {/if}
                </div><!-- /additional-info -->
            </div>
        </div><!-- /basic -->
    </div>
    <div id="geolocation-{$photo->id}" class="has-map">
            <div style="text-align:center">
                <div class="input-append">
                    <input type="text" id="address_search_{$photo->id}" value="{$photo->address}" class="input-xxlarge noentersubmit">
                    <button class="btn" id="geolocate_user_button_{$photo->id}" rel="tooltip" data-original-title="{t}Geolocate photo with my position{/t}"><i class="icon-screenshot"></i></button>
                    <button class="btn" id="geocode_buttom_{$photo->id}"/><i class="icon-search"></i> </button>
                </div>
                <input type="hidden" id="address_{$photo->id}" name="address[{$photo->id}]" value="{$photo->address}">
            </div>

            <div class="map">
                <div id="map_canvas_{$photo->id}"></div>
            </div>
    </div><!-- /geolocation -->

    <input type="hidden" name="resolution[{$photo->id}]" value="{$photo->resolution}" />
    <input type="hidden" name="title[{$photo->id}]" value="{$photo->name}" />
    <input type="hidden" name="category[{$photo->id}]" value="{$photo->category}" />

</div><!-- /photo-{$rnf} -->
<hr>
<script defer="defer">
    jQuery(document).ready(function($){

        var default_location = {
            lat : 42.60548720000001,
            lng:  -8.643296200000009
        }

        $('#photo-{$photo->id}').tabs();
        $('#date-{$photo->id}').datetimepicker({
            hourGrid: 4,
            showAnim: "fadeIn",
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            minuteGrid: 10
        });

        jQuery('#ui-datepicker-div').css('clip', 'auto');

        $("#photo-{$photo->id}").bind("tabsshow", function(event, ui) {
            if ($(ui.panel).is('.has-map')) {
                var pos = map.getCenter();
                map.refresh();
                map.setCenter(pos.Xa, pos.Ya)
            }
        });

        map = new GMaps({
            div: '#map_canvas_{$photo->id}',
            {if is_array($photo->latlong)}
            lat: {$photo->latlong['lat']},
            lng: {$photo->latlong['long']}
            {else}
            lat: default_location.lat,
            lng: default_location.lng
            {/if}
        });

        {if empty($photo->address)}
        geolocate_user();
        {else}
        map.addMarker({
          lat: {$photo->latlong['lat']},
          lng: {$photo->latlong['long']}
        });
        map.setCenter({$photo->latlong['lat']}, {$photo->latlong['long']});
        {/if}
        $('#geocode_buttom_{$photo->id}').on('click', function(e,ui){
            e.preventDefault();
            geolocate_photo()
        });
        $('#geolocate_user_button_{$photo->id}').on('click', function(e,ui){
            e.preventDefault();
            geolocate_user()
        });

        $('#address_search_{$photo->id}').on('blur', function(e,ui){
            e.preventDefault();
            geolocate_photo()
        });
        function geolocate_photo() {
            GMaps.geocode({
                address: $('#address_search_{$photo->id}').val().trim(),
                callback: function(results, status){
                    map.removeMarkers();
                    if (status == 'OK'){
                        var latlng = results[0].geometry.location;
                        map.setCenter(latlng.lat(), latlng.lng());
                        map.addMarker({
                            lat: latlng.lat(),
                            lng: latlng.lng()
                        });
                    }
                    $('#address_{$photo->id}').val(latlng.lat() + ', '+ latlng.lng());
                }
            });
        }
        function geolocate_user() {
            GMaps.geolocate({
                success: function(position) {
                    map.removeMarkers();
                    map.setCenter(position.coords.latitude, position.coords.longitude);
                    map.addMarker({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                },
                error: function(error) {
                    // alert('Geolocation failed: '+error.message);
                },
                not_supported: function() {
                    // alert("Your browser does not support geolocation");
                },
                always: function() {
                    // alert("Geolocated done!");
                }
            });
        }

        $('.iptc-exif .toggler').on('click', function(e, ui) {
            $(this).parent().find('.info').toggle();
        })
    });
</script>
<style>
.iptc-exif .info {
    display:none;
}
.iptc-exif .toggler {
    cursor:pointer;
}
.iptc-exif .icon-plus {
    font-size:.8em;
    vertical-align:middle;
}
.iptc-exif .photo-static-info p {
    margin-bottom:0;
}
</style>
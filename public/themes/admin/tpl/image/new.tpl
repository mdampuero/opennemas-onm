{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .map {
        background: white;
        border-radius: 2px;
    }
    .map > div {
        min-height:400px;
    }
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
    .address_search_input {
        width:435px;
    }
</style>
{/block}

{block name="header-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    {script_tag src="/onm/jquery.datepicker.js"}

    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    {script_tag src="/libs/gmaps.js"}
{/block}

{block name="content"}
<form id="formulario" name="form_upload" action="{url name=admin_image_update}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Editing image{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="IMAGE_UPDATE"}
                <li>
                    <button type="submit" name="action" value="validate">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" >
                        <br />
                        {t}Save{/t}
                    </button>
                </li>
                {/acl}
                <li class="separator"></li>
                <li>
                    {if !isset($smarty.request.stringSearch)}
                        <a href="{url name=admin_images}" class="admin_add" title="{t}Go back{/t}">
                    {else}
                        <a href="{url name=admin_search stringSearch=$smarty.get.stringSearch} photo=on id=0"
                           title="Cancelar">
                    {/if}
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back to listing{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        {foreach from=$photos item=photo name=photo_show}
            {include file="image/_partials/photo_data.tpl" display='inline'}
        {/foreach}
    </div>
</form>


<div class="modal hide fade" id="modal-image-location">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3>{t}Pick a location for the image{/t}</h3>
    </div>
    <div class="modal-body">
        <div id="geolocation" class="has-map">
            <div>
                <div class="input-append">
                    <input type="text" class="address_search_input input-xlarge noentersubmit">
                    <button class="geolocate_user_button btn" rel="tooltip" data-placement="left" data-original-title="{t}Geolocate photo with my position{/t}"><i class="icon-screenshot"></i></button>
                    <button class="btn" class="geocode_button"/><i class="icon-search"></i> </button>
                    <input type="hidden" class="final_address" value="">
                    <input type="hidden" class="target_image_id" value="">
                </div>
            </div>

            <div class="map">
                <div id="map_canvas"></div>
            </div>
    </div><!-- /geolocation -->
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary accept" href="#">{t}Assign location{/t}</a>
    </div>
</div>
{/block}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
    $('[rel="tooltip"]').tooltip({ position: 'left' });

    $('.date').datetimepicker({
        hourGrid: 4,
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
        minuteGrid: 10
    });

    $('#ui-datepicker-div').css('clip', 'auto');

    $('.iptc-exif .toggler').on('click', function(e, ui) {
        $(this).parent().find('.info').toggle();
    });

    var map;

    $('.geocode_button').on('click', function(e,ui){
        e.preventDefault();
        geolocate_photo()
    });

    $('.address_search_input').on('blur', function(e,ui){
        e.preventDefault();
        geolocate_photo()
    });

    $('.geolocate_user_button').on('click', function(e,ui){
        e.preventDefault();
        geolocate_user();
    });

    $("#modal-image-location").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: false
    }).on('shown', function() {

        var orig_address = $('#modal-image-location .final_address').val();
        coordinates = orig_address.split(",");
        coordinates = $.map(coordinates, function(elem) {
            return parseFloat(elem);
        })

        var pos_lat  = coordinates[0];
        var pos_long = coordinates[1];

        var styles = [
            {
                "featureType": "water",
                "stylers": [
                    {
                        "visibility": "on"
                    },
                    {
                        "color": "#acbcc9"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "stylers": [
                    {
                        "color": "#f2e5d4"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#c5c6c6"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e4d7c6"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#fbfaf7"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#c5dac6"
                    }
                ]
            },
            {
                "featureType": "administrative",
                "stylers": [
                    {
                        "visibility": "on"
                    },
                    {
                        "lightness": 33
                    }
                ]
            },
            {
                "featureType": "road"
            },
            {
                "featureType": "poi.park",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "on"
                    },
                    {
                        "lightness": 20
                    }
                ]
            },
            {},
            {
                "featureType": "road",
                "stylers": [
                    {
                        "lightness": 20
                    }
                ]
            }
        ];

        map = new GMaps({
            div: '#map_canvas',
            lat: pos_lat,
            lng: pos_long
        });

        map.addStyle({
            styledMapName:"Styled Map",
            styles: styles,
            mapTypeId: "map_style"
        });

        map.setStyle("map_style");

        if (pos_lat == 0 || pos_long == 0) {
            geolocate_user();
        } else {
            map.addMarker({
              lat: pos_lat,
              lng: pos_long
            });
            map.setCenter(pos_lat, pos_long);
        }
    });

    function geolocate_photo() {
        GMaps.geocode({
            address: $('.address_search_input').val().trim(),
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
                $('.final_address').val(latlng.lat() + ', '+ latlng.lng());
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
                $('.final_address').val(position.coords.latitude + ', '+ position.coords.longitude);
                $('.address_search_input').val(position.coords.latitude + ', '+ position.coords.longitude);
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

    $('button.locate').on('click', function(e, ui) {
        var button = $(this);
        var image_id = $(button).data('image-id');
        var addresss = $(button).closest('.photo-edit').find('.photo_address').val();

        $('.final_address').val(addresss);
        $('.address_search_input').val(addresss);
        $('.target_image_id').val(image_id);
    });

    $('#modal-image-location a.btn.accept').on('click', function(e){
        var location = $('.final_address').val();
        var image_id = $('.target_image_id').val();

        var element = $('#photo-' + image_id).find('.photo_address');
        console.log(element);
        element.val(location);

        jQuery("#modal-image-location").modal('hide');
        e.preventDefault();
    });
});
</script>
{/block}

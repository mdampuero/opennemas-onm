{extends file="base/admin.tpl"}

{block name="content"}
  <form id="formulario" name="form_upload" action="{url name=admin_image_update}" method="POST" ng-controller="ImageCtrl" ng-init="init({json_encode($photos)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-picture-o"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221735-opennemas-c%C3%B3mo-subir-im%C3%A1genes-para-mis-art%C3%ADculos" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
                {t}Images{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
            <li class="quicklinks hidden-xs">
              <h5>{t}Editing image{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_images}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              {acl isAllowed="PHOTO_UPDATE"}
              <li class="quicklinks">
                <button class="btn btn-primary" data-text="{t}Updating{/t}..." type="submit" id="save-button">
                  <span class="fa fa-save"></span>
                  <span class="text">{t}Update{/t}</span>
                </button>
              </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {foreach from=$photos item=photo name=photo_show}
        <div class="grid simple">
          <div class="grid-body">
            {include file="image/_partials/photo_data.tpl" display='inline'}
          </div>
        </div>
      {/foreach}
    </div>
  </form>
  <div class="modal fade" id="modal-image-location">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title">{t}Pick a location for the image{/t}</h4>
        </div>
        <div class="modal-body">
          <div id="geolocation" class="has-map">
            <div class="form-group">
              <div class="input-group">
                <input type="text" class="form-control address_search_input noentersubmit">
                <span class="input-group-btn">
                  <button class="geolocate_user_button btn btn-default" rel="tooltip" data-placement="left" data-original-title="{t}Geolocate photo with my position{/t}">
                    <i class="fa fa-location-arrow"></i>
                  </button>
                  <button class="btn btn-default" class="geocode_button"/>
                    <i class="fa fa-search"></i>
                  </button>
                </span>
              </div>
              <input type="hidden" class="final_address" value="">
              <input type="hidden" class="target_image_id" value="">
            </div>
            <div class="map">
              <div id="map_canvas"></div>
            </div>
           </div><!-- /geolocation -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary accept" href="#" type="button">
            {t}Assign location{/t}
          </a>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="footer-js" append}
  <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
  {javascripts src="@Common/components/gmaps/gmaps.js" output="image"}
    <script type="text/javascript">
      var map, marker;

      jQuery(document).ready(function($) {
        $('[rel="tooltip"]').tooltip({ position: 'left' });

        $('.geocode_button').on('click', function(e,ui){
          e.preventDefault();
          geolocate_photo()
        });

        $('.address_search_input').on({
            blur : function(e,ui){
              e.preventDefault();
              geolocate_photo()
            },
            keyup : function(e,ui){
              if (e.which == 13) {
                e.preventDefault();
                geolocate_photo();
              }
            },
        });

        $('.geolocate_user_button').on('click', function(e,ui){
          e.preventDefault();
          geolocate_user();
        });

        $("#modal-image-location").modal({
          backdrop: false,
          keyboard: true, //Can close on escape
          show: false
        }).on('shown.bs.modal', function() {
          var orig_address = $('#modal-image-location .final_address').val();
          coordinates = orig_address.split(",");
          coordinates = $.map(coordinates, function(elem) {
            return parseFloat(elem);
          });

          if (coordinates.length == 2) {
            var pos_lat  = coordinates[0];
            var pos_long = coordinates[1];
          } else {
            var pos_lat  = 0;
            var pos_long = 0;
          }

            // Styles from http://snazzymaps.com/
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
              map.removeMarkers();
              marker = map.addMarker({
                lat: pos_lat,
                lng: pos_long,
                draggable: true,
                cursor: 'move',
                animation: google.maps.Animation.DROP,
                dragend : function(evt) {
                    $('.final_address').val(evt.latLng.lat() + ', '+ evt.latLng.lng());
                    $('.address_search_input').val(evt.latLng.lat() + ', '+ evt.latLng.lng());
                }
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
                marker = map.addMarker({
                  lat: latlng.lat(),
                  lng: latlng.lng(),
                  draggable: true,
                  cursor: 'move',
                  animation: google.maps.Animation.DROP,
                  dragend : function(evt) {
                    $('.final_address').val(evt.latLng.lat() + ', '+ evt.latLng.lng());
                    $('.address_search_input').val(evt.latLng.lat() + ', '+ evt.latLng.lng());
                  }
                });

                $('.final_address').val(latlng.lat() + ', '+ latlng.lng());
                $('.address_search_input').val(latlng.lat() + ', '+ latlng.lng());
              }
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
                lng: position.coords.longitude,
                draggable: true,
                cursor: 'move',
                animation: google.maps.Animation.DROP,
                dragend : function(evt) {
                  $('.final_address').val(evt.latLng.lat() + ', '+ evt.latLng.lng());
                  $('.address_search_input').val(evt.latLng.lat() + ', '+ evt.latLng.lng());
                }
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

        $('#modal-image-location .btn.accept').on('click', function(e){
          var location = $('.final_address').val();
          var image_id = $('.target_image_id').val();

          var element = $('#photo-' + image_id).find('.photo_address');
          delete map;
          $('#map_canvas').html('&nbsp;');

          element.val(location);

          jQuery("#modal-image-location").modal('hide');
          e.preventDefault();
        });
      });
    </script>
  {/javascripts}
{/block}

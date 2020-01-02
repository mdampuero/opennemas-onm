{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Photos{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="PhotoCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}


{block name="icon"}
  <i class="fa fa-picture-o m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221735-opennemas-c%C3%B3mo-subir-im%C3%A1genes-para-mis-art%C3%ADculos" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_photos_list}">
    {t}Photos{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      {include file="ui/component/content-editor/accordion/tags.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title ng-cloak">
        <i class="fa fa-cog m-r-10"></i>
        {t}Parameters{/t}
      </div>
    </div>
    <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.information = !expanded.information">
      <i class="fa fa-info-circle m-r-10"></i>{t}Information{/t}
      <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.information }"></i>
    </div>
    <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.information }">
      <div class="row">
        <div class="col-sm-6">
          <strong>{t}Resolution{/t}</strong> [% item.width %]px X [% item.height %]px
        </div>
        <div class="col-sm-6">
          <strong>{t}Size{/t}</strong> [% item.size %] KB
        </div>
      </div>
      <div class="form-group">
        <label style="margin-top:10px" for="author_name" class="form-label">{t}Copyright{/t}</label>
        <div class="controls">
          <div class="input-group">
            <input class="form-control" type="text" id="author_name" name="author_name" ng-model="item.author_name"/>
            <span class="input-group-addon add-on">
              <span class="fa fa-copyright"></span>
            </span>
          </div>
        </div>
    </div>
  </div>
</div>
{/block}

{block name="leftColumn"}
 <div class="grid simple">
    <div class="grid-body">
          <div class="thumbnail-wrapper">
            <div class="form-group">
              <div class="thumbnail-placeholder">
                <div class="dynamic-image-placeholder ng-cloak" ng-if="item">
                  <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" ng-if="item" only-image="true">
                  </dynamic-image>
                </div>
              </div>
            </div>
          {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
          {include file="ui/component/content-editor/textarea.tpl"  title="{t}Description{/t}" field="description" rows=20}
        </div>
    </div>
  </div>
{/block}

{block name="footer-js" append}
  <script src="https://maps.google.com/maps/api/js?sensor=true"></script>
  {javascripts src="@Common/components/gmaps/gmaps.js" output="image"}
    <script>
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
          var addresss = $(button).closest('.photo-edit').find('.photo_address').val();

          $('.final_address').val(addresss);
          $('.address_search_input').val(addresss);
        });

        $('#modal-image-location .btn.accept').on('click', function(e){
          var location = $('.final_address').val();

          var element = $('#photo').find('.photo_address');
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

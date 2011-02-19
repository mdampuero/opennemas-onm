<br />
<div id="nifty" style="width:1100px;">
    <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
        <table border='0' width="96%">
            <tr>
                <td align='left'>
                    <img src="{$MEDIA_IMG_URL}{$photo1->path_file}{$photo1->name}" id="change1" border="0" style="padding-left:20px;max-width:440px;max-height:380px;" onload="initialize()" onunload="GUnload()" />
                </td>
                <td align='left'>
                    <b>{t}File:{/t} {$photo1->name}</b> <br />
                    <b>{t}Resolution:{/t}</b> {$photo1->width} x {$photo1->height} (px)
                    <b>{t}Size:{/t}</b> {$photo1->size} Kb<br>
                    <label>Autor:</label><input type="text" id="author_name[{$photo1->id}]" name="author_name[{$photo1->id}]"
                    value='{$photo1->author_name|clearslash|escape:'html'}' size="15"  title="{t}Author{/t}" />
                    <label>{t}Color:{/t}</label><select name="color[{$photo1->id}]" id="color[{$photo1->id}]" />
                                                <option value="{t}B/N{/t}" {if $photo1->color eq 'BN'} selected {/if}>{t}B/N{/t}</option>
                                                <option value="{t}color{/t}" {if $photo1->color eq 'color'} selected {/if}>{t}Color{/t}</option>
                                            </select>
                    <br /><label>{t}Date:{/t}</label><input type="text" size="18" id="fecha[{$photo1->id}]" name="fecha[{$photo1->id}]"
                    value="{$photo1->date|date_format:"%Y-%m-%d %H:%M:%S"}"  title="{t}Date:{/t}" />
                    <br />
                    <label for="title">{t}Keywords:{/t}</label> <br /><textarea id="metadata[{$photo1->id}]" name="metadata[{$photo1->id}]"
                    title="Metadatos" cols="20" rows="2"  style="width:99%" >{$photo1->metadata|strip}</textarea>
                     <br />
                    <label>{t}Description:{/t}</label> <br /><textarea id="description[{$photo1->id}]" name="description[{$photo1->id}]"
                    title="descripcion" cols="20" rows="2" style="width:99%">{$photo1->description|clearslash|escape:'html'}</textarea>
                    <input type="hidden" name="resolution[{$photo1->id}]" value="{$photo1->resolution}" />
                    <input type="hidden" name="title" value="{$photo1->name}" />
                 </td>
                 <td>
                 {literal}
                      <script src="http://maps.google.com/maps?file=api&v=2&sensor=true&key=ABQIAAAAgVYidVUmCOyFEL1KAC7tghQX3mn-UdLSHLRAex27QOmggBRN7hRX8fnnEyRPYTyOKheG6OMs2FXi1w" type="text/javascript"></script>
                      {/literal}

                      <p>
                        <input type="text" value="{$photo1->address}"  id="address[{$photo1->id}]" name="address[{$photo1->id}]" size="20">
                        <input type="button" value="IR" onClick="showAddress(); return false;" />
                      </p>

                      <div id="map_canvas[{$photo1->id}]" style="width: 200px; height: 320px"></div>

                      {literal}
                        <script type="text/javascript" charset="utf-8">
                            var map = new GMap2(document.getElementById("map_canvas{/literal}[{$photo1->id}]{literal}"));


                            map.setCenter(new GLatLng(42.339806,-7.866068), 13);
                            function initialize() {

                            }

                            function showAddress() {
                                 var address = document.getElementById('address{/literal}[{$photo1->id}]{literal}').value;

                                 if(address){
                                     var geocoder = new GClientGeocoder();
                                     if (geocoder) {
                                           geocoder.getLatLng(address, function(point) {
                                           if (!point) {
                                             alert("No se ha podido geolocalizar la dirección" + address);
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

                     {/literal}
                 </td>
            </tr><tr>
                  <td align='right' colspan="2">
                 {if $display eq 'none'}     <a href="#" onclick="new Effect.toggle($('div_datos_{$photo1->id}'),'blind')"><b> {t}+INFO:{/t} </b></a>{/if}
                 </td>
            </tr>
       </table>
 <br style="clear:both;"/>
    <div id="div_datos_{$photo1->id}" style=" display:{$display}">
        {if $photo1->myiptc}
            <div id="iptc" style="margin-left:30px;width:440px;float:left;overflow:auto;background:#F5F5F5 none repeat scroll 0 0;">
              <table style="border:1px solid #000;" class="fuente_cuerpo" width="430">
                  <tr  style="border:1px solid #000;" ><th colspan="2"><h3>{t}IPTC Data:{/t}</h3></th></tr>
                  {foreach item="val" key="caption" from=$photo1->myiptc}
                      {if $val}
                             <tr><td style="border:1px solid #666; padding: 2px;" ><b>{$caption}</b> </td>
                                 <td style="border:1px solid #666; padding: 2px;" > {$val} </td></tr>
                      {/if}
                  {/foreach}
              </table>
              <br />
            </div>
        {/if}
        <div id="exif" style="margin-left:30px;width:440px;overflow:auto;background:#F5F5F5 none repeat scroll 0 0;">
          <table style="border:1px solid #000;" width="430">
            <tr style="border:1px solid #000;"><th colspan="2"><h3>{t}EXIF Data:{/t}</h3></th></tr>
              {foreach item="value" key="k" from=$photo1->exif}
                 <tr><td colspan="2"  ><b>{$k} </b> </td> </tr>
                      {foreach item="dato" key="d" from=$value}
                         <tr><td style="padding-left: 20px;border:1px solid #000;"><b>{$d}</b> </td>
                             <td style="border:1px solid #000; padding: 2px;"> {$dato} </td></tr>
                      {/foreach}
              {/foreach}
          </table>
          <br />
        </div>
    </div>
<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
</div>

<script type="text/javascript" language="javascript">
{literal}

if($('{/literal}fecha[{$photo1->id}]{literal}')) {
    new Control.DatePicker($('{/literal}fecha[{$photo1->id}]{literal}'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });

}
{/literal}
</script>
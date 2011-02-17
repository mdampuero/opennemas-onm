
<table border="0" cellpadding="4" cellspacing="6" id="ads_type_interior_video" width="800">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial - Video Inner (800X600)
                <input type="radio" name="type_advertisement" value="350" {if $advertisement->type_advertisement == 350}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right" width="240" rowspan="8">
            {include file="advertisement/advertisement_map_positions_video_interior.tpl"}
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="301" {if $advertisement->type_advertisement == 301}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                 Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="302" {if $advertisement->type_advertisement == 302}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right"  colspan="2">
            <label>
                Button Column (265x95)
                <input type="radio" name="type_advertisement" value="303" {if $advertisement->type_advertisement == 303}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

     <tr>
        <td colspan="2"><hr /></td>
    </tr>
      <tr>
        <td align="right">
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="309" {if $advertisement->type_advertisement == 309}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                 Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="310" {if $advertisement->type_advertisement == 310}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

</tbody>
</table>

<script defer="defer" type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionVideoInterior = null;

var positions_video_interior = new Array();
positions_video_interior[301] = '2,0,176,24';
positions_video_interior[302] = '178,0,55,24';
positions_video_interior[303] = '170,182,64,30';
positions_video_interior[309] = '2,382,176,24';
positions_video_interior[310] = '178,382,55,24';
positions_video_interior[350] = '0,0,240,435';

var options = {'positions': positions_video_interior, 'radios': $('ads_type_interior_video').select('input[name=type_advertisement]') };
adPositionVideoInterior = new AdPosition('advertisement-mosaic-videointerior', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && ($advertisement->type_advertisement gt 100) && $category == '4'}
    adPositionVideoInterior.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>

<table border="0" cellpadding="4" cellspacing="6" id="ads_type_gallery">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial - Gallery (800X600)
                <input type="radio" name="type_advertisement" value="450" {if $advertisement->type_advertisement == 450}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="340">
            {include file="advertisement/partials/advertisement_map_positions_gallery.tpl"}
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="401" {if $advertisement->type_advertisement == 401}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="402" {if $advertisement->type_advertisement == 402}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td align="right" colspan="2">
            <label>
                Banner1 Column Right (I) (300X*)
                <input type="radio" name="type_advertisement" value="403" {if $advertisement->type_advertisement == 403}checked="checked" {/if}/>
            </label>
        </td>
    </tr>


    <tr>
        <td height="50" align="right" colspan="2">
            <label>
                Banner2 Column Right(II) (300X*)
                <input type="radio" name="type_advertisement" value="405" {if $advertisement->type_advertisement == 405}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="409" {if $advertisement->type_advertisement == 409}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="410" {if $advertisement->type_advertisement == 410}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script defer="defer" type="text/javascript" language="javascript">
/* <![CDATA[ */
var adPositionGallery = null;

var positions_gallery = new Array();
positions_gallery[1] = '2,0,176,24';
positions_gallery[2] = '178,0,55,24';
positions_gallery[3] = '158,106,74,72';
positions_gallery[5] = '158,445,74,60';
positions_gallery[9] = '2,297,176,24';
positions_gallery[10] = '178,297,55,24';
positions_gallery[50] = '0,0,240,352';


var options = { 'positions': positions_gallery, 'radios': $('ads_type_gallery').select('input[name=type_advertisement]') };
//adPositionGallery = new AdPosition('advertisement-mosaic-gallery', options );
document.observe('dom:loaded', function() {

    {if !empty($advertisement->type_advertisement) && $category == '3'}
  //      adPositionGallery.selectPosition({$advertisement->type_advertisement});
    {/if}

});
/* ]]> */
</script>

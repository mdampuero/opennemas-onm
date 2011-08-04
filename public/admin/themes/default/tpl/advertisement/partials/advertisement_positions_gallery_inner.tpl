<table border="0" cellpadding="4" cellspacing="6" id="ads_type_gallery">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial - Gallery (800X600)
                <input type="radio" name="type_advertisement" value="550" {if $advertisement->type_advertisement == 550}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="340">
            {include file="advertisement/partials/advertisement_map_positions_gallery_inner.tpl"}
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td align="right">
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="501" {if $advertisement->type_advertisement == 501}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="502" {if $advertisement->type_advertisement == 502}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="503" {if $advertisement->type_advertisement == 503}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="509" {if $advertisement->type_advertisement == 509}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="510" {if $advertisement->type_advertisement == 510}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script defer="defer" type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionGalleryInner = null;

var positions_galleryInner = new Array();
positions_galleryInner[1] = '2,0,176,24';
positions_galleryInner[2] = '178,0,55,24';
positions_galleryInner[3] = '158,106,74,72';
positions_galleryInner[9] = '2,297,176,24';
positions_galleryInner[10] = '178,297,55,24';
positions_galleryInner[50] = '0,0,240,352';


var options = {'positions': positions_galleryInner, 'radios': $('ads_type_gallery').select('input[name=type_advertisement]') };
// adPositionGalleryInner = new AdPosition('advertisement-mosaic-gallery-inner', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && $category == '3'}
   //     adPositionGalleryInner.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>

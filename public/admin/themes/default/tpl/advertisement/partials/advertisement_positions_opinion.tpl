<table id="ads_type_opinion">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Opinion Frontpage (800X600)
                <input type="radio" name="type_advertisement" value="650" {if isset($advertisement) && $advertisement->type_advertisement == 650}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="6">
            {include file="advertisement/partials/advertisement_map_positions_opinion.tpl"}
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="601" {if isset($advertisement) && $advertisement->type_advertisement == 601}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top  Right (234X90)
                <input type="radio" name="type_advertisement" value="602" {if isset($advertisement) && $advertisement->type_advertisement == 602}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <label>
                Banner Column Right (300X*)
                <input type="radio" name="type_advertisement" value="603" {if isset($advertisement) && $advertisement->type_advertisement == 603}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <label>
                Banner Column Right 2(300X*)
                <input type="radio" name="type_advertisement" value="605" {if isset($advertisement) && $advertisement->type_advertisement == 605}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="609" {if isset($advertisement) && $advertisement->type_advertisement == 609}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="610" {if isset($advertisement) && $advertisement->type_advertisement == 610}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script type="text/javascript">
/* <![CDATA[ */
var adPositionOpinion = null;

var positions_opinion = new Array();
positions_opinion[601] = '2,0,176,24';
positions_opinion[602] = '178,0,55,24';
positions_opinion[603] = '162,217,70,50';
positions_opinion[605] = '162,503,70,50';
positions_opinion[609] = '2,276,176,24';
positions_opinion[610] = '178,276,55,24';
positions_opinion[650] = '0,0,240,327';


var options = { 'positions': positions_opinion, 'radios': $('ads_type_opinion').select('input[name=type_advertisement]') };
//adPositionOpinion = new AdPosition('advertisement-mosaic-opinion', options );
document.observe('dom:loaded', function() {

    {if isset($advertisement) && !empty($advertisement->type_advertisement) && $category == '4'}
  //  adPositionOpinion.selectPosition({$advertisement->type_advertisement});
    {/if}

});
/* ]]> */
</script>

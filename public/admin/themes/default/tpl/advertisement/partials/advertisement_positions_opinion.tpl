<table border="0" cellpadding="4" cellspacing="6" id="ads_type_opinion">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial - Opinion Frontpage (800X600)
                <input type="radio" name="type_advertisement" value="50" {if isset($advertisement) && $advertisement->type_advertisement == 50}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="340">
            {include file="advertisement/partials/advertisement_map_positions_opinion.tpl"}
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="1" {if isset($advertisement) && $advertisement->type_advertisement == 1}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Top  Right (234X90)
                <input type="radio" name="type_advertisement" value="2" {if isset($advertisement) && $advertisement->type_advertisement == 2}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">&nbsp;</td>
        <td align="right">
            <label>
                Banner Column Right (300X*)
                <input type="radio" name="type_advertisement" value="3" {if isset($advertisement) && $advertisement->type_advertisement == 3}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td align="right">
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="9" {if isset($advertisement) && $advertisement->type_advertisement == 9}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="10" {if isset($advertisement) && $advertisement->type_advertisement == 10}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */
var adPositionOpinion = null;

var positions_opinion = new Array();
positions_opinion[1] = '2,0,176,24';
positions_opinion[2] = '178,0,55,24';
positions_opinion[3] = '162,217,70,50';
positions_opinion[9] = '2,276,176,24';
positions_opinion[10] = '178,276,55,24';
positions_opinion[50] = '0,0,240,327';


var options = { 'positions': positions_opinion, 'radios': $('ads_type_opinion').select('input[name=type_advertisement]') };
//adPositionOpinion = new AdPosition('advertisement-mosaic-opinion', options );
document.observe('dom:loaded', function() {

    {if isset($advertisement) && !empty($advertisement->type_advertisement) && $category == '4'}
  //  adPositionOpinion.selectPosition({$advertisement->type_advertisement});
    {/if}

});
/* ]]> */
</script>

<table id="ads_type_interior_opinion">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Opinion Inner (800X600)
                <input type="radio" name="type_advertisement" value="750" {if isset($advertisement) && $advertisement->type_advertisement == 750}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="8">
            {include file="advertisement/partials/advertisement_map_positions_opinion_interior.tpl"}
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="701" {if isset($advertisement) && $advertisement->type_advertisement == 701}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="702" {if isset($advertisement) && $advertisement->type_advertisement == 702}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>

        <td>

        </td>

        <td>
            <label>
               Banner1 Column Right (300X*)
                <input type="radio" name="type_advertisement" value="703" {if isset($advertisement) && $advertisement->type_advertisement == 703}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
            <label>
               Banner2 Column Right (300X*)
                <input type="radio" name="type_advertisement" value="705" {if isset($advertisement) && $advertisement->type_advertisement == 705}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>

    <tr>
        <td>
            <label>
                Robapágina (650X*)
                <input type="radio" name="type_advertisement" value="704" {if isset($advertisement) && $advertisement->type_advertisement == 704}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
               Banner3 Column Right (300X*)
                <input type="radio" name="type_advertisement" value="706" {if isset($advertisement) && $advertisement->type_advertisement == 706}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>

    <tr>
        <td>
        </td>
        <td>
            <label>
               Banner4 Column Right (300X*)
                <input type="radio" name="type_advertisement" value="707" {if isset($advertisement) && $advertisement->type_advertisement == 707}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td>
        </td>
        <td>
            <label>
               Banner5 Column Right (300X*)
                <input type="radio" name="type_advertisement" value="708" {if isset($advertisement) && $advertisement->type_advertisement == 708}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
      <tr>
        <td>
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="709" {if isset($advertisement) && $advertisement->type_advertisement == 709}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="710" {if isset($advertisement) && $advertisement->type_advertisement == 710}checked="checked" {/if}/>
            </label>
        </td>
        <td>&nbsp;</td>
    </tr>
</tbody>
</table>

<script type="text/javascript">
/* <![CDATA[ */
var adPositionOpinionInterior = null;

var positions_opinion_interior = new Array();
positions_opinion_interior[701] = '2,0,176,24';
positions_opinion_interior[702] = '178,0,55,24';
positions_opinion_interior[703] = '161,188,70,50';
positions_opinion_interior[704] = '4,322,154,20';
positions_opinion_interior[705] = '161,375,70,50';
positions_opinion_interior[709] = '2,479,176,24';
positions_opinion_interior[710] = '178,479,55,24';
positions_opinion_interior[750] = '0,0,240,508';


var options = { 'positions': positions_opinion_interior, 'radios': $('ads_type_interior_opinion').select('input[name=type_advertisement]') };
// adPositionOpinionInterior = new AdPosition('advertisement-mosaic-opinioninterior', options );
document.observe('dom:loaded', function() {

    {if isset($advertisement) && !empty($advertisement->type_advertisement) && ($advertisement->type_advertisement gt 100) && $category == '4'}
 //   adPositionOpinionInterior.selectPosition({$advertisement->type_advertisement});
    {/if}

});
/* ]]> */
</script>

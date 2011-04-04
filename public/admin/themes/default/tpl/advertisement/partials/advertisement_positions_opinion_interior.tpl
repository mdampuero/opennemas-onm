
<table border="0" cellpadding="4" cellspacing="6" id="ads_type_interior_opinion">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                {t}Banner Intersticial - Opinion Inner (800X600){/t}
                <input type="radio" name="type_advertisement" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right" width="240" rowspan="8">
            {include file="advertisement/partials/advertisement_map_positions_opinion_interior.tpl"}
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                {t}Big Banner Top (728X90){/t}
                <input type="radio" name="type_advertisement" value="101" {if $advertisement->type_advertisement == 101}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                {t}Banner Top Right (234X90){/t}
                <input type="radio" name="type_advertisement" value="102" {if $advertisement->type_advertisement == 102}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right"  colspan="2">
            <label>
               {t}Banner1 Column Right (300X*){/t}
                <input type="radio" name="type_advertisement" value="103" {if $advertisement->type_advertisement == 103}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    
    <tr>
        <td height="50" align="right" colspan="2">
            <label>
                {t}Robapágina (650X*){/t}
                <input type="radio" name="type_advertisement" value="104" {if $advertisement->type_advertisement == 104}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    
    <tr>
        <td height="50" align="right"  colspan="2">
            <label>
               {t}Banner2 Column Right (300X*){/t}
                <input type="radio" name="type_advertisement" value="105" {if $advertisement->type_advertisement == 105}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
      <tr>
        <td align="right">
            <label>
                {t}Big Banner Bottom (728X90){/t}
                <input type="radio" name="type_advertisement" value="109" {if $advertisement->type_advertisement == 109}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                {t}Banner Bottom Right (234X90){/t}
                <input type="radio" name="type_advertisement" value="110" {if $advertisement->type_advertisement == 110}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionOpinionInterior = null;

var positions_opinion_interior = new Array();
positions_opinion_interior[101] = '2,0,176,24';
positions_opinion_interior[102] = '178,0,55,24';
positions_opinion_interior[103] = '161,188,70,50';
positions_opinion_interior[104] = '4,322,154,20';
positions_opinion_interior[105] = '161,375,70,50';
positions_opinion_interior[109] = '2,479,176,24';
positions_opinion_interior[110] = '178,479,55,24';
positions_opinion_interior[150] = '0,0,240,508';


var options = {'positions': positions_opinion_interior, 'radios': $('ads_type_interior_opinion').select('input[name=type_advertisement]') };
adPositionOpinionInterior = new AdPosition('advertisement-mosaic-opinioninterior', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && ($advertisement->type_advertisement gt 100) && $category == '4'}
    adPositionOpinionInterior.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>

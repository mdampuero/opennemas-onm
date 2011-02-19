
<table border="0" cellpadding="4" cellspacing="6" id="ads_type_interior" width="800">
<tbody>
    <tr>
        <td height="50" align="right" colspan="2">
            <label>
                {t}Banner Intersticial - Inner (800X600){/t}
                <input type="radio" name="type_advertisement" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="8" align="right" width="240">
            {include file="advertisement/advertisement_map_positions_interior.tpl"}
        </td>
    </tr>

    <tr>
        <td align="right">
            <label>
                {t}Big  Banner Top(I) (728X90){/t}
                <input type="radio" name="type_advertisement" value="101" {if $advertisement->type_advertisement == 101}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                 {t}Banner Top Right(I) (728X90){/t}
                <input type="radio" name="type_advertisement" value="102" {if $advertisement->type_advertisement == 102}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td align="right" colspan="2">
            <label>
                {t}Banner1 Column Right (I) (300X*){/t}
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
        <td height="50" align="right" colspan="2">
            <label>
                {t}Banner2 Column Right(I) (300X*){/t}
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
                {t}Big Banner Bottom(I) (728X90){/t}
                <input type="radio" name="type_advertisement" value="109" {if $advertisement->type_advertisement == 109}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                {t}Banner Bottom Right(I) (234X90){/t}
                <input type="radio" name="type_advertisement" value="110" {if $advertisement->type_advertisement == 110}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script defer="defer" type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionInterior = null;

var positions_interior = new Array();
positions_interior[101] = '0,0,176,23';
positions_interior[102] = '177,0,55,23';
positions_interior[103] = '158,106,74,72';
positions_interior[104] = '4,322,154,20';
positions_interior[105] = '158,445,74,60';
positions_interior[109] = '0,518,176,23';
positions_interior[110] = '177,518,55,23';

positions_interior[150] = '0,0,240,550';


var options = {'positions': positions_interior, 'radios': $('ads_type_interior').select('input[name=type_advertisement]') };
adPositionInterior = new AdPosition('advertisement-mosaic-interior', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && ($advertisement->type_advertisement gt 100)} {* && $category != '4' *}
    adPositionInterior.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>

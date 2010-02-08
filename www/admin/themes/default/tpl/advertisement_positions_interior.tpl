
<table border="0" cellpadding="4" cellspacing="6" id="ads_type_interior" width="800">
<tbody>
    <tr>
        <td height="50" align="right">
            <label>
                Banner Intersticial Interior
                <input type="radio" name="type_advertisement" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="5" align="right" width="240">
            {include file="advertisement_map_positions_interior.tpl"}
        </td>
    </tr>
    <tr>
        <td height="50" align="right">
            <label>
                Banner Noticia Interior
                <input type="radio" name="type_advertisement" value="101" {if $advertisement->type_advertisement == 101}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td height="50" align="right">		
            <label>
                Banner Columna Interior 1
                <input type="radio" name="type_advertisement" value="102" {if $advertisement->type_advertisement == 102}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td height="50" align="right">		
            <label>
                Banner Columna Interior 2
                <input type="radio" name="type_advertisement" value="103" {if $advertisement->type_advertisement == 103}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionInterior = null;

var positions_interior = new Array();
positions_interior[101] = '1,298,158,23';
positions_interior[102] = '162,103,75,80';
positions_interior[103] = '162,275,75,80';
positions_interior[150] = '0,0,240,393';

var options = {'positions': positions_interior, 'radios': $('ads_type_interior').select('input[name=type_advertisement]') };
adPositionInterior = new AdPosition('advertisement-mosaic-interior', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && ($advertisement->type_advertisement gt 100) && $category != '4'}
    adPositionInterior.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script> 
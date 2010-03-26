
<table border="0" cellpadding="4" cellspacing="6" id="ads_type_interior" width="800">
<tbody>
    <tr>
        <td height="50" align="right" colspan="2">
            <label>
                Banner Intersticial Interior
                <input type="radio" name="type_advertisement" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="8" align="right" width="240">
            {include file="advertisement_map_positions_interior.tpl"}
        </td>
    </tr>
    
    <tr>
        <td align="right">
            <label>
                Big Banner Superior Izquierdo (Int.)
                <input type="radio" name="type_advertisement" value="101" {if $advertisement->type_advertisement == 101}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Superior Derecho (Int.)
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
                Bot&oacute;n Columna Derecha 1 (Int.)
                <input type="radio" name="type_advertisement" value="103" {if $advertisement->type_advertisement == 103}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    
    <tr>    
        <td height="50" align="right" colspan="2">
            <label>
                Robapágina
                <input type="radio" name="type_advertisement" value="104" {if $advertisement->type_advertisement == 104}checked="checked" {/if}/>
            </label>
        </td>
    </tr>        
        
    <tr>    
        <td height="50" align="right" colspan="2">	
            <label>
                Bot&oacute;n Columna Derecha 2 (Int.)
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
                Big Banner Inferior Izquierdo (Int.)
                <input type="radio" name="type_advertisement" value="106" {if $advertisement->type_advertisement == 106}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Inferior Derecho (Int.)
                <input type="radio" name="type_advertisement" value="107" {if $advertisement->type_advertisement == 107}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionInterior = null;

var positions_interior = new Array();
positions_interior[101] = '0,2,181,21';
positions_interior[102] = '183,2,55,21';
positions_interior[103] = '160,94,72,59';
positions_interior[104] = '6,323,148,18';
positions_interior[105] = '161,357,72,59';
positions_interior[106] = '3,505,177,16';
positions_interior[107] = '182,505,54,16';

positions_interior[150] = '0,0,240,548';

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

<table border="0" cellpadding="4" cellspacing="6" id="ads_type_interior_opinion" width="800">
<tbody>
    <tr>
        <td align="right">
            <label>
                Banner Intersticial Interior
                <input type="radio" name="type_advertisement" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right" width="240" rowspan="3">
            {include file="advertisement_map_positions_opinion_interior.tpl"}
        </td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Bot&oacute;n 1o en Columna
                <input type="radio" name="type_advertisement" value="5" {if $advertisement->type_advertisement == 5}checked="checked" {/if}/>
            </label>
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
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionOpinionInterior = null;

var positions_opinion_interior = new Array();
positions_opinion_interior[5] = '165,133,75,77';
positions_opinion_interior[101] = '0,226,154,25';
positions_opinion_interior[150] = '0,0,240,250';

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
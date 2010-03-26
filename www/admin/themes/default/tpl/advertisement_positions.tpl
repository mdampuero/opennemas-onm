<table border="0" cellpadding="4" cellspacing="6" id="ads_type_portada" width="800">
<tbody>    
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial en Portadas
                <input type="radio" name="type_advertisement" value="50" {if $advertisement->type_advertisement == 50}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="11" align="right" width="240">
            {include file="advertisement_map_positions.tpl"}
        </td>
    </tr>    
    <tr>
        <td align="right">
            <label>
                Big Banner Superior Izquierdo
                <input type="radio" name="type_advertisement" value="1" {if $advertisement->type_advertisement == 1}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Superior Derecho
                <input type="radio" name="type_advertisement" value="2" {if $advertisement->type_advertisement == 2}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    
    <tr>
        <td align="right">
            <label>
                Bot&oacute;n Columna 1
                <input type="radio" name="type_advertisement" value="3" {if $advertisement->type_advertisement == 3}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Bot&oacute;n Columna 3
                <input type="radio" name="type_advertisement" value="4" {if $advertisement->type_advertisement == 4}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    
    <tr>
        <td colspan="2" align="right">
            <label>
                Separador horizontal
                <input type="radio" name="type_advertisement" value="5" {if $advertisement->type_advertisement == 5}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Banner Mini 1 Derecho
                <input type="radio" name="type_advertisement" value="6" {if $advertisement->type_advertisement == 6}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Mini 2 Derecho
                <input type="radio" name="type_advertisement" value="7" {if $advertisement->type_advertisement == 7}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>    
    
    <tr>
        <td align="right" colspan="2">		
            <label>
                Bot&oacute;n Inferior Derecho
                <input type="radio" name="type_advertisement" value="8" {if $advertisement->type_advertisement == 8}checked="checked" {/if}/>
            </label>        
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>    
    
    <tr>
        <td align="right">
            <label>
                Big Banner Inferior Izquierdo
                <input type="radio" name="type_advertisement" value="9" {if $advertisement->type_advertisement == 9}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Inferior Derecho
                <input type="radio" name="type_advertisement" value="10" {if $advertisement->type_advertisement == 10}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionPortada = null;

var positions_portada = ['0,0,0,0', '1,1,182,22', '186,1,53,22', '6,138,73,32', '163,204,73,32',
                         '6,320,154,25', '163,320,73,12', '163,333,73,12', '163,485,73,32', '3,531,179,18',
                         '185,531,54,18' ];

// Intersticial banner
positions_portada[50] = '0,0,240,572';

var options = {'positions': positions_portada, 'radios': $('ads_type_portada').select('input[name=type_advertisement]') };
adPositionPortada = new AdPosition('advertisement-mosaic', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement)}
    adPositionPortada.selectPosition({$advertisement->type_advertisement});
    {else}
    adPositionPortada.selectPosition(1);
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>
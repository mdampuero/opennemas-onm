<table border="0" cellpadding="4" cellspacing="6" id="ads_type_portada" width="800">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial en Portadas
                <input type="radio" name="type_advertisement" value="50" {if $advertisement->type_advertisement == 50}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="12" align="right" width="240">
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
                Big Banner Superior Derecho
                <input type="radio" name="type_advertisement" value="2" {if $advertisement->type_advertisement == 2}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">	 
            <label>
                Banner Cabecera
                <input type="radio" name="type_advertisement" value="3" {if $advertisement->type_advertisement == 3}checked="checked" {/if}/>
            </label>		
        </td>
        <td align="right">		
            <label>
                Banner Flotante Derecho
                <input type="radio" name="type_advertisement" value="4" {if $advertisement->type_advertisement == 4}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Bot&oacute;n 1o en Columna
                <input type="radio" name="type_advertisement" value="5" {if $advertisement->type_advertisement == 5}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Lateral 1o Derecho
                <input type="radio" name="type_advertisement" value="6" {if $advertisement->type_advertisement == 6}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">		
            <label>
                Bot&oacute;n 2o en Columna
                <input type="radio" name="type_advertisement" value="14" {if $advertisement->type_advertisement == 14}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Lateral 2o Derecho
                <input type="radio" name="type_advertisement" value="15" {if $advertisement->type_advertisement == 15}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">		
            <label>
                Bot&oacute;n 3o en Columna
                <input type="radio" name="type_advertisement" value="16" {if $advertisement->type_advertisement == 16}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td colspan="2" align="right">
            <label>
                Separador Horizontal
                <input type="radio" name="type_advertisement" value="7" {if $advertisement->type_advertisement == 7}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Banner Mini 1 Derecho
                <input type="radio" name="type_advertisement" value="8" {if $advertisement->type_advertisement == 8}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Mini 2 Derecho
                <input type="radio" name="type_advertisement" value="9" {if $advertisement->type_advertisement == 9}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Banner Inferior 1o Izquierda
                <input type="radio" name="type_advertisement" value="10" {if $advertisement->type_advertisement == 10}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Inferior 1o Columna
                <input type="radio" name="type_advertisement" value="12" {if $advertisement->type_advertisement == 12}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Banner Inferior 2o Izquierda
                <input type="radio" name="type_advertisement" value="11" {if $advertisement->type_advertisement == 11}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Inferior 2o Columna
                <input type="radio" name="type_advertisement" value="13" {if $advertisement->type_advertisement == 13}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionPortada = null;

var positions_portada = ['0,0,0,0', '1,1,145,19', '146,1,47,18', '83,20,113,16', '197,3,41,113', '91,114,61,65', '155,190,37,106', '0,433,151,22', '152,433,43,12', '152,446,43,12', '0,474,142,18', '0,514,147,21', '143,474,51,18', '148,514,46,21', '89,256,63,54', '154,307,40,105', '89,376,63,54'];

// Intersticial banner
positions_portada[50] = '0,0,240,545';

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
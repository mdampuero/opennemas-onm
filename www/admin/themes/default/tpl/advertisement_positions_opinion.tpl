<table border="0" cellpadding="4" cellspacing="6" id="ads_type_opinion" width="800">
<tbody>
    <tr>
        <td align="right" colspan="2">		
            <label>
                Banner Intersticial en Portadas
                <input type="radio" name="type_advertisement" value="50" {if $advertisement->type_advertisement == 50}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="240">
            {include file="advertisement_map_positions_opinion.tpl"}
        </td>            
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
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
        <td align="right">&nbsp;</td>
        <td align="right">	 
            <label>
                Banner Cabecera
                <input type="radio" name="type_advertisement" value="3" {if $advertisement->type_advertisement == 3}checked="checked" {/if}/>
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
var adPositionOpinion = null;

var positions_opinion = new Array();
positions_opinion[1] = '0,0,180,20';
positions_opinion[2] = '184,0,55,20';
positions_opinion[3] = '90,24,150,14';
positions_opinion[10] = '0,164,180,22';
positions_opinion[12] = '184,164,55,22';
positions_opinion[11] = '0,210,180,22';
positions_opinion[13] = '184,210,55,22';
positions_opinion[50] = '0,0,240,244';

var options = {'positions': positions_opinion, 'radios': $('ads_type_opinion').select('input[name=type_advertisement]') };
adPositionOpinion = new AdPosition('advertisement-mosaic-opinion', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && $category == '4'}
    adPositionOpinion.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>
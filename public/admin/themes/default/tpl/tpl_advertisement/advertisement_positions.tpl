<table border="0" cellpadding="4" cellspacing="6" id="ads_type_portada" width="800">
<tbody>    
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial en Portadas (800X600)
                <input type="radio" name="type_advertisement" value="50" {if $advertisement->type_advertisement == 50}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="11" align="right" width="240">
            {include file="tpl_advertisement/advertisement_map_positions.tpl"}
        </td>
    </tr>    
    <tr>
        <td align="right">
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="1" {if $advertisement->type_advertisement == 1}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Top Right  (234X90)
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
                Button Column 1 (300X*)
                <input type="radio" name="type_advertisement" value="3" {if $advertisement->type_advertisement == 3}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Button Column 3  (300X*)
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
                Horizontal Separator   (650X80)
                <input type="radio" name="type_advertisement" value="5" {if $advertisement->type_advertisement == 5}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Banner Mini 1  (300X40)
                <input type="radio" name="type_advertisement" value="6" {if $advertisement->type_advertisement == 6}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">		
            <label>
                Banner Mini 2 (300X40)
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
                Button2 column3 (300X*)
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
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="9" {if $advertisement->type_advertisement == 9}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="10" {if $advertisement->type_advertisement == 10}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    
</tbody>
</table>

<script type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionPortada = null;


var positions_portada = ['0,0,0,0',
                            '1,1,178,23', '180,1,58,23',
                            '3,277,76,62', '160,346,76,62',
                            '3,553,154,25', '160,553,74,12', '160,566,74,12',
                            '160,753,76,56',
                            '1,826,176,22', '177,826,58,22' ];

// Intersticial banner
positions_portada[50] = '0,0,240,878';

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
<table border="0" cellpadding="4" cellspacing="6" id="ads_type_portada">
<tbody>
    <tr>
        <td align="center" colspan="2">
            <table width=100%>
                <tr>
                    <td align="center">
                        <label>
                            {t}Frontpage Intersticial (800X600){/t}
                            <input type="radio" name="type_advertisement" value="50" {if $advertisement->type_advertisement == 50}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
        <td rowspan="11" align="right" width="240">
            {include file="advertisement/partials/advertisement_map_positions.tpl"}
        </td>
    </tr>
    <tr>
        <td align="center" colspan="2">
            <table width=100%>
                <tr>
                    <td align="left">
                        <label>
                            {t}Top Left LeaderBoard (728X90){/t}
                            <input type="radio" name="type_advertisement" value="1" {if $advertisement->type_advertisement == 1}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td align="right">
                        <label>
                            {t}Top Right LeaderBoard  (234X90){/t}
                            <input type="radio" name="type_advertisement" value="2" {if $advertisement->type_advertisement == 2}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td colspan="2" align="right">
            <table width=100%>
                <tr>
                    <td align="right">
                        <label>
                            {t}Button Column 1 position 1 (300X*){/t}
                            <input type="radio" name="type_advertisement" value="11" {if $advertisement->type_advertisement == 11}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            {t}Button Column 1 position 2  (300X*){/t}
                            <input type="radio" name="type_advertisement" value="12" {if $advertisement->type_advertisement == 12}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td align="right">
                        <label>
                            {t}Button Column 2 position 1 (300X*){/t}
                            <input type="radio" name="type_advertisement" value="21" {if $advertisement->type_advertisement == 21}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            {t}Button Column 2 position 2  (300X*){/t}
                            <input type="radio" name="type_advertisement" value="22" {if $advertisement->type_advertisement == 22}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td align="right">
                        <label>
                            {t}Button Column 3 position 1 (300X*){/t}
                            <input type="radio" name="type_advertisement" value="31" {if $advertisement->type_advertisement == 31}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            {t}Button Column 3 position 2  (300X*){/t}
                            <input type="radio" name="type_advertisement" value="32" {if $advertisement->type_advertisement == 32}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    
    <tr>
        <td colspan=2 align=right>
            <table width=100%>
                <tr>
                    <td align="left">
                        <label>
                            {t}Button Column 1 position 3  (200x200){/t}
                            <input type="radio" name="type_advertisement" value="13" {if $advertisement->type_advertisement == 13}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td align="right">
                        <label>
                            {t}Button Column 3 position 3 (200x200){/t}
                            <input type="radio" name="type_advertisement" value="33" {if $advertisement->type_advertisement == 33}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <label>
                            {t}Center Left LeaderBoard (728X90){/t}
                            <input type="radio" name="type_advertisement" value="3" {if $advertisement->type_advertisement == 3}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td align="right">
                        <label>
                            {t}Center Right LeaderBoard (234x90){/t}
                            <input type="radio" name="type_advertisement" value="4" {if $advertisement->type_advertisement == 4}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    
    <tr>
        <td colspan="2">
            <table width=100%>
                <tr>
                    <td align="right">
                        <label>
                            {t}Button Column 1 position 4 (300X*){/t}
                            <input type="radio" name="type_advertisement" value="14" {if $advertisement->type_advertisement == 14}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            {t}Button Column 1 position 5  (300X*){/t}
                            <input type="radio" name="type_advertisement" value="15" {if $advertisement->type_advertisement == 15}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td align="right">
                        <label>
                            {t}Button Column 2 position 4 (300X*){/t}
                            <input type="radio" name="type_advertisement" value="24" {if $advertisement->type_advertisement == 24}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            {t}Button Column 2 position 5  (300X*){/t}
                            <input type="radio" name="type_advertisement" value="25" {if $advertisement->type_advertisement == 25}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td align="right">
                        <label>
                            {t}Button Column 3 position 4 (300X*){/t}
                            <input type="radio" name="type_advertisement" value="34" {if $advertisement->type_advertisement == 34}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            {t}Button Column 3 position 5  (300X*){/t}
                            <input type="radio" name="type_advertisement" value="35" {if $advertisement->type_advertisement == 35}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td colspan="2">
            <table width=100%>
                <tr>
                    <td align="left">
                        <label>
                            {t}Button Column 1 position 6  (200x200){/t}
                            <input type="radio" name="type_advertisement" value="16" {if $advertisement->type_advertisement == 16}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td align="right">
                        <label>
                            {t}Button Column 3 position 6 (200x200){/t}
                            <input type="radio" name="type_advertisement" value="36" {if $advertisement->type_advertisement == 36}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <label>
                            {t}Bottom Left LeaderBoard (728X90){/t}
                            <input type="radio" name="type_advertisement" value="5" {if $advertisement->type_advertisement == 5}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td align="right">
                        <label>
                            {t}Bottom Right LeaderBoard (234X90){/t}
                            <input type="radio" name="type_advertisement" value="6" {if $advertisement->type_advertisement == 6}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</tbody>
</table>

<script defer="defer" type="text/javascript" language="javascript">
/* <![CDATA[ */
var adPositionPortada = null;

var positions_portada = [ '0,0,0,0', '1,1,175,24', '180,1,58,24',
                          '1,292,175,24', '180,292,58,24',
                          '1,576,175,26', '180,576,58,26' ];

positions_portada[11]='8,48,72,76';
positions_portada[21]='86,48,72,76';
positions_portada[31]='162,48,72,76';
positions_portada[12]='8,134,72,76';
positions_portada[22]='86,134,72,76',
positions_portada[32]='162,135,72,76';

positions_portada[13]='8,224,50,52';
positions_portada[33]='184,224,50,54';

positions_portada[14]='8,335,72,76';
positions_portada[24]='86,335,72,76';
positions_portada[34]='162,335,72,76';
positions_portada[15]='8,422,72,76';
positions_portada[25]='86,422,72,76',
positions_portada[35]='162,422,72,76';

positions_portada[16]='8,512,48,50';
positions_portada[36]='186,509,50,54';


// Intersticial banner
positions_portada[50] = '0,0,240,620';

var options  =  {
                'positions': positions_portada,
                'radios': $('ads_type_portada').select('input[name=type_advertisement]')
                };
adPositionPortada = new AdPosition('advertisement-mosaic', options );
document.observe('dom:loaded', function() {
    {if !empty($advertisement->type_advertisement)}
    adPositionPortada.selectPosition({ $advertisement->type_advertisement });
    {else}
    adPositionPortada.selectPosition(1);
    {/if}
});
/* ]]> */
</script>

<table border="0" cellpadding="4" cellspacing="6" id="ads_type_video">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                {t}Banner Intersticial - Video Frontpage (800X600){/t}
                <input type="radio" name="type_advertisement" value="250" {if $advertisement->type_advertisement == 250}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="340">
            {include file="advertisement/partials/advertisement_map_positions_video.tpl"}
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                {t}Big Banner Top (728X90){/t}
                <input type="radio" name="type_advertisement" value="201" {if $advertisement->type_advertisement == 201}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                 {t}Banner Top Right (234X90){/t}
                <input type="radio" name="type_advertisement" value="202" {if $advertisement->type_advertisement == 202}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="203" {if $advertisement->type_advertisement == 203}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="209" {if $advertisement->type_advertisement == 209}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                {t}Banner Bottom Right (234X90){/t}
                <input type="radio" name="type_advertisement" value="210" {if $advertisement->type_advertisement == 210}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

<script defer="defer" type="text/javascript" language="javascript">
/* <![CDATA[ */{literal}
var adPositionVideo = null;

var positions_video = new Array();
positions_video[201] = '1,0,176,24';
positions_video[202] = '178,0,55,24';
positions_video[209] = '1,348,176,24';
positions_video[210] = '178,348,55,24';
positions_video[250] = '0,0,240,401';



var options = {'positions': positions_video, 'radios': $('ads_type_video').select('input[name=type_advertisement]') };
// adPositionVideo = new AdPosition('advertisement-mosaic-video', options );
document.observe('dom:loaded', function() {
    {/literal}
    {if !empty($advertisement->type_advertisement) && $category == '4'}
//    adPositionVideo.selectPosition({$advertisement->type_advertisement});
    {/if}
    {literal}
});
/* ]]> */{/literal}
</script>

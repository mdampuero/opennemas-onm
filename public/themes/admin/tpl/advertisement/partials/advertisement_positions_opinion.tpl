<table id="ads_type_opinion">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Opinion Frontpage (800X600)
                <input type="radio" name="type_advertisement" value="650" {if isset($advertisement) && $advertisement->type_advertisement == 650}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="6">
            <div id="advertisement-mosaic-opinion" style="">
                <div id="advertisement-mosaic-frame-opinion"></div>
                <img src="{$params.IMAGE_DIR}advertisement/right2Ads.png" width="240" usemap="#mapOpinion" />
            </div>

        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                 Left Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="691" {if isset($advertisement) && $advertisement->type_advertisement == 691}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="692" {if isset($advertisement) && $advertisement->type_advertisement == 692}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="601" {if isset($advertisement) && $advertisement->type_advertisement == 601}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top  Right (234X90)
                <input type="radio" name="type_advertisement" value="602" {if isset($advertisement) && $advertisement->type_advertisement == 602}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <label>
                Banner Column Right (300X*)
                <input type="radio" name="type_advertisement" value="603" {if isset($advertisement) && $advertisement->type_advertisement == 603}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <label>
                Banner Column Right 2(300X*)
                <input type="radio" name="type_advertisement" value="605" {if isset($advertisement) && $advertisement->type_advertisement == 605}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="609" {if isset($advertisement) && $advertisement->type_advertisement == 609}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="610" {if isset($advertisement) && $advertisement->type_advertisement == 610}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

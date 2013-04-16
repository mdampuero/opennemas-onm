<table id="ads_type_video">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Video Frontpage (800X600)
                <input type="radio" name="type_advertisement" value="250" {if isset($advertisement) && $advertisement->type_advertisement == 250}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7">
            <div id="advertisement-mosaic-video" style="">
                <div id="advertisement-mosaic-frame-video"></div>
                <img src="{$params.IMAGE_DIR}advertisement/videoAds.png" width="240" height="401" usemap="#mapVideo" />
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
                <input type="radio" name="type_advertisement" value="291" {if isset($advertisement) && $advertisement->type_advertisement == 291}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="292" {if isset($advertisement) && $advertisement->type_advertisement == 292}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="201" {if isset($advertisement) && $advertisement->type_advertisement == 201}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="202" {if isset($advertisement) && $advertisement->type_advertisement == 202}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td colspan="2">
            <label>
                Banner1 Column Right (I) (300X*)
                <input type="radio" name="type_advertisement" value="203" {if isset($advertisement) && $advertisement->type_advertisement == 203}checked="checked" {/if}/>
            </label>
        </td>
    </tr>


    <tr>
        <td colspan="2"><hr /></td>
    </tr>


    <tr>
        <td>
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="209" {if isset($advertisement) && $advertisement->type_advertisement == 209}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="210" {if isset($advertisement) && $advertisement->type_advertisement == 210}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

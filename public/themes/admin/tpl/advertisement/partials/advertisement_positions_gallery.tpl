<table id="ads_type_gallery">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Gallery (800X600)
                <input type="radio" name="type_advertisement" value="450" {if isset($advertisement) && $advertisement->type_advertisement == 450}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7">
            <div id="advertisement-mosaic-gallery">
                <div id="advertisement-mosaic-frame-gallery"></div>
                <img src="{$params.IMAGE_DIR}advertisement/right2Ads.png" width="240" usemap="#mapGallery" />
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
                <input type="radio" name="type_advertisement" value="491" {if isset($advertisement) && $advertisement->type_advertisement == 491}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="492" {if isset($advertisement) && $advertisement->type_advertisement == 492}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="401" {if isset($advertisement) && $advertisement->type_advertisement == 401}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="402" {if isset($advertisement) && $advertisement->type_advertisement == 402}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="403" {if isset($advertisement) && $advertisement->type_advertisement == 403}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label>
                Banner2 Column Right(II) (300X*)
                <input type="radio" name="type_advertisement" value="405" {if isset($advertisement) && $advertisement->type_advertisement == 405}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="409" {if isset($advertisement) && $advertisement->type_advertisement == 409}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="410" {if isset($advertisement) && $advertisement->type_advertisement == 410}checked="checked" {/if}/>
            </label>
        </td>
        <td>&nbsp;</td>
    </tr>
</tbody>
</table>
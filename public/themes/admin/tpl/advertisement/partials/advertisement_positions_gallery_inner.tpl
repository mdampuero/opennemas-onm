<table id="ads_type_gallery_inner">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Gallery (800X600)
                <input type="radio" name="type_advertisement" value="550" {if isset($advertisement) && $advertisement->type_advertisement == 550}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7">
            <div id="advertisement-mosaic-gallery-inner" style="">
                <div id="advertisement-mosaic-frame-gallery-inner"></div>
                <img src="{$params.IMAGE_DIR}advertisement/right1Ads.png" width="240" usemap="#mapGallery" />
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
                <input type="radio" name="type_advertisement" value="591" {if isset($advertisement) && $advertisement->type_advertisement == 591}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="592" {if isset($advertisement) && $advertisement->type_advertisement == 592}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="501" {if isset($advertisement) && $advertisement->type_advertisement == 501}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="502" {if isset($advertisement) && $advertisement->type_advertisement == 502}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="503" {if isset($advertisement) && $advertisement->type_advertisement == 503}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="509" {if isset($advertisement) && $advertisement->type_advertisement == 509}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="510" {if isset($advertisement) && $advertisement->type_advertisement == 510}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>
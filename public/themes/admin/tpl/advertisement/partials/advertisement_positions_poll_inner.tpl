<table id="ads_type_poll_inner">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - {t}Polls{/t}(800X600)
                <input type="radio" name="type_advertisement" value="950" {if isset($advertisement) && $advertisement->type_advertisement == 950}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7">
             <div id="advertisement-mosaic-poll-inner">
                <div id="advertisement-mosaic-frame-poll-inner"></div>
                <img src="{$params.IMAGE_DIR}/advertisement/right1Ads.png" width="240" usemap="#mapGallery" />
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
                <input type="radio" name="type_advertisement" value="991" {if isset($advertisement) && $advertisement->type_advertisement == 991}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="992" {if isset($advertisement) && $advertisement->type_advertisement == 992}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="901" {if isset($advertisement) && $advertisement->type_advertisement == 901}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="902" {if isset($advertisement) && $advertisement->type_advertisement == 902}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="903" {if isset($advertisement) && $advertisement->type_advertisement == 903}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="909" {if isset($advertisement) && $advertisement->type_advertisement == 909}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="910" {if isset($advertisement) && $advertisement->type_advertisement == 910}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>


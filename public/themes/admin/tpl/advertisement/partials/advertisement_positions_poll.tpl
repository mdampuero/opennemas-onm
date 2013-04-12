<table id="ads_type_poll">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - {t}Polls{/t} (800X600)
                <input type="radio" name="type_advertisement" value="850" {if isset($advertisement) && $advertisement->type_advertisement == 850}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7">
            <div id="advertisement-mosaic-poll">
                <div id="advertisement-mosaic-frame-poll"></div>
                <img src="{$params.IMAGE_DIR}/advertisement/right2Ads.png" width="240" usemap="#mapGallery" />
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
                <input type="radio" name="type_advertisement" value="891" {if isset($advertisement) && $advertisement->type_advertisement == 891}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="892" {if isset($advertisement) && $advertisement->type_advertisement == 892}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="801" {if isset($advertisement) && $advertisement->type_advertisement == 801}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="802" {if isset($advertisement) && $advertisement->type_advertisement == 802}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="803" {if isset($advertisement) && $advertisement->type_advertisement == 803}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label>
                Banner2 Column Right(II) (300X*)
                <input type="radio" name="type_advertisement" value="805" {if isset($advertisement) && $advertisement->type_advertisement == 805}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="809" {if isset($advertisement) && $advertisement->type_advertisement == 809}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="810" {if isset($advertisement) && $advertisement->type_advertisement == 810}checked="checked" {/if}/>
            </label>
        </td>
        <td>&nbsp;</td>
    </tr>
</tbody>
</table>



<table id="ads_type_interior">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Inner (800X600)
                <input type="radio" name="type_advertisement" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="8">
            <div id="advertisement-mosaic-interior" style="">
                <div id="advertisement-mosaic-interior-frame"></div>
                <img src="{$params.IMAGE_DIR}advertisement/ArticleAds.png" width="240" usemap="#mapInterior" />
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
                <input type="radio" name="type_advertisement" value="191" {if isset($advertisement) && $advertisement->type_advertisement == 191}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="192" {if isset($advertisement) && $advertisement->type_advertisement == 192}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big  Banner Top(I) (728X90)
                <input type="radio" name="type_advertisement" value="101" {if $advertisement->type_advertisement == 101}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Banner Top Right(I) (728X90)
                <input type="radio" name="type_advertisement" value="102" {if $advertisement->type_advertisement == 102}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td>

        </td>
        <td>
            <label>
                Banner1 Column Right (I) (300X*)
                <input type="radio" name="type_advertisement" value="103" {if $advertisement->type_advertisement == 103}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <label>
                Banner2 Column Right(I) (300X*)
                <input type="radio" name="type_advertisement" value="105" {if $advertisement->type_advertisement == 105}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label>
                Robapágina (650X*)
                <input type="radio" name="type_advertisement" value="104" {if $advertisement->type_advertisement == 104}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner3 Column Right(I) (300X*)
                <input type="radio" name="type_advertisement" value="106" {if $advertisement->type_advertisement == 106}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <label>
                Banner4 Column Right(I) (300X*)
                <input type="radio" name="type_advertisement" value="107" {if $advertisement->type_advertisement == 107}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <label>
                Banner5 Column Right(I) (300X*)
                <input type="radio" name="type_advertisement" value="108" {if $advertisement->type_advertisement == 108}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Bottom(I) (728X90)
                <input type="radio" name="type_advertisement" value="109" {if $advertisement->type_advertisement == 109}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Banner Bottom Right(I) (234X90)
                <input type="radio" name="type_advertisement" value="110" {if $advertisement->type_advertisement == 110}checked="checked" {/if}/>
            </label>
        </td>
        <td></td>
    </tr>
</tbody>
</table>

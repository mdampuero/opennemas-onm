<table id="ads_type_portada">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Frontpage Intersticial (800X600)
                <input type="radio" name="type_advertisement" value="50" {if isset($advertisement) && $advertisement->type_advertisement == 50}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                 Left Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="91" {if isset($advertisement) && $advertisement->type_advertisement == 91}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="92" {if isset($advertisement) && $advertisement->type_advertisement == 92}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td colspan="2">
            <label>
                Top Mega-LeaderBoard  (972X90)
                <input type="radio" name="type_advertisement" value="9" {if isset($advertisement) && $advertisement->type_advertisement == 9}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="11">
            <div id="advertisement-mosaic">
                <div id="advertisement-mosaic-frame"></div>
                <img src="{$params.IMAGE_DIR}advertisement/front_advertisement.png" style="width:240px;height:628px;" usemap="#mapPortada" />
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <label>
                Top Left LeaderBoard (728X90)
                <input type="radio" name="type_advertisement" value="1" {if isset($advertisement) && $advertisement->type_advertisement == 1}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                Top Right LeaderBoard  (234X90)
                <input type="radio" name="type_advertisement" value="2" {if isset($advertisement) && $advertisement->type_advertisement == 2}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label>
                Right Logo Banner (max-468x60)
                <input type="radio" name="type_advertisement" value="7" {if isset($advertisement) && $advertisement->type_advertisement == 7}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td colspan="2">
            <label>{t}Floating ads (for drop them into columns){/t}
                <input type="radio" name="type_advertisement" value="37" {if isset($advertisement) && $advertisement->type_advertisement == 37}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td colspan="2">
            <table>
                <tr>
                    <td>
                        <label>
                            Button Column 1 position 1 (300X*)
                            <input type="radio" name="type_advertisement" value="11" {if isset($advertisement) && $advertisement->type_advertisement == 11}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            Button Column 1 position 2  (300X*)
                            <input type="radio" name="type_advertisement" value="12" {if isset($advertisement) && $advertisement->type_advertisement == 12}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>
                        <label>
                            Button Column 2 position 1 (300X*)
                            <input type="radio" name="type_advertisement" value="21" {if isset($advertisement) && $advertisement->type_advertisement == 21}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            Button Column 2 position 2  (300X*)
                            <input type="radio" name="type_advertisement" value="22" {if isset($advertisement) && $advertisement->type_advertisement == 22}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>
                        <label>
                            Button Column 3 position 1 (300X*)
                            <input type="radio" name="type_advertisement" value="31" {if isset($advertisement) && $advertisement->type_advertisement == 31}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            Button Column 3 position 2  (300X*)
                            <input type="radio" name="type_advertisement" value="32" {if isset($advertisement) && $advertisement->type_advertisement == 32}checked="checked" {/if}/>
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
        <td colspan=2>
            <table>
                <tr>
                    <td>
                        <label>
                            Button Column 1 position 3  (200x200)
                            <input type="radio" name="type_advertisement" value="13" {if isset($advertisement) && $advertisement->type_advertisement == 13}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <label>
                            Button Column 3 position 3 (200x200)
                            <input type="radio" name="type_advertisement" value="33" {if isset($advertisement) && $advertisement->type_advertisement == 33}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            Center Left LeaderBoard (728X90)
                            <input type="radio" name="type_advertisement" value="3" {if isset($advertisement) && $advertisement->type_advertisement == 3}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <label>
                            Center Right LeaderBoard (234x90)
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
            <table>
                <tr>
                    <td>
                        <label>
                            Button Column 1 position 4 (300X*)
                            <input type="radio" name="type_advertisement" value="14" {if isset($advertisement) && $advertisement->type_advertisement == 14}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            Button Column 1 position 5  (300X*)
                            <input type="radio" name="type_advertisement" value="15" {if isset($advertisement) && $advertisement->type_advertisement == 15}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>
                        <label>
                            Button Column 2 position 4 (300X*)
                            <input type="radio" name="type_advertisement" value="24" {if isset($advertisement) && $advertisement->type_advertisement == 24}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            Button Column 2 position 5  (300X*)
                            <input type="radio" name="type_advertisement" value="25" {if isset($advertisement) && $advertisement->type_advertisement == 25}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>
                        <label>
                            Button Column 3 position 4 (300X*)
                            <input type="radio" name="type_advertisement" value="34" {if isset($advertisement) && $advertisement->type_advertisement == 34}checked="checked" {/if}/>
                        </label>
                        <br>
                        <label>
                            Button Column 3 position 5  (300X*)
                            <input type="radio" name="type_advertisement" value="35" {if isset($advertisement) && $advertisement->type_advertisement == 35}checked="checked" {/if}/>
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
            <table>
                <tr>
                    <td>
                        <label>
                            Button Column 1 position 6  (200x200)
                            <input type="radio" name="type_advertisement" value="16" {if isset($advertisement) && $advertisement->type_advertisement == 16}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <label>
                            Button Column 3 position 6 (200x200)
                            <input type="radio" name="type_advertisement" value="36" {if isset($advertisement) && $advertisement->type_advertisement == 36}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            Bottom Left LeaderBoard (728X90)
                            <input type="radio" name="type_advertisement" value="5" {if isset($advertisement) && $advertisement->type_advertisement == 5}checked="checked" {/if}/>
                        </label>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <label>
                            Bottom Right LeaderBoard (234X90)
                            <input type="radio" name="type_advertisement" value="6" {if isset($advertisement) && $advertisement->type_advertisement == 6}checked="checked" {/if}/>
                        </label>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</tbody>
</table>

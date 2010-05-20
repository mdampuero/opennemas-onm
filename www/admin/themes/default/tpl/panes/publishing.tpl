{* panes/publishing.tpl *}

<fieldset id="pane-publishing">
    
    {if isset($legend)}<legend>{$legend}</legend>{/if}
    
    <dl>
        <dt>
            <label>{t}Status{/t}</label>
        </dt>
        <dd>
            {* TODO: enhance with button switcher with iphone style *}
            <label for="status-available">
                {t}Available{/t}
                <input type="radio" name="status" id="status-available" value="AVAILABLE"
                    {if $content->status == 'AVAILABLE'}checked="checked"{/if}/>
            </label>
            
            <label for="status-pending">
                {t}Pending{/t}
                <input type="radio" name="status" id="status-pending" value="PENDING"
                    {if $content->status == 'PENDING'}checked="checked"{/if}/>
            </label>
        </dd>
        
        <dt>
            <label for="starttime">{t}Start publishing{/t}</label>
        </dt>
        <dd>
            <input type="text" name="starttime" id="starttime" value="{$content->starttime}"/>
        </dd>
        
        <dt>
            <label for="endtime">{t}End publishing{/t}</label>
        </dt>
        <dd>
            <input type="text" name="endtime" id="endtime" value="{$content->endtime}"/>
        </dd>
        
        <dt>
            <label for="published">{t}Published date{/t}</label>
        </dt>
        <dd>
            <input type="text" name="published" id="published" value="{$content->published}"/>
        </dd>
    </dl>
</fieldset>

{literal}
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
    var dtOptions = {
        askSecond: false,
        askEra: false
    };
    
    $("#starttime").AnyTime_picker(dtOptions);    
    $("#endtime").AnyTime_picker(dtOptions);    
    $("#published").AnyTime_picker(dtOptions);
});
/* ]]> */
</script>
{/literal}
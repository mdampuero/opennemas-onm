{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    .onm-help-block {
        position:absolute;
        top:10px;
        right:10px;
        width:400px
    }
    </style>
{/block}

{block name="content"}
<form action="{url name=admin_importer_xmlfile_config}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}XML file importer{/t} :: {t}Module configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}save.png"><br />
                        {t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_importer_xmlfile}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
            {render_messages}

            <div class="form-horizontal panel">
                <div class="control-group">
                    <label class="control-label" for="title">{t}Title:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="title" name="config[title]" value="{$title|default:"title"}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="title_int">{t}Inner title{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="title_int" name="config[title_int]" value="{$title_int|default:"title"}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="subtitle">{t}Pretitle:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="subtitle" name="config[subtitle]" value="{$subtitle|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="summary">{t}Summary:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" name="config[summary]" id="summary" value="{$summary|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="agency">{t}Agency:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="agency" name="config[agency]" value="{$agency|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="created">{t}Created:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="created" name="config[created]" value="{$created|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="metadata">{t}Metadata:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="metadata" name="config[metadata]" value="{$metadata|default:""}" />
                    </div>
                </div>
                <div>
                    <label class="control-label" for="agency">{t}Description:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="description" name="config[description]" value="{$description|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="category_name">{t}Section:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="category_name" name="config[category_name]" value="{$category_name|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="img">{t}Image:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="img" name="config[img]" value="{$img|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="img_footer">{t}Image Footer:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="img_footer" name="config[img_footer]" value="{$img_footer|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="body">{t}Body:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="body" name="config[body]" value="{$body|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="ignored">{t}Ignored labels:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="ignored" name="config[ignored]" value="{$ignored|default:""}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="ignored">{t}Important labels:{/t}</label>
                    <div class="controls">
						<input type="text" class="required" id="important" name="config[important]" value="{$important|default:""}" />
                    </div>
                </div>

            </div>
            <div class="onm-help-block">
				<div class="title"><h4>{t}Definition values{/t}</h4></div>
                <div class="content">
                    <ul>
                        <li>{t}Define the names of labels in the xml files that you want to fill in article fields{/t}</li>
                        <li>Atention: this rules are case sensitive</li>
                        <li>{t}The name can be one attribute or one label in XML file{/t}<br>
                        Example:<br>
                        &LT;FIELD NAME="Title"&GT; This is a title &LT;/FIELD&GT; <br>
                        &LT;title&GT; This is a title &LT;/title&GT;
                        </li>
                        <li>{t}Use ignored labels separated with commas{/t}</li>
                        <li>{t}Check important labels for write with bolder font{/t}</li>
                    </ul>
                </div>
            </div>
    </div>
</form>
{/block}

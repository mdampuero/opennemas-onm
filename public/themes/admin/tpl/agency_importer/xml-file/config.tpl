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
        <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-download"></i>
                            {t}XML importer{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>{t}Settings{/t}</h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_importer_xmlfile}" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i>
                                {t}Save{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        {render_messages}

        <div class="grid simple">
            <div class="grid-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label" for="title">{t}Title:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="title" name="config[title]" type="text" value="{$title|default:"title"}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="title_int">{t}Inner title{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="title_int" name="config[title_int]" type="text" value="{$title_int|default:"title"}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subtitle">{t}Pretitle:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="subtitle" name="config[subtitle]" type="text" value="{$subtitle|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="summary">{t}Summary:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" name="config[summary]" id="summary" type="text" value="{$summary|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="agency">{t}Agency:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="agency" name="config[agency]" type="text" value="{$agency|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="created">{t}Created:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="created" name="config[created]" type="text" value="{$created|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="metadata">{t}Metadata:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="metadata" name="config[metadata]" type="text" value="{$metadata|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="agency">{t}Description:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="description" name="config[description]" type="text" value="{$description|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="category_name">{t}Section:{/t}</label>
                            <div class="controls">
        						<input class="form required" id="category_name" name="config[category_name]" type="text" value="{$category_name|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="img">{t}Image:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="img" name="config[img]" type="text" value="{$img|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="img_footer">{t}Image Footer:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="img_footer" name="config[img_footer]" type="text" value="{$img_footer|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="body">{t}Body:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="body" name="config[body]" type="text" value="{$body|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ignored">{t}Ignored labels:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="ignored" name="config[ignored]" type="text" value="{$ignored|default:""}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ignored">{t}Important labels:{/t}</label>
                            <div class="controls">
        						<input class="form-control required" id="important" name="config[important]" type="text" value="{$important|default:""}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
        				<div class="title"><h4>{t}Definition values{/t}</h4></div>
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
        </div>
    </div>
</form>
{/block}

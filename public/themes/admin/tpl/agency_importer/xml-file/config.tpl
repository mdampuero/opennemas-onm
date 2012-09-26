{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        min-width:150px;
        padding-left:10px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        padding:10px;
    }
    .form-wrapper > div {
        margin-bottom:10px;
    }
    </style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE importer{/t} :: {t}Module configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{url name=admin_importer_xmlfile}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
   <form action="{url name=admin_importer_xmlfile_config}" method="POST">
        {render_messages}

        <table class="adminform">
            <tr>
                <td width="60%">
                    <div class="form-wrapper">
                        <div>
                            <label for="title">{t}Title:{/t}</label>
                            <input type="text" class="required" id="title" name="config[title]" value="{$title|default:"title"}" />
                        </div>
                        <div>
                            <label for="title_int">{t}Inner title{/t}</label>
                            <input type="text" class="required" id="title_int" name="config[title_int]" value="{$title_int|default:"title"}" />
                        </div>
                        <div>
                            <label for="subtitle">{t}Pretitle:{/t}</label>
                            <input type="text" class="required" id="subtitle" name="config[subtitle]" value="{$subtitle|default:""}" />
                        </div>
                         <div>
                            <label for="summary">{t}Summary:{/t}</label>
                            <input type="text" class="required" name="config[summary]" id="summary" value="{$summary|default:""}" />
                        </div>
                        <div>
                            <label for="agency">{t}Agency:{/t}</label>
                            <input type="text" class="required" id="agency" name="config[agency]" value="{$agency|default:""}" />
                        </div>
                        <div>
                            <label for="created">{t}Created:{/t}</label>
                            <input type="text" class="required" id="created" name="config[created]" value="{$created|default:""}" />
                        </div>
                         <div>
                            <label for="metadata">{t}Metadata:{/t}</label>
                            <input type="text" class="required" id="metadata" name="config[metadata]" value="{$metadata|default:""}" />
                        </div>
                        <div>
                            <label for="agency">{t}Description:{/t}</label>
                            <input type="text" class="required" id="description" name="config[description]" value="{$description|default:""}" />
                        </div>
                        <div>
                            <label for="category_name">{t}Section:{/t}</label>
                            <input type="text" class="required" id="category_name" name="config[category_name]" value="{$category_name|default:""}" />
                        </div>
                        <div>
                            <label for="img">{t}Image:{/t}</label>
                            <input type="text" class="required" id="img" name="img" value="{$img|default:""}" />
                        </div>
                        <div>
                            <label for="img_footer">{t}Image Footer:{/t}</label>
                            <input type="text" class="required" id="img_footer" name="img_footer" value="{$img_footer|default:""}" />
                        </div>
                        <div>
                            <label for="body">{t}Body:{/t}</label>
                            <input type="text" class="required" id="body" name="config[body]" value="{$body|default:""}" />
                        </div>
                        <hr>
                        <div>
                            <label for="ignored">{t}Ignored labels:{/t}</label>
                            <input type="text" class="required" id="ignored" name="config[ignored]" value="{$ignored|default:""}" />
                        </div>
                        <div>
                            <label for="ignored">{t}Important labels:{/t}</label>
                            <input type="text" class="required" id="important" name="config[important]" value="{$important|default:""}" />
                        </div>

                    </div>
                </td>
                <td>
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
                </td>
            </tr>
        </table>
        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button green">{t}Save{/t}</button>
            </div>
        </div>
   </form>
</div>
{/block}

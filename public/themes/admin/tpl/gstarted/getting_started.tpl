{extends file="base/admin.tpl"}
{block name="content"}
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}First steps in OpenNeMaS{/t}
                    </h4>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="content welcome-page">
    {render_messages}
    <div class="wrapper-content">
        <div class="row">
            <div class="span12">
                {include file="welcome/wizard.tpl"}
            </div>
        </div>
    </div>
</div>
{/block}

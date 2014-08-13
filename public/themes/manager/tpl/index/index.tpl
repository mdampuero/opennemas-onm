{extends file="base/base.tpl"}

{block name="content" append}
<div class="clearfix"></div>
<div class="content">
    <ul class="breadcrumb">
        <li>
            <p>{t}YOU ARE HERE{/t}</p>
        </li>
            <li><a href="#" class="active">{t}Dashboard{/t}</a>
        </li>
    </ul>
    <div class="page-title">
        <h2>{t}Welcome to OpenNeMas instance manager{/t}</h2>
    </div>

    <p>
        Here you will see some statistics about <strong>instances</strong> and other <br>
        awesome things that will blow out your imagination.
    </p>
</div>
{/block}

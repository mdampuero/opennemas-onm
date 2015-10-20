{extends file="emails/base.tpl"}

{block name="title"}{t}WELCOME TO OPENNEMAS{/t}{/block}

{block name="image"}
{image_tag src="assets/images/email/welcome/header_`$smarty.const.CURRENT_LANGUAGE_SHORT`.jpg" base_url="`$instance_base_url`" align="left" alt="" style="width:100%; max-width:600px; padding-bottom: 0; display: inline !important; vertical-align: bottom;"}
{/block}

{block name="content"}
<h1 style="text-align: center;font-size:31px; color: #222222;line-height: normal;">{t}Welcome to Opennemas{/t}!</h1>

<p>
  {t}Hi{/t} <strong>{$data['internal_name']}</strong>:
</p>

<p>
  {t}Your newspaper is already live:{/t}
</p>

<ul>
  <li>{t}Webpage:{/t} <a href="{$instance_base_url}/">{$instance_base_url}/</a></li>
  <li>{t}Admin panel:{/t} <a href="{$instance_base_url}/admin/">{$instance_base_url}/admin/</a></li>
</ul>

<p>{t}For security reasons we advise you to change your password after first login to Admin Panel.{/t}</p>

<p>
  {t escape=off}If you have doubts about frontpage management or need a quick walk through we have <strong>video demo</strong> 3 minutes long{/t}:
</p>

<ul>
  <li>
    <span>{t escape=off}How to create/edit <strong>articles</strong> and place them in a frontpage{/t} <br>
    <a href="https://youtu.be/UUNlwGeFgL0" target="_blank">https://youtu.be/UUNlwGeFgL0</a>
    </span>
  </li>
</ul>

<ul>
  <li>
    {t escape=off}How to upload and add&nbsp;<strong>images</strong>&nbsp;to your articles&nbsp;{/t}<br>
    <a href="https://youtu.be/PDYiS_mdx6k" target="_blank">https://youtu.be/PDYiS_mdx6k</a>
  </li>
</ul>

<ul>
  <li>
    {t escape=off}How to compose a&nbsp;<strong>frontpage</strong>{/t}
    <br>
    <a href="https://youtu.be/N40x_kPXLdU" target="_blank">https://youtu.be/N40x_kPXLdU</a>
  </li>
</ul>
<p>{t}We also have help page "First Steps in Opennemas" that should help{/t}:</p>
<ul>
  <li>
  <a href="http://help.opennemas.com/knowledgebase/articles/578289-opennemas-first-steps" rel="nofollow">{t}First Steps in Opennemas{/t}</a>

  </li>
</ul>
{/block}

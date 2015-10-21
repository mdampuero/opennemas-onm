{extends file="emails/base.tpl"}

{block name="title"}{t}Welcome to Opennemas! Learn how to publish your news in just few seconds!{/t}{/block}

{block name="image"}
{image_tag src="assets/images/email/welcome/header_`$smarty.const.CURRENT_LANGUAGE_SHORT`.jpg" base_url="`$instance_base_url`" align="left" alt="" width="564" style="max-width:600px; padding-bottom: 0; display: inline !important; vertical-align: bottom;"}
{/block}

{block name="content"}
<h1 class="null" style="text-align: center;"><span style="font-size:31px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif"><span style="color: #222222;line-height: normal;">{t}Welcome to Opennemas{/t}!</span></span></span></h1>

<p style="margin: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 18.1818180084229px;">
  <br>
  <span style="color:#000000"><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif"><span data-mce-style="line-height: 1.4285715;" style="line-height:1.4285715">{t}Hi{/t} <strong>{$data['internal_name']}</strong><span data-mce-style="line-height: 1.4285715;" style="line-height:1.4285715">:</span></span>
  </span>
  </span>
</p>

<div style="margin: 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 22.2222px;">
  <br>
  <span style="color:#000000"><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif">{t}Your newspaper is already live:{/t}</span></span>
  </span>
</div>

<div style="margin: 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 22.2222px;">
  <ul style="margin: 0px;">
    <li><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif"><span style="color:#000000">{t}Webpage:{/t} <a href="{$instance_base_url}/">{$instance_base_url}/</a>
      </span>
      </span>
      </span>
    </li>
    <li><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif"><span style="color:#000000">{t}Admin panel:{/t} <a href="{$instance_base_url}/admin/">{$instance_base_url}/admin/</a>
      </span>
      </span>
      </span>
    </li>
  </ul>
</div>

<p style="margin: 10px 0px 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 22.2222px;"><span style="color:#000000"><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif">{t}For security reasons we advise you to change your password after first login to Admin Panel.{/t}&nbsp;</span></span>
  </span>
</p>

<p style="margin: 10px 0px 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 22.2222px;">
  <br>

  <span style="color: #000000;font-family: trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif;line-height: 22.2222px;">{t escape=off}If you have doubts about frontpage management or need a quick walk through we have <strong>video demo</strong> 3 minutes long{/t}:</span>
</p>

<div style="margin: 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 22.2222px;">
  <ul style="color: #222222;font-family: arial, sans-serif;font-size: 12.8px;line-height: normal;">
    <li style="margin-left: 15px;"><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif">{t escape=off}How to create/edit <strong>articles</strong> and place them in a frontpage{/t} <br>
<a href="https://youtu.be/UUNlwGeFgL0" style="word-wrap: break-word;" target="_blank">https://youtu.be/UUNlwGeFgL0</a></span></span>
    </li>
  </ul>

  <ul style="color: #222222;font-family: arial, sans-serif;font-size: 12.8px;line-height: normal;">
    <li style="margin-left: 15px;"><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif">{t escape=off}How to upload and add&nbsp;<strong>images</strong>&nbsp;to your articles&nbsp;{/t}<br>
<a href="https://youtu.be/PDYiS_mdx6k" style="word-wrap: break-word;" target="_blank">https://youtu.be/PDYiS_mdx6k</a></span></span>
    </li>
  </ul>

  <ul style="color: #222222;font-family: arial, sans-serif;font-size: 12.8px;line-height: normal;">
    <li style="margin-left: 15px;"><span style="font-size:14px"><span style="font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif">{t escape=off}How to compose a&nbsp;<strong>frontpage</strong>{/t}<br>
<a href="https://youtu.be/N40x_kPXLdU" style="word-wrap: break-word;" target="_blank">https://youtu.be/N40x_kPXLdU</a></span></span>
    </li>
  </ul>

  <p style="margin: 0px 0px 0px 30px; padding: 0px;">&nbsp;</p>

  <p style="margin: 10px 0px 0px; padding: 0px;"><span style="color: #000000;line-height: 22.2222px;">{t}We also have help page "First Steps in Opennemas" that should help{/t}:</span><span style="line-height:22.2222px">&nbsp;</span>
  </p>
</div>
<div style="margin: 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 14px; line-height: 22.2222px;">
  <ul style="margin: 0px;">
    <li><a class="external-link" href="http://help.opennemas.com/knowledgebase/articles/578289-opennemas-first-steps" rel="nofollow" style="text-decoration: none;">http://help.opennemas.com/knowledgebase/articles/578289-opennemas-first-steps</a>
    </li>
  </ul>

  <p style="margin: 10px 0px 0px; padding: 0px;">&nbsp;</p>
</div>
{/block}

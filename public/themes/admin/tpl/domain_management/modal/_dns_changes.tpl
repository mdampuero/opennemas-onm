<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h3 class="modal-title">{t}How to setup this domain{/t}</h3>
  </div>
  <div class="modal-body">
    <p>{t}In order to get this domain to work properly with Opennemas you have to change some settings in your DNS record.{/t}</p>

    <h2>Update your DNS records</h2>
    <p>Go to your <a href="https://en.wikipedia.org/wiki/Domain_name_registrar" target="_blank">domain registar</a> page and log in the administration panel. Look for the DNS settings section.</p>

    <h4  class="semi-bold">1. Point www.[% template.item.name %] to the Opennemas service.</h4>
    <p>{t}Change www register in the domain area. In example change to www.domain.com (where "domain.com" would be your domain name). {/t}</p>
    <div class="p-l-30">
      <code class="m-t-20 m-b-20">www IN CNAME [% template.item.name %].opennemas.net.</code>
    </div>

    <h4  class="semi-bold">2. Redirect traffic from [% template.item.name %] to  www.[% template.item.name %]</h4>
    <p>Maybe your users access your site from the root domain (without www at the begining). If you want to get them redirected to www.[% template.item.name %] you can do it following the next steps.</p>
    <p>This change is <strong>NOT done through DNS</strong> but through the control panel settings where the domain and hosting are configured</p>
    <ul>
      <li>{t}Web Traffic -> domain.com -> redirect -> www.domain.com (this should be done by the hosting provider for your domain)  {/t}</li>
      <li>{t}IMPORTANT: It is only necessary to make the change log www. But making the change in Hosting, your newspaper will not have traffic to the domain without the www.{/t}</li>
    </ul>
  </div>
</div>

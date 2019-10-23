{extends file="base/admin.tpl"}
{block name="content"}
  <div  ng-controller="ContentListCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-book m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {t}Books{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="BOOK_CREATE"}
                <li>
                  <a class="btn btn-loading btn-success text-uppercase" href="{url name=admin_books_create}">
                    <i class="fa fa-plus m-r-5"></i>
                    {t}Create{/t}
                  </a>
                </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    {include file="book/partials/_book_list.tpl"}
  </div>
{/block}

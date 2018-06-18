.ad-left,
.ad-right {
  display: inline-block;
  float: none;
  text-align: center;
  vertical-align: top;
}

.ad-left + .ad-right {
  margin-left: 10px;
}

.ads,
.ad-skyscraper-left,
.ad-skyscraper-right {
  overflow: visible;
}

.ads-skyscraper-container {
  padding-left: 0;
  padding-top: 0;
}

.interstitial {
  background: #fff;
  bottom: 0;
  display: none;
  left: 0;
  position: fixed;
  right: 0;
  top: 0;
  z-index: 50000;
}

.interstitial-open {
  height: 100%;
  overflow: hidden;
}

.interstitial-visible {
  display: block;
}

.interstitial-wrapper {
  margin: 0 auto;
  max-width: 95%;
}

.interstitial-content {
  width: 100%;
}

.interstitial-content .oat:before {
  content: "";
}

.interstitial-content .oat > *:not(script) {
  margin: 0 !important
}

.interstitial-header {
  clear: both;
  display: table;
  padding: 8px 0;
  text-align: left;
  width: 100%;
}

.interstitial-close-button {
  cursor: pointer;
  float: right;
}

.interstitial-close-button:hover {
  text-decoration: underline;
}

.oat {
  clear: both;
  display: none;
  overflow: hidden;
  position: relative;
  text-align: center;
  visibility: hidden;
}

.oat:before {
  color: #838383;
  content: attr(data-mark);
  display: block;
  font-size: 10px;
  line-height: 15px;
  margin: 0 auto;
  position: absolute;
  text-align: center;
  text-transform: uppercase;
  width: 100%;
}

.oat > *:not(script) {
  display: block;
}

.oat img {
  height: auto;
  max-width: 100%;
}

.oat-container {
  margin: 0 auto;
  max-width: 100%;
  overflow: hidden;
}

.oat-content {
  border: none;
  margin: 0;
  overflow: hidden;
  padding: 0;
}

.oat-left > *,
.oat-right > * {
  padding-top: 0;
  margin-left: 0;
}

.oat-visible {
  display: block;
  visibility: visible;
}

.oat-bottom > * {
  margin-bottom: 15px;
}

.oat-left > * {
  margin-left: 15px !important;
}

.oat-right > * {
  margin-right: 15px !important;
}

.oat-top > * {
  margin-top: 15px !important;
}

.oat-bottom:before {
  position: absolute;
  margin-bottom: 0;
  bottom: 0;
}

.oat-left:before {
  left: 0;
  margin-left: -35px;
  margin-top: -10px;
  position: absolute;
  top: 50%;
  transform: rotate(-90deg);
}

.oat-right:before {
  right: 0;
  margin-left: auto;
  margin-right: -35px;
  margin-top: -10px;
  position: absolute;
  top: 50%;
  transform: rotate(90deg);
}

@media (max-width: 767px) {
  .oat.hidden-phone {
    display: none !important;
  }
}

@media (min-width: 768px) and (max-width: 991px) {
  .oat.hidden-tablet {
    display: none !important;
  }
}

@media (min-width: 992px) {
  .oat.hidden-desktop {
    display: none !important;
  }
}

.cookies-overlay button {
  background: none;
  border: none;
  margin: 0;
  padding: 5px;
  float: right;
  color: white;
  font-size: 20px;
  text-transform: uppercase;
  font-weight: bolder;
  font-style: normal;
}

.cookies-overlay p {
  width: 85%;
}

.cookies-overlay a {
  color: #fff;
  text-decoration: underline;
}

@media (max-width: 479px) {
  .cookies-overlay p {
    margin: 0;
    float: none;
    width: 100%;
    padding: 5px;
    font-size: .8em;
    line-height: 1.3em;
  }

  .interstitial-header-title {
    display: none;
  }

  .interstitial-header {
    text-align: center;
  }

  .interstitial-close-button {
    float: none;
  }
}

.qc-cmp-button {
  background-color: #007bff !important;
  border-color: #007bff !important;
}
.qc-cmp-ui-content {
  overflow-y: initial !important;
  padding: 30px !important;
}
.qc-cmp-consent-content,
.qc-cmp-purposes-page-content {
  text-align: left;
  max-width: 1024px;
  margin: 0 auto;
}
.qc-cmp-ui-container {
  background: initial !important;
  overflow-y: auto !important;
  bottom: auto !important;
}
.qc-cmp-ui-showing {
  overflow: auto !important;
}
.qc-cmp-toggle-status {
  color: #333 !important;
}
.qc-cmp-button:hover {
  background-color: #7faff7 !important;
  border-color: #7faff7 !important;
}
.qc-cmp-back {
  position: initial !important;
}
.qc-cmp-back:before {
  content: "\f104" !important;
  font-family: "Fontawesome";
  right: 0 !important;
  background: none !important;
  transform: none !important;
}
.qc-cmp-toggle {
  background-color: #007bff !important;
  border: 1px solid #007bff !important;
}
.qc-cmp-toggle-off {
  background-color: #ccc !important;
  border-color: #ccc !important;
}
.qc-cmp-toggle-switch {
  height: 14px !important;
  width: 14px !important;
}
.qc-cmp-alt-action,
.qc-cmp-link {
  color: #007bff !important;
}
.qc-cmp-button {
  color: #fff !important;
}
.qc-cmp-ui {
  background-color: #f5f5f5 !important;
  min-height: auto !important;
  overflow-y: initial !important;
}
.qc-cmp-ui,
.qc-cmp-ui .qc-cmp-main-messaging,
.qc-cmp-ui .qc-cmp-messaging,
.qc-cmp-ui .qc-cmp-beta-messaging,
.qc-cmp-ui .qc-cmp-title,
.qc-cmp-ui .qc-cmp-sub-title,
.qc-cmp-ui .qc-cmp-purpose-info,
.qc-cmp-ui .qc-cmp-table,
.qc-cmp-ui .qc-cmp-table-header,
.qc-cmp-ui .qc-cmp-vendor-list,
.qc-cmp-ui .qc-cmp-vendor-list-title {
  color: #333 !important;
}
.qc-cmp-publisher-purposes-table .qc-cmp-table-header,
.qc-cmp-vendors-purposes-table .qc-cmp-table-header {
  background-color: #fafafa !important;
}
.qc-cmp-publisher-purposes-table .qc-cmp-table-row,
.qc-cmp-vendors-purposes-table .qc-cmp-table-row {
  background-color: #ffffff !important;
}
.qc-cmp-alt-action,
.qc-cmp-button,
.qc-cmp-main-messaging,
.qc-cmp-messaging,
.qc-cmp-sub-title,
.qc-cmp-link,
.qc-cmp-privacy-settings-title,
.qc-cmp-vendor-list-title,
.qc-cmp-purpose-list,
.qc-cmp-tab,
.qc-cmp-title,
.qc-cmp-vendor-list,
.qc-cmp-bold-messaging,
.qc-cmp-table-header {
  font-family: Helvetica,Arial,sans-serif !important;
}
@media (max-width: 479px) {
  .qc-cmp-button,
  .qc-cmp-main-messaging,
  .qc-cmp-messaging,
  .qc-cmp-purpose-list,
  .qc-cmp-tab,
  .qc-cmp-vendor-list {
    font-size: 10px !important;
  }
  .qc-cmp-title,
  .qc-cmp-sub-title {
    font-size: 15px !important;
  }
  .qc-cmp-ui-content {
      padding: 5px 5px 15px 5px !important;
  }
  .qc-cmp-ui-container {
    bottom: 0 !important;
    top: auto !important;
  }
  .qc-cmp-main-messaging {
    padding: 5px !important;
  }
  .qc-cmp-button {
    height: 35px !important;
    max-width: 200px !important;
  }
  .qc-cmp-alt-buttons {
    padding-bottom: 15px !important;
  }
  .qc-cmp-alt-action {
    font-size: 12px !important;
  }
  .qc-cmp-toggle {
    height: 16px !important;
    width: 34px !important;
  }
}
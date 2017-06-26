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
  content: '{t}Advertisement{/t}';
  display: block;
  font-size: 10px;
  left: 50%;
  line-height: 15px;
  margin-left: -40px;
  margin-top: 0;
  position: absolute;
  text-align: center;
  text-transform: uppercase;
  width: 80px;
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
    float: none
  }
}
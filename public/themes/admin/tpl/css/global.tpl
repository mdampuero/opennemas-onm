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
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  z-index: 1000;
}

.interstitial-wrapper {
  margin: 0 auto;
}

.interstitial-content {
  width: 100%;
}

.interstitial-header {
  clear: both;
  display: table;
  padding: 8px 0;
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
  position: relative;
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
  margin-top: -15px;
  position: absolute;
  text-align: center;
  text-transform: uppercase;
  width: 80px;
}

.oat-container {
  margin: 0 auto;
  overflow: hidden;
}

.oat-content {
  border: none;
  margin: 0;
  overflow: hidden;
  padding: 0;
}

.oat-left,
.oat-rigth {
  padding-top: 0;
  margin-left: 0;
}

.oat-visible {
  display: block;
  visibility: visible;
}

.oat-bottom {
  margin-bottom: 15px;
}

.oat-top {
  margin-top: 15px;
}

.oat-bottom + .oat-top {
  margin-top: 30px;
}

.oat-bottom:before {
  position: absolute;
  margin-bottom: -15px;
  bottom: 0;
}

.oat-left:before {
  left: 0;
  margin-left: -50px;
  margin-top: -10px;
  position: absolute;
  top: 50%;
  transform: rotate(-90deg);
}

.oat-right:before {
  right: 0;
  margin-left: auto;
  margin-right: -45px;
  margin-top: -10px;
  position: absolute;
  top: 50%;
  transform: rotate(90deg);
}

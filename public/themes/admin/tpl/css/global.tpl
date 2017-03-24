.ads,
.ad-skyscraper-left,
.ad-skyscraper-right {
  overflow: visible;
}

.ads-skyscraper-container {
  padding-left: 0;
  padding-top: 0;
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
  margin-left: -40px;
  margin-top: -15px;
  position: absolute;
  text-align: center;
  text-transform: uppercase;
  width: 80px;
}

.oat-container {
  overflow: hidden;
}

.oat-content {
  border: none;
  margin: 0;
  overflow: hidden;
  padding: 0;
}

.oat-vertical {
  padding-top: 0;
  margin-left: 0;
}

.oat-visible {
  display: block;
  visibility: visible;
}

.oat-vertical:before {
  left: 0;
  margin-left: -50px;
  position: absolute;
  top: 50%;
  transform: rotate(-90deg);
}

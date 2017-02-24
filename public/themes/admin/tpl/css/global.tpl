.oat {
  color: #838383;
  display: none;
  font-size: 10px;
  text-align: center;
  text-transform: uppercase;
}

.oat:before {
  content: '{t}Advertisement{/t}';
  display: block;
  margin-bottom: 2px;
}

.oat-vertical:before {
  left: 0;
  margin-left: -10px;
  position: absolute;
  top: 50%;
  transform: rotate(-90deg);
}

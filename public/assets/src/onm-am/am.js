/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function(window) {
  'use strict';

  /**
   * @name OAM
   *
   * @description
   *   The OAM component provides actions to render advertisements in Opennemas
   *   newspapers.
   */
  var OAM = function() {
    /**
     * @memberOf OAM
     *
     * @description
     *  The configuration.
     *
     * @type {Object}
     */
    this.config = {};

    var self = this;

    // Initializes the advertisement manager.
    var load = function() {
      setTimeout(function() {
        self.config = window._onmaq;

        self.init();
      }, 1);
    };

    // Initialize the advertisement manager on load
    if (window.addEventListener !== undefined) {
      window.addEventListener('load', load, false);
    } else {
      window.attachEvent('onload', load);
    }
  };

  /**
   * @function close
   * @memberOf OAM
   *
   * @description
   *   Closes the interstitial advertisement.
   *
   * @param {Object} element The HTML element for interstitial.
   * @param {Object} e       The event object.
   */
  OAM.prototype.close = function(element, e) {
    var expires = new Date();

    expires.setMinutes(expires.getMinutes() + this.config.cookieLifetime);

    if (e) {
      e.stopPropagation();
      e.preventDefault();
    }

    document.cookie = '__onm_interstitial=1; expires=' + expires + '; path=/';

    element.remove();
  };

  /**
   * @function createInterstitial
   * @memberOf OAM
   *
   * @description
   *   Returns the HTML element to display an interstitial advertisement.
   *
   * @param {Object} ad The advertisement.
   *
   * @return {Object} The HTML element.
   */
  OAM.prototype.createInterstitial = function(ad) {
    var div = document.createElement('div');

    // TODO: Fix this shitty id (fuck you, frontenders!!!)
    div.id = 'intesticial-ad';

    // TODO: Remove style from <a> element (again, fuck you, frontenders!!!)
    div.innerHTML = '<div class="wrapper">' +
      '<style>body { height: 100%; overflow: hidden; }</style>' +
      '<div class="header">' +
        '<div class="logo-and-phrase">' +
          '<div class="logo"></div>' +
          this.config.strings.entering +
        '</div>' +
        '<div class="closeButton">' +
          '<a href="#" title="' + this.config.strings.skip + '" style="padding-right: 40px; width: auto;">' +
            '<span>' + this.config.strings.skip + '</span>' +
          '</a>' +
        '</div>'+
      '</div>'+
      '<div class="content"></div>' +
    '</div>';

    div.getElementsByClassName('closeButton')[0].onclick = function(e) {
      self.close(div, e);
    };

    var content = div.getElementsByClassName('content')[0];
    var iframe  = this.createNormal(ad);
    var self    = this;
    var size    = this.getSize(ad);

    // Hide interstitial after X seconds
    if (ad.timeout > 0) {
      iframe.onload = function() {
        window.setTimeout(function () {
          self.close(div);
        }, ad.timeout * 1000);
      };
    }

    content.style.width  = size.width + 'px';
    content.style.height = size.height + (size.height === 'auto' ? '' : 'px');

    content.appendChild(iframe);

    return div;
  };

  /**
   * @function createNormal
   * @memberOf OAM
   *
   * @description
   *   Returns the HTML element to display a normal advertisement.
   *
   * @param {Object}  ad       The advertisement.
   * @param {Integer} position The position where advertisement is rendered.
   *
   * @return {Object} The HTML element.
   */
  OAM.prototype.createNormal = function(ad, position) {
    var item = document.createElement('iframe');

    item.className    += 'oat-content';
    item.style.width   = '100%';
    item.style.height  = '100%';

    // Auto-resize on load
    item.onload = function() {
      item.style.height = item.contentWindow.document.body.scrollHeight + 'px';
    };

    item.src = this.normalize(this.config.url + '/' + ad.id);

    // Dispatch event when iframe loaded
    if (position) {
      item.onload = function () {
        var event = document.createEvent('Event');
        event.initEvent('oat-' + position + '-loaded', true, true);
        window.dispatchEvent(event);
      };
    }

    return item;
  };

  /**
   * @function displayInterstitial
   * @memberOf OAM
   *
   * @description
   *   Displays an interstitial advertisement.
   *
   * @param {Array} ads The list of advertisements to display.
   */
  OAM.prototype.displayInterstitial = function (ads) {
    // Display an interstitial if present
    var interstitials = ads.filter(function(e) {
      return e.type === 'interstitial';
    });

    if (!this.getCookie('__onm_interstitial') && interstitials.length > 0) {
      var ad = this.getAdvertisement(interstitials);

      document.getElementsByTagName('body')[0]
        .appendChild(this.createInterstitial(ad));
    }
  };

  /**
   * @function displayInterstitial
   * @memberOf OAM
   *
   * @description
   *   Displays all normal (non-interstitial) advertisements.
   *
   * @param {Array} ads The list of advertisements to display.
   */
  OAM.prototype.displayNormal = function(ads) {
    var self  = this;
    var slots = document.querySelectorAll('.oat');

    // Display normal advertisements
    slots.forEach(function(slot) {
      var type = parseInt(slot.getAttribute('data-type'));
      var id   = parseInt(slot.getAttribute('data-id'));

      var available = ads.filter(function(e) {
        if (id) {
          return e.id === id;
        }

        if (e.type !== 'normal' || !self.isVisible(e)) {
          return false;
        }

        return e.position.indexOf(type) !== -1;
      });

      // Remove advertisement marker if empty
      if (available.length === 0) {
        if (!self.config.debug) {
          slot.remove();
        }

        return;
      }

      var ad   = self.getAdvertisement(available);
      var size = self.getSize(ad);
      var div  = document.createElement('div');

      div.className  += 'oat-container';
      slot.className += ' oat-visible oat-' + type;

      div.style.width    = size.width + 'px';
      div.style.height   = size.height + (size.height === 'auto' ? '' : 'px');

      if (ad.orientation) {
        slot.className += ' oat-' + ad.orientation;
      }

      // TODO: Remove when no support sizes in templates
      if (self.device === 'desktop' && slot.getAttribute('data-width')) {
        div.style.width = parseInt(slot.getAttribute('data-width')) + 'px';
      }

      div.appendChild(self.createNormal(ad, type));
      slot.appendChild(div);
    });
  };

  /**
   * @function getAdvertisement
   * @memberOf OAM
   *
   * @description
   *   Returns an advertisement from a list of available advertisements or the
   *   advertisement with the given id if present.
   *
   * @param {Object}  advertisements The list of available advertisements.
   *
   * @return {Object} The selected advertisement.
   */
  OAM.prototype.getAdvertisement = function(advertisements) {
    return advertisements[Math.floor(Math.random() * advertisements.length)];
  };

  /**
   * @function getAdvertisements
   * @memberOf OAM
   *
   * @description
   *   Requests the list of advertisements to the server.
   */
  OAM.prototype.getAdvertisements = function() {
    var self = this;
    var req  = this.xhr();

    var url = this.normalize(this.config.url) +
      'places=' + this.config.slots.join() +
      '&category=' + this.config.category;

    req.open('GET', url, true);
    req.overrideMimeType('application/json');
    req.onreadystatechange = function() {
      if (req.readyState !== 4 || req.status !== 200) {
        return;
      }

      var ads = JSON.parse(req.response);

      self.displayNormal(ads);
      self.displayInterstitial(ads);
    };

    req.send();
  };

  /**
   * @function getCookie
   * @memberOf OAM
   *
   * @description
   *   Returns the cookie value.
   *
   * @param {String} name The cookie name.
   *
   * @return {String} The cookie value.
   */
  OAM.prototype.getCookie = function(name) {
    var cookies = document.cookie.split(';');
    var pattern = new RegExp('^' + name + '=.*');

    cookies = cookies.filter(function(e) {
      return pattern.test(e.trim());
    });

    if (cookies.length === 0) {
      return;
    }

    return cookies[0].trim().replace(name + '=', '');
  };

  /**
   * @function getDevice
   * @memberOf OAM
   *
   * @description
   *   Returns the device name basing on the window width.
   *
   * @return {String} The device name.
   */
  OAM.prototype.getDevice = function() {
    var width = window.innerWidth || document.documentElement.clientWidth ||
      document.getElementsByTagName('body')[0];

    if (width < 768) {
      return 'phone';
    }

    if (width < 992) {
      return 'tablet';
    }

    return 'desktop';
  };

  /**
   * @function getSize
   * @memberOf OAM
   *
   * @description
   *   Returns the slot size basing on the advertisement.
   *
   * @param {Object} ad The advertisement object.
   *
   * @return {Object} An object with height and width values for slot.
   */
  OAM.prototype.getSize = function(ad) {
    var device  = this.getDevice();

    var sizes = ad.sizes.filter(function(e) {
      return e.device === device;
    });

    if (sizes.length > 0) {
      return sizes[0];
    }

    return { height: 'auto', width: '100%' };
  };

  /**
   * @function getUser
   * @memberOf OAM
   *
   * @description
   *   Returns the user information from cookie.
   *
   * @return {Object} The user information.
   */
  OAM.prototype.getUser = function() {
    var user = this.getCookie('__onm_user');

    if (!user) {
      return null;
    }

    return JSON.parse(decodeURIComponent(user));
  };

  /**
   * @function normalize
   * @memberOf OAM
   *
   * @description
   *   Normalizes URL basing on current URL parameters.
   *
   * @param {String} url The URL to normalize.
   *
   * @return {String} The normalized URL.
   */
  OAM.prototype.normalize = function(url) {
    url += '?';

    if (parseInt(location.search.split('webview=').splice(1).join('')
          .split('&')[0]) === 1) {
      url += 'webview=1&';
    }

    return url;
  };

  /**
   * @function init
   * @memberOf OAM
   *
   * @description
   *   Initializes the advertisement manager.
   */
  OAM.prototype.init = function() {
    this.user   = this.getUser();
    this.device = this.getDevice();

    this.getAdvertisements();
  };

  /**
   * @function isVisible
   * @memberOf OAM
   *
   * @description
   *   Checks if an advertisement is visible basing on user and device
   *   information.
   *
   * @param {Object} ad The advertisement object.
   *
   * @return {Boolean} True if the advertisement is visible. False otherwise.
   */
  OAM.prototype.isVisible = function(ad) {
    var groups    = [];
    var now       = new Date();
    var endtime   = new Date(ad.endtime);
    var starttime = new Date(ad.starttime);

    if (now < starttime || (ad.endtime && now >= endtime)) {
      return false;
    }

    if (this.user) {
      groups = this.user.user_groups.filter(function(e) {
        return ad.user_groups.indexOf(parseInt(e)) !== -1;
      });
    }

    return ad.devices[this.device] === 1 &&
      (ad.user_groups.length === 0 || groups.length > 0);
  };

  /**
   * @function xhr
   * @memberOf OAM
   *
   * @description
   *   Returns a new XMLHttpRequest object.
   *
   * @return {Object} The XMLHttpRequest object.
   */
  OAM.prototype.xhr = function() {
    try {
      // Opera 8.0+, Firefox, Safari, Chrome
      return new XMLHttpRequest();
    } catch (e) {
      // Internet Explorer Browsers
      try {
        return new ActiveXObject('Msxml2.XMLHTTP');
      } catch (e) {
        try {
          return new ActiveXObject('Microsoft.XMLHTTP');
        } catch (e) {
          // Something went wrong
          throw 'Unable to create the request';
        }
      }
    }

    return false;
  };

  window.am = new OAM();
})(window);

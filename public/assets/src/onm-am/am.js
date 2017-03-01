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
   * @function configureInterstitial
   * @memberOf OAM
   *
   * @description
   *   Configures the interstitial advertisement.
   *
   * @param {Object} ad      The advertisement object.
   * @param {Object} element The HTML element.
   */
  OAM.prototype.configureInterstitial = function(ad, element) {
    // Hide interstitial after X seconds
    window.setTimeout(function () {
      element.remove();
    }, ad.timeout * 1000);

    // Removes the interstitial
    var close = function(e) {
      e.stopPropagation();
      e.preventDefault();

      element.remove();
    };

    // Opens the interstital in a new window
    var goTo = function(e) {
      e.stopPropagation();
      e.preventDefault();

      window.open('/ads/' + ad.publicId + '.html', '_blank');

      close(e);
    };

    element.getElementsByClassName('closeButton')[0].onclick = close;
    element.getElementsByClassName('content')[0].onclick     = goTo;

    var expires = new Date();

    expires.setMinutes(expires.getMinutes() + this.config.cookieLifetime);

    // Create cookie for interstitial
    document.cookie = '__onm_interstitial=1; expires=' + expires + '; path=/';
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

    div.getElementsByClassName('content')[0].appendChild(this.createNormal(ad));

    this.configureInterstitial(ad, div);

    return div;
  };

  /**
   * @function createNormal
   * @memberOf OAM
   *
   * @description
   *   Returns the HTML element to display a normal advertisement.
   *
   * @param {Object} ad The advertisement.
   *
   * @return {Object} The HTML element.
   */
  OAM.prototype.createNormal = function(ad) {
    var item = document.createElement('iframe');

    item.style.padding    = 0;
    item.style.width      = '100%';
    item.style.margin     = 0;
    item.style.border     = 'none';
    item.style.overflow   = 'hidden';

    item.src = this.normalize(this.config.url + '/' + ad.id);

    // Auto-resize on load
    item.onload = function() {
      item.style.height = item.contentWindow.document.body.scrollHeight + 'px';
    };

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
      var id = parseInt(slot.getAttribute('data-type'));

      var available = ads.filter(function(e) {
        return e.type === 'normal' &&
          parseInt(e.position) === parseInt(id) &&
          self.isVisible(e);
      });

      // Remove advertisement marker if empty
      if (available.length === 0) {
        if (!self.config.debug) {
          slot.remove();
        }

        return;
      }

      var ad = self.getAdvertisement(available);

      slot.appendChild(self.createNormal(ad));
      slot.style.width      = '100%';
      slot.style.display    = 'block';
      slot.style.visibility = 'visible';

      if (ad.orientation && ad.orientation === 'vertical') {
        slot.className += ' oat-vertical';
      }

      if (self.device === 'desktop' && slot.getAttribute('data-width')) {
        slot.style.width = parseInt(slot.getAttribute('data-width')) + 'px';
      }
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
   * @param {Integer} id             The advertisement id.
   *
   * @return {Object} The selected advertisement.
   */
  OAM.prototype.getAdvertisement = function(advertisements, id) {
    if (id) {
      var advertisement = advertisements.filter(function(e) {
        return e.id === id;
      });

      if (advertisement.length > 0) {
        return advertisement[0];
      }
    }

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

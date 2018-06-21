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

    var that = this;

    // Initializes the advertisement manager.
    var load = function() {
      setTimeout(function() {
        that.config = window._onmaq;

        that.init();
      }, 1);
    };

    // Initialize the advertisement manager on load
    that.addEventListener('load', load);
  };

  /**
   * @function addEventListener
   * @memberOf OAM
   *
   * @description
   *   Adds an event listener.
   *
   * @param {Function} callback The funcion to execute.
   */
  OAM.prototype.addEventListener = function(name, callback) {
    // Initialize the advertisement manager on load
    if (typeof window.addEventListener !== 'undefined') {
      window.addEventListener(name, callback, false);
    } else {
      window.attachEvent(name, callback);
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

    var slot = element.getElementsByClassName('oat');
    var id   = parseInt(slot[0].getAttribute('data-id'));

    if (!this.config.debug) {
      document.cookie = '__onm_interstitial-' + id + '=' + expires +
        ';expires=' + expires + ';path=/';
    }

    document.body.className = document.body.className
      .replace(' interstitial-open', '');

    if (element.parentNode) {
      element.parentNode.removeChild(element);
    }
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
    var that = this;

    div.innerHTML = '<div class="interstitial interstitial-visible">' +
      '<div class="interstitial-wrapper">' +
        '<div class="interstitial-header">' +
          '<span class="interstitial-header-title">' + this.config.strings.entering + '</span>' +
          '<a class="interstitial-close-button" href="#" title="' + this.config.strings.skip + '">' +
            '<span>' + this.config.strings.skip + '</span>' +
          '</a>' +
        '</div>' +
        '<div class="interstitial-content">' +
          '<div class="ad-slot oat oat-visible oat-' + ad.orientation + '" data-id="' + ad.id + '">' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';

    div.getElementsByClassName('interstitial-close-button')[0]
      .onclick = function(e) {
        that.close(div, e);
      };

    var oat     = div.getElementsByClassName('oat')[0];
    var wrapper = div.getElementsByClassName('interstitial-wrapper')[0];
    var iframe  = this.createNormal(ad);
    var size    = this.getSize(ad);

    // Hide interstitial after X seconds
    if (ad.timeout > 0) {
      iframe.onload = function() {
        window.setTimeout(function() {
          that.close(div);
        }, ad.timeout * 1000);
      };
    }

    wrapper.style.width = size.width + 'px';
    oat.style.height    = size.height + (size.height === 'auto' ? '' : 'px');

    oat.appendChild(iframe);

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
   * @param {Integer} index    The slot index in the list of slots.
   *
   * @return {Object} The HTML element.
   */
  OAM.prototype.createNormal = function(ad, position, index) {
    var item = document.createElement('iframe');

    item.className    += 'oat-content';
    item.style.width   = '100%';
    item.style.height  = '100%';

    item.src = this.normalize(this.config.url + '/' + ad.id);

    item.src += 'category=' + this.config.category +
      '&module=' + this.config.extension +
      '&adGroup=' + this.config.adGroup +
      '&contentId=' + this.config.contentId;

    // Dispatch event when iframe loaded
    item.onload = function() {
      if (typeof index !== 'undefined') {
        var event   = document.createEvent('Event');
        var content = item.contentWindow.document.body
          .getElementsByClassName('content')[0];

        event.args = {
          height: content.scrollHeight,
          width:  content.scrollWidth,
        };

        event.initEvent('oat-index-' + index + '-loaded', true, true);
        window.dispatchEvent(event);
      }

      if (position) {
        var event = document.createEvent('Event');

        event.initEvent('oat-' + position + '-loaded', true, true);
        window.dispatchEvent(event);
      }
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
  OAM.prototype.displayInterstitial = function(ads) {
    var that = this;

    // Display an interstitial if present
    var interstitials = ads.filter(function(e) {
      return e.type.indexOf('interstitial') !== -1 && that.isVisible(e);
    });

    if (interstitials.length === 0) {
      return;
    }

    var ad      = this.getAdvertisement(interstitials);
    var expires = new Date();
    var now     = new Date();

    if (this.getCookie('__onm_interstitial-' + ad.id)) {
      expires = new Date(this.getCookie('__onm_interstitial-' + ad.id));
    }

    if (expires <= now) {
      document.body.appendChild(this.createInterstitial(ad));
      document.body.className = document.body.className + ' interstitial-open';
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
    var that  = this;
    var slots = document.querySelectorAll('.oat');

    // Display normal advertisements
    for (var i = 0; i < slots.length; i++) {
      var slot = slots[i];
      var type = parseInt(slot.getAttribute('data-type'));
      var id   = parseInt(slot.getAttribute('data-id'));

      var available = ads.filter(function(e) { // eslint-disable-line no-loop-func
        return that.isVisible(e, type, id);
      });

      // Remove slot when no advertisement
      if (available.length === 0) {
        if (!that.config.debug) {
          slot.remove();
        }

        continue;
      }

      var ad   = that.getAdvertisement(available);
      var size = that.getSize(ad);
      var div  = document.createElement('div');

      div.className  += 'oat-container';
      slot.className += ' oat-visible oat-' + type;
      slot.id         = 'oat-index-' + i;
      slot.setAttribute('data-mark', ad.mark);

      div.style.width    = size.width + 'px';
      div.style.height   = size.height + (size.height === 'auto' ? '' : 'px');

      if (ad.orientation) {
        slot.className += ' oat-' + ad.orientation;
      }

      // TODO: Remove when no support sizes in templates
      if (that.config.device === 'desktop' && slot.getAttribute('data-width')) {
        div.style.width = parseInt(slot.getAttribute('data-width')) + 'px';
      }

      var item = that.createNormal(ad, type, i);

      // Resize container when content loaded
      var resize = function(e) { // eslint-disable-line no-loop-func
        var s = window.document.getElementById(e.type.replace('-loaded', ''));

        if (!s) {
          return;
        }

        var el = s.getElementsByClassName('oat-container')[0];

        if (e.args.height > 0 && e.args.width > 0) {
          el.style.height = e.args.height + 'px';
          el.style.width  = e.args.width + 'px';
        }
      };

      // Remove slot when no height
      var remove = function(e) { // eslint-disable-line no-loop-func
        if (e.args.height === 0) {
          var s = window.document.getElementById(e.type.replace('-loaded', ''));

          if (!s) {
            return;
          }

          s.remove();
        }
      };

      that.addEventListener('oat-index-' + i + '-loaded', resize);

      // Remove DFP slots when empty
      if (ad.format === 'DFP') {
        that.addEventListener('oat-index-' + i + '-loaded', remove);
      }

      div.appendChild(item);
      slot.appendChild(div);
    }
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
    var that = this;
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

      that.displayNormal(ads);
      that.displayInterstitial(ads);
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
      return null;
    }

    return cookies[0].trim().replace(name + '=', '');
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
    var device = this.config.device;

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
   * @function hideInterstitials
   * @memberOf OAM
   *
   * @description
   *   Displays interstitials already present in the HTML document.
   */
  OAM.prototype.hideInterstitials = function() {
    var that          = this;
    var now           = new Date();
    var interstitials = document.getElementsByClassName('interstitial');

    for (var i = 0; i < interstitials.length; i++) {
      var expires = new Date();
      var slot    = interstitials[i].getElementsByClassName('oat');
      var id      = parseInt(slot[0].getAttribute('data-id'));

      if (this.getCookie('__onm_interstitial-' + id)) {
        expires = new Date(this.getCookie('__onm_interstitial-' + id));
      }

      if (expires > now) {
        interstitials[i].remove();
      }
    }

    interstitials = document.getElementsByClassName('interstitial');

    if (interstitials.length > 0) {
      for (var i = 0; i < interstitials.length; i++) {
        var interstitial = interstitials[i];
        var timeout      = interstitial.getElementsByClassName('oat')[0]
          .getAttribute('data-timeout');

        interstitial.getElementsByClassName('interstitial-close-button')[0]
          .onclick = function(e) { // eslint-disable-line no-loop-func
            that.close(interstitial, e);
          };

        interstitial.className = interstitial.className +
          ' interstitial-visible';

        window.setTimeout(function() { // eslint-disable-line no-loop-func
          that.close(interstitial);
        }, timeout * 1000);
      }

      document.body.className = document.body.className + ' interstitial-open';
    }
  };

  /**
   * @function init
   * @memberOf OAM
   *
   * @description
   *   Initializes the advertisement manager.
   */
  OAM.prototype.init = function() {
    this.user = this.getUser();

    if (this.user && !this.user.advertisements) {
      return;
    }

    if (this.config.slots.length > 0) {
      this.getAdvertisements();
    }

    this.hideInterstitials();
  };

  /**
   * @function isVisible
   * @memberOf OAM
   *
   * @description
   *   Checks if an advertisement is visible basing on user, slot and device
   *   information.
   *
   * @param {Object}  ad   The advertisement object.
   * @param {Integer} type The advertisement position.
   * @param {Integer} id   The advertisement id.
   *
   * @return {Boolean} True if the advertisement is visible. False otherwise.
   */
  OAM.prototype.isVisible = function(ad, type, id) {
    if (id && id !== parseInt(ad.id)) {
      return false;
    }

    if (type && ad.position.indexOf(type) === -1) {
      return false;
    }

    /**
     * Change date format to work with all browsers (UTC timezone from server)
     * Before: 2017-08-23 13:38:00
     * After:  2017-08-23T13:38:00Z
     */
    if (ad.starttime) {
      ad.starttime = ad.starttime.replace(/\s+/g, 'T').concat('Z');
    }

    if (ad.endtime) {
      ad.endtime = ad.endtime.replace(/\s+/g, 'T').concat('Z');
    }

    var groups    = [];
    var now       = new Date();
    var endtime   = new Date(ad.endtime);
    var starttime = new Date(ad.starttime);

    if (now < starttime || ad.endtime && now >= endtime) {
      return false;
    }

    if (this.user) {
      groups = this.user.user_groups.filter(function(e) {
        return ad.user_groups.indexOf(parseInt(e)) !== -1;
      });
    }

    return ad.devices[this.config.device] === 1 &&
      (ad.user_groups.length === 0 || groups.length > 0);
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
      } catch (f) {
        try {
          return new ActiveXObject('Microsoft.XMLHTTP');
        } catch (g) {
          // Something went wrong
          throw new Error('Unable to create the request');
        }
      }
    }
  };

  window.am = new OAM();
})(window);

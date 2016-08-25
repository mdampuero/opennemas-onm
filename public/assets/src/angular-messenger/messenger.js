(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.messenger
   *
   * @description
   *   The `onm.messenger` module provides a service to show notifications
   *   (toasts) on the screen.
   */
  angular.module('onm.messenger', [])
    /**
     * @ngdoc service
     * @name  Messenger
     *
     * @description
     *   Service to display notifications (toast) on the screen.
     */
    .service('messenger',  ['$window',
      function ($window) {
        // Check if messenger is enabled
        if (!$window.Messenger) {
          throw 'Unable to load messenger';
        }

        this.hideAfter = 5;
        this.showCloseButton = true;

        /**
         * @function createMessage
         * @memberOf Messenger
         *
         * @description
         *   Creates a new message from a string.
         *
         * @param {String} str  The message text.
         * @param {String} type The message type.
         *
         * @return {Object} The message object.
         */
        this.createMessage = function(str, type) {
          return {
            id:      new Date().getTime(),
            message: str,
            type:    type ? type : 'info'
          };
        };

        /**
         * @function isValid
         * @memberOf Messenger
         *
         * @description
         *   Check if a message is valid.
         *
         * @param {Mixed} message The message to check.
         *
         * @return {Boolean} True if the message is valid. Otherwise, returns
         *                   false.
         */
        this.isValid = function(message) {
          if (typeof message === 'string' || (message instanceof Object &&
              message.hasOwnProperty('message'))) {
            return true;
          }

          return false;
        };

        /**
         * @function post
         * @memberOf Messenger
         *
         * @description
         *   Run messenger post action.
         *
         * @param {Mixed}  message The message to post.
         * @param {String} type    The message type.
         */
        this.post = function(message, type) {
          $window.Messenger.options = {
            extraClasses: 'messenger-fixed messenger-on-bottom'
          };

          // Array of messages
          if (message instanceof Array) {
            this.postMessages(message);
            return;
          }

          // Single message
          this.postMessage(message, type);
        };

        /**
         * @function postMessage
         * @memberOf Messenger
         *
         * @description
         *   Posts a message.
         *
         * @param {Object} message The message to post.
         * @param {String} type    The message type.
         */
        this.postMessage = function(message, type) {
          if (!this.isValid(message)) {
            return;
          }

          if (typeof message === 'string') {
            message = this.createMessage(message, type);
          }

          message.hideAfter       = this.hideAfter;
          message.showCloseButton = this.showCloseButton;

          this._post(message);
        };

        /**
         * @function postMessages
         * @memberOf Messenger
         *
         * @description
         *   Posts an array of messages.
         *
         * @param {Array} messages The array of messages.
         */
        this.postMessages = function(messages) {
          for (var i = 0; i < messages.length; i++) {
            this.postMessage(messages[i]);
          }
        };

        /**
         * @function _post
         * @memberOf Messenger
         *
         * @description
         *   Calls to Messenger library post.
         *
         * @param {Object} message The message to post.
         */
        this._post = function(message) {
          if (message.text) {
            $window.Messenger().post(message);
          }
        };

        return this;
      }
    ]);
})();

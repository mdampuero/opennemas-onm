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
    .service('messenger',  function () {
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
         * @function post
         * @memberOf Messenger
         *
         * @description
         *   Run messenger post action.
         *
         * @param mixed  message The message to post.
         * @param string type    The message type.
         */
        this.post = function(message, type) {
          Messenger.options = {
            extraClasses: 'messenger-fixed messenger-on-bottom'
          };

          if (typeof message === 'string') {
            message = this.createMessage(message, type);
          }

          // Array of messages
          if (message instanceof Array) {
            return this.postMessages(message, type);
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
         */
        this.postMessage = function(message) {
          message.hideAfter       = this.hideAfter;
          message.showCloseButton = this.showCloseButton;

          Messenger().post(message);
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

        return this;
    });
})();

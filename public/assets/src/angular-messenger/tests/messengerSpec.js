(function () {
  'use strict';

  describe('onm.Messenger', function() {
    describe('when messenger library is not initialized', function() {

      var messenger;

      // Create a fake window without Messenger library
      beforeEach(function() {
        var fakeWindow = {};

        module('onm.messenger', function($provide) {
          $provide.value('$window', fakeWindow);
        });
      });

      it('should throw an exception',
       inject(function($injector) {
        expect(function () {
          messenger = $injector.get('messenger');
        }).toThrow('Unable to load messenger');
      }));
    });

    describe('when messenger library is initialized', function() {
      var messenger;

      beforeEach(module('onm.messenger'));

      beforeEach(inject(function (_messenger_) {
        messenger = _messenger_;
      }));

      describe('and creating a message from string and type', function() {
        it('should return an object', function() {
          var msg = messenger.createMessage('Hello', 'success');

          expect(msg.message).toBe('Hello');
          expect(msg.type).toBe('success');
          expect(msg.id).not.toBe(null);
        });
      });

      describe('and checking a valid message', function() {
        it('should return true', function() {
          var valid = messenger.isValid('Hello');
          expect(valid).toBeTruthy();

          valid = messenger.isValid({ message: 'Hello' });
          expect(valid).toBeTruthy();

          valid = messenger.isValid({ message: 'Hello', type: 'error' });
          expect(valid).toBeTruthy();
        });
      });

      describe('and checking a non-valid message', function() {
        it('should return false', function() {
          var valid = messenger.isValid(1234);
          expect(valid).toBeFalsy();

          valid = messenger.isValid([]);
          expect(valid).toBeFalsy();

          valid = messenger.isValid({ text: 'Hello' });
          expect(valid).toBeFalsy();

          valid = messenger.isValid({ text: 'Hello', type: 'error' });
          expect(valid).toBeFalsy();
        });
      });

      describe('and posting a message from string', function() {
        it('should call to createMessage() function', function() {
          spyOn(messenger, 'createMessage').and.callThrough();
          spyOn(messenger, 'postMessage').and.callThrough();

          messenger.post('Hello', 'success');

          expect(messenger.createMessage).toHaveBeenCalledWith('Hello', 'success');
          expect(messenger.postMessage).toHaveBeenCalled();
        });
      });

      describe('and posting a message from a valid object', function() {
        it('should call to postMessage() function', function() {
          spyOn(messenger, 'postMessage').and.callThrough();
          spyOn(messenger, 'isValid').and.callThrough();

          messenger.post({ message: 'Hello', type: 'success' });

          expect(messenger.postMessage).toHaveBeenCalled();
        });
      });

      describe('and posting a message from a non-valid object', function() {
        it('should call to postMessage() function', function() {
          spyOn(messenger, '_post').and.callThrough();

          messenger.post({ text: 'Hello', type: 'success' });

          expect(messenger._post).not.toHaveBeenCalled();
        });
      });

      describe('and posting messages from an array of valid objects', function() {
        it('should call to postMessage() function 3 times', function() {
          spyOn(messenger, 'postMessage').and.callThrough();

          var messages = [
            { message: 'Hello', type: 'success' },
            { message: 'Hello', type: 'success' },
            { message: 'Hello', type: 'success' }
          ];

          messenger.post(messages);

          expect(messenger.postMessage.calls.count()).toEqual(3);
        });
      });

      describe('and posting messages from an array of valid and invalid objects', function() {
        it('should call to postMessage() function 3 times', function() {
          spyOn(messenger, 'postMessage').and.callThrough();
          spyOn(messenger, '_post').and.callThrough();

          var messages = [
            { message: 'Hello', type: 'success' },
            'asdf',
            { text: 'Hello', type: 'success' }
          ];

          messenger.post(messages);

          expect(messenger.postMessage.calls.count()).toEqual(3);
          expect(messenger._post.calls.count()).toEqual(2);
        });
      });
    });

  });
})();

/**
 * Onm.oql-encoder Module
 *
 * Angular module to convert internal filters to post-able filters.
 */
angular.module('onm.oqlEncoder', [])
  .service('Encoder', function() {
    return {
      encode: function(criteria) {
        var cleaned = {};

        for (var name in criteria) {
          if (typeof criteria[name] != 'undefined' &&
            criteria[name] != -1 &&
            criteria[name] !== ''
          ) {
            var operator = '=';
            var value    = [{
              operator: operator,
              value:    criteria[name]
            }];

            if (name.indexOf('_like') !== -1) {
              var values = criteria[name].split(' ');

              name  = name.substr(0, name.indexOf('_like'));
              value = [];
              for (var i = 0; i < values.length; i++) {
                value.push({
                  operator: 'LIKE',
                  value:    '%' + values[i] + '%'
                });
              }
            }

            if (name.indexOf('_regexp') !== -1) {
              name = name.substr(0, name.indexOf('_regexp'));

              value = [{
                operator: 'REGEXP',
                value: '(^' + criteria[name] + ',)|(' +
                  ',' + criteria[name] + ',)|(' +
                  criteria[name] + '$)'
              }];
            }

            cleaned[name] = value;
          }
        }

        return cleaned;
      }
    };
  }
);

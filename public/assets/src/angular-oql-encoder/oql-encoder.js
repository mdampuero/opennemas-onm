/**
*  onm.oql-encoder Module
*
* Angular module to convert internal filters to post-able filters.
*/
angular.module('onm.oqlEncoder', [])
  .service('oqlEncoder', function() {
    return {
      encode: function(criteria) {
        var cleaned = {};

        for (var name in criteria) {
          if (name != 'union') {
            for (var i = 0; i < criteria[name].length; i++) {
              if (criteria[name][i]['value']
                && criteria[name][i]['value'] != -1
                && criteria[name][i]['value'] !== ''
              ){
                var values = criteria[name][i]['value'].split(' ');

                if (name.indexOf('_like') !== -1 ) {
                  var shortName = name.substr(0, name.indexOf('_like'));
                  cleaned[shortName] = [];

                  for (var i = 0; i < values.length; i++) {
                    cleaned[shortName][i] = {
                      value:    '%' + values[i] + '%',
                      operator: 'LIKE'
                    };
                  }
                } else {
                  cleaned[name] = [];
                  for (var i = 0; i < values.length; i++) {
                    if (criteria[name]['operator']) {
                        switch(criteria[name]['operator']) {
                          case 'like':
                          cleaned[name][i] = {
                            value:    '%' + values[i] + '%',
                            operator: 'LIKE'
                          };
                          break;
                          case 'regexp':
                            cleaned[name][i] = {
                              value:    '(^' + values[i] + ',)|('
                                + ',' + values[i] + ',)|('
                                + values[i] + '$)',
                              operator: 'REGEXP'
                            };
                            break;
                          default:
                            cleaned[name][i] = {
                              value:    values[i],
                              operator: criteria[name]['operator']
                            };
                        }
                    } else {
                      cleaned[name][i] = {
                        value: values[i],
                      };
                    }
                  }
                }
              }
            }
          } else {
            cleaned[name] = criteria[name];
          }
        };

        return cleaned;
      }
    }
  }
);

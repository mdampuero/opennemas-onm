/**
 * Service to implement common actions related to item.
 *
 * @param Object $http        Http service
 * @param Object $location    Location service
 * @param Object $modal       Modal service
 * @param Object fosJsRouting Onm routing service.
 *
 * @return Object The item service.
 */
angular.module('onm.item', []).factory('itemService', function ($http, $location, $modal, fosJsRouting) {
    /**
     * The item service.
     *
     * @type Object
     */
    var itemService = {};

    /**
     * Redirects to the listing.
     *
     * @param string route The route name.
     */
    itemService.cancel = function(route) {
        var url = fosJsRouting.generate(route);
        $location.path(url);
    }

    /**
     * Cleans the criteria for the current listing.
     *
     * @param Object criteria The search criteria.
     *
     * @return Object The cleaned criteria.
     */
    itemService.cleanFilters = function (criteria) {
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

    /**
     * Deletes a plugin given its id.
     *
     * @param string  route The route name.
     * @param integer id    The item id.
     *
     * @return Object The response object.
     */
    itemService.delete = function (route, id) {
        var url = fosJsRouting.generate(route, { id: id });

        return $http.post(url).success(function (response) {
            return response;
        });
    };

    /**
     * Deletes a plugin given its id.
     *
     * @param string route    The route name.
     * @param array  selected The selected items.
     *
     * @return Object The response object.
     */
    itemService.deleteSelected = function (route, selected) {
        var url  = fosJsRouting.generate(route);
        var data = { selected: selected };

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Parses the current URL and initializes the current filters.
     */
    itemService.decodeFilters = function() {
        var params = $location.search();
        params = angular.copy(params);

        var filters = {};

        if (params.epp) {
            filters.epp = params.epp;
        }
        delete params.epp;

        if (params.page) {
            filters.page = params.page
        }
        delete params.page

        if (params.orderBy) {
            filters.orderBy = {};

            var orders = params.orderBy.split(',');

            for (var i = 0; i < orders.length; i++) {
                var order = orders[i].split(':');

                filters.orderBy[order[0]] = order[1];
            };

        }
        delete params.orderBy;

        var union = null;
        if (params.union) {
            union = params.union;
        }
        delete params.union;

        filters.criteria = {};
        var empty = 1;
        var pattern = /[a-z_]\d+/;
        for (var name in params) {
            var target = name;
            if (pattern.test(name)) {
                target = name.substr(0, name.lastIndexOf('_'));
            }

            if (!filters.criteria[target]) {
                filters.criteria[target] = [];
            }

            filters.criteria[target].push({ value: params[name] });
            empty = 0;

            delete params[name];
        }

        if (!empty && union) {
            filters.criteria.union = union;
        }

        return filters;
    }

    /**
     * Parses the current filters and updates the URL.
     *
     * @param Object  criteria The criteria to search by.
     * @param Object  orderBy  The order to use while searching.
     * @param integer epp      The elements per page.
     * @param integer page     The current page.
     * @param integer union    The operator to join filters.
     */
    itemService.encodeFilters = function(criteria, orderBy, epp, page, union) {
        var empty = true;
        for (var name in criteria) {
            empty = false;
            for (var i in criteria[name]) {
                if (criteria[name][i].value != '' && criteria[name][i].value != -1) {
                    $location.search(name + '_' + i, criteria[name][i].value);
                } else {
                    $location.search(name + '_' + i, null);
                }
            };
        }

        var order = null;
        for (var name in orderBy) {
            if (!order) {
                order = [];
            }

            order.push(name + ':' + orderBy[name]);
        }

        if (order) {
            order = order.join(',');
        }

        $location.search('orderBy', order);

        if (epp) {
            $location.search('epp', epp);
        }

        if (page) {
            $location.search('page', page)
        }

        $location.search('union', null);
        if (!empty && union) {
            $location.search('union', union)
        }
    }

    /**
     * Checks if the given name is available.
     *
     * @param string route The route name.
     * @param string name  The name to check.
     *
     * @return Object The response object.
     */
    itemService.isAvailable = function(route, name) {
        var url = fosJsRouting.generate(route);
        var data = { name: name };

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Returns a list of items.
     *
     * @param string route    The route name.
     * @param object criteria The parameters to search by.
     *
     * @return Object The response object.
     */
    itemService.list = function(route, data) {
        var url = fosJsRouting.generate(route);

        // Decode filters from URL and overwrite data
        var filters = itemService.decodeFilters();
        filters.criteria = itemService.cleanFilters(filters.criteria);

        // Merge data with filters from URL
        if (filters.criteria && !data.criteria) {
            data.criteria = {};
        }

        for(var name in filters.criteria) {
            data.criteria[name] = filters.criteria[name];
        }


        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Returns the template parameters to create a new item.
     *
     * @param string  route The route name.
     *
     * @return Object The response object.
     */
    itemService.new = function (route) {
        var url = fosJsRouting.generate(route);

        return $http.post(url).success(function (response) {
            return response;
        });
    };

    /**
     * Saves an item.
     *
     * @param  string id   The route name.
     * @param  object data The item data.
     *
     * @return Object The response object.
     */
    itemService.save = function(route, data) {
        var url = fosJsRouting.generate(route);

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Enables/disables an item.
     *
     * @param string  route The route name.
     * @param integer id The item id.
     * @param integer id The enabled value.
     *
     * @return Object The response object.
     */
    itemService.setEnabled = function (route, id, enabled) {
        var url = fosJsRouting.generate(route, { id: id });
        var data = { enabled: enabled };

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Enables/disables a list of elements.
     *
     * @param string  route    The route name.
     * @param Object  selected The selected elements.
     * @param integer enabled  The enabled value.
     *
     * @return Object The response object.
     */
    itemService.setEnabledSelected = function (route, selected, enabled) {
        var url = fosJsRouting.generate(route);
        var data = { enabled: enabled, selected: selected };

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Returns an item given its id.
     *
     * @param string  route The route name.
     * @param integer id The item id.
     *
     * @return Object The response object.
     */
    itemService.show = function (route, id) {
        var url = fosJsRouting.generate(route, { id: id });

        return $http.post(url).success(function (response) {
            return response;
        });
    };

    /**
     * Updates an item.
     *
     * @param  string id   The route name.
     * @param  mixed  id   The item id.
     * @param  object data The item data.
     *
     * @return Object The response object.
     */
    itemService.update = function(route, id, data) {
        var url = fosJsRouting.generate(route, { id: id });

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Executes a command and returns its name and output.
     *
     * @param  string route         The route name.
     * @param  mixed  command_name  The name of the command.
     * @param  object data          Additional data to execute the command.
     *
     * @return Object The response object.
     */
    itemService.executeCommand = function(route, command_name, data) {
        var parameters = {
            command_name: command_name,
            data: data
        };

        var url = fosJsRouting.generate(route, parameters);

        return $http.get(url).success(function (response) {
            return response;
        });
    };

    /**
     * Fetches the Zend Opcache.
     *
     * @param  string id   The route name.
     * @param  mixed  id   The item id.
     * @param  object data The item data.
     *
     * @return Object The response object.
     */
    itemService.fetchOpcacheStatus = function(route) {
        var url = fosJsRouting.generate(route);

        return $http.get(url).success(function (response) {
            return response;
        });
    };

    return itemService;
})

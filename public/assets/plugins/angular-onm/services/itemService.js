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
    itemService.list = function(route, criteria) {
        var url = fosJsRouting.generate(route);

        return $http.post(url, criteria).success(function (response) {
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

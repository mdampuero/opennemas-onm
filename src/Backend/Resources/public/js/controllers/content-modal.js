/**
 * Controller to handle modal actions.
 *
 * Note: Don't use array notation to inject dependencies as controller wouldn't
 *       recognize the rest of parameters.
 *
 * @param int    id       Selected item id.
 * @param int    index    Index of the selected content in contents array.
 * @param array  contents Array of contents.
 * @param string title     Selected item title.
 * @param array  selected Array of selected items.
 */
function ContentModalCtrl($http, $scope, $modalInstance, id, index, contents, title, selected) {
    $scope.id       = id;
    $scope.index    = index;
    $scope.contents = contents;
    $scope.title    = title;
    $scope.selected = selected;

    /**
     * Closes the current modal.
     */
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };

    /**
     * Deletes a content on confirmation.
     *
     * @param int    id    Item id.
     * @param int    index Index of the item in the array of contents.
     * @param string route Route title.
     */
    $scope.delete = function (id, index, route) {
        // Enable spinner
        $scope.deleting = 1;

        var url = Routing.generate(route, { id: id });
        $http.post(url).success(function(data) {
            if (data.status == 'OK') {
                $scope.contents.splice(index, 1);
                $modalInstance.close();
            }

            // Disable spinner
            $scope.deleting = 0;
        }).error(function(data) {
            // Disable spinner
            $scope.deleting = 0;
        });
    };

    /**
     * Deletes selected contents on confirmation.
     *
     * @param string route Route title.
     */
    $scope.deleteSelected = function (route) {
        // Enable spinner
        $scope.deleting = 1;

        var url = Routing.generate(route);
        $http.post(url, { ids: $scope.selected }).success(function(response) {
            if (response.status == 'OK') {
                // Remove selected items from contents array
                for (var i = 0; i < $scope.contents.length; i++) {
                    var j = 0;
                    while (j < $scope.selected.length
                        && $scope.contents[i].id != $scope.selected[j]
                    ) {
                        j++;
                    }

                    if (j < $scope.selected.length) {
                        $scope.contents.splice(i, 1);
                    }
                };
                $modalInstance.close();
            }

            // Disable spinner
            $scope.deleting = 0;
        }).error(function() {
            // Disable spinner
            $scope.deleting = 0;
        });
    };
}

// Register ModalCtrl function as AngularJS controller
angular.module('BackendApp.controllers').controller('ContentModalCtrl', ContentModalCtrl);

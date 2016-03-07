(function () {
  'use strict';

  angular.module('multiselect.tpl.html', []).run([
    '$templateCache',
    function($templateCache) {
      $templateCache.put('multiselect.tpl.html',
          "<div class=\"btn-group dropup\">\n" +
          "  <button type=\"button\" class=\"btn btn-default dropdown-toggle\" ng-click=\"toggleSelect()\" ng-disabled=\"disabled\" ng-class=\"{'error': !valid()}\">\n" +
          "    {{header}} <span class=\"caret\"></span>\n" +
          "  </button>\n" +
          "  <ul class=\"dropdown-menu\" ng-style=\"ulStyle\">\n" +
          "    <li class=\"toggle\" ng-show=\"multiple\">\n" +
          "      <button type=\"button\" class=\"btn-link btn-small\" ng-click=\"checkAll()\"><i class=\"fa fa-check-square-o\"></i>Check all</button>\n" +
          "      <button type=\"button\" class=\"btn-link btn-small\" ng-click=\"uncheckAll()\"><i class=\"fa fa-square-o\"></i> Uncheck all</button>\n" +
          "    </li>\n" +
          "    <li data-stopPropagation=\"true\" ng-repeat=\"i in items\">\n" +
          "      <a ng-click=\"select($event, i)\">\n" +
          "        <i class=\"glyphicon\" ng-class=\"{'glyphicon-ok': i.checked, 'empty': !i.checked}\"></i> {{i.label}}</a>\n" +
          "    </li>\n" +
          "  </ul>\n" +
          "</div>"
        );
    }
  ]);

})();

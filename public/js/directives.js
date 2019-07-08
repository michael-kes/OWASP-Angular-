/**
 * Directives
 */

var app = angular.module("app");

app.directive('header', function() {

    return {
        restrict: 'C', // A = Attribute, C = CSS Class, E = HTML Element, M = HTML Comment
        templateUrl: 'views/header.html',
        controller: 'HeaderController'
    };
});
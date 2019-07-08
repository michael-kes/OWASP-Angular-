/**
 * AngularJS controllers
 */

var app = angular.module("app");

// WorkshopController is de basis controller voor alle cases
app.controller('WorkshopController', function($scope, $state, CaseService, $timeout, localStorageService, SidebarService, OverlayService, PopupService) {

    $scope.pageTitle = 'Owasp';

    $scope.state = $state;
    $scope.SidebarService = SidebarService;
    $scope.overlay = OverlayService;

    $scope.popup = false;
    $scope.popupScore = 0;

    $scope.nextCase = function(){
        $state.go('workshop.case', {case:parseInt(CaseService.active().id) + 1});
    };

    // Deze functie wordt aangeroepen als de popup zichtbaar wordt
    PopupService.register(function() {

        $scope.popupScore = PopupService.score();
        $scope.popupTitle = PopupService.title();
        $scope.score = CaseService.score();

        $scope.popup = true;

        $timeout(function () {
            $scope.popup = false;
        }, 2000);

        if(CaseService.active().id != 5) {
            $timeout(function () {
                OverlayService.show('prevention');
            }, 2500);
        }
    });

    // Deze functie wordt aangeroepen als de case data wijzigt
    CaseService.register(function() {
        var active = CaseService.active();
        $scope.activeCase = active;
        $scope.sidebarTitle = active.name;
        $scope.sidebarStatus = active.status;
        $scope.sidebarTemplate = 'views/sidebar/'+active.templateUrl;
    });
});

// CasesController verschaft alle info over de cases
app.controller('CasesController', function($scope, CaseService)
{
    // Scope voorzien van alle case data
    $scope.cases = CaseService.data();
});

// HeaderController voor de header
app.controller('HeaderController', function($scope, CaseService, OverlayService, SidebarService) {

    // Toggle overlay
    $scope.toggleOverlay = OverlayService.toggle;

    // Toggle sidebar
    $scope.toggleSidebar = function() {

        // Als de overlay nog zichtbaar is deze eerst sluiten
        if (OverlayService.isVisible())
            OverlayService.hide();
        // Als de overlay niet zichtbaar is togglen we de sidebar
        else
            SidebarService.toggle();
    }

    // Get the score
    $scope.score = CaseService.score();

});

// OverlayController voor de overlay
app.controller('OverlayController', function($scope, OverlayService)
{
    $scope.overlayStyle = {};

    $scope.hideOverlay = OverlayService.hide;

    // Als de visibility van de overlay verandert wordt onderstaande functie aangeroepen
    OverlayService.register(function() {
        $scope.overlayStyle = { backgroundColor : OverlayService.page('color') }
        $scope.overlayTemplate = OverlayService.page('template');
    });
});


/*===============================
    Controllers voor elke case
=================================*/

/*
 * OWASP Attack 1) Injections
 * Het uitvoeren van een SQL injection
 */
app.controller('CaseA1Controller', function($scope, $http, PopupService, CaseService)
{
    // Init variabelen voor de View
    $scope.data = {};
    $scope.response = null;

    // Submit methode zodra het formulier wordt gesubmit
    $scope.submitForm = function()
    {
        // verkrijg de ID van de active case
        var active = CaseService.active();

        // stuur invoervelden van het formulier naar de PHP die
        // de Query zal uitvoeren op de SQLite file van de deelnemer
        $http.post('app/case1', $scope.data).success(function(data)
        {
            // controleer response data
            if (data.success)
            {
                // gebruik de services om de case af te ronden en de 'voltooid' animatie te tonen
                CaseService.done(active.id, data.success.score);
                PopupService.show(data.success.message, data.success.score);
                $scope.response = null;
            }
            else
            {
                $scope.response = data;
            }
        });
    }

});

/*
 * OWASP Attack 3) Reflected XSS
 * Het uitvoeren van een Reflected XSS aanval
 */
app.controller('CaseA3Controller', function ($scope, $window, $timeout, $sce, CaseService, PopupService, $http)
{
    // init variabelen
    $scope.messages = [];
    $scope.searchTerm = {};
    $scope.loading = false;

    //$scope.unsafe = null;         // voor het invoeren van alle JavaScript
    $scope.searchResult = false;    // tonen van de zoekresultaten

    // functie zodra de form wordt verstuurd
    $scope.submitForm = function()
    {
        // toon de loading gif
        $scope.loading = true;

        // maak een POST naar de webservice om
        // een nieuw bericht toe te voegen
        $http.post('app/case3', $scope.searchTerm).success(function(data)
        {
            if (data.message)
            {
                // we ontvangen het nieuwe bericht
                //$scope.searchTerm.unshift(data.message);
            }
            // Id ophalen van active case
            var active = CaseService.active();
            // toevoegen van de score
            if (data.success)
            {
                // strip de tags zodat alleen de ingevoerde string overblijft
                var array = $scope.searchTerm.message.split("(");

                var str = array[1].split(")")[0];
                // verwijder het eerste en het laatste character ( ' of " )
                var text = str.substring(1, str.length - 1);
                // voer een alert uit met behulp van de AngularJS $window dependency
                // timout om eerst van scherm te veranderen en dan de alert te laten zien

                $timeout(function() {
                    $window.alert(text);
                }, 100);

                $scope.searchTerm = "";

                CaseService.done(active.id, data.success.score);
                PopupService.show(data.success.message, data.success.score);
            }

            // leeg de velden van het formulier
            $scope.newMessage = {};
        })
            .error(function () {
                // POST van bericht is mislukt
            })
            .finally(function() {
                //$scope.unsafe = $sce.trustAsHtml($scope.searchTerm);  // toon de unsafe string
                $scope.searchResult = true;

                // stop met laden
                $scope.loading = false;
            });
    }
});

/*
 * OWASP Attack 3 variant 2) Stored XSS
 * Een kwestbare gastenboek waarin JavaScript kan worden ingevoerd
 */
app.controller('CaseA32Controller', function ($scope, $http, $sce, CaseService, PopupService)
{
    // init variabelen
    $scope.messages = [];
    $scope.newMessage = {};
    $scope.loading = true;

    // haal de berichten op van de database
    $http.get('app/case2').success(function(data)
    {
        // data ophalen is gelukt
        $scope.messages = data.messages.reverse();
    })
    .error(function() {
        // data ophalen is mislukt
    })
    .finally(function() {
        // error of gelukt verberg de loader
        $scope.loading = false;
    });

    // functie zodra de form wordt verstuurd
    $scope.submitForm = function()
    {
        // toon de loading gif
        $scope.loading = true;

        // maak een POST naar de webservice om
        // een nieuw bericht toe te voegen
        $http.post('app/case2', $scope.newMessage).success(function(data)
        {
            if (data.message)
            {
                // we ontvangen het nieuwe bericht
                $scope.messages.unshift(data.message);
            }
            // Id ophalen van active case
            var active = CaseService.active();
            // toevoegen van de score
            if (data.success)
            {
                CaseService.done(active.id, data.success.score);
                PopupService.show(data.success.message, data.success.score);
            }

            // leeg de velden van het formulier
            $scope.newMessage = {};
        })
        .error(function () {
            // POST van bericht is mislukt
        })
        .finally(function() {
            // stop met laden
            $scope.loading = false;
        });
    }

});

/*
 * OWASP Attack 4) Indirect Object Reference
 * Het simuleren van een IDOR door de querystring aan te passen
 * deze case wordt in zijn geheel gesimuleerd binnen Angular - geen communicatie naar DB via PHP
 */
app.controller('CaseA4Controller', function ($scope, $http, $sce, CaseService, PopupService)
{
    // initialiseer de variabelen voor de scope
    $scope.login = false;
    $scope.username = "Joe Doe";
    $scope.password = "topsecret ;)";
    $scope.fakeBrowserUrl = "https://www.fifa.com/login";
    $userNotFound = false;
    // fake user data om te tonen
    var users = [{
        name: "Joe Doe",
        accountNumber: "NL60 STEN 0532 8398 12",
        mail: "joedoe@stenden.com"
    },
    {
        name: "Sepp Blatter",
        accountNumber: "SW60 FIFA 0532 8398 12",
        mail: "sblatter@fifa.com"
    }];
    $scope.user = users[0];
    // de expressie waaraan de GET querystring moet voldoen
    // geldige string: ?userID=12&type=submit
    var pattern = /^\?userID=\d+(&type=submit)?$/i;
    var test = "halloo";

    // wijzig de status zodra je op login/logout klikt
    // in de front-end wordt dan automatisch de juiste div getoond
    // door middel van ng-show="login" of ng-hide="login"
    $scope.changeLogin = function ()
    {
        $scope.login = !$scope.login;
        if ($scope.login)
        {
            $scope.fakeBrowserUrl = "https://www.fifa.com/user";
            $scope.query = "?userID=1&type=submit";
        }
        else
        {
            $scope.fakeBrowserUrl = "https://www.fifa.com/login";
            $scope.query = "";
        }
    }

    // deze functie wordt uitgevoerd zodra het 'queryString' model wijzigt
    var testQueryString = function ()
    {
        // controleer of de expressie voldoet
        if (pattern.test($scope.query))
        {
            // strip de query om de userId op te halen
            var id = $scope.query.match(/\d+/)[0];
            // de ID moet in binnen de index van de array vallen
            if (id <= users.length && id > 0)
            {
                $scope.user = users[id - 1];
                $http.post('app/case4', id).success(function(data)
                    {
                        // Id ophalen van active case
                        var active = CaseService.active();
                        // toevoegen van de score
                        if (data.success)
                        {
                            CaseService.done(active.id, data.success.score);
                            PopupService.show(data.success.message, data.success.score);
                        }
                    })
                    .error(function () {
                        // POST van bericht is mislukt
                    })
                    .finally(function() {
                        // stop met laden
                        $scope.loading = false;
                    });
                $scope.userNotFound = false;
            }
            else
            {
                // gebruiker ID valt buiten het bereik van de array
                $scope.userNotFound = true;
            }
        }
        else
        {
            // queryString voldoet niet aan eisen
            $scope.userNotFound = true;
        }
    }

    // functie zodra de input 'queryString' wijzigt
    // deze controleert of de query getest moet worden
    $scope.queryStringChanged = function ($event)
    {
        // voer alleen een test uit zodra er op 'enter' wordt gedrukt
        if($event.keyCode == 13)
        {
            $scope.userNotFound = false;
            testQueryString();
        }
    }

});

/*
 * OWASP Attack 5) Security misconfiguration
 * Simuleren van misconfiguraties (standaard wachtwoorden, onbeveiligde admin toegang)
 */
app.controller('CaseA5Controller', function ($scope, $sce, $timeout, $http, CaseService, PopupService, OverlayService)
{
    // init variabelen bij openen
    $scope.login = false;
    $scope.username = "";
    $scope.response = "";
    $scope.rights = "You have no admin rights.";

    var user = {
        login: $scope.login,
        admin: false
    };

    // methode voor het tonen van de user details
    var showLoggedinPage = function()
    {
        if ($scope.login == true)
        {
            user['login'] = $scope.login;
            // variabelen om weer te geven
            $scope.username = "Sepp Blatter";
            $scope.message = "You are now logged in as : ";

            console.log(user);
            $http.post('app/case5', user).success(function(data)
            {
                    // Id ophalen van active case
                    var active = CaseService.active();
                    // toevoegen van de score
                    if (data.success)
                    {
                        CaseService.done(active.id, data.success.score);
                        PopupService.show(data.success.message, data.success.score);
                    }
                })
                .error(function () {
                    // POST van bericht is mislukt
                })
                .finally(function() {
                    // stop met laden
                    $scope.loading = false;
                });

        }
    }

    // functie die aangeroepen wordt bij submit
    $scope.update = function(user)
    {
        //set user door middel van informatie uit de form
        $scope.user = angular.copy(user);

        // controleer of de gegevens correct zijn
        // en log in, indien dit niet zo is, geeft daarvan een melding.
        if ($scope.user.email.toLowerCase() == 'sepp.blatter@fifa.com' && $scope.user.password == '10031936')
        {
            $scope.login = true;
            showLoggedinPage();
        }
        else
        {
            $scope.response = "Incorrect username or password!"
        }
    };

    // wijzig de staat van de pagina
    var changePage = function()
    {
        if($scope.query.toLowerCase() == 'admin')
        {
            $scope.rights = "You have admin rights.";

            user['admin'] = true;

            $http.post('app/case5', user).success(function(data)
                {
                    console.log(user);
                    // Id ophalen van active case
                    var active = CaseService.active();
                    // toevoegen van de score
                    if (data.success)
                    {
                        CaseService.done(active.id, data.success.score);
                        PopupService.show(data.success.message, data.success.score);
                    }
                })
                .error(function () {
                    // POST van bericht is mislukt
                })
                .finally(function() {
                    // stop met laden
                    $scope.loading = false;
                });

            $timeout(function () {
                OverlayService.show('prevention');
            }, 2500);
        }
        else if($scope.query == '')
        {
            $scope.rights = "You have no admin rights.";
            showLoggedinPage();
        }
        else
        {
            $scope.message = "";
            $scope.username = "ERROR 404: Page not found";
            $scope.rights = "";
        }
    }

    // deze methode wordt uitgevoerd zodra de model (string) van de queryString wijzigt
    $scope.queryStringChanged = function ($event)
    {
        // voer alleen een test uit zodra er op 'enter' wordt gedrukt
        if($event.keyCode == 13)
        {
            changePage();
        }
    }
});


/*
 * OWASP Attack 6) Sensitive data exposure
 */
app.controller('CaseA6Controller', function($scope, $http, PopupService, CaseService)
{
    // Init variabelen voor de View
    $scope.data = {};
    $scope.response = null;

});


/*
 * OWASP Attack 7 Missing funtion level access control
 */
app.controller('CaseA7Controller', function ($scope, $http, $sce, CaseService, PopupService)
{
    // init variabelen
    $scope.messages = [];
    $scope.newMessage = {};
    $scope.newMessage.author = "Joe Doe";
    $scope.query = '_user';
    $scope.loading = true;

    // haal de berichten op van de database
    $http.get('app/case7').success(function(data)
        {
            // data ophalen is gelukt
            $scope.messages = data.messages.reverse();
        })
        .error(function() {
            // data ophalen is mislukt
        })
        .finally(function() {
            // error of gelukt verberg de loader
            $scope.loading = false;
            console.log($scope.messages);
        });

    // functie zodra de form wordt verstuurd
    $scope.submitForm = function()
    {
        // toon de loading gif
        $scope.loading = true;

        // maak een POST naar de webservice om
        // een nieuw bericht toe te voegen
        $http.post('app/case7', $scope.newMessage).success(function(data)
            {
                if (data.message)
                {
                    // we ontvangen het nieuwe bericht
                    $scope.messages.unshift(data.message);
                }
                // Id ophalen van active case
                var active = CaseService.active();
                // toevoegen van de score
                if (data.success)
                {
                    CaseService.done(active.id, data.success.score);
                    PopupService.show(data.success.message, data.success.score);
                }

                // leeg de velden van het formulier
                $scope.newMessage = {};
                $scope.newMessage.author = "Joe Doe";
            })
            .error(function () {
                // POST van bericht is mislukt
            })
            .finally(function() {
                // stop met laden
                $scope.loading = false;
            });
    }

    $scope.showDelete = function(author){
        if($scope.query === '_admin') return true;
        return author === $scope.newMessage.author;
    }

    $scope.deleteMessage = function(message){

        message.delete = true;
        message.admin = ($scope.query === '_admin') ? true : false;

        $scope.loading = true;

        $http.post('app/case7', message).success(function(data)
            {
                // data ophalen is gelukt
                $scope.messages = data.messages.reverse();

                // Id ophalen van active case
                var active = CaseService.active();
                // toevoegen van de score
                if (data.success)
                {
                    CaseService.done(active.id, data.success.score);
                    PopupService.show(data.success.message, data.success.score);
                    console.log(data.success.author);
                }
            })
            .error(function () {
                // POST van bericht is mislukt
            })
            .finally(function() {
                // stop met laden
                $scope.loading = false;
            });
    }

});
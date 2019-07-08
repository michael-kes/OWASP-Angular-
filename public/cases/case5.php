<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">

        <title>Case 5</title>

        <!-- Styles -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/style-cases.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>

        <!-- Jquery -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row" id="mainarea">
                <div class="col-xs-1 col-md-3"></div>
                <div class="col-xs-10 col-md-6">
                    <form data-toggle="validator" role="form">
                        <div class="form-group">
                            <label for="Email">Email address</label>
                            <input type="email" class="form-control" id="Email" placeholder="Enter email" data-error="Bruh, that email address is invalid" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label for="Username">Username</label>
                            <input type="text" class="form-control" id="Username" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label for="Password1">Password</label>
                            <input type="password" class="form-control" id="Password1" placeholder="Enter Password" name="Password1" required>
                        </div>
                        <div class="form-group">
                            <label for="Password2">Password</label>
                            <input type="password" class="form-control" id="Password2" placeholder="Confirm Password" data-match="#Password1" data-match-error="Whoops, these don't match" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
                <div class="col-xs-1 col-md-3"></div>
            </div>
        </div>

        <!-- Bootstrap and Jquery -->
        <script src="../js/validator.js"></script>
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>
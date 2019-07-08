<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">

        <title>Case 1</title>

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
                <!-- left bootstrap block (for extra text or something -->
                <div class="col-xs-1 col-md-3"></div>
                <!-- Main block -->
                <div class="col-xs-10 col-md-6">
                    <form>
                        <div class="form-group">
                            <label for="Email">Email address</label>
                            <input type="email" class="form-control" id="Email" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="Password1">Password</label>
                            <input type="password" class="form-control" id="Password1" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="Textarea">Textarea</label>
                            <textarea class="form-control" id="Textarea" rows="3"></textarea>
                            <p class="help-block">Vul maar wat leuke tekst in deze textarea in.</p>
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
                <!-- right bootstrap block (for extra text or something -->
                <div class="col-xs-1 col-md-3"></div>
            </div>
        </div>

        <!-- Bootstrap and Jquery -->
        <script src="../js/bootstrap.min.js"></script>
    </body>
</html>
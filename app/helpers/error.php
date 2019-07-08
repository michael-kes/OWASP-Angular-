<!DOCTYPE html>
<html lang="en">
<head>
<title>Error</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

*{
margin:0;
padding:0;
}

body {
background-color: #f7f7f7;
font: 13px/20px normal 'Segoe UI', 'Open Sans', 'Roboto', Helvetica, Arial, sans-serif;
color: #4F5155;
}

#container a {
color: #003399;
background-color: transparent;
font-weight: normal;
}

#container h1 {
color: #444;
background-color: transparent;
border-bottom: 1px solid #D0D0D0;
font-size: 30px;
line-height:1.2;
font-weight: bold;
margin: -5px 0 14px 0;
padding: 0 15px 18px 13px;
}

#container h2 {
color: #444;
background-color: transparent;
font-size: 20px;
font-weight: 300;
margin: 0 0 14px 0;
padding: 0px 10px 2px 13px;
line-height:28px;
}

#container {
background:white;
margin: 10% auto 10%;
border: 1px solid #ddd;
border-radius:10px;
padding:35px;
max-width:500px;
box-shadow: 0 0 8px #DDD;
}

#container p {
color: #999;
margin:0;
padding: 10px 0 0 14px;
font-size:15px;
}

</style>
</head>
<body>
    <div id="container">
        <h1><?php echo $header; ?> on line <?php echo $line; ?></h1>
        <h2><?php echo $message; ?></h2>
        <p><?php echo substr($filepath, strlen(App::basePath())+1); ?></p>
    </div>
</body>
</html>
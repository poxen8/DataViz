<!DOCTYPE html>
<html lang="en">
<head>
  <title>Acceuil - DataViz</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="favicon.png">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="bootstrap-3.3.6-dist/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="index.php"><img src="favicon.png" height="25px" /></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="index.php">Acceuil</a></li>
        <li><a href="#">A Propos</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
      </ul>
    </div>
  </div>
</nav>
  
<div class="container-fluid text-center">    
  <div class="row content">
    <div class="col-sm-2 sidenav">
      <p><a href="#">Link1</a></p>
      <p><a href="#">Link2</a></p>
      <p><a href="#">Link3</a></p>
    </div>
    <div class="col-sm-10 text-left" style="text-align: center;"> 
      <h1>Welcome</h1>
	  
      <div style="text-align: center;margin-top: 180px;">
        <h4>Upload your XML Files Here:</h4>
        <form enctype="multipart/form-data" action="upload.php" method="post">
			<input type="hidden" name="MAX_FILE_SIZE" value="500000000" />
			<span class="btn btn-default btn-file"> <input name="userfile[]" type="file" multiple="multiple" > </span>
			<br/><br/>
			<input class="btn btn-primary" type="submit" name="submit" value="Envoyers les fichiers" />
		</form>
      </div>
	  
	  
    </div>
</div>

</body>
</html>

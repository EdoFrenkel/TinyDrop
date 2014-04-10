<?php
require_once("tinydrop.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Optimize Unity3D Atlases automatically from your server using PHP script - Dropbox and TinyPNG">
    <meta name="author" content="Edo Frenkel">

    <title>TinyDrop</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jumbotron-narrow.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/jumbotron-narrow.css" rel="stylesheet">

</head>

<body>
<a href="https://github.com/EdoFrenkel/TinyDrop" target="_blank"><img style="position: absolute; top: 0; right: 0; border: 0; width: 149px; height: 149px;" src="http://aral.github.com/fork-me-on-github-retina-ribbons/right-cerulean@2x.png" alt="Fork me on GitHub"></a>
<div class="container">
    <div class="header">
        <ul class="nav nav-pills pull-right">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="http://www.lightapps.co.il/blog" target="_blank">Docs</a></li>
        </ul>
        <h3 class="text-muted">TinyDrop:: TinyPNG && Dropbox</h3>
    </div>

    <div class="jumbotron">
        <h1>Select your folders...</h1>
        <p>
            <button type="button" class="btn-default btn-xs" id="checkall">Check All</button>
            <button type="button" class="btn-default btn-xs" id="uncheckall">Uncheck All</button>
        </p>
        <form class="form-horizontal" role="form" name="tinydrop" action="<?php echo $_SERVER['PHP_SELF'] ?>"
              method="post">
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <?php
                    for ($i = 0; $i < sizeof($name); $i++) {
                        ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="folder<?php echo $i; ?>"
                                       name="folder<?php echo $i; ?>"><?php echo $name[$i]; ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <br/>
            <div class="form-group">
                <label for="username" class="col-sm-3 control-label">User Name</label>

                <div class="col-sm-6">
                    <input type="text" class="form-control" id="username" name="username" placeholder="User Name">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-3 control-label">Password</label>

                <div class="col-sm-6">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
            </div>

            <p>
                <input class="btn btn-lg btn-success" name="startProcess" type="submit" role="button" value="Start Process" />
            </p>

            <?php if ($warning != "") { ?>
            <div class="alert alert-danger">
                <?php echo $warning; ?>
            </div>
            <?php } ?>
            <?php if ($error != "") { ?>
                <div class="alert alert-warning text-left">
                    <strong>Mmm...youv'e got some mistakes!</strong>
                    <br/>
                    <small>correct them in order to start the process...</small>
                    <br/><br/>
                    <?php echo $error; ?>
                </div>
            <?php } ?>
            <?php if ($message != "") { ?>
                <div class="alert alert-info text-left">
                    <strong>Yap...you did it!</strong>
                    <br/>
                    <small>Lets see the results...</small>
                    <br/><br/>
                    <?php echo $message; ?>
                </div>
            <?php } ?>
        </form>
    </div>

    <div class="footer">
        <p>Lightapps.co.il</p>
    </div>

</div>
<!-- /container -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/tinydrop.js"></script>
</body>
</html>
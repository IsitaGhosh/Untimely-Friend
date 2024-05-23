<?php
    ob_start();
    session_start();

    // First check user is already logged in or not
    if(isset($_SESSION['valid']) && $_SESSION['valid']){
        header("Location: ../index.php"); exit;
    }

    // Include database file
    require_once("../class/database.php");
    $db = new database();
    $settings = $db->getSettings();
    $settings = $settings[0];
    $isInValid = false;
    $controller = "login";
    $isPasswordResetSuccess = false;
    $resetEmailInvalid = false;
    $isResetTokenMatched = false;
    $changePasswordErrorMessage;

    // Forgot password
    if(isset($_GET['forgot_password']) && $_GET['forgot_password'] == 1){
        $controller = "forgot_password";
    } elseif(isset($_GET['reset_token']) && !empty($_GET['reset_token'])){
        $controller = "reset_token";
        $token = $_GET['reset_token'];

        // Now check reset token
        $checkToken = $db->checkResetToken($token);
        if($checkToken){
            $isResetTokenMatched = true;
        } else{
            $isResetTokenMatched = false;
        }
    } else{
        $controller = "login";
    }

    // Processing form submitted value
    if(isset($_POST['login_form']) && isset($_POST['email']) && isset($_POST['password'])){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $login = $db->userLogin($email, $password);

        if($login){
            $_SESSION['valid'] = true;
            $_SESSION['id'] = $login[0]['id'];
            $_SESSION['name'] = $login[0]['name'];
            $_SESSION['email'] = $login[0]['email'];
            $_SESSION['password'] = $login[0]['password'];
            $_SESSION['role'] = $login[0]['role'];

            // Check user role
            if($login[0]['role'] == "Admin"){
                header("Location: ../dashboard/index.php"); exit;
            } elseif($login[0]['role'] == "Customer"){
                // If login request from book page
                if(isset($_GET["redirect_to"]) && !empty($_GET["redirect_to"])){
                    header("Location: ".$_GET["redirect_to"]); exit;
                } else{
                    // Now redirect to home page
                    header("Location: ../index.php"); exit;
                }
                
            }

            
        } else{
            $isInValid = true;
        }
    }

    // Processing forgot password form data
    if(isset($_POST['forgot_password_form']) && isset($_POST['email'])){
        $email = $_POST['email'];

        // Now send reset password email
        $resetPass = $db->sendPasswordResetEmail($email);

        if($resetPass){
             $isPasswordResetSuccess = true;
        } else{
            $resetEmailInvalid = true;
        }
    }

    // Changing password
    if(isset($_POST['change_password']) && isset($_POST['password1']) && isset($_POST['password2']) && isset($_POST['token'])){
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];
        $token = $_POST['token'];

        if($password1 != $password2){
            $changePasswordErrorMessage = "Both Password Must Be Same";
        } else{
            $changePassword = $db->changePassword($password1, $token);

            // If password change success
            if($changePassword){
                header("Location: index.php"); exit;
            } else{
                $changePasswordErrorMessage = "There was an error";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
    ================================================== -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $settings["project_name"]; ?></title>

    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="">

    <!-- Mobile Specific Metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- Favicon
    ================================================== -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon.png">

    <!-- Stylesheets
    ================================================== -->
    <!-- Bootstrap core CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

    <header id="masthead" class="site-header">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php $projectName = explode(" ", $settings["project_name"]); echo $projectName[0]; ?></span><?php echo $projectName[1]; ?></a>

                </div><!-- /.navbar-header -->

                <div class="collapse navbar-collapse" id="agency-navbar-collapse">

                    <ul class="nav navbar-nav navbar-right">

                        <li><a href="../index.php">Home</a></li>
                        <!--
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages<i class="fa fa-caret-down hidden-xs" aria-hidden="true"></i></a>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                              <li><a href="portfolio.html">Portfolio</a></li>
                              <li><a href="blog.html">Blog</a></li>
                            </ul>

                        </li>
                        -->
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>

                    </ul>

                </div>

            </div>   
        </nav><!-- /.site-navigation -->
        <div class="login-seperator"></div>
    </header><!-- /#mastheaed -->

    <main id="main" class="site-main login-page">

        <?php if($controller == "login"): ?>
        <div class="global-container">
            <div class="card login-form">
            <div class="card-body">
                <h3 class="card-title text-center">Login</h3>
                <div class="card-text">
                    <!--
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">Incorrect username or password.</div> -->
                    <form method="POST">
                        <!-- to error: add class "has-danger" -->
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" class="form-control form-control-sm" id="exampleInputEmail1" aria-describedby="emailHelp" required="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <a href="?forgot_password=1" style="float:right;font-size:12px;">Forgot password?</a>
                            <input type="password" name="password" class="form-control form-control-sm" id="exampleInputPassword1" required="">
                        </div>
                        <?php if($isInValid): ?>
                            <div class="login-error">Email OR Password doesn't matched</div>
                        <?php endif; ?>
                        <button type="submit" name="login_form" class="btn btn-primary btn-block">Sign in</button>
                        
                        <div class="sign-up">
                            Don't have an account? <a href="../registration/index.php">Create One</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <?php elseif($controller == "forgot_password"): ?>
        <div class="global-container">
            <div class="card login-form">
            <div class="card-body">
                <h3 class="card-title text-center">Forgot Password</h3>
                <div class="card-text">
                    <!--
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">Incorrect username or password.</div> -->
                    <form method="POST">
                        <!-- to error: add class "has-danger" -->
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" class="form-control form-control-sm" id="exampleInputEmail1" aria-describedby="emailHelp" required="">
                        </div>
                        <?php if($resetEmailInvalid): ?>
                            <div class="login-error">Email not found</div>
                        <?php endif; ?>
                        <?php if($isPasswordResetSuccess): ?>
                            <div class="success-message">Password Reset Email Sent</div>
                        <?php endif; ?>
                        <button type="submit" name="forgot_password_form" class="btn btn-primary btn-block">Reset</button>
                        
                    </form>
                </div>
            </div>
        </div>
        </div>
        <?php else: ?>
        <div class="global-container">
            <div class="card login-form">
            <div class="card-body">
                <h3 class="card-title text-center"><?php if(!$isResetTokenMatched){ echo "Invalid Token"; } else{ echo "Change Password"; } ?></h3>
                <div class="card-text">
                    <!--
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">Incorrect username or password.</div> -->
                    <form method="POST">
                        <?php if($isResetTokenMatched): ?>
                            <div class="form-group">
                                <label for="exampleInputPassword1">New Password</label>
                                <input type="password" name="password1" class="form-control form-control-sm" id="exampleInputPassword1" required="">
                                <input type="hidden" name="token" value=<?php echo $_GET["reset_token"]; ?>>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Confirm Password</label>
                                <input type="password" name="password2" class="form-control form-control-sm" id="exampleInputPassword1" required="">
                            </div>
                            <?php if(isset($changePasswordErrorMessage)): ?>
                                <div class="login-error"><?php echo $changePasswordErrorMessage; ?></div>
                            <?php endif; ?> 
                            <button type="submit" name="change_password" class="btn btn-primary btn-block">Change</button>
                        <?php else: ?>
                            <div class="login-error">Reset Token doesn't matched</div>
                        <?php endif; ?>
                        
                    </form>
                </div>
            </div>
        </div>
        </div>
        <?php endif; ?>

    </main><!-- /#main -->

    <footer class="login-footer">
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <p>&copy; ProjectTitle | All Rights Reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.copyright -->
    </footer><!-- /#footer -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/bootstrap-select.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>
    <script src="../assets/js/script.js"></script>
  
</body>
</html>
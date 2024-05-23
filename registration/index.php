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
    $isError = false;
    $errorMessage;

    // Processing form submitted value
    if(isset($_POST['signup_form']) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['phone']) && !empty($_POST['address'])){

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $checkEmail = $db->checkEmail($email);

        if($checkEmail){
            $isInValid = true;
        } else{
            $createUser = $db->createRecords($db->userTable, array("name", "email", "password", "role"), array($name, $email, md5($password), "Customer"));

            if($createUser){
                $addCustomerInfo = $db->createRecords($db->customerInfoTable, array("user_id", "phone", "address"), array($createUser, $phone, $address));

                $_SESSION['valid'] = true;
                $_SESSION['id'] = $createUser;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                $_SESSION['role'] = "Customer";

                // My redirect to my account page
                header("Location: ../my-account.php"); exit;
                
            } else{
                $isError = true;
                $errorMessage = "Failed to create customer account";
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

    <header id="masthead" class="site-header position-relative">
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
        <div class="global-container">
            <div class="card login-form">
            <div class="card-body">
                <h3 class="card-title text-center">Registration</h3>
                <div class="card-text">
                    <!--
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">Incorrect username or password.</div> -->
                    <form method="POST">
                        <?php if($isInValid): ?>
                            <div class="login-error">An account has already been opened with this email.</div>
                        <?php endif; ?>
                        <!-- to error: add class "has-danger" -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" id="name" required="">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm" id="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control form-control-sm" id="password" required="">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" name="phone" class="form-control form-control-sm" id="phone" required="">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" class="form-control form-control-sm" id="address" required="">
                        </div>
                        <button type="submit" name="signup_form" class="btn btn-primary btn-block">Sign Up</button>
                        
                        <div class="sign-up">
                            Already have an account? <a href="../login/index.php">Login</a>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </main><!-- /#main -->
    <div class="margin-top"></div>
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
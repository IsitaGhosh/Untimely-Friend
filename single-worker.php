<?php
    
    session_start();
    $isLoggedIn = false;
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : "";

    // First check user is already logged in or not
    if(isset($_SESSION['valid']) && $_SESSION['valid']){
        $isLoggedIn = true;
    }

    // Include database file
    require_once("class/database.php");
    $db = new database();

    $settings = $db->getSettings();
    $settings = $settings[0];
    $singleWorker = false;

    // Get all categories for dropdown
    if(isset($_GET["worker_id"]) && !empty($_GET["worker_id"])){
        $singleWorker = $db->getSocialWorkerById($_GET["worker_id"]);
        if($singleWorker){
            $singleWorker = $singleWorker[0];
        }
    }


?>
<?php include("header.php"); ?>

    <header id="masthead" class="site-header">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php $projectName = explode(" ", $settings["project_name"]); echo $projectName[0]; ?></span><?php echo $projectName[1]; ?></a>

                </div><!-- /.navbar-header -->

                <div class="collapse navbar-collapse" id="agency-navbar-collapse">

                    <ul class="nav navbar-nav navbar-right">

                        <li><a href="index.php">Home</a></li>
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
                        <li><?php if($role == "Admin"): ?><a href="dashboard">Dashboard</a><?php elseif($role == "Customer"): ?> <a href="my-account.php">My Account</a> <?php endif; ?></li>
                        <li><?php if($isLoggedIn): ?><a href="logout.php">Log Out</a><?php else: ?> <a href="login">Login</a> <?php endif; ?></li>

                    </ul>

                </div>

            </div>   
        </nav><!-- /.site-navigation -->
    </header><!-- /#mastheaed -->

     <div id="hero" class="hero overlay subpage-hero contact-hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1><?php if($singleWorker){ echo $singleWorker["name"]; } else{ echo "Wrong Worker ID"; } ?></h1>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                  <li class="breadcrumb-item active">Single Worker</li>
                </ol>
            </div><!-- /.hero-text -->
        </div><!-- /.hero-content -->
    </div><!-- /.hero -->

    <main id="main" class="site-main">
        <section class="site-section section-services gray-bg">
            <div class="container white-bg padding-top-bottom">
                <div class="row">
                    <?php if($singleWorker): ?>
                    <div class="col-sm-4">
                        <img class="img-thumbnail single-worker-photo" src="<?php echo "uploads/".$singleWorker["photo"]; ?>" alt="">
                    </div>
                    <div class="col-sm-8">
                        <h3 class="social-worker-name"><?php echo $singleWorker["name"]; ?></h3>
                        <div class="seperator"></div>
                        <h5>Phone: <a title="Call Now" href="tel:<?php echo $singleWorker["phone"]; ?>"><?php echo $singleWorker["phone"]; ?></a></h5>
                        <h5>Email: <a href="mailto:<?php echo $singleWorker["email"]; ?>"><?php echo $singleWorker["email"]; ?></a></h5>
                        <h5>Full Address: <a target="_blank" href="http://maps.google.com/?q=<?php echo $singleWorker["address"]; ?>"><?php echo $singleWorker["address"]; ?></a></h5>
                        <p>Service Description: <?php echo $singleWorker["description"]; ?></p>
                        <a href="book.php?worker_id=<?php echo $singleWorker["id"]; ?>" class="btn btn-border mb-10">Book Now</a>
                    </div>
                    <?php else: ?>
                    <div class="error-text">Invalid Social Worker ID</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main><!-- /#main -->

    <footer id="colophon" class="site-footer">
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <p>&copy; <?php echo $settings["project_name"]; ?> | All Rights Reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.copyright -->
    </footer><!-- /#footer -->

<?php include("footer.php"); ?>
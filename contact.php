<?php
    
    session_start();
    $isLoggedIn = false;
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : "";
    $id = isset($_SESSION['id']) ? $_SESSION['id'] : "";

    // First check user is already logged in or not
    if(isset($_SESSION['valid']) && $_SESSION['valid']){
        $isLoggedIn = true;
    }

    // Include database file
    require_once("class/database.php");
    $db = new database();

    $settings = $db->getSettings();
    $settings = $settings[0];
    
    $sendEmail = false;

    // Processing form data
    if(isset($_POST["contact_us"]) && !empty($_POST["name"]) && !empty($_POST["email"]) && !empty($_POST["subject"]) && !empty($_POST["message"])){
        
        $sendEmail = $db->sendEmail($_POST["email"], $_POST["subject"], $_POST["message"]);

        if($sendEmail){
            $sendEmail = true;
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
                        <li class="active"><a href="about.php">About Us</a></li>
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
                <h1>About Us</h1>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                  <li class="breadcrumb-item active">About Us</li>
                </ol>
            </div><!-- /.hero-text -->
        </div><!-- /.hero-content -->
    </div><!-- /.hero -->

    <main id="main" class="site-main">
        <section class="site-section subpage-site-section section-contact-us">
            <div class="container">
                <div class="row">
                    <?php if($sendEmail): ?>
                    <div class="col-md-12">
                        <div class="success-text">
                            Thanks for contacting with us. We received your query.
                        </div>
                        <div class="margin-top"></div>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-7">
                        <h2>Send a message</h2>
                        <form method="POST">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                      <label for="name">Name:</label>
                                      <input type="text" name="name" class="form-control" id="name" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                      <label for="email">E-mail:</label>
                                      <input type="email" name="email" class="form-control" id="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                              <label for="subject">Subject:</label>
                              <input class="form-control" name="subject" id="subject" required></input>
                            </div>
                            <div class="form-group">
                              <label for="message">Message:</label>
                              <textarea name="message" class="form-control form-control-comment" id="message" required></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="contact_us" class="btn btn-green">Contact us</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-5">
                        <div class="contact-info">
                            <h2>Contact information</h2>
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3>Address</h3>
                                    <ul class="list-unstyled">
                                        <li><?php echo $settings["address"]; ?></li>
                                    </ul>
                                    <h3>E-mail</h3>
                                    <a href="mailto:<?php echo $settings["email"]; ?>" target="_blank"><?php echo $settings["email"]; ?></a>
                                    <h3>Phone</h3>
                                    <a href="tel:<?php echo $settings["phone"]; ?>" target="_blank"><?php echo $settings["phone"]; ?></a>
                                </div>
                            </div>
                        </div><!-- /.contact-info -->
                    </div>
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
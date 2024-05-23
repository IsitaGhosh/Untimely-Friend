<?php
    
    session_start();
    $isLoggedIn = false;
    $isCustomer = false;
    $orderPlaced = false;
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : "";
    $id = isset($_SESSION['id']) ? $_SESSION['id'] : "";
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : "";

    // First check user is already logged in or not
    if(isset($_SESSION['valid']) && $_SESSION['valid']){
        $isLoggedIn = true;
    }

    // Check the user role
    if($role == "Customer"){
        $isCustomer = true;
    }

    // Include database file
    require_once("class/database.php");
    $db = new database();

    $settings = $db->getSettings();
    $settings = $settings[0];
    $singleWorker = false;
    $customerInfo = false;

    // Get all categories for dropdown
    if(isset($_GET["worker_id"]) && !empty($_GET["worker_id"])){
        $singleWorker = $db->getSocialWorkerById($_GET["worker_id"]);
        if($singleWorker){
            $singleWorker = $singleWorker[0];
        }
    } else{
        die("Worker ID doesn't exist");
    }

    // Get logged in customer info
    $customerInfo = $db->getCustomerInfoById($id);
    if($customerInfo){
        $customerInfo = $customerInfo[0];
    }

    // Confirm Booking
    if(isset($_GET["worker_id"]) && !empty($_GET["worker_id"]) && isset($_GET["confirm"]) && $_GET["confirm"] == "true"){
        $workerId = $_GET["worker_id"];
        $customerId = $customerInfo["id"];
        
        $createOrder = $db->createRecords($db->orderTable, array("worker_id", "customer_id", "status"), array($workerId, $customerId, "order_placed"));
        if($createOrder){
            // Send confirmation email to customer
            $customerEmailBody = "Congratulation, with this email we confirm that your booking is complete.";
            $db->sendEmail($customerInfo["email"], "Booking Confirmation", $customerEmailBody);
            // Send confirmation email to social worker
            $socialWorkerEmailBody = 'You have been booked from '.$settings["project_name"].'. Customer details are given below. <br><br> Customer Name: '.$customerInfo["name"].' <b> Phone No: '.$customerInfo["phone"].' <b> Full Address: '.$customerInfo["address"].'';
            $db->sendEmail($email, "New Booking Notification", $socialWorkerEmailBody);

            $orderPlaced = true;
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
                <h1><?php if($singleWorker && !$orderPlaced){ echo "Confirm Booking"; } elseif($orderPlaced){ echo "Success"; } else{ echo "Wrong Worker ID"; } ?></h1>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                  <li class="breadcrumb-item active">Confirm Booking</li>
                </ol>
            </div><!-- /.hero-text -->
        </div><!-- /.hero-content -->
    </div><!-- /.hero -->

    <main id="main" class="site-main">
        <section class="site-section section-services gray-bg">
            <?php if($isLoggedIn): ?>
            <div class="container padding text-center">
                <div class="row">
                    <div class="col-md-12">
                       <?php if($isLoggedIn && $role == "Admin"): ?>
                        <h5 class="red-text">Sorry, <?php echo $role; ?> Can not Booking.</h5>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="margin-top"></div>
            <div class="margin-top"></div>
            <?php else: ?>
            <div class="container padding text-center">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="red-text">You have to <a href="login/?redirect_to=../book.php?worker_id=<?php echo $_GET["worker_id"]; ?>">Login</a> as a Customer before Booking.</h5>
                    </div>
                </div>
            </div>
            <div class="margin-top"></div>
            <div class="margin-top"></div>
            <?php endif; ?>
            <?php if($role == "Customer"): ?>
            <div class="container white-bg padding-top-bottom">
                <div class="row">
                    <?php if($singleWorker && !$orderPlaced): ?>
                    <div class="col-sm-6">
                        <h3 class="social-worker-name">You're <b><?php echo $customerInfo["name"]; ?></b></h3>
                        <div class="seperator"></div>
                        <h5>Phone: <a title="Call Now" href="tel:<?php echo $singleWorker["phone"]; ?>"><?php echo $customerInfo["phone"]; ?></a></h5>
                        <h5>Email: <a href="mailto:<?php echo $singleWorker["email"]; ?>"><?php echo $customerInfo["email"]; ?></a></h5>
                        <h5>Full Address: <a target="_blank" href="http://maps.google.com/?q=<?php echo $singleWorker["address"]; ?>"><?php echo $customerInfo["address"]; ?></a></h5>
                        
                    </div>
                    <div class="col-sm-6">
                        <h3 class="social-worker-name">Booking to <b><?php echo $singleWorker["name"]; ?></b></h3>
                        <div class="seperator"></div>
                        <h5>Phone: <a title="Call Now" href="tel:<?php echo $singleWorker["phone"]; ?>"><?php echo $singleWorker["phone"]; ?></a></h5>
                        <h5>Email: <a href="mailto:<?php echo $singleWorker["email"]; ?>"><?php echo $singleWorker["email"]; ?></a></h5>
                        <h5>Full Address: <a target="_blank" href="http://maps.google.com/?q=<?php echo $singleWorker["address"]; ?>"><?php echo $singleWorker["address"]; ?></a></h5>
                        <h5>Service Category: <a href="service-category.php?category_id=<?php echo $singleWorker["category_id"]; ?>"><?php echo $db->getServiceCategoryById($singleWorker["category_id"])[0]["name"]; ?></a></h5>
                        <p>Service Description: <?php echo $singleWorker["description"]; ?></p>
                    </div>
                    <div class="col-md-12 text-center">
                        <a href="?worker_id=<?php echo $singleWorker["id"]; ?>&confirm=true" class="btn btn-border mb-10">Confirm Booking</a>
                    </div>
                    <?php elseif($orderPlaced): ?>
                    <div class="success-text margin-left-right ">You have booked successfully. We sent a confirmation email to your email address. You can check Booking list in <a href="my-account.php">My Account</a> Page</div>
                    <?php else: ?>
                    <div class="error-text margin-left-right ">Invalid Social Worker ID</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
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
<?php
    
    session_start();
    $isLoggedIn = false;
    $isCustomer = false;
    $orderPlaced = false;
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : "";
    $id = isset($_SESSION['id']) ? $_SESSION['id'] : "";

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
    $customerInfo = false;

    // Get logged in customer info
    $customerInfo = $db->getCustomerInfoById($id);
    if($customerInfo){
        $customerInfo = $customerInfo[0];
    } else{
        die('You have to <a href="login/">login</a> before access this page');
    }

    $orderList = $db->getOrderListByCustomerId($customerInfo["id"]);

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
                        <li class="active"><?php if($role == "Admin"): ?><a href="dashboard">Dashboard</a><?php elseif($role == "Customer"): ?> <a href="my-account.php">My Account</a> <?php endif; ?></li>
                        <li><?php if($isLoggedIn): ?><a href="logout.php">Log Out</a><?php else: ?> <a href="login">Login</a> <?php endif; ?></li>

                    </ul>

                </div>

            </div>   
        </nav><!-- /.site-navigation -->
    </header><!-- /#mastheaed -->

     <div id="hero" class="hero overlay subpage-hero contact-hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1><?php echo $customerInfo["name"]; ?></h1>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                  <li class="breadcrumb-item active">My Account</li>
                </ol>
            </div><!-- /.hero-text -->
        </div><!-- /.hero-content -->
    </div><!-- /.hero -->

    <main id="main" class="site-main">
        <section class="site-section section-services gray-bg">
            <?php if(!$isLoggedIn): ?>
            <div class="container padding text-center">
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!$isLoggedIn): ?>
                        <h5 class="red-text">You have to <a href="login/?redirect_to=../book.php?worker_id=<?php echo $_GET["worker_id"]; ?>">Login</a> as a Customer before Booking.</h5>
                        <?php elseif($role == "Admin"): ?>
                            <h5 class="red-text">Sorry, <?php echo $role; ?> Can not Booking.</h5>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="margin-top"></div>
            <div class="margin-top"></div>
            <?php endif; ?>
            <?php if($role == "Customer"): ?>
            <div class="container white-bg padding-top-bottom">
                <div class="row">
                    <?php if($customerInfo): ?>
                    <div class="col-sm-4">
                        <h3 class="social-worker-name">Welcome <b><?php echo $customerInfo["name"]; ?></b></h3>
                        <div class="seperator"></div>
                        <h5>Phone: <a title="Call Now" href="tel:<?php echo $singleWorker["phone"]; ?>"><?php echo $customerInfo["phone"]; ?></a></h5>
                        <h5>Email: <a href="mailto:<?php echo $singleWorker["email"]; ?>"><?php echo $customerInfo["email"]; ?></a></h5>
                        <h5>Full Address: <a target="_blank" href="http://maps.google.com/?q=<?php echo $singleWorker["address"]; ?>"><?php echo $customerInfo["address"]; ?></a></h5> 
                    </div>
                    <?php endif; ?>
                    <?php if($orderList): ?>
                    <div class="col-sm-8">
                        <h3 class="social-worker-name">Booking List</b></h3>
                        <div class="seperator"></div>
                        <div class="table-responsive margin-top">
                            <table class="table table-bordered table-striped table-hover"> 
                                <thead> 
                                    <tr> 
                                        <th>#</th> 
                                        <th>Photo</th> 
                                        <th>Name</th> 
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Booking Date</th>
                                    </tr> 
                                </thead> 
                                <tbody>
                                    <?php 
                                        foreach ($orderList as $row):
                                        $workerInfo = $db->getSocialWorkerById($row["worker_id"]);
                                        $workerInfo = $workerInfo[0];
                                    ?> 
                                    <tr> 
                                        <th scope="row"><?php echo $row["id"]; ?></th> 
                                        <td><img class="service-icon" src="<?php echo "uploads/".$workerInfo["photo"]; ?>"></td> 
                                        <td><a href="single-worker.php?worker_id=<?php echo $workerInfo["id"]; ?>"><?php echo $workerInfo["name"]; ?></a></td>  
                                        <td><?php echo $workerInfo["phone"]; ?></td> 
                                        <td><?php echo $workerInfo["address"]; ?></td> 
                                        <td><?php echo $row["date_time"]; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody> 
                            </table>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="col-sm-8">
                        <h3 class="social-worker-name">Booking List</b></h3>
                        <div class="seperator"></div>
                        <div class="error-text">No Booking list found</div>
                    </div>
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
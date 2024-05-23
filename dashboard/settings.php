<?php
    session_start();

    // First check user is already logged in or not
    if(isset($_SESSION['role']) && $_SESSION['valid'] && $_SESSION['role'] == "Admin"){
        
    } else{
        die("You don't have permission to access this page");
    }

    $id = $_SESSION['id'];
    $name = $_SESSION['name'];

    // Include database file
    require_once("../class/database.php");
    $db = new database();
    $settings = $db->getSettings();
    $settings = $settings[0];
    $allCategories = $db->getAllCategories();

    $target_dir = $db->uploadDirectory;
    $isError = false;
    $isSuccess = false;
    $errorMessage = "";

    // Upload file and processing form data
    if(isset($_POST['update_settings']) && !empty($_POST['email']) && !empty($_POST['phone']) && !empty($_POST['address']) && !empty($_POST['project_name']) && !empty($_POST['project_title']) && !empty($_POST['project_subtitle']) && !empty($_POST['service_title']) && !empty($_POST['service_subtitle']) && !empty($_POST['social_worker_title']) && !empty($_POST['social_worker_title']) && !empty($_POST['social_worker_subtitle'])){
        
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $projectName = $_POST['project_name'];
        $projectTitle = $_POST['project_title'];
        $projectSubtitle = $_POST['project_subtitle'];
        $serviceTitle = $_POST['service_title'];
        $serviceSubtitle = $_POST['service_subtitle'];
        $socialWorkerTitle = $_POST['social_worker_title'];
        $socialWorkerSubtitle = $_POST['social_worker_subtitle'];

        $updateSettigs = $db->updateSettings($settings["id"], $email, $phone, $address, $projectName, $projectTitle, $projectSubtitle, $serviceTitle, $serviceSubtitle, $socialWorkerTitle, $socialWorkerSubtitle);

        // If insert success
        if($updateSettigs){
            $isSuccess = true;
            $errorMessage = "Settings Updated Successfully.";
            // Fresh Settings
            $settings = $db->getSettings();
    		$settings = $settings[0];
        } else{
            $isError = true;
            $errorMessage = "Failed to update settings.";
        }
    }
?>
<?php include("header.php"); ?>

    <header id="masthead" class="site-header position-relative">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php echo $settings["project_name"]; ?></span> | Settings</a>

                </div><!-- /.navbar-header -->

                <div class="collapse navbar-collapse" id="agency-navbar-collapse">

                    <ul class="nav navbar-nav navbar-right">

                        <li><a href="index.php">Dashboard</a></li>
                        
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Category<i class="fa fa-caret-down hidden-xs" aria-hidden="true"></i></a>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                              <li><a href="new-category.php">New Category</a></li>
                              <li><a href="all-categories.php">All Categories</a></li>
                            </ul>

                        </li>

                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Social Worker<i class="fa fa-caret-down hidden-xs" aria-hidden="true"></i></a>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                              <li><a href="new-worker.php">New Worker</a></li>
                              <li><a href="all-workers.php">All Workers</a></li>
                            </ul>

                        </li>

                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages<i class="fa fa-caret-down hidden-xs" aria-hidden="true"></i></a>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                              <li><a href="about-edit.php">About US</a></li>
                            </ul>

                        </li>

                        <li><a href="settings.php">Settings</a></li>
                        <li><a href="../logout.php">Log Out</a></li>
                    </ul>

                </div>

            </div>   
        </nav><!-- /.site-navigation -->
        <div class="login-seperator"></div>
    </header><!-- /#mastheaed -->

     

    <main id="main" class="site-main">

        <section class="site-section subpage-site-section section-contact-us">

            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h2>Update Settings</h2>
                        <?php if($isError): ?>
                        <div class="error-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($isSuccess): ?>
                        <div class="success-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                              <label for="email">Email:</label>
                              <input type="text" value="<?php echo $settings["email"]; ?>" class="form-control" id="email" name="email" required="">
                            </div>
                            <div class="form-group">
                              <label for="phone">Phone:</label>
                              <input type="text" value="<?php echo $settings["phone"]; ?>" class="form-control" id="phone" name="phone" required="">
                            </div>
                            <div class="form-group">
                              <label for="address">Full Address:</label>
                              <input type="text" value="<?php echo $settings["address"]; ?>" class="form-control" id="address" name="address" required="">
                            </div>
                            <div class="form-group">
                              <label for="project-name">Project Name:</label>
                              <input type="text" value="<?php echo $settings["project_name"]; ?>" class="form-control" id="project-name" name="project_name" required="">
                            </div>
                            <div class="form-group">
                              <label for="project-title">Project Title:</label>
                              <input type="text" value="<?php echo $settings["project_title"]; ?>" class="form-control" id="project-title" name="project_title" required="">
                            </div>
                            <div class="form-group">
                              <label for="project-subtitle">Project SubTitle:</label>
                              <input type="text" class="form-control" id="project-subtitle" value="<?php echo $settings["project_subtitle"]; ?>" name="project_subtitle" required="">
                            </div>
                            <div class="form-group">
                              <label for="service-title">Service Title:</label>
                              <input type="text" value="<?php echo $settings["service_title"]; ?>" class="form-control" id="service-title" name="service_title" required="">
                            </div>
                            <div class="form-group">
                              <label for="service-subtitle">Service SubTitle:</label>
                              <input type="text" class="form-control" id="service-subtitle" value="<?php echo $settings["service_subtitle"]; ?>" name="service_subtitle" required="">
                            </div>
                            <div class="form-group">
                              <label for="social-worker-title">Social Worker Title:</label>
                              <input type="text" value="<?php echo $settings["social_worker_title"]; ?>" class="form-control" id="social-worker-title" name="social_worker_title" required="">
                            </div>
                            <div class="form-group">
                              <label for="social-worker-subtitle">Social Worker SubTitle:</label>
                              <input type="text" value="<?php echo $settings["social_worker_subtitle"]; ?>" class="form-control" id="social-worker-subtitle" name="social_worker_subtitle" required="">
                            </div>
                            <div class="text-center">
                                <button class="btn btn-green" type="submit" name="update_settings">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </section><!-- /.section-contact-us -->

    </main><!-- /#main -->

    <footer class="login-footer">
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
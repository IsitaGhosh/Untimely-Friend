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
    $isError = false;
    $isSuccess = false;
    $errorMessage = "";

    $aboutPage = $db->getPageByTitle("About Us");

    if($aboutPage){
        $aboutPage = $aboutPage[0];
    }

    // Upload file and processing form data
    if(isset($_POST['update_about']) && isset($_POST['description'])){

        $updatePage = $db->updateAboutPageById($aboutPage["id"], $_POST['description']);

        // If insert success
        if($updatePage){
            $isSuccess = true;
            $errorMessage = "About page updated successfully.";

            // Refresh the page
            $aboutPage = $db->getPageByTitle("About Us");
            if($aboutPage){
                $aboutPage = $aboutPage[0];
            }
        } else{
            $isError = true;
            $errorMessage = "Failed to update about page.";
        }
    }
?>
<?php include("header.php"); ?>

    <header id="masthead" class="site-header position-relative">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php echo $settings["project_name"]; ?></span> | Edit About</a>

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
                        <h2>Update About Page</h2>
                        <?php if($isError): ?>
                        <div class="error-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($isSuccess): ?>
                        <div class="success-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                              <label for="description">Description:</label>
                              <textarea name="description" class="form-control form-control-comment" id="message" required=""><?php echo $aboutPage["description"]; ?></textarea>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-green" type="submit" name="update_about">Update</button>
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
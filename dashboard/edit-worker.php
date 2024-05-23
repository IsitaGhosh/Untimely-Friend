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
    $socialWorkerInfo = false;
    $fileName;

    $target_dir = $db->uploadDirectory;
    $isError = false;
    $isSuccess = false;
    $errorMessage = "";

    // Get category by ID
    if (isset($_GET["worker_id"])) {
        $socialWorkerInfo = $db->getSocialWorkerById($_GET["worker_id"]);
        if($socialWorkerInfo){
            $socialWorkerInfo = $socialWorkerInfo[0];
        } else{
            die("Social Worker ID doesn't exist");
        }
    }

    // Upload file and processing form data
    if(isset($_POST['create_social_worker']) && !empty($_POST['worker_name']) && !empty($_POST['email']) && !empty($_POST['phone']) && !empty($_POST['address']) && !empty($_POST['category']) && !empty($_POST['description'])){
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        if(isset($_FILES["photo"]) && $_FILES["photo"]["name"]){
            // Check file type before upload
            if($imageFileType == "jpeg" || $imageFileType == "jpg" || $imageFileType == "png"){
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $fileName = htmlspecialchars( basename( $_FILES["photo"]["name"]));
                  } else {
                    $isError = true;
                    $errorMessage = "Sorry, there was an error uploading your file.";
                  }
            } else{
                $isError = true;
                $errorMessage = "Only jpeg, jpg and png files are allowed.";
            }
        } else{
            $fileName = $socialWorkerInfo["photo"];
        }

        // Check if no error found then update
        if(!$isError){
            $category = $_POST['category'];
            $name = $_POST['worker_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $description = $_POST['description'];

            // Update Social worker
            $updateWorker = $db->updateSocialWorker($_GET["worker_id"], $category, $name, $email, $phone, $address, $fileName, $description);

            // If update success
            if($updateWorker){
                $isSuccess = true;
                $errorMessage = "Worker Updated Successfully.";

                //Refresh the info
                $socialWorkerInfo = $db->getSocialWorkerById($_GET["worker_id"]);
                $socialWorkerInfo = $socialWorkerInfo[0];
            } else{
                $isError = true;
                $errorMessage = "Failed to update worker.";
            }
        }

        
    }
?>
<?php include("header.php"); ?>

    <header id="masthead" class="site-header position-relative">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php echo $settings["project_name"]; ?></span> | Edit Worker</a>

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
                        <h2>Edit Social Worker</h2>
                        <?php if($isError): ?>
                        <div class="error-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($isSuccess): ?>
                        <div class="success-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                              <label for="name">Name:</label>
                              <input type="text" value="<?php echo $socialWorkerInfo["name"]; ?>" class="form-control" id="name" name="worker_name" required="">
                            </div>
                            <div class="form-group">
                              <label for="email">Email:</label>
                              <input type="text" value="<?php echo $socialWorkerInfo["email"]; ?>" class="form-control" id="email" name="email" required="">
                            </div>
                            <div class="form-group">
                              <label for="phone">Phone:</label>
                              <input type="text" value="<?php echo $socialWorkerInfo["phone"]; ?>" class="form-control" id="phone" name="phone" required="">
                            </div>
                            <div class="form-group">
                              <label for="address">Full Address:</label>
                              <input type="text" value="<?php echo $socialWorkerInfo["address"]; ?>" class="form-control" id="address" name="address" required="">
                            </div>
                            <div class="form-group">
                                <label for="category">Select Category</label>
                                <select id="category" name="category" class="form-control" required="">
                                    <?php 
                                        if($allCategories): ?>
                                            <option>Select</option>
                                            <?php foreach ($allCategories as $category): ?>
                                                <option value="<?php echo $category["id"]; ?>" <?php if($socialWorkerInfo["category_id"] == $category["id"]){ echo "selected"; } ?>><?php echo $category["name"]; ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                        <option>No category Found</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                              <label for="photo">Photo: (JPEG, JPG & PNG are supported - 100x100)</label>
                              <input class="form-control" id="photo" name="photo" type="file"></input>
                            </div>
                            <div class="form-group">
                              <label for="description">Service Description:</label>
                              <textarea name="description" class="form-control form-control-comment" id="description" required=""><?php echo $socialWorkerInfo["description"]; ?></textarea>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-green" type="submit" name="create_social_worker">Create</button>
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
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
    $allWorkers;

    $isError = false;
    $isSuccess = false;
    $errorMessage = "";

    // Delete by ID
    if(isset($_GET["delete_id"])){
        $delete = $db->deleteWorkerById($_GET["delete_id"]);
        if($delete){
            $isSuccess = true;
            $errorMessage = "Deleted Successfully";
        } else{
            $isError = true;
            $errorMessage = "There was a problem to delete";
        }
    }

    // Get social workers under a category
    if(isset($_GET["category_id"])){
        $allWorkers = $db->getAllSocialWorkersById($_GET["category_id"]);
    } else{
        // Get all social workers
        $allWorkers = $db->getAllSocialWorkers();
    }
?>
<?php include("header.php"); ?>

    <header id="masthead" class="site-header position-relative">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php echo $settings["project_name"]; ?></span> | All Workers</a>

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
                        <div class="row">
                            <div class="col-md-8 col-sm-8">
                                <h2>All Social Workers</h2>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <select class="form-control" id="service-category">
                                    <option value="all">All Categories</option>
                                    <?php 
                                        if($allCategories):
                                            foreach ($allCategories as $category): ?>
                                                <option value="<?php echo $category["id"]; ?>" <?php if(isset($_GET["category_id"]) && $_GET["category_id"] == $category["id"]){ echo "selected"; } ?>><?php echo $category["name"]; ?></option>
                                            <?php endforeach; 
                                            else:
                                            ?>
                                            <?php echo "<option>No category Found</option>"; 
                                        endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <?php if($isError): ?>
                        <div class="error-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($isSuccess): ?>
                        <div class="success-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($allWorkers): ?>
                        <div class="table-responsive margin-top">
                            <table class="table table-bordered table-striped table-hover margin-top"> 
                                <thead> 
                                    <tr> 
                                        <th>#</th> 
                                        <th>Photo</th> 
                                        <th>Name</th> 
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Service Description</th>
                                        <th>Edit</th>
                                        <th>Delete</th>  
                                    </tr> 
                                </thead> 
                                <tbody>
                                    <?php foreach ($allWorkers as $row): ?> 
                                    <tr> 
                                        <th scope="row"><?php echo $row["id"]; ?></th> 
                                        <td><img class="service-icon" src="<?php echo $db->uploadDirectory.$row["photo"]; ?>"></td> 
                                        <td><?php echo $row["name"]; ?></td> 
                                        <td><?php echo $row["email"]; ?></td> 
                                        <td><?php echo $row["phone"]; ?></td> 
                                        <td><?php echo $row["address"]; ?></td> 
                                        <td><div class="service-description-admin"><?php echo $row["description"]; ?></div></td> 
                                        <td><a class="edit-button" href="edit-worker.php?worker_id=<?php echo $row["id"]; ?>">Edit</a></td>
                                        <td><button class="delete-button" onclick="confirmAction(<?php echo $row["id"]; ?>)">Delete</button></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody> 
                            </table>
                        </div>
                        <?php else: ?>
                            <div class="error-text">No Social Worker Found</div>
                        <?php endif; ?>
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

    <script type="text/javascript">
         function confirmAction(id) {
            let confirmAction = confirm("Are you sure to delete this worker?");
            if (confirmAction) {
              window.location.replace("?delete_id="+id);
            } else {
              //alert("Action canceled");
            }
          }
    </script>

    <?php include("footer.php"); ?>
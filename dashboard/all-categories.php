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

    // Delete by ID
    if(isset($_GET["delete_id"])){
        $delete = $db->deleteCategoryById($_GET["delete_id"]);
        if($delete){
            $isSuccess = true;
            $errorMessage = "Deleted Successfully";
        } else{
            $isError = true;
            $errorMessage = "There was a problem to delete";
        }
    }

    // Get all categories
    $allCategories = $db->getAllCategories();
?>
<?php include("header.php"); ?>

    <header id="masthead" class="site-header position-relative">
        <nav id="primary-navigation" class="site-navigation">
            <div class="container">

                <div class="navbar-header">
                   
                    <a class="site-title"><span><?php echo $settings["project_name"]; ?></span> | All Categories</a>

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
                        <h2>All Service Categories</h2>
                        <?php if($isError): ?>
                        <div class="error-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($isSuccess): ?>
                        <div class="success-text"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
                        <?php if($allCategories): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-responsive margin-top"> 
                                <thead> 
                                    <tr> 
                                        <th>#</th> 
                                        <th>Icon</th> 
                                        <th>Name</th> 
                                        <th>Description</th>
                                        <th>Edit</th>
                                        <th>Delete</th>  
                                    </tr> 
                                </thead> 
                                <tbody>
                                    <?php foreach ($allCategories as $row): ?> 
                                    <tr> 
                                        <th scope="row"><?php echo $row["id"]; ?></th> 
                                        <td><img class="service-icon" src="<?php echo $db->uploadDirectory.$row["file_name"]; ?>"></td> 
                                        <td><?php echo $row["name"]; ?></td> 
                                        <td><?php echo $row["description"]; ?></td> 
                                        <td><a class="edit-button" href="edit-category.php?category_id=<?php echo $row["id"]; ?>">Edit</a></td>
                                        <td><button class="delete-button" onclick="confirmAction(<?php echo $row["id"]; ?>)">Delete</button></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody> 
                            </table>
                        </div>
                        <?php else: ?>
                            <div class="error-text">No Category Found</div>
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
            let confirmAction = confirm("Are you sure to delete this category?");
            if (confirmAction) {
              window.location.replace("?delete_id="+id);
            } else {
              //alert("Action canceled");
            }
          }
    </script>

    <?php include("footer.php"); ?>
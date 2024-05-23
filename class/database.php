<?php

class database{
	// Database info
    private $db;
    private $dbhost = 'localhost';
    private $dbname = 'fy_project';
    private $dbuser = 'root';
    private $dbpass = '';

    // Upload Directory
    public $uploadDirectory = "../uploads/";
    public $siteAddress = "http://localhost/student-project/";

    // Database Table
    public $userTable = "users";
    public $socialWorkerTable = "social_workers";
    public $serviceCategoryTable = "service_categories";
    public $orderTable = "orders";
    public $pageTable = "pages";
    public $customerInfoTable = "customers_info";
    private $settingsTable = "settings";

    // Connect with database
    public function connect(){
       
	   if ($this->db) {
			// There already is a connection, return it instead
			return $this->db;
		}
	   
        try {
            
			$this->db = new PDO("mysql:host={$this->dbhost};dbname={$this->dbname}",$this->dbuser,$this->dbpass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			
        }

        catch(PDOException $e) {
           echo "Connection error: ".$e->getMessage();
        }
    }

    // User login
    public function userLogin($email, $password){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->userTable where email = ? AND password = ? ORDER BY id DESC");
        $statement->execute(array($email, md5($password)));

        $num = $statement->rowCount();

        if($num>0){
            return $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Send password reset email
    public function sendPasswordResetEmail($email){
        $this->Connect();
        $token = md5($email.time());
        $statement = $this->db->prepare("update $this->userTable SET token = ? where email = ?");
        $statement->execute(array($token, $email));

        $num = $statement->rowCount();

        if($num>0){
            $body = 'Click <a href="'.$this->siteAddress.'login/?reset_token='.$token.'" target="_blank">here</a> to reset your password on $this->siteAddress';
            $sendEmail = $this->sendEmail($email, "Password Reset", $body);
            return true;
        }
        
        return false;
        
    }

    // Change Password
    public function changePassword($password, $token){
        $this->Connect();
        $statement = $this->db->prepare("update $this->userTable SET password = ?, token = ? where token = ?");
        $statement->execute(array(md5($password), "", $token));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Check reset token
    public function checkResetToken($token){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->userTable where token = ?");
        $statement->execute(array($token));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Check if email already exist
    public function checkEmail($email){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->userTable where email = ?");
        $statement->execute(array($email));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Create new db records
    public function createRecords($tablename,$fields,$values){
        $this->Connect();
        
        $items = $fields;
        $id = count($items);
        $newarray = array();
        for($i = 0; $i<$id; $i++){
            $newarray[] = "?";
        }
        $nval = implode(",",$newarray);
        $fields = implode(",",$fields);
        $statement = $this->db->prepare("insert into $tablename ($fields) values($nval)");
        $statement->execute($values);
        $id = $this->db->lastInsertId();

        return $id;
    }

    // Get all Categories
    public function getAllCategories(){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->serviceCategoryTable ORDER BY id DESC");
        $statement->execute();
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get order list by customer id
    public function getOrderListByCustomerId($id){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->orderTable where customer_id = ? ORDER BY id DESC");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get page by title
    public function getPageByTitle($title){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->pageTable where title = ? ORDER BY id DESC");
        $statement->execute(array($title));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Update category
    public function updateCategory($id, $name, $description, $fileName){
        $this->Connect();
        $statement = $this->db->prepare("update $this->serviceCategoryTable SET name = ?, description = ?, file_name = ? where id = ?");
        $statement->execute(array($name, $description, $fileName, $id));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Update worker
    public function updateSocialWorker($id, $categoryId, $name, $email, $phone, $address, $photo, $description){
        $this->Connect();
        $statement = $this->db->prepare("update $this->socialWorkerTable SET category_id = ?, name = ?, email = ?, phone =?, address = ?, photo = ?, description = ? where id = ?");
        $statement->execute(array($categoryId, $name, $email, $phone, $address, $photo, $description, $id));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Update About page
    public function updateAboutPageById($id, $description){
        $this->Connect();
        $statement = $this->db->prepare("update $this->pageTable SET description = ? where id = ?");
        $statement->execute(array($description, $id));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Update Settings
    public function updateSettings($id, $email, $phone, $address, $projectName, $projectTitle, $projectSubtitle, $serviceTitle, $serviceSubtitle, $socialWorkerTitle, $socialWorkerSubtitle){
        $this->Connect();
        $statement = $this->db->prepare("update $this->settingsTable SET email = ?, phone = ?, address = ?, project_name = ?, project_title = ?, project_subtitle = ?, service_title = ?, service_subtitle = ?, social_worker_title = ?, social_worker_subtitle =? where id = ?");
        $statement->execute(array($email, $phone, $address, $projectName, $projectTitle, $projectSubtitle, $serviceTitle, $serviceSubtitle, $socialWorkerTitle, $socialWorkerSubtitle, $id));

        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Get delete category by id
    public function deleteCategoryById($id){
        $this->Connect();
        $statement = $this->db->prepare("Delete from $this->serviceCategoryTable where id = ?");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Get delete category by id
    public function deleteWorkerById($id){
        $this->Connect();
        $statement = $this->db->prepare("Delete from $this->socialWorkerTable where id = ?");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return true;
        }
        
        return false;
        
    }

    // Get service category by id
    public function getServiceCategoryById($id){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->serviceCategoryTable where id = ?");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get Settings
    public function getSettings(){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->settingsTable where id = ?");
        $statement->execute(array("1"));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get all social worker under a category
    public function getAllSocialWorkersById($id){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->socialWorkerTable where category_id = ? ORDER BY id DESC");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get all social workers
    public function getAllSocialWorkers(){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->socialWorkerTable ORDER BY id DESC");
        $statement->execute();
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get a social worker by ID
    public function getSocialWorkerById($id){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->socialWorkerTable where id = ? ORDER BY id DESC");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Get Customer info by ID
    public function getCustomerInfoById($id){
        $this->Connect();
        $statement = $this->db->prepare("select * from $this->userTable INNER JOIN $this->customerInfoTable ON $this->userTable.id = $this->customerInfoTable.user_id WHERE id = ? ORDER BY ID DESC");
        $statement->execute(array($id));
        $num = $statement->rowCount();

        if($num>0){
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
        
    }

    // Send email
    public function sendEmail($to, $subject, $body){
        $getSettings = $this->getSettings();
        $getSettings = $getSettings[0];
        $subject = $subject;
        $to_email = $to;
        $to_fullname = "User";
        $from_email = $getSettings["email"];
        $from_fullname = "Admin";
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "To: $to_fullname <$to_email>\r\n";
        $headers .= "From: $from_fullname <$from_email>\r\n";
        $message = "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en\" xml:lang=\"en\">\r\n
        <head>\r\n
        <title>$subject</title>\r\n
        </head>\r\n
        <body>\r\n
        <p></p>\r\n
        <p style=\"color: #00CC66; font-weight:600; font-style: italic; font-size:14px; float:left; margin-left:7px;\">$body</p>\r\n
        </body>\r\n
        </html>";

        if (mail($to_email, $subject, $message, $headers)) { 
            return true;
        }

        return false;
    }

}

?>
<?php
include 'connection.php';
require __DIR__ . '/../vendor/autoload.php';
use \PHPMailer\PHPMailer\PHPMailer;
// Include PHPMailer classes
require_once __DIR__ . '/../phpMailer/PHPMailer.php';
require_once __DIR__ . '/../phpMailer/SMTP.php';
require_once __DIR__ . '/../phpMailer/Exception.php';

// --- Encryption configuration ---
define('ENCRYPT_METHOD', 'AES-256-CBC');
define('SECRET_KEY', 'your-strong-secret-key'); 
define('SECRET_IV', 'your-strong-secret-iv');    
function encryptPassword($password) {
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    return openssl_encrypt($password, ENCRYPT_METHOD, $key, 0, $iv);
}
function decryptPassword($encryptedPassword) {
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    return openssl_decrypt($encryptedPassword, ENCRYPT_METHOD, $key, 0, $iv);
}
// --- End of Encryption configuration ---

function basics($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
//open validation
function validateIdNumber($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 \/]*$/", $data))
        return 1;
    else
        return 0;
}
//validate date 
function checkDateOfBirth($dob) {
    // Check if the date is in YYYY-MM-DD format
    $d = DateTime::createFromFormat('Y-m-d', $dob);
    if ($d && $d->format('Y-m-d') === $dob) {
        return true; // valid date format
    } else {
        return false; // invalid date format
    }
}

//validate number
function validateNumber($data)
{
    $data = basics($data);
    if (preg_match("/^[0-9 \/]*$/", $data))
        return 1;
    else
        return 0;
}
//validatePhoneNumber
function validatePhoneNumber($data)
{
    $data = basics($data);
    if (preg_match("/^(\+?\d{10,15})$/", $data))
        return 1;
    else
        return 0;
}
//validateProfilePicture
function validateProfilePicture($data)
{
    $maxFileSize = 5 * 1024 * 1024;
    $allowedMimeTypes = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'];
    if ($data['error'] !== UPLOAD_ERR_OK) {
        return "An error occurred while uploading the file. Error code: " . $data['error'];
    }
    if ($data['size'] > $maxFileSize) {
        return "File size exceeds the maximum limit of 5MB.";
    }
    $mimeType = mime_content_type($data['tmp_name']);
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
    }
    if (!getimagesize($data['tmp_name'])) {
        return "The file is not a valid image.";
    }
    return true;
}
//validateUploadedFile
function validateUploadedFile($file, $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'], $maxFileSize = 10 * 1024 * 1024)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "An error occurred during file upload. Error code: " . $file['error'];
    }
    if ($file['size'] > $maxFileSize) {
        return "File size exceeds the maximum limit of " . ($maxFileSize / (1024 * 1024)) . "MB.";
    }
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return "Invalid file type. Allowed types are: " . implode(", ", $allowedMimeTypes) . ".";
    }
    return true;
}
//validateFileName
function validateName($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z]*$/", $data))
        return 1;
    else
        return 0;
}
//validate email
function validateEmail($email) {
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $parts = explode('@', $email);
    if (count($parts) != 2) {
        return false;
    }
    return true;
}

//validate gender
function validateGender($data)
{
    $data = basics($data);
    if ($data == "M" or $data == "F")
        return 1;
    else
        return 0;
}

//validate password
function validatePassword($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 @#$]*$/", $data))
        return 1;
    else
        return 0;
}
//password strength
function isStrongPassword($password) {
    $errors = [];
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "one lowercase letter";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "one uppercase letter";
    }
    if (!preg_match('/[@#$%^&+=!]/', $password)) {
        $errors[] = "one special character (@#$%^&+=!)";
    }
    if (strlen($password) < 8) {
        $errors[] = "at least 8 characters";
    }

    if (!empty($errors)) {
        return "Password must contain " . implode(", ", $errors) . ".";
    }
    return true;
}

function checkUserCredentials($username, $inputPassword) {
    global $conn;

    // Prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        // Decrypt stored password
        $decryptedPassword = decryptPassword($row['password']);

        // Compare with input password
        if ($inputPassword === $decryptedPassword) {
            return $row; // Login successful
        }
    }

    return false; // Login failed
}
// New function to check students table
function checkStudentCredentials($student_id, $inputPassword) {
    global $conn;
    $student_id = mysqli_real_escape_string($conn, $student_id);

    $query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$student_id'");
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        // Decrypt password like users
        $decryptedPassword = decryptPassword($row['password']);
        if ($inputPassword === $decryptedPassword) {
            return $row;
        }
    }

    return false;
}


//user password get
function getUserPassword($conn, $user_id) {
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $query = "SELECT password FROM users WHERE uid = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['password'];
    } else {
        return false;
    }
}
//change password
function changeUserPassword($conn, $user_id, $old_password, $new_password) {
    $stored_password = getUserPassword($conn, $user_id);
    if ($stored_password === false) {
        return ['status' => false, 'message' => 'User not found.'];
    }
    if (encryptPassword($old_password) !== $stored_password) {
        return ['status' => false, 'message' => 'Old password is incorrect.'];
    }
    $new_encrypted = encryptPassword($new_password);
    $update_query = "UPDATE users SET password = '$new_encrypted' WHERE uid = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        return ['status' => true, 'message' => 'Password changed successfully.'];
    } else {
        return ['status' => false, 'message' => 'Error updating password.'];
    }
}

//validate check password
function comparePasswords($data1, $data2)
{
    $data1 = basics($data1);
    $data2 = basics($data2);
    if ($data1 == $data2)
        return 1;
    else
        return 0;
}
//validate user type
function validateUserType($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 _]*$/", $data))
        return 1;
    else
        return 0;
}
//validate class type
function validateClassType($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 _]*$/", $data))
        return 1;
    else
        return 0;
}
  // Function to validate blood group
    function validateBloodGroup($bloodGroup) {
      $validBloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
      return in_array($bloodGroup, $validBloodGroups);
    }
// End validation

//function for user_status 
function user_status($status) {
    if ($status == 0) {
        return "<span class='badge light badge-warning'>OFFLINE</span>";
    } else if ($status == 1) {
        return "<span class='badge light badge-success'>ONLINE</span>";
    } else if ($status == 2) {
        return "<span class='badge light badge-danger'>BLOCKED</span>";
    } else {
        return "<span class='badge light badge-secondary'>UNKNOWN</span>";
    }
}
//function for case_status
function case_status($status) {
    if ($status == 0) {
        return "<span class='badge light badge-danger'>PENDING</span>";        
    } else if ($status == 1) {
        return "<span class='badge light' style='background-color:rgb(0, 0, 0); color: white;'>OPENED</span>";   
    } else if ($status == 2) {
        return "<span class='badge light badge-warning'>DISTRIBUTED</span>";    
    } else if ($status == 3) {
        return "<span class='badge light badge-primary'>APPOINTMENT GIVEN</span>"; 
    } else if ($status == 4) {
        return "<span class='badge light badge-info'>WAITING CONFIRM</span>";   
    } else if ($status == 5) {
        return "<span class='badge light badge-secondary'>APPOINTED</span>";    
    } else if ($status == 6) {  
        return "<span class='badge light' style='background-color:rgb(168, 203, 30); color: white;'>DECISION GIVEN</span>"; 
    } else if ($status == 7) {
       return "<span class='badge light' style='background-color:rgb(255, 145, 0); color: white;'>WAITING DECISION</span>"; 
    } else if ($status == 8) {
        return "<span class='badge light badge-success'>DECIDED</span>";      
    } else {
        return "<span class='badge light badge-dark'>UNKNOWN</span>";
    }
}

function getOrdinalWord($number) {
    $ords = [1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth'];
    return $ords[$number] ?? $number . 'th';
}

function getCaseStatusList() {
    return [
        0 => 'PENDING',
        1 => 'OPENED',
        2 => 'DISTRIBUTED',
        3 => 'APPOINTMENT GIVEN',
        4 => 'WAITING CONFIRM',
        5 => 'APPOINTED',
        6 => 'DECISION GIVEN',
        7 => 'WAITING DECISION',
        8 => 'DECIDED'
    ];
}

function checkUserByUsername($data)
{
    global $conn;
    $query = mysqli_query($conn, "select uid from users where username ='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function getRoleByUsername($data)
{
    global $conn;

    if(mysqli_num_rows(mysqli_query($conn, "select * from users"))>0)
    {
    $query = mysqli_query($conn, "select uid, password, user_type, user_status from users where username='$data'");
    $result = mysqli_fetch_array($query);
    return $result;
    }
    else
    {
        echo "no user found";
    }
}


function checkIs_loggedIn($data) {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM users WHERE is_logged = '$data'");
    $row = mysqli_fetch_assoc($query);
    if ($row) {
        return true; 
    } else {
        return false; 
    }
}

function getRoleByPassword($data)
{
    global $conn;
    if(mysqli_num_rows(mysqli_query($conn, "select * from users"))>0)
    {
     $query = mysqli_query($conn, "select uid, user_type, user_status from users where password='$data'");
     $result = mysqli_fetch_array($query);
     return $result;
    }
    else
    {
         echo "no user found";
    }
}

function getNextIdNumber() {
    global $conn;
    $query = "SELECT student_id FROM students ORDER BY Student_id DESC LIMIT 1"; 
    $result = mysqli_query($conn, $query);
    $gregorianYear = date("Y");
    $month = (int)date("n"); 
    $ethiopianYear = $gregorianYear - 7;
    if ($month < 9) {
        $ethiopianYear -= 1;
    }
    $prefix = "BSS/STU/";
    $newNumber = 1;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['student_id']; 
        $parts = explode("/", $lastId);
        if (count($parts) === 4) {
            $newNumber = (int)$parts[2] + 1;
        }
    }
    $paddedNumber = str_pad($newNumber, 4, "0", STR_PAD_LEFT);
    return $prefix . $paddedNumber . "/" . substr($ethiopianYear, -2);
}

// Function to get role type abbreviation by ID
function getRoleTypeById($roleId) {
    global $conn;
    if (!is_numeric($roleId)) {
        return null; 
    }
    $query = "SELECT `abbreviation_name` FROM role_type WHERE rid = $roleId";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['abbreviation_name']; 
    }
    return null; 
}

// Function to get the next school ID based on role type
function getNextSchoolId($roleType) {
    global $conn;
    $roleTypeName = getRoleTypeById($roleType);
    if (!$roleTypeName) {
        return "";
    }
    // Remove spaces in role name
    $roleTypeName = str_replace(' ', '', $roleTypeName); 
    // Ethiopian year calculation
    $gregorianYear = date("Y");
    $month = (int)date("n");
    $ethiopianYear = $gregorianYear - 7;
    if ($month < 9) {
        $ethiopianYear -= 1;
    }
    // Prepare search pattern
    $likePattern = "BSS/$roleTypeName/%";
    $query = "SELECT idNumber FROM `users` WHERE idNumber LIKE '$likePattern' ORDER BY idNumber DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $newNumber = 1;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $parts = explode("/", $row['idNumber']);
        if (count($parts) === 4) {
            $newNumber = (int)$parts[2] + 1;
        }
    }
    $paddedNumber = str_pad($newNumber, 4, "0", STR_PAD_LEFT);
    return "BSS/$roleTypeName/$paddedNumber/" . substr($ethiopianYear, -2);
}

// Function to add a new user
function addUser($idNumber, $profile_pic, $firstName, $fatherName, $gFatherName, $gender, 
$role_type, $username, $encrypted_password, $email, $phone, $userStatus)
{
    global $conn;
    $query = "INSERT INTO users (idNumber, profile_picture, first_name, father_name, grandfather_name, gender,
     user_type, username, password, email, phone, user_status)
    VALUES 
    ('$idNumber', '$profile_pic', '$firstName', '$fatherName', '$gFatherName', '$gender',
     '$role_type', '$username', '$encrypted_password', '$email', '$phone', $userStatus)";
  
}

// Function to check if a student exists by ID
function studentExist($student_id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT student_id FROM students WHERE student_id='$student_id'");
    return mysqli_num_rows($query); // returns 0 if not found, >0 if exists
}


// Function to register a new student
function registerStudent( $student_photo, $student_id, $first_name, $father_name, $grand_father_name,
    $gender, $dob, $email, $phone, $birth_place, $nationality,
    $region, $zone, $woreda, $kebele, $username, $role_type, $password,
    $mother_name = null, $father_contact = null, $mother_contact = null,
    $father_occupation = null, $mother_occupation = null,
    $emergency_contact_name = null, $emergency_contact_phone = null,
    $blood_group = null, $medical_condition = null, $other_condition = null,
    $disabilities = null, $previous_school = null, $academic_status = null,
    $previous_documents = null)
{
    global $conn;
    // Sanitize variables
    $student_id = mysqli_real_escape_string($conn, $student_id);
    $first_name = mysqli_real_escape_string($conn, $first_name);
    $father_name = mysqli_real_escape_string($conn, $father_name);
    $grand_father_name = mysqli_real_escape_string($conn, $grand_father_name);
    $gender = mysqli_real_escape_string($conn, $gender);
    $dob = mysqli_real_escape_string($conn, $dob);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $birth_place = mysqli_real_escape_string($conn, $birth_place);
    $nationality = mysqli_real_escape_string($conn, $nationality);
    $region = mysqli_real_escape_string($conn, $region);
    $zone = mysqli_real_escape_string($conn, $zone);
    $woreda = mysqli_real_escape_string($conn, $woreda);
    $kebele = mysqli_real_escape_string($conn, $kebele);
    $username = mysqli_real_escape_string($conn, $username);
    $role_type = mysqli_real_escape_string($conn, $role_type);
    $password = mysqli_real_escape_string($conn, $password);
    $mother_name = mysqli_real_escape_string($conn, $mother_name);
    $father_contact = mysqli_real_escape_string($conn, $father_contact);
    $mother_contact = mysqli_real_escape_string($conn, $mother_contact);
    $father_occupation = mysqli_real_escape_string($conn, $father_occupation);
    $mother_occupation = mysqli_real_escape_string($conn, $mother_occupation);
    $emergency_contact_name = mysqli_real_escape_string($conn, $emergency_contact_name);
    $emergency_contact_phone = mysqli_real_escape_string($conn, $emergency_contact_phone);
    $blood_group = mysqli_real_escape_string($conn, $blood_group);
    $medical_condition = mysqli_real_escape_string($conn, $medical_condition);
    $other_condition = mysqli_real_escape_string($conn, $other_condition);
    $disabilities = mysqli_real_escape_string($conn, $disabilities);
    $previous_school = mysqli_real_escape_string($conn, $previous_school);
    $academic_status = mysqli_real_escape_string($conn, $academic_status);
    $previous_documents = mysqli_real_escape_string($conn, $previous_documents);

    // INSERT query matching your table columns
    $query = "
        INSERT INTO students (
            student_photo, student_id, first_name, father_name, grand_father_name, gender, dob, email, phone, birth_place, nationality,
            region, zone, woreda, kebele, username, role_type, password, mother_name, father_contact, mother_contact,
            father_occupation, mother_occupation, emergency_contact_name, emergency_contact_phone, blood_group, medical_condition,
            other_condition, disabilities, previous_school, academic_status, previous_documents
        ) VALUES (
            '$student_photo', '$student_id', '$first_name', '$father_name', '$grand_father_name', '$gender', '$dob', '$email', '$phone',
            '$birth_place', '$nationality', '$region', '$zone', '$woreda', '$kebele', '$username', '$role_type', '$password',
            '$mother_name', '$father_contact', '$mother_contact', '$father_occupation', '$mother_occupation', '$emergency_contact_name',
            '$emergency_contact_phone', '$blood_group', '$medical_condition', '$other_condition', '$disabilities', '$previous_school',
            '$academic_status', '$previous_documents'
        )
    ";
    $query = mysqli_query($conn, $query);
    if ($query) {
        return 1;
    } else {
        echo "MySQL Error: " . mysqli_error($conn);
        return 0;
    }
}

function addCase($case_id, $plaintiff, $defendant, $case_type, $decision, $case_status)
{
    global $conn;
    $case_id = mysqli_real_escape_string($conn, $case_id);
    $plaintiff = mysqli_real_escape_string($conn, ucfirst($plaintiff));
    $defendant = mysqli_real_escape_string($conn, ucfirst($defendant));
    $decision = $decision !== NULL ? mysqli_real_escape_string($conn, ucfirst($decision)) : NULL;
    // Use DateTimeFactory to get the current Ethiopian DateTime object
    $ethiopian = \Andegna\DateTimeFactory::now();
    $ethiopian_date = $ethiopian->format('Y-m-d');
    $query = mysqli_query($conn, "INSERT INTO `case` (case_id, plaintiff, defendant, case_type, decision, case_status, ethiopian_date)
        VALUES ('$case_id', '$plaintiff', '$defendant', '$case_type', '$decision', '$case_status', '$ethiopian_date')");
     if ($query)
        return 1;
    else
        return 0;
}



function addReason( $appointment_reason)
{
    global $conn;
    $appointment_reason = mysqli_real_escape_string($conn, ucfirst($appointment_reason));
    $query = mysqli_query($conn, "INSERT INTO Reason (appointment_reason)
    values('$appointment_reason')");
    if ($query)
        return 1;
    else
        return 0;
}

function addCaseType( $name, $abbreviation_name)
{
    global $conn;
    $name = mysqli_real_escape_string($conn, ucfirst($name));
    $abbreviation_name = mysqli_real_escape_string($conn, strtoupper($abbreviation_name));
    $query = mysqli_query($conn, "INSERT INTO case_type (name, abbreviation_name)
    values('$name','$abbreviation_name')");
    if ($query)
        return 1;
    else
        return 0;
}

function addAppointment($appointment_date, $case_id,$reason_id, $record_date)
{
    global $conn;
    $query = mysqli_query($conn, "INSERT INTO appointment(appointment_date, case_id,reason_id, record_date)
    values('$appointment_date','$case_id','$reason_id', '$record_date')");
    if ($query)
        return 1;
    else
        return 0;
}

function addFile($cid, $record_date, $file,$file_pages) {
    global $conn;
    $query = mysqli_query($conn, "INSERT INTO attach_files (case_id, record_date, file) VALUES ('$cid', '$record_date', '$file')");
    if ($query) {
        return 1;
    } else {
        return 0;
    }
}

function getAttachFileByFid($fid) {
    global $conn;
    $fid = mysqli_real_escape_string($conn, $fid);
    $query = "SELECT * FROM attach_files WHERE fid = '$fid'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    } else {
        return null; 
    }
}
function getOriginalFileByFid($kid) {
    global $conn;
    $fid = mysqli_real_escape_string($conn, $kid);
    $query = "SELECT * FROM case_info WHERE kid = '$kid'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    } else {
        return null; 
    }
}
function updateAttachFile($fid, $record_date, $file, $file_pages) {
    global $conn;
    if (empty($fid)) {
        return 0;
    }
    $query = mysqli_query($conn, " UPDATE attach_files  SET record_date = '$record_date', file = '$file'
        WHERE fid = '$fid'");
    if ($query) {
        return 1;
    } else {
        return 0;
    }
}
function updateOriginalFile($kid, $file, $file_pages) {
    global $conn;
    if (empty($kid)) {
        return 0;
    }
    $query = mysqli_query($conn, " UPDATE case_info  SET file = '$file' WHERE case_id = '$kid'");
    if ($query) {
        return 1;
    } else {
        return 0;
    }
}
function updateFilePagesInCaseInfo($cid, $newPages) {
    global $conn;
    
    // Sanitize inputs
    $cid = mysqli_real_escape_string($conn, $cid);
    $newPages = (int)$newPages;
    $query = "UPDATE case_info SET file_pages = '$newPages' WHERE case_id = '$cid'";
    return mysqli_query($conn, $query);
}

function addCaseInfo($cid, $first_name, $father_name, $gfather_name, $gender, $email, $region,
    $zone, $woreda, $kebele, $wogen, $argument_money, $judgement_money,
    $phone, $litigant_type, $file_pages, $file)
{
    global $conn; 
    $first_name = mysqli_real_escape_string($conn, ucfirst($first_name));
    $father_name = is_string($father_name) ? mysqli_real_escape_string($conn, ucfirst($father_name)) : NULL;
    $gfather_name = is_string($gfather_name) ? mysqli_real_escape_string($conn, ucfirst($gfather_name)) : NULL;
    $gender = is_string($gender) ? mysqli_real_escape_string($conn, ucfirst($gender)) : NULL;
    $email = mysqli_real_escape_string($conn, ucfirst($email));
    $region = mysqli_real_escape_string($conn, ucfirst($region));
    $zone = mysqli_real_escape_string($conn, ucfirst($zone));
    $woreda = mysqli_real_escape_string($conn, ucfirst($woreda));
    $kebele = mysqli_real_escape_string($conn, ucfirst($kebele));
    $wogen = mysqli_real_escape_string($conn, ucfirst($wogen));
    $litigant_type = mysqli_real_escape_string($conn, ucfirst($litigant_type));;
    $litigant_type = ucfirst($litigant_type);
    $query = mysqli_query($conn, "INSERT INTO case_info(case_id, first_name, father_name, grandfather_name, 
    gender, email, region, zone, woreda, kebele, wogen, argument_money, judgement_money, phone, litigant_type,
    file_pages, file)
    VALUES ('$cid', '$first_name', '$father_name', '$gfather_name', '$gender', '$email', '$region',
    '$zone', '$woreda', '$kebele', '$wogen', '$argument_money', '$judgement_money',
    '$phone', '$litigant_type', '$file_pages', '$file')");
    if ($query)
        return 1;
    else
        return 0;
}

function addDecision($cid, $decision, $who_won) {
    global $conn;   
    // Sanitize inputs
    $decision = mysqli_real_escape_string($conn, ucfirst($decision));
    $who_won = mysqli_real_escape_string($conn, ucfirst($who_won));
    // Update `case` table
    $update_case = mysqli_query($conn, "UPDATE `case` SET decision = '$decision', case_status = 8 WHERE cid = '$cid'");
    // Get case_id from cid
    $case_res = mysqli_query($conn, "SELECT cid FROM `case` WHERE cid = '$cid'");
    $row = mysqli_fetch_assoc($case_res);
    $case_id = $row['cid'];
    // Insert into resolutions
      $ethiopian = \Andegna\DateTimeFactory::now();
    $ethiopian_date = $ethiopian->format('Y-m-d');
    $insert_resolution = mysqli_query($conn, "INSERT INTO resolutions (case_id, decision_details, who_won, resolution_date) 
                                          VALUES ('$cid', '$decision', '$who_won', '$ethiopian_date')");

    if ($update_case && $insert_resolution) {
        return 1;
    } else {
        return 0;
    }
}

function getCasesBySearch($caseNumber, $plaintiff, $defendant) {
    global $conn;

    $conditions = [];
    if (!empty($caseNumber)) {
        $conditions[] = "c.case_id LIKE '%" . mysqli_real_escape_string($conn, $caseNumber) . "%'";
    }
    if (!empty($plaintiff)) {
        $conditions[] = "c.plaintiff LIKE '%" . mysqli_real_escape_string($conn, $plaintiff) . "%'";
    }
    if (!empty($defendant)) {
        $conditions[] = "c.defendant LIKE '%" . mysqli_real_escape_string($conn, $defendant) . "%'";
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    $sql = "SELECT c.case_id, c.plaintiff, c.defendant,
                   DATE_FORMAT(c.ethiopian_date, '%d/%m/%Y') as created_date,
                   r.who_won,
                   DATE_FORMAT(r.resolution_date, '%d/%m/%Y') as date_resolved
            FROM `case` c
            LEFT JOIN resolutions r ON c.cid = r.case_id
            $whereClause
            ORDER BY c.ethiopian_date DESC";

    $result = mysqli_query($conn, $sql);
    $cases = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }
    return $cases;
}

function decisionExist($case_id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT decision FROM `case` WHERE cid = '$case_id' AND decision != ''");
    if (mysqli_num_rows($result) > 0) {
        return 1;
    } else {
        return 0;
    }
}

function caseExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select cid from `case` where cid='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function reasonExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select rid from Reason where rid='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function appointmentExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select aid from appointment where aid='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function caseinfoExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select kid from caseinfo where kid ='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function userExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select uid from users where uid='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function appExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select aid from appointment where aid='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function getRegionById($data)
{
    global $conn;
    $query = mysqli_query($conn, "select name from regions where id='$data'");
    $result = mysqli_fetch_array($query)["name"];
    return $result;
}
function getZoneById($data)
{
    global $conn;
    $query = mysqli_query($conn, "select name from zones where id='$data'");
    $result = mysqli_fetch_array($query)["name"];
    return $result;
}
function getWoredaById($data)
{
    global $conn;
    $query = mysqli_query($conn, "select name from woredas where id='$data'");
    $result = mysqli_fetch_array($query)["name"];
    return $result;
}
function getUserByID($data)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM users WHERE uid = '$data'");
    $result = mysqli_fetch_array($query);
    if ($result && isset($result["password"])) {
        $result["password"] = decryptPassword($result["password"]);
    }
    return $result;
}

function getStudentByID($student_id) {
    global $conn;
    $student_id = mysqli_real_escape_string($conn, $student_id);
    $query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$student_id'");
    if (!$query) {
        die("Query error: " . mysqli_error($conn));
    }
    return mysqli_fetch_assoc($query);
}
function getStudentSidByID($id) {
    global $conn;
    $id = (int)$id; // make sure it's an integer
    $query = mysqli_query($conn, "SELECT * FROM students WHERE sid = $id");
    if (!$query) {
        die("Query error: " . mysqli_error($conn));
    }
    return mysqli_fetch_assoc($query);
}


function getUserIdNumberByID($data)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT IdNumber FROM users WHERE uid = '$data'");
    $result = mysqli_fetch_array($query);
    if ($result && isset($result["password"])) {
        $result["password"] = decryptPassword($result["password"]);
    }
    return $result;
}
function getCaseInfoByID($data)
{
    global $conn;
    $query = mysqli_query($conn, "select * from case_info where `case_id`='$data'");
    $result = mysqli_fetch_array($query);
    return $result;
}

function getCaseByID($data)
{
    global $conn;
        $query = mysqli_query($conn, "select * from `case` where cid ='$data'");
        $result = mysqli_fetch_array($query);
        return $result;
}
function getCaseByCase_ID($data)
{
    global $conn;
        $query = mysqli_query($conn, "select * from `case` where `case_id` ='$data'");
        $result = mysqli_fetch_array($query);
        return $result;
}

function getUserType($data) {
    global $conn;
    $query = mysqli_query($conn, "SELECT user_type FROM users WHERE uid = '$data'");
    $result = mysqli_fetch_assoc($query);
    return $result; 
}

function getAssignedJudgeType($data) {
    global $conn;
    $query = mysqli_query($conn, "SELECT judge_type FROM assigned_judges WHERE user_id = '$data'");
    $result = mysqli_fetch_assoc($query);
    return $result;
}

function getAssignedCasesWithAppointments() {
    global $conn;   
    $query = mysqli_query($conn, "  SELECT ap.case_id,c.case_id, ap.reason_id, ap.appointment_date,
     c.plaintiff, c.defendant, c.case_status, r.appointment_reason, c.cid
        FROM appointment ap
        JOIN `case` c ON ap.case_id = c.cid
        JOIN reason r ON ap.reason_id = r.rid
          ORDER BY ap.case_id ASC ");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
 } 


 function gregorianToEthiopian($year, $month, $day) {
    $jd = gregoriantojd($month, $day, $year);
    $ethioEpoch = 1723856; // Ethiopian Epoch (Julian Day Number)
    $r = ($jd - $ethioEpoch) % 1461;
    $n = ($r % 365) + 365 * intval($r / 1460);

    $ethYear = 1 + 4 * intval(($jd - $ethioEpoch) / 1461) + intval($n / 365);
    $ethMonth = 1 + intval(($n % 365) / 30);
    $ethDay = 1 + ($n % 365) % 30;

    return [$ethYear, $ethMonth, $ethDay];
}

function getAssignedCasesWithAppointmentReason() {
    global $conn;

    // Get today's date in Ethiopian calendar
    $ethiopian = \Andegna\DateTimeFactory::now();
    $current_date = $ethiopian->format('Y-m-d');

    // Use $current_date in your query
    $query = mysqli_query($conn, "
        SELECT ap.case_id, c.case_id, ap.reason_id, ap.appointment_date,
               c.plaintiff, c.defendant, c.case_status, r.appointment_reason, c.cid
        FROM appointment ap
        JOIN `case` c ON ap.case_id = c.cid
        JOIN reason r ON ap.reason_id = r.rid
        WHERE ap.reason_id = 5
          AND ap.appointment_date <= '$current_date'
        ORDER BY ap.case_id ASC
        LIMIT 1
    ");

    $result = [];
    while ($row = mysqli_fetch_array($query)) {
        $result[] = $row;
    }
    return $result;
}



 function sendConfirmationEmail($case_id) {
    global $conn;
    $sql = " SELECT ci.litigant_type, ci.email, ci.first_name, ci.father_name,ci.grandfather_name,
             c.cid  AS case_id,c.case_id AS id ,a.appointment_date, r.appointment_reason 
        FROM case_info ci
        JOIN `case` c ON ci.case_id = c.cid
        LEFT JOIN appointment a ON a.case_id = c.cid
        LEFT JOIN reason r ON a.reason_id = r.rid
        WHERE c.cid = '$case_id'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $recipient_email = $row['email'];
            $recipient_name = $row['first_name'] . ' ' . $row['father_name'].''.$row['grandfather_name'];
            $litigant_type = ucfirst(strtolower($row['litigant_type'])); // "Plaintiff" or "Defendant"
            $appointment_date = $row['appointment_date'];
            $appointment_reason = $row['appointment_reason'];
            $row_case_id = $row['id'];
            if (empty($recipient_email)) {
                echo "Recipient email not found for $litigant_type in case ID: $case_id.<br>";
                continue;  // skip to next recipient
            }
            if (empty($appointment_date) || empty($appointment_reason)) {
                echo "Appointment details not found for case ID: $case_id.<br>";
                return;  // stop if appointment info missing
            }
            // Send email
            $mail = new PHPMailer();
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'yekiworedacourt@gmail.com';
                $mail->Password = 'gsfrfwytrqkivgag';
                $mail->Port = 465;
                $mail->SMTPSecure = "ssl";

                $mail->setFrom('yekiworedacourt@gmail.com', 'Yeki Woreda Court');
                $mail->addReplyTo('yekiworedacourt@gmail.com', 'Yeki Woreda Court');
                $mail->addAddress($recipient_email);
                $mail->isHTML(true);
                $mail->Subject = "Court Appointment - Case ID: $row_case_id";

             $mail->Body = '
<div style="font-family: \'Segoe UI\', Roboto, sans-serif; max-width: 600px; margin: auto; background: #f4f4f4; padding: 30px;">
    <div style="border-radius: 10px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background-color: #ecf0f1; padding: 25px; text-align: center;">
            <h2 style="color: #2c3e50; margin: 0; font-size: 24px;">የፍርድ ቤት ቀጠሮ ማስታወቂያ</h2>
            <p style="color: #2980b9; font-size: 16px; margin: 5px 0 0;">Court Appointment Notification</p>
        </div>

        <!-- Body -->
        <div style="background-color: #ffffff; padding: 30px;">
            <!-- Amharic Section -->
            <p style="font-size: 16px; color: #333;"><strong>ውድ ' . htmlspecialchars($litigant_type) . ' ' . htmlspecialchars($recipient_name) . ',</strong></p>
            <p style="font-size: 15px; color: #555;">ለእርስዎ የተመደበ የክስ ቀጠሮ ዝርዝሮች እንደሚከተሉት ናቸው።</p>

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 15px;">
                <tr>
                    <td style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;"><strong>የክስ መለያ:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row_case_id) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;"><strong>የቀጠሮ ቀን:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($appointment_date) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;"><strong>የቀጠሮ ምክንያት:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($appointment_reason) . '</td>
                </tr>
            </table>

            <p style="font-size: 15px; color: #444;">እባኮትን በቀጠሮው ቀን ተዘጋጅተው ይገኙ።</p>

            <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

            <!-- English Section -->
            <p style="font-size: 16px; color: #333;"><strong>Dear ' . htmlspecialchars($litigant_type) . ' ' . htmlspecialchars($recipient_name) . ',</strong></p>
            <p style="font-size: 15px; color: #555;">An appointment has been scheduled for your case with the following details:</p>

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 15px;">
                <tr>
                    <td style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;"><strong>Case ID:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row_case_id) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;"><strong>Appointment Date:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($appointment_date) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;"><strong>Reason:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($appointment_reason) . '</td>
                </tr>
            </table>

            <p style="font-size: 15px; color: #444;">Please make sure to be present and prepared on the scheduled date.</p>

        </div>

        <!-- Footer -->
        <div style="background-color: #ecf0f1; text-align: center; padding: 20px; font-size: 13px; color: #7f8c8d;">
            <p style="margin: 5px 0;">Regards, <br><strong>Yeki Woreda Court Administration</strong></p>
            <p style="margin: 5px 0;">Yeki Woreda Court, Bench Maji Zone, Ethiopia</p>
            <p style="margin: 5px 0;">Phone: +251-913744565</p>
            <p style="margin: 5px 0;">Email: <a href="mailto:yekiworedacourt@gmail.com" style="color: #2980b9; text-decoration: none;">yekiworedacourt@gmail.com</a></p>
        </div>
    </div>
</div>
';
             $mail->send();
             "Email notification sent successfully to $litigant_type $recipient_name ($recipient_email).<br>";
             sleep(2);

            } catch (Exception $e) {
                echo "Email could not be sent to $litigant_type $recipient_name. Mailer Error: {$mail->ErrorInfo}<br>";
            }
        }
    } else {
        echo "No Plaintiff or Defendant found for case ID: $case_id.";
    }
}

function getAssignedCasesWithAppointmentsToday() {
    global $conn;

    // Get today's Ethiopian date as string
    $ethiopian = \Andegna\DateTimeFactory::now();
    $ethiopian_date = $ethiopian->format('Y-m-d'); // e.g., '2016-10-11'

    // Direct string comparison since date is stored in Ethiopian format
    $query = mysqli_query($conn, "
        SELECT ap.case_id, c.case_id AS case_id_dup, ap.reason_id, ap.appointment_date,
               c.plaintiff, c.defendant, c.case_status, r.appointment_reason, c.cid
        FROM appointment ap
        JOIN `case` c ON ap.case_id = c.cid
        JOIN reason r ON ap.reason_id = r.rid
        WHERE ap.appointment_date = '$ethiopian_date'
        ORDER BY ap.case_id ASC
    ");

    $result = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
    }
    return $result;
}


function getAppointmentDetailsByCaseId($data) {
    global $conn;
    $sql = "SELECT a.appointment_date, r.appointment_reason, c.case_status ,c.cid, c.case_id,
            a.record_date, a.aid
            FROM appointment a
            JOIN reason r ON a.reason_id = r.rid
            JOIN `case` c ON a.case_id = c.cid
            WHERE a.case_id = '$data'
            ORDER BY a.appointment_date ASC";

    $result = mysqli_query($conn, $sql);
    $appointments = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
    }
    return $appointments;
}

function getAllUsers()
{
    global $conn;
    $query = mysqli_query($conn, "select * from users");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

function getAllRegions()
{
    global $conn;
    $query = mysqli_query($conn, "select * from regions");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

function getAllRoleType()
{
    global $conn;
    $query = mysqli_query($conn, "select * from role_type");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getRoleNameById($role_id) {
    global $conn;
    $role_id = (int)$role_id;  // cast to int for safety
    $query = "SELECT role_name FROM role_type WHERE rid = $role_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['role_name'];
    }
    return null;
}


function getAllJudges() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM users WHERE user_type = 'judge'"); 
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

function isJudgeAlreadyAssigned($conn, $case_id, $judge_id) {
    $query = "SELECT 1 FROM assigned_judges WHERE user_id = '$judge_id' AND case_id = '$case_id' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

function transferCaseToJudge($conn, $case_id, $old_judge_id, $new_judge_id) {
    $update_query = "UPDATE assigned_judges SET user_id = '$new_judge_id' WHERE case_id = '$case_id' AND user_id = '$old_judge_id'";
    if (mysqli_query($conn, $update_query)) {
        if (mysqli_affected_rows($conn) > 0) {
            return true;
        }
    }
    return false;
}

function getAllAssignedJudges() {
    global $conn;
    $query = mysqli_query($conn, " SELECT DISTINCT
            u.uid,
            u.idNumber,
            u.profile_pic,
            u.first_name,
            u.father_name,
            u.username
        FROM users u
        JOIN assigned_judges aj ON u.uid = aj.user_id
        WHERE u.user_type = 'judge'
    ");

    $result = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
    }

    return $result;
}

function getAssignedCasesByJudge($data) {
    global $conn;

    $query = mysqli_query($conn, " SELECT 
            c.case_id,
            c.plaintiff,
            c.defendant,
            c.case_status,
            c.cid,
            aj.judge_type
        FROM assigned_judges aj
        JOIN `case` c ON c.cid = aj.case_id
        JOIN users u ON u.uid = aj.user_id
        WHERE aj.user_id = '$data'
    ");

    $cases = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $cases[] = $row;
    }

    return $cases;
}

function getAllCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case`");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

function getAllCasesByID($data) {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE cid = '$data' ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getPendingCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 0 ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getOpenCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 1 ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getDistributedCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 2 ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getPendingAppointmentCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 3 || case_status = 4");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getAppointedCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 5 ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getPendingDecisionCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 6 || case_status = 7 ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getDecidedCases() {
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM `case` WHERE case_status = 8 ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getAssignedCasesByIdPendingAppointmentStatus($data) {
        global $conn;   
        $query = mysqli_query($conn, "
            SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,c.cid,
                   j.first_name, j.father_name, j.profile_pic, c.case_id, j.idNumber , j.username,
                   c.case_status ,c.cid
            FROM assigned_judges aj
            JOIN `case` c ON aj.case_id = c.cid
            JOIN users j ON aj.user_id = j.uid
            WHERE j.uid = '$data' AND c.case_status = 3 || c.case_status = 4
            ORDER BY aj.case_id ASC");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    }
function getAssignedCasesByIdAppointedStatus($data) {
    global $conn;   
    $query = mysqli_query($conn, "
        SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant, c.cid,
               j.first_name, j.father_name, j.profile_pic, c.case_id, j.idNumber, j.username, c.case_status
        FROM assigned_judges aj
        JOIN `case` c ON aj.case_id = c.cid
        JOIN users j ON aj.user_id = j.uid
        WHERE j.uid = '$data' 
        AND c.case_status = 5
        ORDER BY aj.case_id ASC
    ");
    
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

    function getAssignedCasesByIdPendingDecisionStatus($data) {
        global $conn;   
        $query = mysqli_query($conn, "
            SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,c.cid,
                   j.first_name, j.father_name, j.profile_pic, c.case_id, j.idNumber , j.username,c.case_status
                   ,c.cid
            FROM assigned_judges aj
            JOIN `case` c ON aj.case_id = c.cid
            JOIN users j ON aj.user_id = j.uid
            WHERE j.uid = '$data' AND c.case_status = 6 || c.case_status = 7
            ORDER BY aj.case_id ASC");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    }
  function getAssignedCasesByIdDecidedStatus($data) {
        global $conn;   
        $query = mysqli_query($conn, "
            SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,c.cid,
                   j.first_name, j.father_name, j.profile_pic, c.case_id, j.idNumber , j.username,c.case_status
                   ,c.cid
            FROM assigned_judges aj
            JOIN `case` c ON aj.case_id = c.cid
            JOIN users j ON aj.user_id = j.uid
            WHERE j.uid = '$data'
             AND c.case_status = 8
            ORDER BY aj.case_id ASC");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    }
function getAssignedCases() {
    global $conn;   
    $query = mysqli_query($conn, "  SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,
        j.first_name, j.father_name, j.profile_pic,c.case_id, j.idNumber , j.username,  c.case_status, c.cid
        FROM assigned_judges aj
        JOIN `case` c ON aj.case_id = c.cid
        JOIN users j ON aj.user_id = j.uid
        ORDER BY aj.case_id ASC ");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    } 
   function getAssignedJUdgesByCaseId($data) {
    global $conn;   
    $query = mysqli_query($conn, "  SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,
        j.first_name, j.father_name, j.profile_pic,c.case_id, j.idNumber , j.username,  c.case_status, c.cid
        FROM assigned_judges aj
        JOIN `case` c ON aj.case_id = c.cid
        JOIN users j ON aj.user_id = j.uid
        WHERE aj.case_id ='$data'");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    } 

    function getAssignedCaseCount($judge_id) {
    global $conn; 
    $judge_id = (int)$judge_id; 
    $query = "SELECT COUNT(*) as case_count FROM assigned_judges WHERE user_id = $judge_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['case_count'];
    }
    return 0;
}

function getAssignedCasesDisplayOnce() {
    global $conn;
    $query = mysqli_query($conn, "
        SELECT DISTINCT c.cid, c.case_id, c.plaintiff, c.defendant, c.case_status
        FROM `case` c
        JOIN assigned_judges aj ON aj.case_id = c.cid
        ORDER BY c.cid ASC
    ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
 

    function getAssignedCasesById($data) {
        global $conn;   
        $query = mysqli_query($conn, "
            SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,c.cid,
                   j.first_name, j.father_name, j.profile_pic, c.case_id, j.idNumber , j.username,c.case_status
                   ,c.cid
            FROM assigned_judges aj
            JOIN `case` c ON aj.case_id = c.cid
            JOIN users j ON aj.user_id = j.uid
            WHERE j.uid = '$data'
            ORDER BY aj.case_id ASC");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    }
    
    function getAllCaseInfo()
    {
        global $conn;
        $query = mysqli_query($conn, " SELECT case_info.*, case.case_id AS case_id 
        FROM case_info  JOIN `case` ON case_info.case_id = case.cid ");     
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    }
    function getAttachFilesByCaseId($case_id) {
        global $conn;
        $query = mysqli_query($conn, "SELECT af.*
         FROM attach_files af 
         JOIN `case` c ON af.case_id = c.cid
          WHERE c.case_id = '$case_id'");
        $files = array();
        while ($row = mysqli_fetch_assoc($query)) {
            $files[] = $row;
        }
        return $files;
    }    
   


    function getAllCaseInfoById($data)
    {
        global $conn;
        $query = mysqli_query($conn, " SELECT cf.*, c.* 
            FROM case_info cf
            JOIN `case` c ON cf.case_id = c.cid 
            WHERE c.case_id = '$data'
            ");
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, $row);
        }
        return $result;
    }
function getAllCasesByCid($cid) {
    global $conn;
    $query = mysqli_query($conn, "SELECT cf.*, c.* 
        FROM case_info cf
        JOIN `case` c ON cf.case_id = c.cid 
        WHERE cf.kid = '$cid'");
    $result = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
    }
    return $result;
}




    function getFilePagesByCaseInfoId($cid) {
        global $conn;
        $file_pages = "";
        $cid = mysqli_real_escape_string($conn, $cid);       
        $query = "SELECT file_pages FROM case_info WHERE case_id = '$cid'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $file_pages = $row['file_pages'];
        }    
        return $file_pages;
    }
    
function getCaseInfoForUpdateId($data)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT cf.*, c.* 
        FROM case_info cf
        JOIN `case` c ON cf.case_id = c.cid
        WHERE cf.kid = '$data'");
    return mysqli_fetch_assoc($query); 
}

function getAllReasons()
{
    global $conn;
    $query = mysqli_query($conn, "select * from Reason");
    $result = array();
    while ($row = mysqli_fetch_array(result: $query)) {
        array_push($result, $row);
    }
    return $result;
}

function getAllFeedbacks()
{
    global $conn;
    $query = mysqli_query($conn, "select * from feedback");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

function getConfirmAppointment($judge_id) {
    global $conn;

    $query = "
        SELECT ap.aid, ap.case_id, ap.appointment_date, ap.record_date, ap.is_confirmed,
               r.appointment_reason, 
               c.plaintiff, c.defendant, c.case_status, c.cid, c.case_id AS id
        FROM appointment ap
        JOIN `case` c ON ap.case_id = c.cid
        JOIN assigned_judges aj ON aj.case_id = c.cid
        JOIN reason r ON ap.reason_id = r.rid
        WHERE aj.user_id = '$judge_id'
        ORDER BY ap.record_date DESC
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $appointments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
        return $appointments;
    } else {
        return false;
    }
}


function getConfirmDecision($judge_id) {
    global $conn;
    $query = "
        SELECT ap.case_id, ap.appointment_date, r.appointment_reason, ap.aid, c.plaintiff,
         c.defendant,c.case_id ,ap.record_date, c.case_status ,c.decision,r.appointment_reason,c.cid
        FROM appointment ap
        JOIN `case` c ON ap.case_id = c.cid
        JOIN assigned_judges aj ON aj.case_id = c.cid
        JOIN reason r ON ap.reason_id = r.rid
        WHERE aj.user_id = '$judge_id'
         AND c.decision IS NOT NULL 
          AND c.decision != ''
           ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $appointments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
        return $appointments;
    } else {
        return false;
    }
}


function updateUser($uid, $idNumber, $profile_pic, $firstName, $fatherName, $gFatherName, $gender,
       $role_type, $username, $encryptedPassword, $email, $phone, $userStatus)
{
    global $conn;
    $query = mysqli_query($conn, "UPDATE users SET 
        idNumber='$idNumber', profile_picture='$profile_pic', first_name='$firstName',
        father_name='$fatherName', grandfather_name='$gFatherName', gender='$gender', 
        email='$email', phone='$phone', username='$username', password='$encryptedPassword',
         user_type='$role_type' WHERE uid='$uid'
    ");

    if ($query) {
        return 1;
    } else {
        echo "MySQL Error: " . mysqli_error($conn);
        return 0;
    }
}



    function updateCaseInfo($kid, $first_name, $father_name, $gfather_name, $gender, $email, $region,
    $zone, $woreda, $kebele, $wogen, $argument_money, $judgement_money,
    $phone, $litigant_type, $file_pages, $file)
    {
    global $conn;
    
    // Sanitize inputs

    
    $query = mysqli_query($conn, "UPDATE case_info SET 
        first_name='$first_name', father_name='$father_name', 
        grandfather_name='$gfather_name', gender='$gender', email='$email', region='$region',
        zone='$zone', woreda='$woreda', kebele='$kebele', wogen='$wogen', 
        argument_money='$argument_money', judgement_money='$judgement_money', phone='$phone', 
        litigant_type='$litigant_type', file_pages='$file_pages', file='$file' 
        WHERE kid='$kid'
    ");
    
    return $query ? 1 : 0;
    }

function isTeacherAssigned($uid) {
    global $conn;
    $checkQuery = mysqli_query($conn, "SELECT * FROM assign_teacher WHERE teacher_id = '$uid'");
    return mysqli_num_rows($checkQuery) > 0;
}


function deleteUserById($data)
{
    global $conn;
    $query = mysqli_query($conn, "DELETE from users WHERE uid ='$data'");
    if ($query)
        return 1;
    else
        return 0;
}

function deleteCaseById($data)
{
    global $conn;
    $checkQuery = mysqli_query($conn, "SELECT COUNT(*) as count FROM `case_info` WHERE `case_id` = '$data'");
    $result = mysqli_fetch_assoc($checkQuery);
    if ($result['count'] > 0) {
        return -1;
    }
    $query = mysqli_query($conn, "DELETE from `case` WHERE  `cid` ='$data'");
    if ($query)
        return 1;
    else
        return 0;
}

function deleteLitigantById($data)
{
    global $conn;
    $query = mysqli_query($conn, "DELETE from `case_info` WHERE kid ='$data'");
    if ($query)
        return 1;
    else
        return 0;
}

function updateUserStatus($status, $uid)
{
    global $conn;
    $query = mysqli_query($conn, "update users set user_status=$status where uid='$uid'");
    if ($query)
        return 1;
    else
        return 0;
}

function UpdateAppointment_date($aid, $appointment_date,$record_date) {
    global $conn;
    $query = mysqli_query($conn, "UPDATE appointment SET appointment_date='$appointment_date', record_date='$record_date' WHERE aid='$aid'");
    if ($query) {
        return 1;
    } else {
        return 0;
    }
}
function getAppointmentById($aid) {
    global $conn;
    $query = mysqli_query($conn, "SELECT appointment_date,record_date FROM appointment WHERE aid='$aid'");
    if ($query) {
        return mysqli_fetch_assoc($query);
    } else {
        return null;
    }
}
function updateIs_loggedIn($status, $uid)
{
    global $conn;
    $query = mysqli_query($conn, "update users set is_logged=$status where uid='$uid'");
    if ($query)
        return 1;
    else
        return 0;
}

function sendMessage($fullname, $email, $subject, $message)
{
    global $conn;
    // Sanitize and capitalize each value properly
    $fullname = mysqli_real_escape_string($conn, ucfirst($fullname));
    $email = mysqli_real_escape_string($conn, ucfirst($email));
    $subject = mysqli_real_escape_string($conn, ucfirst($subject));
    $message = mysqli_real_escape_string($conn, ucfirst($message));
    $query = mysqli_query($conn, "INSERT INTO feedback(full_name, email, subject, message)
    values('$fullname','$email','$subject', '$message')");
    if ($query)
        return 1;
    else
        return 0;
}

function getAllOpenCases() {
    global $conn;
    $query = "
    SELECT c.cid, c.case_id, c.plaintiff, c.defendant, c.case_status,
      SUM(CASE WHEN ci.litigant_type = 'plaintiff' THEN 1 ELSE 0 END) AS plaintiff_count,
      SUM(CASE WHEN ci.litigant_type = 'defendant' THEN 1 ELSE 0 END) AS defendant_count
    FROM `case` c
    LEFT JOIN case_info ci ON c.cid = ci.case_id
    WHERE c.case_id NOT IN (SELECT DISTINCT case_id FROM assigned_judges)
    GROUP BY c.cid, c.case_id, c.plaintiff, c.defendant, c.case_status
    HAVING plaintiff_count >= 1 AND defendant_count >= 1;
    ";
    
    $result = mysqli_query($conn, $query);
    $validCases = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $case_id = mysqli_real_escape_string($conn, $row['case_id']);
        $updateQuery = "UPDATE `case` SET case_status = 1 WHERE case_id = '$case_id'";
        mysqli_query($conn, $updateQuery);
        $validCases[] = [
            'cid' => $row['cid'],
            'case_id' => $row['case_id'],
            'plaintiff' => $row['plaintiff'],
            'defendant' => $row['defendant'],
            'case_status' => $row['case_status']
        ];
    }   
    return $validCases; 
}

function getAllOpenCasesForAssign() {
    global $conn;
    $query = "
    SELECT c.cid, c.case_id, c.plaintiff, c.defendant, c.case_status,
      SUM(CASE WHEN ci.litigant_type = 'plaintiff' THEN 1 ELSE 0 END) AS plaintiff_count,
      SUM(CASE WHEN ci.litigant_type = 'defendant' THEN 1 ELSE 0 END) AS defendant_count
    FROM `case` c
    LEFT JOIN case_info ci ON c.cid = ci.case_id
    WHERE c.case_id NOT IN (SELECT DISTINCT case_id FROM assigned_judges)
    GROUP BY c.case_id
    HAVING plaintiff_count >= 1 AND defendant_count >= 1;
    ";   
    $result = mysqli_query($conn, $query);
    $validCases = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $case_id = mysqli_real_escape_string($conn, $row['case_id']);
        $updateQuery = "UPDATE `case` SET case_status = 1 WHERE case_id = '$case_id'";
        mysqli_query($conn, $updateQuery);
        $validCases[] = [
             'cid' => $row['cid'],
            'case_id' => $row['case_id'],
            'plaintiff' => $row['plaintiff'],
            'defendant' => $row['defendant'],
            'case_status' => $row['case_status']
        ];
    }   
    return $validCases;   
}

function getAllCasesWithoutAjTable() {
    global $conn;
    $query = " SELECT  c.cid, c.case_id, c.plaintiff, c.defendant, c.case_status,
        SUM(CASE WHEN ci.litigant_type = 'plaintiff' THEN 1 ELSE 0 END) AS plaintiff_count,
        SUM(CASE WHEN ci.litigant_type = 'defendant' THEN 1 ELSE 0 END) AS defendant_count
    FROM `case` c
    LEFT JOIN case_info ci ON c.cid = ci.case_id
    LEFT JOIN assigned_judges aj ON c.cid = aj.case_id
    WHERE aj.case_id IS NULL
    GROUP BY c.cid
    HAVING plaintiff_count >= 1 AND defendant_count >= 1";   
    $result = mysqli_query($conn, $query);
    $cases = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }
    return $cases;
}

function searchUsers($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 
    $sql = "SELECT * FROM users WHERE
            uid LIKE '$searchTerm' OR
            idNumber LIKE '$searchTerm' OR
            username LIKE '$searchTerm' OR
            first_name LIKE '$searchTerm' OR
            user_type LIKE '$searchTerm'";
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $users;
}

function searchJudges($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 
    $sql = "SELECT * FROM users WHERE user_type = 'Judge' AND (
            uid LIKE '$searchTerm' OR
            username LIKE '$searchTerm' OR
            first_name LIKE '$searchTerm' OR
            idNumber LIKE '$searchTerm')";
            
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $users;
}

function searchFeedbacks($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 
    $sql = "SELECT * FROM feedback WHERE
            fid LIKE '$searchTerm' OR
            full_name LIKE '$searchTerm' OR
            `subject` LIKE '$searchTerm' OR
            email LIKE '$searchTerm'";

    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $users;
}

function searchCases($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 
    $sql = "SELECT * FROM `case` WHERE
            case_id LIKE '$searchTerm' OR
            plaintiff LIKE '$searchTerm' OR
            defendant LIKE '$searchTerm' OR
            case_status LIKE '$searchTerm'";

    $result = mysqli_query($conn, $sql);
    $cases = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $cases;
}

function searchCaseInfo($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 

    $sql = "SELECT case_info.*, `case`.*
            FROM case_info 
            INNER JOIN `case` ON case_info.case_id = `case`.cid 
            WHERE
                `case`.case_id LIKE '$searchTerm' OR
                case_info.first_name LIKE '$searchTerm' OR
                case_info.father_name LIKE '$searchTerm' OR
                case_info.grandfather_name LIKE '$searchTerm' OR
                case_info.gender LIKE '$searchTerm' OR
                case_info.email LIKE '$searchTerm' OR
                case_info.kebele LIKE '$searchTerm' OR
                case_info.wogen LIKE '$searchTerm' OR
                case_info.phone LIKE '$searchTerm' OR
                case_info.litigant_type LIKE '$searchTerm'";
    $result = mysqli_query($conn, $sql);
    $cases = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $cases;
}

function searchAssignedCase($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 
    $sql = "SELECT aj.case_id, aj.user_id, aj.judge_type, c.plaintiff, c.defendant,
                   j.first_name, j.father_name, j.profile_pic ,c.case_id, j.idNumber , j.username
            FROM assigned_judges aj
            JOIN `case` c ON aj.case_id = c.cid
            JOIN users j ON aj.user_id = j.uid
            WHERE 
                c.case_id LIKE '$searchTerm' OR
                aj.judge_type LIKE '$searchTerm' OR
                j.username LIKE '$searchTerm'
            ORDER BY aj.case_id ASC";
    $result = mysqli_query($conn, $sql);
    $cases = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $cases;
}

function searchAppointedCase($searchTerm) {
    global $conn; 
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $searchTerm = "%" . $searchTerm . "%"; 
    $sql = "SELECT aj.case_id, aj.appointment_date, aj.record_date, c.plaintiff, c.defendant,
                r.appointment_reason, c.case_id 
            FROM appointment aj
            JOIN `case` c ON aj.case_id = c.cid
            JOIN reason r ON aj.reason_id = r.rid
            WHERE 
                c.case_id LIKE '$searchTerm' OR
                aj.appointment_date LIKE '$searchTerm' OR
                aj.record_date LIKE '$searchTerm' 
            ORDER BY aj.case_id ASC";
    $result = mysqli_query($conn, $sql);
    $cases = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $cases;
}


function getCaseSummaryData() {
    global $conn;
    $caseStatuses = [
        0 => 'PENDING',
        1 => 'OPENED',
        2 => 'DISTRIBUTED',
        3 => 'APPOINTMENT GIVEN',
        4 => 'WAITING CONFIRM',
        5 => 'APPOINTED',
        6 => 'DECISION GIVEN',
        7 => 'WAITING DECISION',
        8 => 'DECIDED'
    ];
    $caseTypes = [
        1 => 'CRIMINAL',
        2 => 'CIVIL',
        3 => 'FAMILY',
        4 => 'LABOR',
    ];
    $data = [
        'status' => [],
        'type' => []
    ];
    // Case status count
    $sqlStatus = "SELECT case_status, COUNT(*) as count FROM `case` GROUP BY case_status";
    $resStatus = mysqli_query($conn, $sqlStatus);
    while ($row = mysqli_fetch_assoc($resStatus)) {
        $statusCode = $row['case_status'];
        $data['status'][] = [
            'label' => $caseStatuses[$statusCode] ?? 'Unknown',
            'count' => $row['count']
        ];
    }
    // Case type count
    $sqlType = "SELECT case_type, COUNT(*) as count FROM `case` GROUP BY case_type";
    $resType = mysqli_query($conn, $sqlType);
    while ($row = mysqli_fetch_assoc($resType)) {
        $typeCode = $row['case_type'];
        $data['type'][] = [
            'label' => $caseTypes[$typeCode] ?? 'Unknown',
            'count' => $row['count']
        ];
    }
    return $data;
}


// report generation
//litigant report
function getFilteredCaseInfoReports($gender, $region, $zone, $woreda, $litigant_type, $wogen) {
    global $conn;

    $caseInfoFilters = [];
    if (!empty($gender))         $caseInfoFilters[] = "ci.gender = '".mysqli_real_escape_string($conn, $gender)."'";
    if (!empty($region))         $caseInfoFilters[] = "ci.region = '".mysqli_real_escape_string($conn, $region)."'";
    if (!empty($zone))           $caseInfoFilters[] = "ci.zone = '".mysqli_real_escape_string($conn, $zone)."'";
    if (!empty($woreda))         $caseInfoFilters[] = "ci.woreda = '".mysqli_real_escape_string($conn, $woreda)."'";
    if (!empty($litigant_type))  $caseInfoFilters[] = "ci.litigant_type = '".mysqli_real_escape_string($conn, $litigant_type)."'";
    if (!empty($wogen))          $caseInfoFilters[] = "ci.wogen = '".mysqli_real_escape_string($conn, $wogen)."'";

    $sql = "SELECT c.case_id, c.plaintiff, c.defendant, ct.name as case_type, 
                   c.case_status, c.ethiopian_date,
                   ci.first_name, ci.father_name, ci.grandfather_name, ci.gender,
                   r.name as region_name, z.name as zone_name, w.name as woreda_name,
                   ci.litigant_type, ci.wogen
            FROM `case` c
            LEFT JOIN case_type ct ON c.case_type = ct.ctid
            LEFT JOIN case_info ci ON ci.case_id = c.cid
            LEFT JOIN regions r ON ci.region = r.id
            LEFT JOIN zones z ON ci.zone = z.id
            LEFT JOIN woredas w ON ci.woreda = w.id";
            
    if (!empty($caseInfoFilters)) {
        $sql .= " WHERE " . implode(" AND ", $caseInfoFilters);
    }
    
    $sql .= " ORDER BY c.ethiopian_date DESC";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn));
        return [];
    }
    
    $cases = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }

    return $cases;
}
//general report
function getFilteredCaseReports($status, $case_type, $created_filter) {
    global $conn;
    $caseFilters = [];
    
    // Filter status
    if (!empty($status)) {
        $caseFilters[] = "c.case_status = '" . mysqli_real_escape_string($conn, $status) . "'";
    }
    
    // Filter case type
    if (!empty($case_type)) {
        $caseFilters[] = "c.case_type = '" . mysqli_real_escape_string($conn, $case_type) . "'";
    }
    
    // Date range filters
    if (!empty($_GET['from_created']) && !empty($_GET['to_created'])) {
        $from = mysqli_real_escape_string($conn, $_GET['from_created']);
        $to = mysqli_real_escape_string($conn, $_GET['to_created']);
        $caseFilters[] = "c.ethiopian_date BETWEEN '$from' AND '$to'";
    }
    
    if (!empty($_GET['from_distributed']) && !empty($_GET['to_distributed'])) {
        $from = mysqli_real_escape_string($conn, $_GET['from_distributed']);
        $to = mysqli_real_escape_string($conn, $_GET['to_distributed']);
        $caseFilters[] = "c.distributed_date BETWEEN '$from' AND '$to'";
    }
    
    if (!empty($_GET['from_end']) && !empty($_GET['to_end'])) {
        $from = mysqli_real_escape_string($conn, $_GET['from_end']);
        $to = mysqli_real_escape_string($conn, $_GET['to_end']);
        $caseFilters[] = "r.resolution_date BETWEEN '$from' AND '$to'";
    }
    
    // Time filter shortcuts
    switch ($created_filter) {
        case 'daily':
            $caseFilters[] = "DATE(c.ethiopian_date) = CURDATE()";
            break;
        case 'weekly':
            $caseFilters[] = "YEARWEEK(c.ethiopian_date, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'yearly':
            $caseFilters[] = "YEAR(c.ethiopian_date) = YEAR(CURDATE())";
            break;
    }
    
    $sql = "
        SELECT 
            c.cid,
            c.case_id, 
            c.plaintiff, 
            c.defendant, 
            ct.name as case_type,
            c.case_status, 
            c.ethiopian_date as created_date, 
            c.distributed_date,
            c.decision, 
            IFNULL(r.resolution_date, '') AS end_date,
            (SELECT COUNT(*) FROM case_info ci WHERE ci.case_id = c.cid AND ci.litigant_type = 'plaintiff') AS total_plaintiff,
            (SELECT COUNT(*) FROM case_info ci WHERE ci.case_id = c.cid AND ci.litigant_type = 'defendant') AS total_defendent
        FROM `case` c
        LEFT JOIN case_type ct ON c.case_type = ct.ctid
        LEFT JOIN resolutions r ON c.cid = r.case_id
    ";
    
    if (!empty($caseFilters)) {
        $sql .= " WHERE " . implode(" AND ", $caseFilters);
    }
    
    $sql .= " ORDER BY c.ethiopian_date DESC";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn));
        return [];
    }
    
    $cases = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Ensure all fields are set with default values
        $cases[] = [
            'cid' => $row['cid'] ?? '',
            'case_id' => $row['case_id'] ?? '',
            'plaintiff' => $row['plaintiff'] ?? '',
            'defendant' => $row['defendant'] ?? '',
            'case_type' => $row['case_type'] ?? '',
            'case_status' => $row['case_status'] ?? '',
            'created_date' => $row['created_date'] ?? '',
            'distributed_date' => $row['distributed_date'] ?? '',
            'decision' => $row['decision'] ?? '',
            'end_date' => $row['end_date'] ?? '',
            'total_plaintiff' => $row['total_plaintiff'] ?? '0',
            'total_defendent' => $row['total_defendent'] ?? '0'
        ];
    }
    return $cases;
}

function getAllGenders() {
    global $conn;
    $sql = "SELECT DISTINCT gender FROM case_info";
    $result = mysqli_query($conn, $sql);
    $genders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $genders[] = $row['gender'];
    }
    return $genders;
}
function getAllCaseStatuses() {
    global $conn;
    $sql = "SELECT DISTINCT case_status FROM `case`";
    $result = mysqli_query($conn, $sql);
    $statuses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $statuses[] = $row['case_status'];
    }
    return $statuses;
}
function getRegions() {
    global $conn;
    $sql = "SELECT DISTINCT r.id, r.name
            FROM regions r
            INNER JOIN case_info ci ON ci.region = r.id
            ORDER BY r.name";
    $result = mysqli_query($conn, $sql);
    $regions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $regions[$row['id']] = $row['name'];
    }
    return $regions;
}
function getZones() {
    global $conn;
    $sql = "SELECT DISTINCT z.id, z.name
            FROM zones z
            INNER JOIN case_info ci ON ci.zone = z.id
            ORDER BY z.name";
    $result = mysqli_query($conn, $sql);
    $zones = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $zones[$row['id']] = $row['name'];
    }
    return $zones;
}
function getWoredas() {
    global $conn;
    $sql = "SELECT DISTINCT w.id, w.name
            FROM woredas w
            INNER JOIN case_info ci ON ci.woreda = w.id
            ORDER BY w.name";
    $result = mysqli_query($conn, $sql);
    $woredas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $woredas[$row['id']] = $row['name'];
    }
    return $woredas;
}
function getLitigantTypes() {
    global $conn;
    $sql = "SELECT DISTINCT litigant_type FROM case_info WHERE litigant_type IS NOT NULL";
    $result = mysqli_query($conn, $sql);
    $types = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $types[$row['litigant_type']] = ucfirst($row['litigant_type']);
    }
    return $types;
}
function getCaseTypes() {
    global $conn;
    $sql = "SELECT ctid, name FROM case_type ORDER BY name";
    $result = mysqli_query($conn, $sql);
    $types = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $types[$row['ctid']] = $row['name'];
    }
    return $types;
}
function getWogens() {
    global $conn;
    $sql = "SELECT DISTINCT wogen FROM case_info WHERE wogen IS NOT NULL ORDER BY wogen";
    $result = mysqli_query($conn, $sql);
    $wogens = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $wogens[$row['wogen']] = ucfirst($row['wogen']);
    }
    return $wogens;
}
//end report generation


?>




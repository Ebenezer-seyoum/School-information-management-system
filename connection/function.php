<?php
include 'connection.php';
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
    if (preg_match("/^[a-zA-Z0-9 \/ `|]*$/", $data))
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

function getSectionNameById($conn, $section_id) {
    $section_id = (int)$section_id;
    $query = "SELECT section_name FROM sections WHERE cid='$section_id' LIMIT 1";
    $res = mysqli_query($conn, $query);
    if($res && mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        return $row['section_name'];
    }
    return "Unknown Section";
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


function addUser($idNumber, $profile_pic, $firstName, $fatherName, $gFatherName, $gender, 
$role_type, $username, $encrypted_password, $email, $phone, $userStatus)
{
    global $conn;

    // Make sure to properly quote strings and escape special characters
    $idNumber = mysqli_real_escape_string($conn, $idNumber);
    $profile_pic = mysqli_real_escape_string($conn, $profile_pic);
    $firstName = mysqli_real_escape_string($conn, $firstName);
    $fatherName = mysqli_real_escape_string($conn, $fatherName);
    $gFatherName = mysqli_real_escape_string($conn, $gFatherName);
    $gender = mysqli_real_escape_string($conn, $gender);
    $role_type = mysqli_real_escape_string($conn, $role_type);
    $username = mysqli_real_escape_string($conn, $username);
    $encrypted_password = mysqli_real_escape_string($conn, $encrypted_password);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);

    $query = "INSERT INTO users 
        (idNumber, profile_picture, first_name, father_name, grandfather_name, gender,
         user_type, username, password, email, phone, user_status)
        VALUES 
        ('$idNumber', '$profile_pic', '$firstName', '$fatherName', '$gFatherName', '$gender',
         '$role_type', '$username', '$encrypted_password', '$email', '$phone', $userStatus)";

    if (mysqli_query($conn, $query)) {
        return 1; // success
    } else {
        // optional: for debugging
        // echo "Error: " . mysqli_error($conn);
        return 0; // failure
    }
}


// Function to check if a student exists by ID
function studentExist($student_id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT student_id FROM students WHERE student_id='$student_id'");
    return mysqli_num_rows($query); // returns 0 if not found, >0 if exists
}


// // Function to register a new student
// function registerStudent( $student_photo, $student_id, $first_name, $father_name, $grand_father_name,
//     $gender, $dob, $email, $phone, $birth_place, $nationality,
//     $region, $zone, $woreda, $kebele, $username, $password,
//     $mother_name = null, $father_contact = null, $mother_contact = null,
//     $father_occupation = null, $mother_occupation = null,
//     $emergency_contact_name = null, $emergency_contact_phone = null,
//     $blood_group = null, $medical_condition = null, $other_condition = null,
//     $disabilities = null, $previous_school = null,
//     $previous_documents = null)
// {
//     global $conn;
//     // Sanitize variables
//     $student_id = mysqli_real_escape_string($conn, $student_id);
//     $first_name = mysqli_real_escape_string($conn, $first_name);
//     $father_name = mysqli_real_escape_string($conn, $father_name);
//     $grand_father_name = mysqli_real_escape_string($conn, $grand_father_name);
//     $gender = mysqli_real_escape_string($conn, $gender);
//     $dob = mysqli_real_escape_string($conn, $dob);
//     $email = mysqli_real_escape_string($conn, $email);
//     $phone = mysqli_real_escape_string($conn, $phone);
//     $birth_place = mysqli_real_escape_string($conn, $birth_place);
//     $nationality = mysqli_real_escape_string($conn, $nationality);
//     $region = mysqli_real_escape_string($conn, $region);
//     $zone = mysqli_real_escape_string($conn, $zone);
//     $woreda = mysqli_real_escape_string($conn, $woreda);
//     $kebele = mysqli_real_escape_string($conn, $kebele);
//     $username = mysqli_real_escape_string($conn, $username);
//     $password = mysqli_real_escape_string($conn, $password);
//     $mother_name = mysqli_real_escape_string($conn, $mother_name);
//     $father_contact = mysqli_real_escape_string($conn, $father_contact);
//     $mother_contact = mysqli_real_escape_string($conn, $mother_contact);
//     $father_occupation = mysqli_real_escape_string($conn, $father_occupation);
//     $mother_occupation = mysqli_real_escape_string($conn, $mother_occupation);
//     $emergency_contact_name = mysqli_real_escape_string($conn, $emergency_contact_name);
//     $emergency_contact_phone = mysqli_real_escape_string($conn, $emergency_contact_phone);
//     $blood_group = mysqli_real_escape_string($conn, $blood_group);
//     $medical_condition = mysqli_real_escape_string($conn, $medical_condition);
//     $other_condition = mysqli_real_escape_string($conn, $other_condition);
//     $disabilities = mysqli_real_escape_string($conn, $disabilities);
//     $previous_school = mysqli_real_escape_string($conn, $previous_school);
//     $previous_documents = mysqli_real_escape_string($conn, $previous_documents);

//     // INSERT query matching your table columns
//     $query = "
//         INSERT INTO students (
//             student_photo, student_id, first_name, father_name, grand_father_name, gender, dob, email, phone, birth_place, nationality,
//             region, zone, woreda, kebele, username password, mother_name, father_contact, mother_contact,
//             father_occupation, mother_occupation, emergency_contact_name, emergency_contact_phone, blood_group, medical_condition,
//             other_condition, disabilities, previous_school,, previous_documents
//         ) VALUES (
//             '$student_photo', '$student_id', '$first_name', '$father_name', '$grand_father_name', '$gender', '$dob', '$email', '$phone',
//             '$birth_place', '$nationality', '$region', '$zone', '$woreda', '$kebele', '$username','$password',
//             '$mother_name', '$father_contact', '$mother_contact', '$father_occupation', '$mother_occupation', '$emergency_contact_name',
//             '$emergency_contact_phone', '$blood_group', '$medical_condition', '$other_condition', '$disabilities', '$previous_school',
//              '$previous_documents'
//         )
//     ";
//     $query = mysqli_query($conn, $query);
//     if ($query) {
//         return 1;
//     } else {
//         echo "MySQL Error: " . mysqli_error($conn);
//         return 0;
//     }
// }

function registerStudent($mysql, $student_photo, $student_id, $first_name, $father_name, $grand_father_name, $gender, $dob, $email, $phone, $birth_place, $nationality, $region, $zone, $woreda, $kebele, $username, $password, $father_full_name, $mother_name, $father_contact, $mother_contact, $father_occupation, $mother_occupation, $emergency_contact_name, $emergency_contact_phone, $blood_group, $medical_condition, $other_condition, $disabilities, $previous_school, $previous_documents) {
    // Check if connection is valid
   global $conn;
    // Escape all string inputs to prevent SQL injection
    $student_photo = mysqli_real_escape_string($mysql, $student_photo);
    $student_id = mysqli_real_escape_string($mysql, $student_id);
    $first_name = mysqli_real_escape_string($mysql, $first_name);
    $father_name = mysqli_real_escape_string($mysql, $father_name);
    $grand_father_name = mysqli_real_escape_string($mysql, $grand_father_name);
    $gender = mysqli_real_escape_string($mysql, $gender);
    $dob = mysqli_real_escape_string($mysql, $dob);
    $email = mysqli_real_escape_string($mysql, $email);
    $phone = mysqli_real_escape_string($mysql, $phone);
    $birth_place = mysqli_real_escape_string($mysql, $birth_place);
    $nationality = mysqli_real_escape_string($mysql, $nationality);
    $region = mysqli_real_escape_string($mysql, $region);
    $zone = mysqli_real_escape_string($mysql, $zone);
    $woreda = mysqli_real_escape_string($mysql, $woreda);
    $kebele = mysqli_real_escape_string($mysql, $kebele);
    $username = mysqli_real_escape_string($mysql, $username);
    $password = mysqli_real_escape_string($mysql, $password);
    $father_full_name = mysqli_real_escape_string($mysql, $father_full_name);
    $mother_name = mysqli_real_escape_string($mysql, $mother_name);
    $father_contact = mysqli_real_escape_string($mysql, $father_contact);
    $mother_contact = mysqli_real_escape_string($mysql, $mother_contact);
    $father_occupation = mysqli_real_escape_string($mysql, $father_occupation);
    $mother_occupation = mysqli_real_escape_string($mysql, $mother_occupation);
    $emergency_contact_name = mysqli_real_escape_string($mysql, $emergency_contact_name);
    $emergency_contact_phone = mysqli_real_escape_string($mysql, $emergency_contact_phone);
    $blood_group = mysqli_real_escape_string($mysql, $blood_group);
    $medical_condition = mysqli_real_escape_string($mysql, $medical_condition);
    $other_condition = mysqli_real_escape_string($mysql, $other_condition);
    $disabilities = mysqli_real_escape_string($mysql, $disabilities);
    $previous_school = mysqli_real_escape_string($mysql, $previous_school);
    $previous_documents = mysqli_real_escape_string($mysql, $previous_documents);

    // Prepare the SQL query
    $query = "INSERT INTO students (
        student_photo, student_id, first_name, father_name, grand_father_name, gender, dob, email, phone, birth_place, nationality,
        region, zone, woreda, kebele, username, password, father_full_name, mother_name, father_contact, mother_contact,
        father_occupation, mother_occupation, emergency_contact_name, emergency_contact_phone, blood_group, medical_condition,
        other_condition, disabilities, previous_school, previous_documents
    ) VALUES (
        '$student_photo', '$student_id', '$first_name', '$father_name', '$grand_father_name', '$gender', '$dob', '$email', '$phone', '$birth_place', '$nationality',
        '$region', '$zone', '$woreda', '$kebele', '$username', '$password', '$father_full_name', '$mother_name', '$father_contact', '$mother_contact',
        '$father_occupation', '$mother_occupation', '$emergency_contact_name', '$emergency_contact_phone', '$blood_group', '$medical_condition',
        '$other_condition', '$disabilities', '$previous_school', '$previous_documents'
    )";

    // Execute the query
    if (mysqli_query($mysql, $query)) {
        return 1; // success
    } else {
        error_log("Query failed: " . mysqli_error($mysql));
        return 0; // failure
    }
}
// Function to get students not yet assigned
function getUnassignedStudents($conn) {
    $query = "
        SELECT * 
        FROM students 
        WHERE sid NOT IN (SELECT student_id FROM assign_student)
        ORDER BY first_name ASC
    ";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "MySQL Error: " . mysqli_error($conn);
        return []; // return empty array on error
    }

    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    return $students;
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

function add_section( $section_name, $role_type)
{
    global $conn;
    $section_name = mysqli_real_escape_string($conn, ucfirst($section_name));
    $role_type = mysqli_real_escape_string($conn, strtoupper($role_type));
    $query = mysqli_query($conn, "INSERT INTO sections (section_name , class_type)
    values('$section_name','$role_type')");
    if ($query)
        return 1;
    else
        return 0;
}
function add_subject( $subject_name, $abbreviation_name)
{
    global $conn;
    $subject_name = mysqli_real_escape_string($conn, ucfirst($subject_name));
    $abbreviation_name = mysqli_real_escape_string($conn, strtoupper($abbreviation_name));
    $query = mysqli_query($conn, "INSERT INTO subjects (subject_name , abbreviation_name)
    values('$subject_name','$abbreviation_name')");
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

function sectionExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select cid from sections where cid='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}
function subjectExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select suid from subjects where suid='$data'");
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
    $query = mysqli_query($conn, "select idNumber from users where idNumber ='$data'");
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
function getAllStudents()
{
    global $conn;
    $query = mysqli_query($conn, "select * from students");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getAllTeachers()
{
    global $conn;
    $query = mysqli_query($conn, "select * from users where user_type = 1");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}
function getAllInstructors()
{
    global $conn;
    $query = mysqli_query($conn, "
        SELECT DISTINCT u.*
        FROM users u
        INNER JOIN assign_instructor ai ON ai.instructor_id = u.uid
        WHERE u.user_type = 1
        ORDER BY u.first_name ASC
    ");
    $result = array();
    while ($row = mysqli_fetch_array($query)) {
        array_push($result, $row);
    }
    return $result;
}

function getAllSections()
{
    global $conn;
    $query = mysqli_query($conn, "select * from sections");
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


function getStudentCurrentSection($conn, $sid) {
    $query = "
        SELECT  a.section_id AS current_section_id, sec.section_name 
        FROM students s
        LEFT JOIN assign_student a ON s.sid = a.student_id 
        LEFT JOIN sections sec ON a.section_id = sec.cid
        WHERE s.sid = '$sid'
    ";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function transferStudent($conn, $sid, $old_section_id, $new_section_id) {
    $update_query = "UPDATE assign_student SET section_id = '$new_section_id' WHERE student_id = '$sid'";
    if (mysqli_query($conn, $update_query)) {
        if (mysqli_affected_rows($conn) > 0) {
            return true;
        }
    }
    return false;
}
function fetchAssignedStudents($conn, $search = '') {

    $students_query = "
    SELECT s.*,
           sec.section_name,
           sec.class_type,
           a.section_id AS current_section_id
    FROM students s
    INNER JOIN assign_student a 
           ON s.sid = a.student_id
    INNER JOIN sections sec 
           ON a.section_id = sec.cid
    WHERE 1
    ";

    if ($search) {
        $search_esc = mysqli_real_escape_string($conn, $search);
        $students_query .= " AND (s.student_id LIKE '%$search_esc%' 
                                 OR s.first_name LIKE '%$search_esc%' 
                                 OR s.father_name LIKE '%$search_esc%')";
    }

    $students_query .= " ORDER BY s.first_name ASC";
    return mysqli_query($conn, $students_query);
}

// Get current assignment for an instructor
function getInstructorCurrentAssignment($conn, $instructor_id) {
    $instructor_id = intval($instructor_id);
    $query = "
        SELECT ai.section_id AS current_section_id
        FROM assign_instructor ai
        WHERE ai.instructor_id = '$instructor_id'
        LIMIT 1
    ";
    $res = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($res);
}

// Function to transfer section only
function transferInstructorSectionOnly($conn, $instructor_id, $old_section, $new_section) {
    $instructor_id = intval($instructor_id);
    $new_section = intval($new_section);
    $old_section = intval($old_section);

    $update = "
        UPDATE assign_instructor
        SET section_id = '$new_section'
        WHERE instructor_id = '$instructor_id'
          AND section_id = '$old_section'
    ";
    if (!mysqli_query($conn, $update)) {
        throw new Exception("Failed to transfer instructor");
    }
}

 
// Fetch class info by atid
function getClassInfo($conn, $atid) {
    $atid = (int)$atid; // cast to int for safety
    $sql = "
        SELECT at.section_id, s.section_name, s.class_type, at.academic_year, sub.subject_name
        FROM assign_teacher at
        LEFT JOIN sections s ON at.section_id = s.cid
        LEFT JOIN subjects sub ON at.subject_id = sub.suid
        WHERE at.atid = $atid
        LIMIT 1
    ";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}
//class info for instructor
function getClassInfoInstructor($conn, $hid) {
    $hid = (int)$hid; // cast to int for safety
    $sql = "
        SELECT at.section_id, s.section_name, s.class_type, at.academic_year
        FROM assign_instructor at
        LEFT JOIN sections s ON at.section_id = s.cid
        
        WHERE at.hid = $hid
        LIMIT 1
    ";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}
// Fetch students by section and academic year
function getStudentsBySection($conn, $section_id, $academic_year) {
    $section_id = (int)$section_id;
    $academic_year = (int)$academic_year;

    $sql = "
        SELECT u.sid, u.first_name, u.father_name, u.gender,u.student_id, u.student_photo
        FROM assign_student ast
        LEFT JOIN students u ON ast.student_id = u.sid
        WHERE ast.section_id = $section_id AND ast.academic_year = $academic_year
        ORDER BY u.first_name ASC
    ";
    $res = mysqli_query($conn, $sql);
    $students = [];
    if ($res) {
        while($r = mysqli_fetch_assoc($res)) {
            $students[] = $r;
        }
    }
    return $students;
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



function updateStudent($student_id, $student_photo, $firstName, $fatherName, $gFatherName, $gender,
    $email, $nationality, $region, $zone, $woreda, $kebele, $dob, $birth_place,
    $emergency_contact_name, $emergency_contact_phone, $username, $encryptedPassword,
    $phone, $father_full_name, $mother_name, $father_contact, $mother_contact,
    $father_occupation, $mother_occupation, $blood_group, $medical_condition,
    $other_condition, $disabilities, $previous_school, $previous_documents)
{
    global $conn;
    $query = mysqli_query($conn, "UPDATE studens SET 
        profile_picture='$student_photo', 
        first_name='$firstName',
        father_name='$fatherName', 
        grandfather_name='$gFatherName', 
        gender='$gender',
        email='$email', 
        nationality='$nationality',
        region='$region',
        zone='$zone',
        woreda='$woreda',
        kebele='$kebele',
        date_of_birth='$dob',
        birth_place='$birth_place',
        emergency_contact_name='$emergency_contact_name',
        emergency_contact_phone='$emergency_contact_phone',
        username='$username',
        password='$encryptedPassword',
        phone='$phone',
        father_full_name='$father_full_name',
        mother_name='$mother_name',
        father_contact='$father_contact',
        mother_contact='$mother_contact',
        father_occupation='$father_occupation',
        mother_occupation='$mother_occupation',
        blood_group='$blood_group',
        medical_condition='$medical_condition',
        other_condition='$other_condition',
        disabilities='$disabilities',
        previous_school='$previous_school',
        previous_documents='$previous_documents'
        WHERE student_id='$student_id'
    ");

    if ($query) {
        return 1;
    } else {
        echo "MySQL Error: " . mysqli_error($conn);
        return 0;
    }
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

function updateUserStatus($status, $uid)
{
    global $conn;
    $query = mysqli_query($conn, "update users set user_status=$status where uid='$uid'");
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
?>




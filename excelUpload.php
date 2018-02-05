<?php
include "menu.php";
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
    <input type="file" name="staffList" id="xlsx_uploads" name="xlsx_uploads" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
    <br>
    <input type="submit" name="Submitted" value="Upload">
</form>

<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $staffListFile = $_FILES['staffList']['tmp_name'];
        $fileError = $_FILES['staffList']['error'];
        $upload_dir = '/var/www/html/reftool/tmp/';

        if ($fileError) {
            switch ($fileError) {
                case 1:
                    echo "UPLOAD_ERR_INI_SIZE = Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                    break;
                case 2:
                    echo "UPLOAD_ERR_FORM_SIZE = Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                    break;
                case 3:
                    echo "UPLOAD_ERR_PARTIAL = Value: 3; The uploaded file was only partially uploaded.";
                    break;
                case 4:
                    echo "UPLOAD_ERR_NO_FILE = Value: 4; No file was uploaded.";
                    break;
                case 5:
                    echo "Error unknown.";
                    break;
                case 6:
                    echo "UPLOAD_ERR_NO_TMP_DIR = Value: 6; Missing a temporary folder. Introduced in PHP 5.0.3.";
                    break;
                case 7:
                    echo "UPLOAD_ERR_CANT_WRITE = Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.";
                    break;
                case 8:
                    echo "UPLOAD_ERR_EXTENSION = Value: 8; A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.";
                    break;
            }
        }

        $canProceed = false;
        if (is_dir($upload_dir)) {
            echo 'Upload directory exists <br>';
            $canProceed = true;
        } else {
            echo 'Upload directory does not exist. <br>';
        }

        if  (is_writable($upload_dir)) {
            echo 'Upload is  writable <br>';
        } else {
            echo 'Upload is not writable <br>';
            $canProceed = false;
        }

        if($canProceed) {                                                               // if no error messsage
            $staffListName = sha1_file($_FILES['staffList']['tmp_name']) . ".xlsx";     // Rename with unique name from its binary data.
            $fileSavePath = $upload_dir . $staffListName;
            $_SESSION['filePath'] = $fileSavePath;                                      // save path to session
            if (is_uploaded_file($staffListFile)) {                                     // check if editing the uploaded file. For security
                if (!move_uploaded_file($staffListFile,$fileSavePath)) {                // check if any problem moving file
                    echo 'Could not save file.<br>';
                } else {
                    echo 'upload worked: $fileSavePath';
                    header ('Location: /reftool/readExcel.php');
                }
            } else {
                echo 'You are trying to edit a different file from the uploaded one.';
            }
        }
    }
?>

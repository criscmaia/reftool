<body style="text-align:center">
<?php
    if (!array_key_exists('Submitted',$_POST)) {
?>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="staffList" id="xlsx_uploads" name="xlsx_uploads" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
        <br>
        <input type="submit" name="Submitted" value="Upload">
    </form>
    <?php
        } else {
            //process the  form
            $staffListFile = $_FILES['staffList']['tmp_name'];
//            $fileSize = $_FILES['staffList']['size'];
//            $fileType = $_FILES['staffList']['type'];
            $fileError = $_FILES['staffList']['error'];

            $staffListName=$_POST['FirstName'] . '_' . $_POST['LastName'] . '_staffList.xlsx';
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
                endPage();
            }

            $upload_dir = '/var/www/html/reftool/tmp/';

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

            $fileSavePath = $upload_dir . $staffListName;
            if (is_uploaded_file($staffListFile)) {
                echo "is uploaded work.";
                if (!move_uploaded_file($staffListFile,$fileSavePath)) {
                    echo 'Could not save file.';
                    endPage();
                }
            } else {
                //This case happens if somehow the file we are working with was already on the server. It's to stop hackers.
                echo 'Hey, what is going on here? Are you being bad?';
                endPage();
            }
    ?>
    <h2>Thanks!</h2>
    <b>We got your staffList.</b>
    <hr>
    <form>
        <textarea cols="60" rows="20"><?php echo $staffList ?></textarea>
    </form>
    <?php
}

function endPage() {
    echo '</body>';
    exit;
}

function makeFileSafe($filePath) {
    $fP = @fopen($filePath,'r+');
    if (!$fP) {
        return "Could not read file";
    }
    $contents = fread($fP,filesize($filePath));
    $contents = strip_tags($contents);
    rewind($fP);
    fwrite($fP,$contents);
    fclose($fP);
    return $contents;
}
?>

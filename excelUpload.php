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
            $fileSize = $_FILES['staffList']['size'];
            $fileType = $_FILES['staffList']['type'];
            $fileError = $_FILES['staffList']['error'];

            $staffListName=$_POST['FirstName'] . '_' . $_POST['LastName'] . '_staffList.xlsx';
            if ($fileError) {
                echo "We could not upload the file:<br>$fileError";
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
            $staffList=makeFileSafe($fileSavePath);
    ?>
    <h2>Thanks!</h2>
    <b>We got your staffList.</b>
    <hr>
    <form>
        <textarea cols="60" rows="20"><?php echo $staffList ?></textarea>
    </form>
    </p>
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




<?php
////    if ($_SERVER["REQUEST_METHOD"] == "POST") {
//        $target_dir = "reftool/tmp/";
//        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
//        $uploadOk = 1;
//        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
//        // Check if image file is a actual image or fake image
//        if(isset($_POST["submit"])) {
//            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
//            if($check !== false) {
//                echo "File is an image - " . $check["mime"] . ".";
//                $uploadOk = 1;
//            } else {
//                echo "File is not an image.";
//                $uploadOk = 0;
//            }
//        }
//
//
//        $uploaddir = '/var/www/html/reftool/tmp/';
//        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
//
//        echo '<pre>';
//        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
//            echo "File is valid, and was successfully uploaded.\n";
//        } else {
//            echo "Possible file upload attack!\n";
//        }
//
//        echo 'Here is some more debugging info:';
//        print_r($_FILES);
//
//        print "</pre>";
////    }
?>

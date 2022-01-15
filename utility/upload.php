<?php
    function uploadFiles($key)
    {
        $destination_path = getcwd().DIRECTORY_SEPARATOR;
        $target_dir = $destination_path . "/MVC/View/themes/default/src/images/upload/";
        $uploadedFiles = [];
        if ($_FILES[$key]['size'][0]>0) {
            for ($i=0; $i < count($_FILES[$key]["name"]); $i++) { 
                $target_file = $target_dir . time() . basename($_FILES[$key]["name"][$i]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                $check = getimagesize($_FILES[$key]["tmp_name"][$i]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
                }
    
                if ($_FILES[$key]["size"][$i] > 500000) {
                    $uploadOk = 0;
                  }
    
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $uploadOk = 0;
                }
    
                if ($uploadOk == 1) {
                    move_uploaded_file($_FILES[$key]["tmp_name"][$i], $target_file);
                    array_push($uploadedFiles,str_replace($target_dir,"",$target_file));
                }
            }
        }

        return $uploadedFiles;
    }
    


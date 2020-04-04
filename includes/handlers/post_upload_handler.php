<?php
if(isset($_POST['post'])){
    $uploadOk=1;

    $imageName= $_FILES['fileToUpload']['name'];
    $errMsg=NULL;
    
    if($imageName != NULL){
        $targetDir ="assets/images/posts/";
        $imageName ="$targetDir".uniqid().basename($imageName);
        $imageFileType=pathinfo($imageName,PATHINFO_EXTENSION);

        if($_FILES['fileToUpload']['size'] > 1000000){
            $errMsg = "Sorry your file is too large";
            $uploadOk=0;
        }
        if(
            strtolower($imageFileType) != "jpeg" &&
            strtolower($imageFileType) != "jpg" &&
            strtolower($imageFileType) != "png" &&
            strtolower($imageFileType) != "gif"
            && strtolower($imageFileType) != "bmp"
            ){
            $errMsg = "Sorry only jpeg jpg png gif bmp files are allowed";
            $uploadOk=0;
        }
        if($uploadOk){
            if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'],$imageName)){
                //image uploaded
            }else{
                //image did not uploaded
                $uploadOk =0;
            }
        }
    }
    //echo "<script>alert('Alert $imageName')</script>";
    if($uploadOk){
        $post = new Post($con,$userLoggedIn);
        $post->submitPost($_POST['post_text'],'none',$imageName);
    }else{
        
    }
}


?>
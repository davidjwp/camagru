<?php 	
    require_once '../functs.php';
	ob_start();
    session_start();

    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false]);
        exit;
    }

    $user_tmp = '/var/www/html/tmp/' . $_SESSION['user']['id'] . '/';
    if (!is_dir($user_tmp)) {
        mkdir($user_tmp, 0755, true);
        $_SESSION['tmp_dir'] = $user_tmp;
    }

    if (!isset($_SESSION['tmp_dir'])) {$_SESSION['tmp_dir'] = $user_tmp;}

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['get_thumbs'])) {
        $files = null;
        $tmp_dir = '/var/www/html/tmp/' . $_SESSION['user']['id'] . '/';
        if (is_dir($tmp_dir)) {
            $thumbnails = scandir($tmp_dir, SCANDIR_SORT_ASCENDING);
            array_splice($thumbnails, 0, 2);
            if (count($thumbnails)) $files = $thumbnails;
        }
        $json = ['files'=> $files, 'path' => '/tmp/'.$_SESSION['user']['id'].'/'];
        echo json_encode($json);
        exit ;
    }

    if (isset($data['delete_thumbnail'])) { 
        $thumb = basename($data['img_src']); 
        $thumb = $_SESSION['tmp_dir'].$thumb;
        unlink($thumb);
        exit ;
    }

    // decode webcam/uploaded image data
    $image_data = $data['image'];
    $image_data = preg_replace('/^data:image\/\w+;base64,/', '', $data['image']);
    $image_data = base64_decode($image_data);

    // create GD image from webcam data with alpha enabled
    $webcam = imagecreatefromstring($image_data);
    $width = imagesx($webcam);
    $height = imagesy($webcam);
    imagealphablending($webcam, true);
    imagesavealpha($webcam, true);
    
    // get sticker path
    $sticker_path = basename($data['sticker']);
    $sticker_full = '/var/www/html/Stickers/' . $sticker_path;
    
    if (!file_exists($sticker_full)) {
        echo json_encode(['success' => false, 'error' => 'sticker not found']);
        exit;
    }
    
    $sticker = imagecreatefrompng($sticker_full);
    // create sticker then alpha canvas then resize the sticker over that
    $sticker_w = imagesx($sticker);
    $sticker_h = imagesy($sticker);
    
    
    //create black canvas then copy webcam on top then copy sticker on top
    $output = imagecreatetruecolor($width, $height);
    imagecopy($output, $webcam, 0,0,0,0, $width, $height);  
    imagealphablending($output, true);
    
    $x = $data['stickerX'];
    $y = $data['stickerY'];
    
    // imagecopy($output, $sticker_resized, 0,0,0,0, $width, $height);
    imagecopy($output, $sticker,$x,$y,0,0, $sticker_w, $sticker_h);

    $filename = bin2hex(random_bytes(16)) . '.jpg';
    $dest = $user_tmp . $filename;
    imagejpeg($output, $dest, 90);

    $thumbnail = '/tmp/'.$_SESSION['user']['id'].'/'.$filename;

   // ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'file' => $thumbnail]);
    exit;

<?php 	
	ob_start();
    session_start();
    require_once 'functs.php';

    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false]);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['get_thumbs'])) {
        $thumbnails = scandir('/var/www/html/tmp/', SCANDIR_SORT_ASCENDING);
        array_splice($thumbnails, 0, 2);
        $files = ['files'=> $thumbnails];
        if (!count($thumbnails)) $files['files'] = null;

        echo json_encode($files);
        exit ;
    }

    if (isset($data['delete_thumbnail'])) { 
        $thumb = basename($data['img_src']);
        $thumb = '/var/www/html/tmp/'.$thumb;
        unlink($thumb);
        exit ;
    }

    // decode webcam image data
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
    
    // create sticker then alpha canvas then resize the sticker over that
    $sticker = imagecreatefrompng($sticker_full);
    $sticker_w = imagesx($sticker);
    $sticker_h = imagesy($sticker);

    $sticker_resized = imagecreatetruecolor($width, $height);
    imagealphablending($sticker_resized, false);
    imagesavealpha($sticker_resized, true);
    $transparent = imagecolorallocatealpha($sticker_resized, 0, 0, 0, 127);
    imagefill($sticker_resized, 0, 0, $transparent);
    imagecopyresampled($sticker_resized, $sticker, 0, 0, 0, 0, $width, $height, $sticker_w, $sticker_h);

    //create black canvas then copy webcam on top then copy sticker on top
    $output = imagecreatetruecolor($width, $height);
    imagecopy($output, $webcam, 0,0,0,0, $width, $height);  
    imagealphablending($output, true);
    imagecopy($output, $sticker_resized, 0,0,0,0, $width, $height);

    // save to tmp
    $filename = bin2hex(random_bytes(16)) . '.jpg';
    $dest = '/var/www/html/tmp/' . $filename;
    imagejpeg($output, $dest, 90);

    // track in session
    if (!isset($_SESSION['tmp_images'])) $_SESSION['tmp_images'] = [];
        $_SESSION['tmp_images'][] = $filename;

    // $files = scandir('/var/www/html/tmp/', SCANDIR_SORT_ASCENDING);

    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'file' => $filename]);
    exit;

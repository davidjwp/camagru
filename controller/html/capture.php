<?php 	
	ob_start();
    session_start();
    require_once 'functs.php';

   
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false]);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
        
    // decode base64 webcam image
    $image_data = $data['image'];
    $image_data = preg_replace('/^data:image\/\w+;base64,/', '', $data['image']);
    $image_data = base64_decode($image_data);

    // create GD image from webcam data
    $webcam = imagecreatefromstring($image_data);
    $width = imagesx($webcam);
    $height = imagesy($webcam);
    imagealphablending($webcam, true);
    imagesavealpha($webcam, true);

    // load sticker — validate path to prevent directory traversal
    $sticker_path = basename($data['sticker']);
    $sticker_full = '/var/www/html/Stickers/' . $sticker_path;
    
    if (!file_exists($sticker_full)) {
        echo json_encode(['success' => false, 'error' => 'sticker not found']);
        exit;
    }
    
    $sticker = imagecreatefrompng($sticker_full);
    
    // scale sticker to webcam size
    $sticker_resized = imagescale($sticker, $width, $height);

    // composite — copy sticker over webcam preserving transparency
    imagecopy($webcam, $sticker_resized, 0, 0, 0, 0, $width, $height);

    // save to tmp
    $filename = bin2hex(random_bytes(16)) . '.jpg';
    $dest = '/var/www/html/tmp/' . $filename;
    imagejpeg($webcam, $dest, 90);

    // track in session
    if (!isset($_SESSION['tmp_images'])) $_SESSION['tmp_images'] = [];
        $_SESSION['tmp_images'][] = $filename;

    // free memory
    // imagedestroy($webcam);
    // imagedestroy($sticker);
    // imagedestroy($sticker_resized);

    // $files = scandir('/var/www/html/tmp/', SCANDIR_SORT_ASCENDING);

    ob_clean();
    error_log('ERROR LOG 4');
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'files' => $filename]);
    exit;

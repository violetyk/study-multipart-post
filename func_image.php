<?php
// http://doruby.kbmj.com/deliku/20110221/_PHP_

function get_image_ext($orig_file) {
    $ext = '';

    $image_info = getimagesize($orig_file);
    list($orig_width, $orig_height, $image_type) = $image_info;

    switch ($image_type) {
    case 1: // gif
        $ext = 'gif';
    break;

    case 2: // jpeg
        $ext = 'jpg';
    break;

    case 3: // jpeg
        $ext = 'png';
    break;
    }

    return $ext;
}

function save_image($orig_file, $new_file, $resize_width = null, $resize_height = null, $quality = null) {
    try {
        $image_info = getimagesize($orig_file);
        list($orig_width, $orig_height, $image_type) = $image_info;
        if (!is_numeric($resize_width)) {
            $resize_width = $orig_width;
        }
        if (!is_numeric($resize_height)) {
            $resize_height = $orig_height;
        }

        // 画像をコピー
        switch ($image_type) {
        case 1: // gif
            $im = imagecreatefromgif($orig_file);
        break;

        case 2: // jpeg
            $im = imagecreatefromjpeg($orig_file);
        break;

        case 3: // png
            $im = imagecreatefrompng($orig_file);
        break;

        default:
            throw new Exception();
        }

        // コピー先となる空の画像作成
        $new_image = imagecreatetruecolor($resize_width, $resize_height);
        if (!$new_image) {
            imagedestroy($im);
            throw new Exception();
        }

        // GIF、PNGの場合、透過処理の対応を行う
        if (($image_type == 1) OR ($image_type == 3)) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $resize_width, $resize_height, $transparent);
        }

        // コピー画像を指定サイズで作成
        if (!imagecopyresampled($new_image, $im, 0, 0, 0, 0, $resize_width, $resize_height, $orig_width, $orig_height)) {
            imagedestroy($im);
            imagedestroy($new_image);
            throw new Exception();
        }

        // コピー画像を保存
        switch ($image_type) {
        case 1: // gif
            $result = imagegif($new_image, $new_file);
        break;

        case 2: // jpeg
            if (empty($quality)) {
                $quality = 75;
            }
            $result = imagejpeg($new_image, $new_file, $quality);
        break;

        case 3: // png
            if (empty($quality)) {
                // 0 - 9 0は圧縮しない
                $quality = 0;
            }
            $result = imagepng($new_image, $new_file, $quality);
        break;

        default:
            throw new Exception();
        }

        if (!$result) {
            imagedestroy($im);
            imagedestroy($new_image);
            throw new Exception();
        }

        // 不要になった画像データ削除
        imagedestroy($im);
        imagedestroy($new_image);

        return true;

    } catch (Exception $e) {
        return false;
    }
}


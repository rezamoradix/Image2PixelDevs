<?php
if (isset($argv[1]) && is_file($argv[1])) {
    $_start = time();
    $filename = $argv[1];
    echo "File: $filename" . PHP_EOL;
    $name = pathinfo($argv[1], PATHINFO_BASENAME);

    $image_info = getimagesize($filename);
    $width = $image_info[0];
    $height = $image_info[1];
    $imgType = $image_info[2];

    if ($imgType == IMAGETYPE_JPEG) {
        $img = imagecreatefromjpeg($filename);
    } elseif ($imgType == IMAGETYPE_GIF) {
        $img = imagecreatefromgif($filename);
    } elseif ($imgType == IMAGETYPE_PNG) {
        $img = imagecreatefrompng($filename);
    } else {
        throw new Exception("The file you're trying to open is not supported");
    }

    $generatedHtml = "<!DOCTYPE html><html lang=\"en\"><head><title>$name | Image2PixelDivs</title>";
    $generatedHtml .= "<style>.container{width:{$width}px;height:{$height}px;}.row{margin:0;padding:0;height:1px}.col{display:inline-block;width:1px;height:1px}</style>";
    $generatedHtml .= "</head><body><div class=\"container\">";
    for ($j = 0; $j < $height; $j++) {
        $row = '<div class="row">';
        for ($i = 0; $i < $width; $i++) {
            $ic = imagecolorat($img, $i, $j);
            $icfi = imagecolorsforindex($img, $ic);
            $color = sprintf("#%02x%02x%02x", $icfi['red'], $icfi['green'], $icfi['blue']);
            $row .= "<div class=\"col\" style=\"background:$color\"></div>";
        }
        $row .= '</div>';
        $generatedHtml .= $row;
    }
    $generatedHtml .= '</div></body></html>';
    file_put_contents(str_replace(' ', '_', $name . '.html'), $generatedHtml);
    echo 'Finished in ' . (time() - $_start) . ' seconds.';
} else {
    throw new Exception("File not found!");
}

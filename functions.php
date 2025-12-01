<?php
function get_asset_url($path) {
    $full_path = __DIR__ . '/' . $path;
    if (file_exists($full_path)) {
        return $path . '?v=' . filemtime($full_path);
    }
    return $path;
}
?>

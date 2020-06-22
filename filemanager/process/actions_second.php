<?php defined('isCMS') or die;

// upload form
if (!empty($request['get']['upload']) && !FM_READONLY) {
    //get the allowed file extensions
    function getUploadExt() {
        $extArr = explode(',', FM_UPLOAD_EXTENSION);
        if(FM_UPLOAD_EXTENSION && $extArr) {
            array_walk($extArr, function(&$x) {$x = ".$x";});
            return implode(',', $extArr);
        }
        return '';
    }
    require $module -> elements . 'actions_upload.php';
	$fm_not_display_form = true;
}

// copy form POST
if (!empty($request['post']['copy']) && !FM_READONLY) {
    $copy_files = !empty($request['post']['file']) ? $request['post']['file'] : null;
    if (!is_array($copy_files) || empty($copy_files)) {
        fm_set_msg('Nothing selected', 'alert');
        fm_redirect();
    }
    require $module -> elements . 'actions_copy_post.php';
	$fm_not_display_form = true;
}

// copy form
if (
	!empty($request['get']['copy']) &&
	empty($request['get']['finish']) &&
	!FM_READONLY
) {
    $copy = $request['get']['copy'];
    $copy = fm_clean_path($copy);
    if ($copy == '' || !file_exists(FM_ROOT_PATH . '/' . $copy)) {
        fm_set_msg('File not found', 'error');
        fm_redirect();
    }
    require $module -> elements . 'actions_copy.php';
	$fm_not_display_form = true;
}

if (!empty($request['get']['help'])) {
	require $module -> elements . 'actions_help.php';
	$fm_not_display_form = true;
}

// file viewer
if (!empty($request['get']['view'])) {
    $file = $request['get']['view'];
    $file = fm_clean_path($file, false);
    $file = str_replace('/', '', $file);
    if ($file == '' || !is_file($path . '/' . $file) || !fm_is_exclude_items($file)) {
        fm_set_msg('File not found', 'error');
        fm_redirect();
    }

    $file_url = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? FM_PATH . '/' : '') . $file);
    $file_path = $path . '/' . $file;

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize = fm_get_filesize(filesize($file_path));

    $is_zip = false;
    $is_gzip = false;
    $is_image = false;
    $is_audio = false;
    $is_video = false;
    $is_text = false;
    $is_onlineViewer = false;

    $view_title = 'File';
    $filenames = false; // for zip
    $content = ''; // for text
    $online_viewer = strtolower(FM_DOC_VIEWER);

    if($online_viewer && $online_viewer !== 'false' && in_array($ext, fm_get_onlineViewer_exts())){
        $is_onlineViewer = true;
    } elseif ($ext == 'zip' || $ext == 'tar') {
        $is_zip = true;
        $view_title = 'Archive';
        $filenames = fm_get_zif_info($file_path, $ext);
    } elseif (in_array($ext, fm_get_image_exts())) {
        $is_image = true;
        $view_title = 'Image';
    } elseif (in_array($ext, fm_get_audio_exts())) {
        $is_audio = true;
        $view_title = 'Audio';
    } elseif (in_array($ext, fm_get_video_exts())) {
        $is_video = true;
        $view_title = 'Video';
    } elseif (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }
	
	require $module -> elements . 'actions_view.php';
	$fm_not_display_form = true;
}

// file editor
if (!empty($request['get']['edit'])) {
    $file = $request['get']['edit'];
    $file = fm_clean_path($file, false);
    $file = str_replace('/', '', $file);
    if ($file == '' || !is_file($path . '/' . $file)) {
        fm_set_msg('File not found', 'error');
        fm_redirect();
    }
    //header('X-XSS-Protection:0');
    //require $module -> elements . 'header.php'; // HEADER

    $file_url = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? FM_PATH . '/' : '') . $file);
    $file_path = $path . '/' . $file;

    // normal editer
    $isNormalEditor = true;
    $isAdvancedEditor = null;
    if (!empty($request['get']['env'])) {
		$isNormalEditor = false;
		$isAdvancedEditor = $request['get']['env'];
        //if ($request['get']['env'] == "ace") {
        //    $isNormalEditor = false;
        //}
    }

    // Save File
    if (!empty($request['post']['savedata'])) {
        $writedata = $request['post']['savedata'];
        $fd = fopen($file_path, "w");
        @fwrite($fd, $writedata);
        fclose($fd);
        fm_set_msg('File Saved Successfully');
    }

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize = filesize($file_path);
    $is_text = false;
    $content = ''; // for text

    if (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }

    require $module -> elements . 'actions_edit.php';
	$fm_not_display_form = true;

}

// chmod (not for Windows)
if (
	!empty($request['get']['chmod']) && !FM_READONLY && !FM_IS_WIN) {
    $file = $request['get']['chmod'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || (!is_file($path . '/' . $file) && !is_dir($path . '/' . $file))) {
        fm_set_msg('File not found', 'error');
        fm_redirect();
    }

    $file_url = FM_ROOT_URL . (FM_PATH != '' ? FM_PATH . '/' : '') . $file;
    $file_path = $path . '/' . $file;

    $mode = fileperms($path . '/' . $file);

    require $module -> elements . 'actions_chmod.php';
	$fm_not_display_form = true;
}

?>
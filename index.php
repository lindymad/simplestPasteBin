<?php
session_start();
/**
 * Copy the settings.ini.example file to settings.ini and set the save path there.
 */
if (isset($_GET['do']) && $_GET['do']="logout") {
    unset($_SESSION['authorized']);
    header("Location: ".preg_replace('/\??do=logout/', '', $_SERVER['REQUEST_URI']));
    exit();
}
if (file_exists("settings.ini")) {
    $settings = parse_ini_file("settings.ini");
    if (isset($settings['savePath'])) define('SAVEPATH', $settings['savePath']);
    if (isset($settings['password'])) {
        $hash=base64_encode(password_hash($settings['password'], PASSWORD_DEFAULT));
        header("Content-type:text/plain");
        print "Please remove the line beginning \"password=\" in your settings.ini file and add:\n\npasswordHash=\"$hash\"\n";
        exit();
    }
    if (isset($settings['passwordHash']) && isset($_POST['password'])) {
        $_SESSION['authorized']=password_verify($_POST['password'], base64_decode($settings['passwordHash']));
        if ($_SESSION['authorized']) {
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit();
        }
    }
    if (isset($settings['passwordHash']) && (!isset($_SESSION['authorized']) || !$_SESSION['authorized'])) {
        printHtmlHead("Password Required");

        ?><body class="login" onload="document.getElementById('password').focus();"><h1>Simplest Pastebin : Password Required</h1><?php
        if (isset($_POST['password'])) print "<p class='error'>Incorrect Password</p>";
        ?><form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div><label for="password">Password: </label><input type="password" name="password" id="password"></div>
            <div><input type="submit" value="Enter"></div>
        </form>
        </body></html>
            <?php
        exit();
    }

}
if (isset($_GET['p'])) {
    header("Content-type: text/plain");
    if (!defined('SAVEPATH')) {
        print "Error: no settings.ini file found, or no savePath was specified.\n";
    } else {
        $fn = SAVEPATH . "/" . $_GET['p'] . ".pastebin";
        if (file_exists($fn)) {
            print file_get_contents($fn);
            exit();
        } else print $fn . " was not found";
    }
    exit();

}
if (!defined('SAVEPATH')) {
    printHtmlHead("Error");
    ?>
    <body>
    <div class='error'><p><strong>Error: no settings.ini file found, or no savePath was specified.</strong></p>
        <p> Please copy the settings.ini.example file to settings.ini and update
            it to use the path you wish to save your pastes in.</p>
        <p><strong>Important</strong>: Make sure to save the files
            somewhere outside of /tmp to ensure the OS doesn't delete them!</p>
    </div>
    </body></html>
    <?php
    exit();
}
if (isset($_POST['action'])) {
    doAction($_POST);
} else if (isset($_GET['action'])) {
    doAction($_GET);
} else {
    if (isset($_GET['load'])) {
        $fn = SAVEPATH . "/" . $_GET['load'] . ".pastebin";
        $contentVal = false;
        if (file_exists($fn)) {
            $titleVal = 'value="' . htmlentities($_GET['load'], ENT_QUOTES) . '"';
            $contentVal = file_get_contents($fn);
            $copyLink = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . preg_replace("/load=/", "p=", $_SERVER['REQUEST_URI']);
            $htmlTitle = " : " . htmlentities($_GET['load'], ENT_QUOTES);
        }
        if (isset($_GET['contentOnly'])) {
            header("Content-type: text/plain");
            if ($contentVal) print $contentVal;
            else print "$fn was not found";
            exit();
        }
    } else {
        $htmlTitle = "";
        $titleVal = "";
        $contentVal = "";
        $copyLink = "";
    }
    printHtmlHead($htmlTitle);
    ?>
    <body>
    <main>
        <div id="list">
            <div id="search" class="empty"><input id="searchVal"></div>
            <ul id='pbList'>
                <?php
                echo getList(); ?>
            </ul>
        </div>
        <div id="pasteWrapper">
            <div id="top">
                <button id="menu">&gt;</button>
                <button id="save">Save</button>
                <button id="clear">Clear</button>
                <a id="copyLink" target="_blank" href="<?php
                echo $copyLink; ?>"><?php
                    echo $copyLink; ?></a>
                <button id="copy" class="<?php
                echo $copyLink ? '' : 'hidden'; ?>"
                        onclick="copyTextToClipboard('#copy', document.getElementById('copyLink').href);">Copy
                </button>
                <?php if (isset($settings['passwordHash'])) { ?>
            <button id="logout" onclick="location.href='index.php?do=logout';">Logout</button>
            <?php } ?>
            </div>
            <div id="content">
                <div id="titleWrapper">
                    <label for="title">Title:</label>
                    <input id="title" <?php
                    echo $titleVal; ?>>
                </div>
                <div id="pasteImmediateWrapper"><textarea id="paste"><?php
                        echo $contentVal; ?></textarea></div>
            </div>
        </div>
    </main>
    <script src="main.js?v=4"></script>
    </body>
    </html>
    <?php
}

function getSequentialTitle() {
     $files = scandir(SAVEPATH);
     $latest=0;
    foreach ($files as $f) {
        if (preg_match('/spb(\d+)\.pastebin$/', $f, $m)) {
            if ($m[1]>$latest) $latest=$m[1];
        }
    }
    return "spb".($latest+1);
}

function getList() {
    $list = [];
    $files = scandir(SAVEPATH);
    foreach ($files as $f) {
        if (preg_match('/(.+)\.pastebin$/', $f, $m)) {
            $list[] = $m[1];
        }
    }
    if (empty($list)) return "<li class='noPastes'><em>No pastes found in " . SAVEPATH . "</em></li>";
    else {
        $return = "";
        foreach ($list as $l) {
            $return .= "<li><a class='g'>$l</a> <a class='d'>🗑</a></li>";
        }
        return $return;
    }
}

function doAction($parms) {
    if ($parms['action'] === "save") {
        if (!$parms['title']) {
            $parms['title'] = getSequentialTitle();
        }
        else $parms['title']=preg_replace("/[\/\\\]/", "", $parms['title']);
        $filename =  $parms['title'] . ".pastebin";
        file_put_contents(SAVEPATH . "/" . $filename, $parms['content']);
        $uri = preg_replace("/\/index\.php/", "/", $_SERVER['SCRIPT_NAME']) . "?load=" . urlencode($parms['title']);
        $duri = preg_replace("/\/index\.php/", "/", $_SERVER['SCRIPT_NAME']) . "?p=" . urlencode($parms['title']);

        print json_encode(["status" => "success", "title" => $parms['title'], "newList" => getList(), "url" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $uri, "dUrl" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $duri]);
    } else if ($parms['action'] === "delete") {
        $fn = SAVEPATH . "/" . $parms['file'] . ".pastebin";
        if (file_exists($fn)) {
            unlink($fn);
            print json_encode(["status" => "success", "newList" => getList()]);
        } else print json_encode(["status" => "error", "message" => "File '$fn' not found"]);
    } else if ($parms['action'] === "get") {
        $fn = SAVEPATH . "/" . $parms['file'] . ".pastebin";
        if (file_exists($fn)) {
            $uri = preg_replace("/\/index\.php/", "/", $_SERVER['SCRIPT_NAME']) . "?load=" . urlencode($parms['file']);
            $duri = preg_replace("/\/index\.php/", "/", $_SERVER['SCRIPT_NAME']) . "?p=" . urlencode($parms['file']);

            print json_encode(["status" => "success", "url" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $uri, "dUrl" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $duri, "content" => file_get_contents($fn)]);
        } else {
            print json_encode(["status" => "error", "message" => "File '$fn' not found"]);
        }
    } else {
        print json_encode(["status" => "error", "message" => "Unknown action " . $parms['action']]);
    }
}

function printHtmlHead($htmlTitle) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Simplest Pastebin<?php
        echo $htmlTitle; ?></title>
    <link rel="stylesheet" href="style.css?v=4">
</head>
    <?php
}

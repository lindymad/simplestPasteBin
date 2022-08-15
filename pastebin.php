<?php
define('SAVEPATH', '/tmp');
if (isset($_POST['action']))
{
    doAction($_POST);
} else if (isset($_GET['action']))
{
    doAction($_GET);
} else
{
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Simple Pastebin</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <main>
        <div id="list">
            <ul id='pbList'>
                <?php echo getList(); ?>
            </ul>
        </div>
        <div id="pasteWrapper">
            <div id="top">
                <button id="save">Save</button>
                <button id="clear">Clear</button>
            </div>
            <div id="content">
                <div id="titleWrapper"><label for="title">Title:</label><input id="title"></div>
                <textarea id="paste"></textarea>
            </div>
        </div>
    </main>
    <script src="main.js"></script>
    </body>
    </html>
    <?php
}

function getList()
{
    $list = [];
    $files = scandir(SAVEPATH);
    foreach ($files as $f)
    {
        if (preg_match('/(.+)\.pastebin$/', $f, $m))
        {
            $list[] = $m[1];
        }
    }
    if (empty($list)) return "<li class='noPastes'><em>No pastes found in " . SAVEPATH . "</em></li>";
    else
    {
        $return = "";
        foreach ($list as $l)
        {
            $return .= "<li><a class='g'>$l</a> <a class='d'>🗑</a></li>";
        }

        return $return;
    }
}

function doAction($parms)
{
    if ($parms['action'] == "save")
    {
        $filename = $parms['title'] . ".pastebin";
        file_put_contents(SAVEPATH . "/" . $filename, $parms['content']);
        print json_encode(["status" => "success", "newList" => getList()]);
    } else if ($parms['action'] == "delete")
    {
        $fn = SAVEPATH . "/" . $parms['file'] . ".pastebin";
        if (file_exists($fn))
        {
            unlink($fn);
            print json_encode(["status" => "success", "newList" => getList()]);
        } else print json_encode(["status" => "error", "message" => "File '$fn' not found"]);
    } else if ($parms['action'] == "get")
    {
        $fn = SAVEPATH . "/" . $parms['file'] . ".pastebin";
        if (file_exists($fn))
        {
            print json_encode(["status" => "success", "content" => file_get_contents($fn)]);
        } else
        {
            print json_encode(["status" => "error", "message" => "File '$fn' not found"]);
        }
    } else
    {
        print json_encode(["status" => "error", "message" => "Unknown action " . $parms['action']]);
    }
}

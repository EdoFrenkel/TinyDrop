<?php
/**
 * Created by Edo Frenkel
 * http://www.lightapps.co.il
 * Date: 09/04/14
 */

// important - don't remove
set_time_limit(0);

// DropPHP - A simple Dropbox client that works without cURL.
require_once("lib/DropboxClient.php");

$config = array(
    // Define a username && password - without the correct username & password you can't start the process (which is a good thing!)
    "username" => "USER_NAME",
    "password" => "PASSWORD",
    // Define Dropbox app data
    "dropboxAppKey" => "DROPBOX_APP_KEY",
    "dropboxAppSecret" => "DROPBOX_APP_SECRET",
    // Define your tinyPNG api key
    "tinyPNGApiKey" => "TINYPNG_API_KEY",
    // the folder structure of the current project on your server
    // for example if your your project root is in: SERVER_ROOT/test/tinydrop/
    // you should define: "/test/tinydrop/"
    "serverPath" => "SERVER_PATH",
    // the path to Dropbox folder
    // for example if your folder is in: c:\dropbox\project\Assets\SpriteCollection\COLLECTION_NAME
    // define: project/Assets/SpriteCollection/
    "dropboxPath" => "DROPBOX_PATH",
    // the end of the Dropbox folder - optional, if there's no need leave it empty - ""
    // if we'll take the last example: c:\dropbox\project\Assets\SpriteCollection\COLLECTION_NAME Data\
    // define: " Data/"
    "dropboxPathEnd" => "DROPBOX_PATH_END",
    // the name convension of the image
    // keep the same name convention to your atlases or change the script
    "imageName" => "IMAGE_NAME"
);

/*
 * Here you will insert your folders names as they appear in the Dropbox folder
 * if you want insted to define image names change the script...
 * I use 2dToolkit so the name convention is Bla/Bla/{$Name} Data/atlas0.png
 * but you can change it as you like of course
 */
$name = array(
    "Folder1",
    "Folder2",
    "Folder3"
    // ...
);


// do not change
$warning = "";
$message = "";
$error = "";

// init Dropbox API
$dropbox = new DropboxClient(array(
    'app_key' => $config["dropboxAppKey"],
    'app_secret' => $config["dropboxAppSecret"],
    'app_full_access' => true,
), 'en');

$access_token = load_token("access");

if (!empty($access_token)) {
    $dropbox->SetAccessToken($access_token);

} elseif (!empty($_GET['auth_callback'])) // are we coming from Dropbox's auth page?
{
    // then load our previosly created request token
    $request_token = load_token($_GET['oauth_token']);
    if (empty($request_token)) die('Request token not found!');

    // get & store access token, the request token is not needed anymore
    $access_token = $dropbox->GetAccessToken($request_token);
    store_token($access_token, "access");
    delete_token($_GET['oauth_token']);
}

// checks if access token is required
if (!$dropbox->IsAuthorized()) {
    // redirect user to Dropbox auth page
    $return_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?auth_callback=1";
    $auth_url = $dropbox->BuildAuthorizeUrl($return_url);
    $request_token = $dropbox->GetRequestToken();
    store_token($request_token, $request_token['t']);
    die("Authentication required. <a href='$auth_url'>Click here.</a>");
}

// if the form submited
if (isset($_POST["startProcess"])) {
    // if the password is correct
    if ($_POST['password'] == $config["password"] && $_POST['username'] == $config["username"]) {

        /*
         * Start the process
         */

        // first make sure that all the folder exist
        for ($i = 0; $i < sizeof($name); $i++) {
            if ($_POST["folder" . $i] == "on") {
                try {
                    $folder = $dropbox->GetMetadata($config["dropboxPath"] . $name[$i] . $config["dropboxPathEnd"]);
                } catch (Exception $e) {
                    $error .= $e->getMessage() . "<br/>";
                }
            }
        }

        if ($error == "") {
            // loop over the Folders
            for ($i = 0; $i < sizeof($name); $i++) {

                // if checkbox is on start process
                if ($_POST["folder" . $i] == "on") {

                    $imageAlreadyCompressed = false;

                    // display progress when loading...
                    echo "<div class=\"process\">";
                    echo "<pre>";
                    echo "<img src=\"images/ajax-loader.gif\" border=\"0\" /> Process " . $name[$i] . "...\n";
                    echo "</pre>";
                    echo "</div>";

                    $message .= "<strong>" . $name[$i] . "</strong><br/>";

                    // check if folder images exist
                    if (!file_exists("images")) {
                        mkdir("images", 0777);
                        $message .= "The directory: images, was successfully created.<br/>";
                    }

                    // check if folder $name[$i] exist
                    if (!file_exists("images/" . $name[$i])) {
                        mkdir("images/" . $name[$i], 0777);
                        $message .= "Created directory: " . $name[$i] . ", to store temp files.<br/>";
                    }

                    // check if file size the same as the file on server...
                    if (file_exists("images/" . $name[$i] . "/tempNew.png")) {
                        $fileSizeServer = filesize("images/" . $name[$i] . "/tempNew.png");
                        $fileSizeDropbox = $dropbox->GetMetadata($config["dropboxPath"] . $name[$i] . $config["dropboxPathEnd"] . $config["imageName"] . ".png");

                        if ($fileSizeServer == $fileSizeDropbox->bytes) {
                            $imageAlreadyCompressed= true;
                        }
                    }

                    if (!$imageAlreadyCompressed) {

                        $download = $dropbox->DownloadFile($config["dropboxPath"] . $name[$i] . $config["dropboxPathEnd"] . $config["imageName"] . ".png", "images/" . $name[$i] . "/tempOrg.png", 0);

                        $message .= "Fetch the file: <br/>" . $download->path . "<br/>";
                        $message .= "The original file size: " . $download->size . "<br/>";

                        if (actionTiny($name[$i])) {

                            $upload = $dropbox->UploadFile("images/" . $name[$i] . "/tempNew.png", $config["dropboxPath"] . $name[$i] . $config["dropboxPathEnd"] . $config["imageName"] . ".png");
                            $message .= "After compression file size: " . $upload->size . "<br/>";

                            // calculate compression rate
                            $sizeOrg = str_replace(" KB", "", $download->size);
                            $sizeNew = str_replace(" KB", "", $upload->size);
                            $compression = (100 - (((float)$sizeNew / (float)$sizeOrg) * 100));
                            $compression = number_format($compression, 2, '.', '');

                            $message .= "Compression ratio of: " . $compression . "%<br/>";

                            // dlete temp files
                            if (unlink('images/' . $name[$i] . '/tempOrg.png')) {
//                            unlink('images/' . $name[$i] . '/tempNew.png')) {
                                $message .= "Delete original temp file...<br/><br/>";
                            }


                            $message .= $name[$i] . " upadated at Dropbox!";
                            $message .= "<br/><hr/>";
                        }

                    } else {
                        $message .= $name[$i] . " Already compressed...<br/><br/>";
                    }
                }
            }
        }
    } else {
        $warning = "Username || Password Incorrect!";
    }
}

function actionTiny($folderName)
{
    global $config;
    global $message;
    require_once('lib/TinyPNG.php');
    // define TinyPNG api key
    $api = new TinyPNG($config["tinyPNGApiKey"]);

    if ($api->shrink($_SERVER['DOCUMENT_ROOT'] . $config["serverPath"] . 'images/' . $folderName . '/tempOrg.png')) {
        $result = $api->getResultJson();


        $outputPath = $result->output->url;
        $image = $_SERVER['DOCUMENT_ROOT'] . $config["serverPath"] . 'images/' . $folderName . '/tempNew.png';

        if (file_put_contents($image, file_get_contents($outputPath))) {
            return true;
        } else {
            $message .= "Mmm...There was a problem WRITING the file<br/>";
            return false;
        }

    } else {
        $message .= "Mmm...There was a problem to READING the file<br/>";
        return false;
    }
}

function store_token($token, $name)
{
    // create tokens folder if not exist
    if (!file_exists("tokens")) {
        mkdir("tokens", 0777);
    }
    if (!file_put_contents("tokens/$name.token", serialize($token)))
        die('<br />Could not store token! <b>Make sure that the directory `tokens` exists and is writable!</b>');
}

function load_token($name)
{
    if (!file_exists("tokens/$name.token")) return null;
    return @unserialize(@file_get_contents("tokens/$name.token"));
}

function delete_token($name)
{
    @unlink("tokens/$name.token");
}

?>
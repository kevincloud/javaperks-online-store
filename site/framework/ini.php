<?php

//////////////////////////////////////////////////////////////////////////////////
// PHP AND SERVER INITIALIZATION
//////////////////////////////////////////////////////////////////////////////////

// if ($_SERVER['SERVER_PORT'] != 443)
// {
// 	header("HTTP/1.1 301 Moved Permanently");
// 	header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
// 	exit();
// }

// Set error reporting for development mode
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');
error_reporting(E_ALL);
$region = getenv("REGION");
$s3bucket = getenv("S3_BUCKET");

// ***INLINESQL***
// include_once("plugins/ezsql/ez_sql_core.php");
// include_once("plugins/ezsql/ez_sql_mssql.php");

// AUTO INCLUDE LOCAL CLASSES
//spl_autoload_register(function ($class) {
//    include_once 'classes/' . $class . '.php';
//});

include_once("classes/ApplicationSettings.php");
include_once("classes/Utilities.php");
include_once("classes/BasePage.php");
include_once("classes/RestRunner.php");
include_once("classes/AjaxHandler.php");
include_once("classes/Product.php");
include_once("classes/Account.php");
include_once("classes/Order.php");
include_once("classes/Invoice.php");
include_once("classes/ShoppingCart.php");

// if(file_exists('config.php'))
//     include_once("config.php");

// Set the default time zone
date_default_timezone_set('America/New_York');

// set the Content-Type header with character set
header('Content-Type: text/html; charset=iso-8859-1');

// Initialize the session
@session_start();

// Regenerate session ID to reduce risk of session cloning
// if (!isset($_SESSION['userID']))
// {
// 	session_regenerate_id();
// }

// Open and clear the output buffer with gzip compression enabled
ob_start("ob_gzhandler");
ob_implicit_flush(false);



//////////////////////////////////////////////////////////////////////////////////
// API INITIALIZATION
//////////////////////////////////////////////////////////////////////////////////

$authapi = getenv("JPAPI_AUTH_HOST");
$productapi = getenv("JPAPI_PROD_HOST");
$customerapi = getenv("JPAPI_CUST_HOST");
$cartapi = getenv("JPAPI_CART_HOST");
$orderapi = getenv("JPAPI_ORDR_HOST");
$vaulturl = getenv("VAULT_ADDR");
$vaulttoken = getenv("VAULT_TOKEN");

$k8s = false;
if (getenv("KUBERNETES_SERVICE_HOST") != "")
    $k8s = true;

if ($k8s) {
    $kube_token = file_get_contents("/var/run/secrets/kubernetes.io/serviceaccount/token");
    $r = new RestRunner();

    $result = $r->Post(
        $vaulturl."/v1/auth/kubernetes/login", 
        "{\"jwt\": \"$KUBE_TOKEN\", \"role\": \"cust-api\"}");
    $vaulttoken = $result->auth->client_token;
}

//////////////////////////////////////////////////////////////////////////////////
// UNIVERSAL FUNCTIONS INITIALIZATION
//////////////////////////////////////////////////////////////////////////////////

include_once("functions.php");

if ($region == "")
    $region = "us-east-1";

if ($s3bucket == "")
    $s3bucket = "hc-workshop-2.0-assets";

if ($authapi == "")
    $authapi = "http://auth-api.service.".$region.".consul:5825";

if ($productapi == "")
    $productapi = "http://product-api.service.".$region.".consul:5821";

if ($customerapi == "")
    $customerapi = "http://customer-api.service.".$region.".consul:5822";

if ($cartapi == "")
    $cartapi = "http://cart-api.service.".$region.".consul:5823";

if ($vaulturl == "")
    $vaulturl = "http://vault-main.service.".$region.".consul:8200";

if ($orderapi == "")
    $orderapi = "http://order-api.service.".$region.".consul:5826";

$assetbucket = "https://s3.amazonaws.com/".$s3bucket."/";




?>

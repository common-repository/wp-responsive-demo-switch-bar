<?php 
## Allowing Direct File Access to plugin files
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global $redirect, $current_theme;
$redirect = true; 
update_option('xredirect',1); 
require("themesdemo.php");
?>
<!DOCTYPE HTML>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title> 
        <script type="text/javascript">
		top.location.href = '<?php echo site_url() ?>/<?php echo get_option("wpt_slug_name") ?>/?theme=<?php echo $current_theme; ?>';
        </script>     
</head>
<body>

</body>
</html>
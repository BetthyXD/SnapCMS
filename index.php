<?php
require_once("cms/cms.php");
$cms = new SCMS();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample page</title>
</head>
<body>
<h1>
    <?php
    $cms->text('nadpis', SCMS::SHORT);
    ?>
</h1>
<?php
$cms->text('uvod');
?>
</body>
</html>
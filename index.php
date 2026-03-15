<?php
require_once("cms/cms.php");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample page</title>
    <?php echo $cms->link(); ?>
</head>
<body>
<?php $cms->showAdminBar(); ?>
<h1>
    <?php
    $cms->text('nadpis', SCMS::SHORT);
    ?>
</h1>
<?php
$cms->text('uvod');
?>



<?php $cms->showAlerts(); ?>
</body>
</html>
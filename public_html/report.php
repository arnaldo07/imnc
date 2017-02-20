<?php require_once("../includes/initialize.php"); ?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo SITE_PUBLIC ?>static/stylesheets/report.css">
  <title>IMNC</title>
</head>
<body class="body">
  <header class="main-header">
    <div class="header-content"><span class="logo">IMNC</span></div>
  </header>
  <div class="content-text">
    <?php if(isset($session->message) & !empty($session->message)): ?>
      <span><?php print($session->message); ?></span>
    <?php else: ?>
      <?php redirect_to('404.php'); ?>
    <?php endif; ?>
  </div>
</body>
</html>

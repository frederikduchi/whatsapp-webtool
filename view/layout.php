<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/style.css">
  <title>D4E1 - WCA <?php echo $title;?></title>
</head>
<body>
  <header class="header">
    <h1 class="header__title">Whatsapp conversation analyser</h1>
  </header>
  <main class="content">
    <?php if (!empty($_SESSION['info'])): ?>
      <div class="session-message info"><?php echo $_SESSION['info']; ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="session-message error"><?php echo $_SESSION['error']; ?></div>
    <?php endif; ?>
    
    <?php echo $content; ?>
  </main>
  <script src="js/script.js"></script>
</body>
</html>

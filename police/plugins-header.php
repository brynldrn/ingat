
<!-- header link -->
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title id="contactContent">
      <?php
          // Get Current File Name and Display
          $url = pathinfo(basename($_SERVER['PHP_SELF']), PATHINFO_FILENAME);

          // Remove the prefix
          $removePrefix = str_replace('http://localhost/ingat-main/', '', $url);

          // Get the file name without the ".php" extension
          $fileNameWithoutPhp = pathinfo(basename($removePrefix), PATHINFO_FILENAME);
          $cleanedString = preg_replace('/[^a-zA-Z0-9]/', ' ', $fileNameWithoutPhp);
          $sentencecase = ucwords($cleanedString);

          // Output the result
          echo $sentencecase;
      ?>
    </title>
    <link rel="icon" href="../images/logo.png" type="image/png">
    <!-- BOOTSTRAP 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="asset/css/users.css">

    <!-- FONT AWESOME OFFLINE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.css">
</head>


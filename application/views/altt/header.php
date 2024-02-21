<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title><?=$title?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<?= $description; ?>"/>
        <meta name="keywords" content=""/>
        <meta name="theme-color" content="#111c25" />
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="AltcoinsTalks Archives">

        <link rel="shortcut icon" href="<?=base_url('assets/images/logo/favicon.webp');?>">
        <link rel="manifest" href="/manifest.json" crossorigin="use-credentials">
        <link rel="canonical" href="<?=$canonical_url;?>">
        
        <link href="https://unicons.iconscout.com/release/v4.0.8/css/line.css" rel="stylesheet" >
        <link href="<?=base_url()?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="light-style" />
        <link href="<?=base_url()?>assets/css/styles.css?v=<?=filemtime('assets/css/styles.css')?>" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/default.css?v=<?=filemtime('assets/css/default.css')?>" rel="stylesheet" type="text/css" />
    </head>

    <body>
    <script type='application/ld+json'>
        {
          "@context":"https:\/\/schema.org",
          "@type":"Organization",
          "url":"https:\/\/pxzone.online\/",
          "sameAs":["https:\/\/www.twitter.com\/bypxzone\/"],
          "@id":"https://pxdev.click/#Organization",
          "name":"PX Zone",
          "logo":"https://pxzone.online/assets/images/logo/logo.png"
        }
    </script>  
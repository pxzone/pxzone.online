<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?=($title == 'index') ? $siteSetting['website_name'] .'' : $title?> </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<?= ($title == 'index') ? $siteSetting['description'].' ' :  $description ?>"/>
        <meta name="keywords" content=""/>
        <meta name="theme-color" content="#111c25" />
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="<?=$siteSetting['website_name']?>">

        <!-- App link -->
        <link rel="apple-touch-icon" href="" crossorigin="anonymous">
        <link rel="shortcut icon" href="<?=base_url('assets/images/logo/favicon.webp');?>">
        <link rel="manifest" href="/manifest.json" crossorigin="use-credentials">
        <link rel="canonical" href="<?=$canonical_url;?>">
        
        <link href="<?=base_url()?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="light-style" />
        <link href="https://unicons.iconscout.com/release/v4.0.8/css/line.css" rel="stylesheet" >
        <!-- <link href="<?=base_url()?>assets/css/mdi.css" rel="stylesheet" type="text/css" id="light-style" /> -->
        <link href="<?=base_url()?>assets/css/styles.css?v=<?=filemtime('assets/css/styles.css')?>" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/default.css?v=<?=filemtime('assets/css/default.css')?>" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/theme.css?v=<?=filemtime('assets/css/theme.css')?>" rel="stylesheet" type="text/css" />
		<?php if ($state == 'statistics') {?><link href="<?=base_url()?>assets/css/croppie.css?v=<?=filemtime('assets/css/croppie.css')?>" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/daterangepicker.css?v=<?=filemtime('assets/css/daterangepicker.css')?>" rel="stylesheet" type="text/css" /><?php } ?>

        <meta property="fb:app_id" content="103993588751492" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?=($title=='index')?$siteSetting['website_name']:$title.' - '.$siteSetting['website_name']?>" />
        <meta property="og:description" content="<?=($title=='index')?$siteSetting['description'].'':$description?>" />
        <meta property="og:url" content="<?=$canonical_url;?>" />
        <meta property="og:site_name" content="<?=$siteSetting['website_name']?>" />
        <meta property="og:image" content="<?=base_url('assets/images/other/cover.webp')?>" />
        <meta property="og:image:width" content="800" />
        <meta property="og:image:height" content="580" />
        <meta property="og:image:alt" content="<?=($title=='index')?$siteSetting['description'].'':$description?>" />

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="<?=base_url()?>">
        <meta name="twitter:creator" content="bypxzone">
        <meta name="twitter:title" content="<?=($title=='index')?$siteSetting['website_name']:$title.' - '.$siteSetting['website_name']?>">
        <meta name="twitter:description" content="<?=($title=='index')?$siteSetting['description'].'':$description?>">
        <meta name="twitter:image" content="<?=base_url('assets/images/other/cover.webp')?>">
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-BH2L4YCB4W"></script>
    </head>

    <body class="" >
    <script type='application/ld+json'>
        {
          "@context":"https:\/\/schema.org",
          "@type":"Organization",
          "url":"https:\/\/pxzone.online\/",
          "sameAs":["https:\/\/www.twitter.com\/bypxzone\/"],
          "@id":"https://pxzone.online/#Organization",
          "name":"PX Dev",
          "logo":"https://pxzone.online/assets/images/logo/logo.png"
        }
    </script>    
    
    <?php if ($state !== 'index') {?><script type='application/ld+json'>
    {
    "@context":"https://schema.org",
    "@type":"BreadcrumbList",
    "itemListElement":[{
      "@type":"ListItem",
      "position":1,
      "item":
        {
          "@id":"<?=base_url()?>",
          "name":"Home"
        }
      },
      {
        "@type":"ListItem",
        "position":2,
        "item":{
          "@id":"<?=$canonical_url?>",
          "name":"<?=$title?>"
        }
      }
      ]
    }
    </script>
    <?php }?>

    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-BH2L4YCB4W');
    </script>
    
    <div class="position-relative">
		<div class="custom-alert-box" id="_custom_alert" hidden="hidden"></div>	
	</div>
    <!-- Begin page -->
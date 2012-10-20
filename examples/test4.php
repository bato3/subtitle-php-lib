<?php

   header('Content-Type: text/html; charset=utf-8'); 
   
?><h1> Test 4</h1><pre><?php


  define('MTimeDefaultFPS',29.97);

  require_once('mtime.class.php');
  require_once('mtext.class.php');
  require_once('subtitle.class.php');
  //print MText::fixPunctuation('ala ,ma kota !');
  require_once('subtitle.ass.class.php');
  require_once('subtitle.tmp.class.php');
  require_once('subtitle.mdvd.class.php');
  require_once('subtitle.srt.class.php');
  
  $a = new AssSubtitle();
  $a->open('C:/filmy/Michiko_to_Hatchin/[BSS]_Michiko_to_Hatchin_-_04_[1EF519E4].ass');
  $x = $a->getSubTable();
  var_export($x);
      

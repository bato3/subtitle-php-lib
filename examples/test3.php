<?php

   header('Content-Type: text/html; charset=utf-8'); 
   
?><h1> Test 3</h1><pre><?php


  define('MTimeDefaultFPS',29.97);

  require_once('mtime.class.php');
  require_once('mtext.class.php');
  require_once('subtitle.class.php');
  //print MText::fixPunctuation('ala ,ma kota !');
  require_once('subtitle.ass.class.php');
  require_once('subtitle.tmp.class.php');
  require_once('subtitle.mdvd.class.php');
  require_once('subtitle.srt.class.php');
  
  
  $d = Array('xxxHolic - 03 - Angel (Xvid)',
'xxxHolic - 04 - Fortune-Telling (Xvid)',
'xxxHolic - 05 - Word-Chain Game (Xvid)',
'xxxHolic - 06 - Indulgence (Xvid)',
'xxxHolic - 07 - Hydrangea (Xvid)',
'xxxHolic - 08 - Contract (Xvid)',
'xxxHolic - 09 - Promise (Xvid)',
'xxxHolic - 10 - Light (Xvid)',
'xxxHolic - 11 - Confession (Xvid)',
'xxxHolic - 12 - Summer Shadow (Xvid)',
'xxxHolic - 13 - Transfiguration (Xvid)');
  
  
  
  
  
  //foreach($d AS $f)
  {
    
  $f = 'xxxHolic - 05 - Word-Chain Game (Xvid)';
      //$a = new AssSubtitle();
      $a = new MdvdSubtitle();
      //$a->setFps(119.88);
      $a->setFps(29.97);
      //$a->setEncoding('utf-8');
      //$fname = 'C:\\filmy\\Wild Arms\\[LIME]_Wild_Arms_03.ass';
      $fname = sprintf('C:\\filmy\\xxxHOLiC\\%s.txt',$f);
      
      //$a->setEncoding('cp1250');
      $a->open($fname);
      
      $a->setFps(29.97);
      
      //$a->fixPunctuation();
                               // to    ->      from
      //$a->addTime(-MTime::fromAss('0:01:49.26')+MTime::fromMdvd('3267'), MTime::fromAss('0:01:36.05'));
      $a->addTime(-26.69, MTime::fromAss('0:01:36.05'));
      
       
      //$a->save($fname, true);
  }

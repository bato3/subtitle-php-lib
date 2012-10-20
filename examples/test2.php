<?php

   header('Content-Type: text/html; charset=utf-8'); 
   
?><h1> Test 2</h1><pre><?php


  define('MTimeEqPrecision',.16);
  define('MTimeDefaultFPS',29.97);

  require_once('mtime.class.php');
  require_once('mtext.class.php');
  require_once('subtitle.class.php');
  
  require_once('subtitle.ass.class.php');
  require_once('subtitle.tmp.class.php');
  
  
  
  
  
  $_fnames = Array(
    'fb'=>'Kodomo_No_Jikan_01_(DVD_x264_AC3)[Loli-pop Subs][01BE050C]v3',
    
    'a'=>'C:\\filmy\\Kodomo_No_Jikan_1-12_(DVD_x264_AC3)[Loli-pop Subs]\\sub\\%s.ass',
    'b'=>'C:\\filmy\\Kodomo_No_Jikan_1-12_(DVD_x264_AC3)[Loli-pop Subs]\\sub\\%s_Track1.ass',
    'c'=>'C:\\filmy\\Kodomo_No_Jikan_1-12_(DVD_x264_AC3)[Loli-pop Subs]\\%s.ass',
    );
  
  
  

  $a = new AssSubtitle();
  $a->open(sprintf($_fnames['a'],    $_fnames['fb']));
  $b = new AssSubtitle();
  $b->open(sprintf($_fnames['b'],    $_fnames['fb']));
  
  
  
  $noveup=Array('Songs up');
  
  
  
  $lb = $b->reset();
  $la = $a->reset();
  $o = Array();
  for(;true;){
    if($lb === false && $la === false) break;
    if($lb === false){ while($la = $a->next()) $o[] = $la; break;}
    if($la === false){ while($lb = $b->next()) $o[] = $lb; break;}
  
    if(in_array($lb['style'],$noveup)){
      $lb['//'] = false;
      $o[] = $lb;
      $lb = $b->next();
    } else {
      $eq = MTime::eq($lb['start'],$la['start']);
      if($eq == 0){
        $la['//'] = $lb;
        $o[] = $la;
        $lb = $b->next();
        $la = $a->next();
      } else if($eq < 0) {
        $lb['//']  = $lb;
        $lb['text'] = '{translate}';
        $o[] = $lb;
        $lb = $b->next();
      } else if($eq > 0) {
        $la['//'] = '{lack}';
        $o[] = $la;
        $la = $a->next();
      } 
    }
  }
  
  $c = clone $b;
  
  $c->setSubTable($o);
  
  $c->save(sprintf($_fnames['c'], $_fnames['fb']));
  

  ?>

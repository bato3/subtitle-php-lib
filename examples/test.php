<?php

   header('Content-Type: text/html; charset=utf-8'); 
   
?><h1> Tests</h1>

<pre><?php


  define('MTimeEqPrecision',.95);
  define('MTimeDefaultFPS',29.97);

  require_once('mtime.class.php');
  require_once('mtext.class.php');
  require_once('subtitle.class.php');
  
  require_once('subtitle.ass.class.php');
  require_once('subtitle.tmp.class.php');
  
  
  
  
  
  $jp_styles = array('Jap_canción','canción japones','Canción japones');
  $_fnames = Array(
    '[HL]-IMMSE_13[Sub-Dub]',//txt
    'Strawberry Eggs 13',//ssa
    '[anime-works]_IMMSE_ep_13_EDE183EE',//avifilename
    't1'=>MTime::fromAss('0:00:02.32')-MTime::fromTmp('00:00:22'),
    't2'=>0.81,
    //'t2'=>-MTime::FromAss('0:01:38.13')+MTime::fromAss('0:01:39.19'),
    'a'=>'C:\\filmy\\drHouse\\[anime-works]_I_My_Me_Strawberry-eggs_eps_1-13\\%s.txt',
    'b'=>'C:\\filmy\\drHouse\\[anime-works]_I_My_Me_Strawberry-eggs_eps_1-13\\%s.ssa',
    'c'=>'C:\\filmy\\drHouse\\[anime-works]_I_My_Me_Strawberry-eggs_eps_1-13\\%s.ssa',
    );
  
  //Napisy z www.asg.of.pl
  // \N Z hiszpańskiego ASS zmaltretował: bato3
  
  

  $a = new TmpSubtitle();
  $a->setEncoding('cp1250');
  $a->open(sprintf($_fnames['a'],    $_fnames[0]));
  $b = new AssSubtitle();
  $b->setEncoding('cp1250');
  $b->open(sprintf($_fnames['b'],    $_fnames[1]));
  
  $a->addTime($_fnames['t1']);
  
  
  
  $b->setDefaultRow(array (
    'marked' => 'Marked=0',
    'start' => 0,
    'end' => 0,
    'style' => 'DefaultS',
    'name' => '',
    'marginl' => '0000',
    'marginr' => '0000',
    'marginv' => '0000',
    'effect' => '',
    'text' => '',
  ));
  //print_r($b);
  
  
  
  
  $l1 = $b->reset();
  $l2 = $a->reset();
  $o = Array();
  for(;true;){
    if($l1 === false && $l2 === false) break;
    if($l1 === false){ while($l2 = $a->next()) $o[] = $l2; break;}
    if($l2 === false){ while($l1 = $b->next()) $o[] = $l1; break;}
    
    if(in_array($l1['style'],$jp_styles)){
      //commentsomestyles
      $l1['//'] = false;
      $o[] = $l1;
      $l1 = $b->next();
    } else {
      $eq = MTime::eq($l1['start'],$l2['start']);
      if($eq == 0){
        $l1['//'] = $l1['text'];
        $l1['text']   = $l2['text'];
        $o[] = $l1;
        $l1 = $b->next();
        $l2 = $a->next();
      } else if($eq < 0) {
        $o[] = $l1;
        $l1 = $b->next();
      } else if($eq > 0) {
        $o[] = $l2;
        $l2 = $a->next();
      } 
    }
  }
  
  $c = clone $b;
  
  $c->setSubTable($o);
  $c->addTime($_fnames['t2']);
  
  //var_export($c->get());
  
  $c->save(sprintf($_fnames['c'], $_fnames[2]), true);
  
  //Napisy z www.asg.of.pl
  
  //31.18 -> 31.75

  ?>

<?php

   header('Content-Type: text/html; charset=utf-8'); 
   
?><h1>Ranma 1/2</h1><pre><?php


  define('MTimeEqPrecision',.35);
  define('MTimeDefaultFPS',29.97);

  require_once('../mtime.class.php');
  require_once('../mtext.class.php');
  require_once('../subtitle.class.php');
  
  require_once('../subtitle.ass.class.php');
  require_once('../subtitle.tmp.class.php');
  require_once('../subtitle.srt.class.php');
  
  $fp = 'C:\Documents and Settings\wojtekb\My Documents\Downloads\[AM]_Ranma_S1-S7_(Dual_Audio)_H264\\';
  
  

  $a = new AssSubtitle();
  $a->open($fp.'ranma5.ass');
  $b = new TmpSubtitle();
  $b->setEncoding('cp1250');
  $b->open($fp.'Ranma - Season 1 - Episode 05.txt');
  $b->addTime(MTime::fromAss('0:01:42.43')-MTime::fromTmp('00:01:37'));
  $b->save($fp.'Ranma.S1.05.(Dual.Audio).H264.[AM].txt');
  
  
  $c = new SrtSubtitle();
  $c->open($fp.'ranma005pl.srt');
  
  
  $copy = Array('1edkaraoke','1opkaraoke');
  $block = Array('1edtrad', '1optrad',
    //'1edkaraoke','1opkaraoke'
    );
  
  //print_r($c);print_r($a);die();
  
  $la = $a->reset();
  $lb = $b->reset();
  $ec = $c->getSubTable();
  
 /* print_r($ec);
  //print_r($lb);
  631148496630.01
  63114849663.01
  */
  $o = Array();
  for(;true;){
    if($lb === false && $la === false) break;
    if($lb === false){ while($la = $a->next()) $o[] = $la; break;}
    if($la === false){ while($lb = $b->next()) $o[] = $lb; break;}
  
    if(in_array($la['style'],$block)){
      $la = $a->next();
    } else if(in_array($la['style'],$copy)){
      $la['//'] = FALSE;
      $o[] = $la;
      $la = $a->next();
    } else {
      $st = ($la['start']*1000).'.01';
      //print_r($st);print "{$la['text']}\n";
      $eq = MTime::eq($la['start'],$lb['start']);
      if($eq == 0){
        $la['//']=$ec[$st]['text'] . ' ->' . $la['text'];
        $la['text'] = $lb['text'];
        $o[] = $la;
        $lb = $b->next();
        $la = $a->next();
      } else if($eq > 0) {
        $lb['//']  = ' {timings}';//$lb;
        //$lb['text'] = '    {timing}';
        $o[] = $lb;
        $lb = $b->next();
      } else if($eq < 0) {
        $la['//'] = $ec[$st]['text'] . ' ->' . $la['text'];
        $la['text'] = ' ';
        $o[] = $la;
        $la = $a->next();
      }
    }
  }
  
  /*
  $c = new SrtSubtitle();
  $c->setSubTable($a->getSubTable());
  $c->save($fp.'ranma005es.srt');
  */
  $a->setSubTable($o);
  $a->save($fp.'ranma005pl2.ass');
  //print_r($o);
  /*
  $c = clone $b;
  
  $c->setSubTable($o);
  
  */

  ?>

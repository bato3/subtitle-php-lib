<?php
/**
 * Parsing TMPlayer format.
 */

///http://www.gtw.avx.pl/modules.php?name=Content2&pa=showpage&pid=39
class TmpSubtitle extends Subtitle{
  //  private $events;
  //  protected final function _addLine($l)
  protected function _parse(&$f){
    //$a = explode("\n",$f);
    preg_match_all("/(\d+:\d+:\d+)[: =](.+)/",$f,$a,PREG_SET_ORDER);
    $ll = '';
    foreach($a AS $l){
      if($ll != $l[2]){
         $ll = $l[2];
         $this->_addLine(Array(
               //'_org'  => $l[0],
               'start' => MTime::fromTmp($l[1]),
               'text'  => MText::fromTmp(trim($l[2]))
             ));
      }
    }
      
    return true;
  }
  public function get(){
    ksort($this->events);
    $out = Array();
    foreach($this->events AS $l){
      $out[] = MTime::toTmp($l['start']).':'.MText::toTmp($l['text']);
      if(!empty($l['end']) && ($l['start']+4) < $l['end'])
        $out[] = MTime::toTmp($l['start']+4).':'.MText::toTmp($l['text']);
    }
    return join("\n", $out);
  }
  public function decodeTime($v){
    return MTime::fromTmp($v);
  }
  public function encodeTime($t){
    return MTime::toTmp($t);
  }
  
  public static function getVersion(){
    return '$Id: subtitle.tmp.class.php,v 1.2 2009/08/29 22:47:50 wojtekb Exp $';
  }
}


?>

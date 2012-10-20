<?php
  /**
    * Klsa do obsługi napisów w formacie mDVD
    * @author: Wojciech.bajon@gmail.com
    * @version: $Id: subtitle.mdvd.class.php,v 1.4 2009/11/06 08:16:06 wojtekb Exp $
    * Opis formatu mDVD :
              http://www.gtw.avx.pl/modules.php?name=Content2&pa=showpage&pid=35
    */
  
 
class MdvdSubtitle extends Subtitle{
  private $fps = null;
  public function __construct($fps = null){
    if($fps && is_numeric($fps))
      $this->setFps($fps);
  }
  public function setFps($fps){
    $this->fps = floatval($fps);
  }
  public function getFps(){
    return $this->fps;
  }
  //  private $events;
  protected function _parse(&$f){
    if($this->fps != null)
      MTime::setFps($this->fps);
    
    $s = explode("\n",$f);
    $xp = "/^\{(\d+)\}\{(\d*)\}(.*)$/"; 
    for($i=0, $l=count($s); $i<$l; $i++){
      if(preg_match($xp,$s[$i],$ma))
        $this->_addLine(Array('start' => MTime::fromMdvd($ma[1]),
                              'end'   => MTime::fromMdvd($ma[2]),
                              'text'  => MText::fromMdvd($ma[3])));
    }
  }
  public function get(){
    ksort($this->events);
    if($this->fps != null)
      MTime::setFps($this->fps);
    foreach($this->events AS $r){
      $l[] = sprintf('{%d}{%d}%s', MTime::toMdvd($r['start']),
                                   MTime::toMdvd($r['end']), 
                                   MText::toMdvd($r['text']));
    }
    return join("\n",$l);
  }
  public function decodeTime($v){
    return MTime::fromMdvd($v);
  }
  public function encodeTime($t){
    return MTime::toMdvd($t);
  }
  public static function getVersion(){
    return '$Id: subtitle.mdvd.class.php,v 1.4 2009/11/06 08:16:06 wojtekb Exp $';
  }
}

?>

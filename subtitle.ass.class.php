<?php

///http://wiki.multimedia.cx/index.php?title=SubStation_Alpha
///http://www.gtw.avx.pl/modules.php?name=Content2&pa=list_pages2_categories&cid=3


class AssSubtitle extends Subtitle{
  public $type='ASS';
  /**
    * Use styles witch 0 in translate and format methods.
    */
  protected $styles = Array();
  //  private $events;
  protected $eventsnames;
  protected $head = Array();
  public static function getVersion(){
    return '$Id: subtitle.ass.class.php,v 1.5 2009/11/06 08:16:06 wojtekb Exp $';
  }
  protected function _parse(&$f){
    /// determine ASS/SSA by  [V4+ Styles]/[V4 Styles]
    preg_match('/\[(V\d+\+?) Styles\]/',$f,$x);
    if($x[1] == 'V4+')
      $this->type = 'ASS';
    if($x[1] == 'V4')
      $this->type = 'SSA';
    $this->setDefaultRow($this->type);
      
    /// find subtitle structure
    preg_match('/\[Events\][\r\n]+Format:\s*(.+)\r?\n/',$f, $x);
    
    $this->eventsnames 
              =  array_flip(preg_split('/,\s*/',strtolower(trim($x[1]))));
    
    $is = $this->eventsnames['start'];
    $ie = $this->eventsnames['end'];
    $fields = count($this->eventsnames); 
    
    $s = explode("\n",$f);
    
    for($i=0, $l=count($s); $i<$l; $i++){
      if(substr($s[$i], 0, 9) != 'Dialogue:'){
        $this->head[] = trim($s[$i]);
        continue;
      }
      $ll = array_combine(array_keys($this->eventsnames),
                          array_values(explode(',',trim(substr($s[$i],9)),$fields)));
      
      $ll['start'] = MTime::fromAss($ll['start']);
      $ll['end']   = MTime::fromAss($ll['end']);
      //$ll['_org']  = trim($s[$i]);
      $this->_addLine($ll);
    }
    return true;
  }
  /**
    * @param: bool $verbose - dodaj linie z oryginalnym tlumaczeniem 
    * jako koment oraz informacje, o braku odpowiadajacego wpisu w 2 subie
    * {lack} i porawy czasu końcowego/stylu {endtime} 
    */
  public function get($verbose = true){
    $out = Array($this->head);
    $out = array_pop($out);
    ksort($this->events);
    
    foreach($this->events AS $l){
      
      if($verbose) $out[] = "";
      
      if(!empty($l['//']))
       $lt = $l['//'];
      
      if($verbose && empty($l['end']))
        $out[] = 'Comment: '.join(',',$this->defaultRow)."{endtime}";
      
      $out[] = 'Dialogue: '.$this->_formatLine($l);
      
      if($verbose && $lt != FALSE){
        if(is_array($lt)) {
          $out[] = 'Comment: '.$this->_formatLine($lt);
        } else {
          $out[] = 'Comment: '.$this->_formatLine($l,!empty($lt)? $lt:'{lack}');
        }
      }
      unset($lt);
    }
    return join("\n",$out);
  }
  
  
  public function decodeTime($v){
    return MTime::fromAss($v);
  }
  public function encodeTime($t){
    return MTime::toAss($t);
  }
  
  /**
    * Remove not-standard tags, clear '!Effect' tag, add missed tags and
    * recalculate times.
    * @param array $line - unformated sub line 
    * @param string $altText - alternative sub-text-content
    * @return string - formated line, <strong>without</strong>
    *                  <i>Dialogue/Comment</i> start-tag.
    */
  protected function _formatLine($l, $altText = false){
    unset($l['_org']);
    unset($l['text-1']);
    unset($l['//']);
    
      // lack end time (TMplayer)
      if(empty($l['end']))
        $l['end'] = $l['start']+$this->displayTime;
      
      // remove empty effect
      if($l['effect']{0} == '!' && $l['effect']{1} == 'E')
        $l['effect'] = '';
      
      // line isn't real ass/ssa
      if(empty($l['style'])){
        $l = $this->mixRow($l);
      }
      
      if($altText !== false)
        $l['text'] = $altText;
      
      $l['start'] = MTime::toAss($l['start']); 
      $l['end']   = MTime::toAss($l['end']);
      
      return join(',',$l);
  }
  
  public function setDefaultRow($a){
    if(is_string($a)){
      if(strtoupper($a) == 'ASS')
        return $this->defaultRow = $this->defaultASSRow;
        
      if(strtoupper($a) == 'SSA')
        return $this->defaultRow = $this->defaultSSARow;
    }
    $this->defaultRow = $a;
  }
  /**
    * Return full ASS/SSA row, witch all values.
    * @access: private
    * @param: $a Array - values, to set in row
    * @return: Array - ASS/SSA row
    */
  protected function mixRow($a = Array()){
    return array_merge($this->defaultRow,$a);
  }
  
  /**
    * Return full ASS/SSA row, witch all values, safe version.
    * @param: $a Array - values, to set in row
    * @return: Array - ASS/SSA row
    */
  public function fixRow($a = Array()){
    $o = Array();
    foreach($this->defaultRow AS $k=>$v)
      $o[$k] = isset($a[$k])?$a[$k]:$v;
    return $o;
  }
  
  private $defaultRow = array();
  private $defaultSSARow = array (
    'marked' => 'Marked=0',
    'start' => 0,
    'end' => 0,
    'style' => 'Default',
    'name' => '',
    'marginl' => '0000',
    'marginr' => '0000',
    'marginv' => '0000',
    'effect' => '',
    'text' => '',
  );
  private $defaultASSRow = array (
    'layer' => '0',
    'start' => 0,
    'end' => 0,
    'style' => 'Default',
    'name' => '',
    'marginl' => '0000',
    'marginr' => '0000',
    'marginv' => '0000',
    'effect' => '',
    'text' => '',
  );
  /**
    * Get ASS/SSA Style list: all/japanese
    * @param: $japanese bool - retturn only japanes styles (don't use 
                               it for translate comparation).
    * @return: Array - ASS/SSA styles list
    */
  public function getStyleList($jp = false){
    if($jp){
      $o = Array();
      foreach($this->styles AS $k=>$v)
        if($v > 0)
          $o[] = $k;
      return $o;
    }
    
    if(count($this->styles) > 0)
      return array_keys($this->styles);
    
    $this->styles = Array();
    foreach($this->events AS $ev)
      if(!isset($this->styles[$ev['style']]))
        $this->styles[$ev['style']] = 0;
    
    return array_keys($this->styles); 
  }
  public function countStyles(){
    return count($this->styles);
  }
  public function setDontUseStyles(Array $a){
    $this->styles = array_merge($this->styles,$a);
  }
}

?>

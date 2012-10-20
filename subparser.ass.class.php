<?php
/*
  header("Content-Type: text/html; charset=utf8");


  require_once('subparser.class.php');
  require('subparser.exception.class.php');
  */
   /**
     * Parser napisów ASS.
     * @author: Wojciech.Bajon{gmail}
     */
  
  class AssParser extends SubParser{
  
    private $sub;
    
    
    
    private function getEventsFormat(){
      preg_match('/\[Events\][\r\n]+Format:\s*(.+)\r?\n/',$this->sub, $x);
      return array_flip(preg_split('/,\s*/',trim($x[1])));
    }
    
    public function setSub($str){
      $this->sub = $str; 
      
    }
    public function getSub(){
      return $this->sub;
    }
    /**
      * Change time in sub (for selected time).
      * @param: double $shift - time to shift
      * @param: double|string $start - start time for shift, default from begin
      * @param: double|string $end - end time for shift, default to end
      * @return: void
      */
    public function shiftSub($shift, $start=0, $end=36000){
      global $fn;
      $x = $this->getEventsFormat();
      $is = $x['Start'];
      $ie = $x['End'];
      if($is == 0 || $ie == 0)
        throw new SubParserException('This subtitle cann\'t be shifted. (Time is on first possition.)');
      
      $fields = count($x); 
      
      if(!is_numeric($start))
        $start = $this->time2stamp($start);
       else
        $start += $this->time2stamp('00:00:00.0');
        
      if(!is_numeric($end))
        $end = $this->time2stamp($end);
       else
       $end += $this->time2stamp('00:00:00.0');
      
      printf( 'Plik: '.$fn.' Od: '.$this->stamp2time($start).' (%.2f sec) o: %.2f sec. <br />',$start-$this->time2stamp('00:00:00.0'),$shift);
      
      $s = explode("\n",$this->sub);
      
      $ct = 0;
      $ll = '';
      for($i=0, $l=count($s); $i<$l; $i++){
        if(substr($s[$i], 0, 9) != 'Dialogue:')
          continue;
        $ll = explode(',',$s[$i],$fields);
        $ct = $this->time2stamp(trim($ll[$is]));
        
        if($ct >= $start && $ct <= $end){
          $ll[$is]=$this->add2Stamp($ct, $shift);
          $ll[$ie]=$this->add2Time(trim($ll[$ie]), $shift);
          $s[$i] = join(',',$ll);
        }
      }
      $this->sub = join("\n",$s);
    }
    private $subHead, $subData;
    //=========================================================================
    public function parseAndSort(){
      $x = $this->getEventsFormat();
      $is = $x['Start'];
      $fields = count($x); 
      
      
      $s = explode("\n",$this->sub);
      for($i=0, $l=count($s); $i<$l; $i++){
        if(substr($s[$i], 0, 9) != 'Dialogue:'){
          $this->subHead[] = trim($s[$i]);
          continue;
        }
        $ll = explode(',',$s[$i],$fields);
        $ct = $this->time2stamp(trim($ll[$is]));
        while(array_key_exists($ct, $this->subData))
          $ct+0.0001;
        $this->subData[$ct] = trim($ll[$is]);
      }
      $this->subData = ksort($this->subData, SORT_NUMERIC);
    }
    //=========================================================================
    public function getSubtitleHead(){
      return $this->subHead;
    }
    public function getSubtitleData(){
      return $this->subHead;
    }
    //=========================================================================
    public function compareSubtitles(&$head, &$data){
      
      $lk = $lv = $rk = $rv = null;
      $ln = $rn = true;
      reset($this->subData);
      reset($data);
      
      while(true){
        if(!$ln && !$rn)
          break;
        if(!$lk && $ln && !list($lk, $lv) = each($this->subData))
          $ln = false; //left end
        if(!$rk && $rn && !list($rk, $rv) = each($data))
          $rn = false; //right end
      }
      
    }
    
  }
  
  
  /*
  
  $x = new AssParser();
  //print '<pre>';
  $fn='zx.eva.renewal.21.divx511.ssa';
  $x->setSub(file_get_contents('./'.$fn));
  
  $x->shiftSub($x->diffTime('0:03:17.54','0:01:46.00'));
  //$x->shiftSub(0);
  //print_r($x);
  file_put_contents('C:\filmy\Neon Genesis Evangelition\\'.$fn,$x->getSub());
  
  */

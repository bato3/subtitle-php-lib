<?php


   /**
     * Parser napisów.
     * @author: Wojciech.Bajon{gmail}
     */
     
     
  class SubParser{
  
    protected static $dateBase = '2000-01-01'; 
    /**
      * Convert formated time to timestamp with miliseconds
      * @param: string $time eg: 00:01:20,700
      * @param: char(1) $mDelim - miliseconds delim
      * @return: double timestamp with miliseconds (not microseconds)
      */
      
    public function time2stamp($time, $mDelim='.'){
      $t = explode($mDelim, $time);
      if(count($t)!=2)
        throw new SubParserException('Time to stamp conversion error.');
      $x = (strtotime(SubParser::$dateBase.' '.$t[0]) + ($t[1]/100));
      
      //print "\n".$time.' '.date('G:i:s',floor($x)).' '.$x.' '.floor($x)."\n";
      return $x;
    }
    
    /**
      * Convert timestamp with miliseconds to time
      * @param: double $stamp enchanted unix timestamp (double)
      * @param: char(1) $mDelim - miliseconds delim 
      * @return: string time with miliseconds
      */
    public function stamp2time($stamp, $mDelim='.'){
      return date('G:i:s',
                   floor($stamp)).$mDelim.round(($stamp-floor($stamp))*100);
    }
    
    /**
      * Calc time difference form 2 times. Used in: move time to time
      * @param: string $current base time, eg: 00:01:20,700
      * @param: string $target target time
      * @return: double time difference - to add for time
      */
    public function diffTime($current, $target){
      return $this->time2stamp($target)-$this->time2stamp($current);
    }
    
    /**
      * Add xx seconds to time.
      * @param: string $current current time, eg: 00:01:20,700
      * @param: double $add seconds.miliseconds to add to time.
      * @return: string formated time
      */
    public function add2Time($current, $add){
      return $this->stamp2time($this->time2stamp($current) + $add);
    }
    /**
      * Add xx seconds to timestamp.
      * @param: double $current current timestamp
      * @param: double $add seconds.miliseconds to add to time.
      * @return: string formated time
      */
    public function add2Stamp($current, $add){
      return $this->stamp2time($current + $add);
    }
    
    
    /**
      * Speacial equal - with equal range.
      * @param: double $x seconds.miliseconds
      */
    
  }
     
     
  
  
  

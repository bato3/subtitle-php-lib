<?php

  class MTimeException extends Exception{};
  
  /**
    * Klasa pomocnicza, do konwersji czasów filu z i na timestampa 
    * (z milisekundami).
    * @access: public
    * @author: Wojciech Bajon
    * @version: $Id: mtime.class.php,v 1.5 2009/09/11 22:01:25 wojtekb Exp $
    */

  if(!defined('MTimeBaseDate'))
    define('MTimeBaseDate', '1990-01-01');
  define('MTimeBaseTime', strtotime(MTimeBaseDate.' 00:00:00'));
  if(!defined('MTimeDefaultFPS'))
    define('MTimeDefaultFPS',23.9);
  define('MTimeDefaultFPSf', 1/MTimeDefaultFPS);
  if(!defined('MTimeEqPrecision'))
  define('MTimeEqPrecision',0.9); //sec
    
  class MTime /* throws MTimeException */{
    /**
     * Only static usage
     */
    private function __construct(){ }
    //==========================================================================
    
    /**
     * Micro DVD (mdvd): {1100}{1199}.
     */
    /// Fps factor.
    private static $fpsf = MTimeDefaultFPSf; /* 23.9 */
    public static function setFps($v){
      self::$fpsf = 1.0/$v;
    }
    public static function getFps(){
      return 1.0/self::$fpsf;
    }
    
    public static function fromMdvd($v){
      $v = preg_replace("/[^\d]/",'',$v);
      if($v == '')
        return null;
      return MTimeBaseTime + floatval($v)*self::$fpsf;
    }
    public static function toMdvd($t){
      return round(($t-MTimeBaseTime)/self::$fpsf);
    }
    //==========================================================================
    /**
      * TMPlayer (tmp): 00:01:40.
      */
    public static function fromTmp($v){
      return strtotime(MTimeBaseDate.' '
                       .trim(preg_replace("/[^\d:]/",'',$v),':'));
    }
    public static function toTmp($t){
      return date('G:i:s', round($t));
    }
    //==========================================================================
    /**
      * Mplayer 2 (mpl): [994][1023].
      */
    public static function fromMpl($v){
      return MTimeBaseTime + floatval(preg_replace("/[^\d]/",'',$v))/10;
    }
    public static function toMpl($t){
      return round(($t-MTimeBaseTime)*10);
    }
    //==========================================================================
    /**
      * SubStation Alpha (SSA) && Advanced SSA (ASS): 0:00:04.95,0:00:05.70
      * !! Throws Errors
      */
    public static function fromAss($v,$d='.'){
      $v = explode($d,preg_replace("/[^\d:".($d=='.'?'\.':$d)."]/",'',$v));
      if(count($v)!=2)
        throw new MTimeException('Conversion Error.',1);
      
      return strtotime(MTimeBaseDate.' '.$v[0])+floatval('0.'.$v[1]);
    }
    public static function toAss($t,$d='.'){
      list($foo,$tm) = explode('.',$t);
      return date($d=='.'?'G:i:s':'H:i:s',floor($t))
                        .$d.($d=='.'?substr($tm.'0000',0,2):substr($tm.'0000',0,3));
    }
    //==========================================================================
    /**
      * SRT (srt): 00:02:09,700 --> 00:02:12,300 
      * !! Throws Errors
      */
    public static function fromSrt($v){
      return self::fromAss($v,',');
    }
    public static function toSrt($t){
      return self::toAss($t,',');
    }
    
    /**
      * Comparison with the reference time accuracy.
      * The comparison function return an integer less than, equal to, 
      * or greater than zero if the first argument is considered to be
      * respectively less than, equal (witch precision) to, or greater 
      * than the second. 
      * Porównaj czasy z zadaną prezycją (domyślnie MTimeEqPrecision), 
      * ale może być inna.
      *
      * @param float $t1: Time 1
      * @param float $t2: Time 2
      * @param float $pr: Precision.
      * @return int -1: t1 less than t2; 0: t1 eq t2; +1 t1 more than t2
      */
    public static function eq($t1, $t2, $pr = MTimeEqPrecision){
      if((abs($t1-$t2) - $pr) < $pr)
        return 0;
      if(($t1-$t2) < 0)
        return -1;
      return 1;
    }
    
    
    public static function getVersion(){
      return '$Id: mtime.class.php,v 1.5 2009/09/11 22:01:25 wojtekb Exp $';
    }
  }

  
?>

<?php



  /// http://www.gtw.avx.pl/modules.php?name=Content2
  /// http://www.gtw.avx.pl/modules.php?name=Content2&pa=showpage&pid=3
    
  /**
    * Klasa pomocnicza, do konwersji napisów z formatu natywnego na wewnętrzny (ASS).
    * @access: public
    * @author: Wojciech Bajon
    * @version: $Id: mtext.class.php,v 1.7 2009/11/06 08:16:06 wojtekb Exp $
    */
    
  class MText{
    
    private function __construct(){ }
    public static function getVersion(){
      return '$Id: mtext.class.php,v 1.7 2009/11/06 08:16:06 wojtekb Exp $';
    }
    /**
      * TMplayer. @url: http://www.gtw.avx.pl/modules.php?name=Content2&pa=showpage&pid=39
      */
    public static function fromTmp($t){
      return str_replace('|',' \N ',$t);
    }
    public static function toTmp($t){
      $rx = Array(
          '/\{[^\}]*\}/',
          '/\\N/',
          '/\\n/'
        );
      $tx = Array(
          '',
          '|',
          '|'
        );
      return preg_replace($rx,$tx,$t);
    }
    /**
      * ASS/SSA
      */
    public static function fromAss($t){
      return $t;
    }
    public static function toAss($t){
      return $t;
    }
    /**
      * mDVD: http://www.gtw.avx.pl/modules.php?name=Content2&pa=showpage&pid=35
      */
    public static function fromMdvd($t){
      //{y:i}{y:i,b,u}{Y:i}
      //{c:$BBGGRR} {b:$BBGGRR}
      //{s:18}
      //{f:Arial}
      //{o:x,y} - pozycja
      //{P:0|1}, {H:kodowanie lini}
      
      $l = explode('|',trim($t));
      
      return str_replace('|','\N',trim($t));
      //return str_replace('|','\N',preg_replace('/\{[^\}]*\}/','',trim($t)));
    }
    public static function toMdvd($t){
      return str_replace('\N','|',$t);
      /*$rx = Array(
          '/\{[^\}]*\}/',
          '/\\N/',
          '/\\n/'
        );
      $tx = Array(
          '',
          '|',
          '|'
        );
      return preg_replace($rx,$tx,$t);*/
      //.'|write MText::toMdvd and MText::fromMdvd methods'
    }
    //{\i1}
    
    /**
     * SRT (semi HTML formatting)
     */
    
     public static function fromSrt($t){
       return preg_replace(
                   Array('#<([iub])>#ie','#</([iub])>#ie','#</?f(ont)?[^>]*>#i','#<a[^>]*>#i'),
                   Array("'{\\\\\\'.strtolower('\\1').'1}'","'{\\\\\\'.strtolower('\\1').'0}'",'',''),
              $t  );
     }
     public static function toSrt($t){
        return preg_replace(
                   Array('/\{\\\([iub])1\}/i','/\{\\\([iub])0\}/i','/\\\n/i'),
                   Array('<$1>',             '</$1>',              "\n"),
              $t  );
     }
     
     
     
    
    /** 
     * Fix standard punctuation errors.
     * napraw typowe błedy interpunkcyjne
     */
    public static function fixPunctuation($t){
      return preg_replace(Array('/ *(,) */', '/ +([\.\?!:])/', '/\s+/'),
                          Array('$1 ',        '$1',             ' '), 
                          $t );
    }
    
  }
?>

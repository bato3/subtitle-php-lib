<?php

/**
  * @author: Wojciech Bajon
  * @version: $Id: subtitle.class.php,v 1.8 2009/11/06 08:16:06 wojtekb Exp $
  * @license: Commercial.
  * Udziela się nieodpłatnej licencji dla serwisu fansubis.pl.
  * Każda modyfikacja musi zostać zgłoszona do mnie, 
  * czyli na adres: wojciech.bajon@gmail.com oraz zaznacz się, że nie można
  * modyfikować metody subtitle::getAuthor(). 
  * Dodanie kolejnych elementów jest możliwe w przypadku napisania obsługi
  * formatu filmu.
  */
  
class SubtitleException        extends Exception{}
class UploadException          extends SubtitleException{}
class UnknownSubtitleException extends SubtitleException{}

/**
  * Klasa bazowa reprezentująca zawatość pliku z napisami. 
  * Właściwe metody zaimplemntowane w klasach odpowiadających samym napisom :D
  virtual class subtitle{
  
  * Tablica pojedyńczych krotek napisów, array(start, end, text). 
  * Przy ASS zawiera pozostałe dane
    array $events;
  
  * Zmienne na przyszłość
    string $head; /// - wszystko co nie jest krotkami napisów, 
               /// z pominięciem komentarzy 
    float $fps;
    
    
  * Dane o autorze - prezentacja punbliczna, np. na www, patrz licencja
    array final public static function getAuthor()
    
  * Zwracana wartość jest dodawana na końcu tablicy zwracanej przez getAuthor()
    array public function getGreetings()
  }
  
  
*/


abstract class Subtitle /*throws SubtitleException, UploadException*/{
  protected $events = Array();
  protected $orginName=null;
  
  private $encoding = null;
  /**
    * Set orginal file encoding.
    * Ustaw kodowanie znaków w oryginalnym pliku. Jeżeli <strong>nie zostanie
    * podane</strong>, system postara się wykryć, windows-1250 i przekoduje 
    * na utf-8.
    * <strong>UWAGA</strong>: iso-8859-2 też wykrywa jako windows-1250 
    * Aby przekodować z innych formatów należy ustawić kodowanie.
    *
    * @param string file encoding
    * @return void
    */
  public function setEncoding($enc){
    $this->encoding = $enc;
  }
  public function getEncoding(){
    return $this->encoding;
  }
  
  protected $displayTime = 0.8;
  /**
    * Set time used, if end time is empty (TMPlayer).
    * Ustawia czas, którzy będzie wykorzystywany w przypadku, kiedy nie ma
    * czasu końcowego (w TMPlayer).
    * @param float $t time.
    */
  final public function setDisplayTime($t){
    $this->displayTime = $t;
  }
  /**
    * Open and parse subtitle file.
    * Może otwierać plik przesłany przez przegłądarkę (Element tablicy $_FILES)
    * lub bezpośrednio z dysku (nazwa na dysku).
    * Metoda rzuca wyjątkami: 
    * <b>UploadException</b>
    *  err == -1: to nie jest prawidłowa tablica $_FILES
    *  err == -2: !is_uploaded_file
    *  err > 0: Błędy przesyłania, zobacz: http://pl.php.net/manual/pl/features.file-upload.errors.php
    * <b>SubtitleException</b>
    * err == 1: plik nie istnieje
    * err == 2: nie mogę odczytać zawartości
    *
    * @param  string -v1:$f - file name
    * @param  array -v2:$f - $_FILES[fieldname] (array from upload proccess)
    * @return bool parse status
    */
  
  public function open($f){
    //print_pre($f);
    if(is_array($f)){
      if(isset($f['error']) && $f['error'] != 0)
        throw new UploadException('Upload error', $f['error']);
      
      if(empty($f['name']) || empty($f['tmp_name']) || empty($f['size']))
        throw new UploadException('Invalid _FILES array', -1);
      
      if(!is_uploaded_file($f['tmp_name']))
        throw new UploadException('Possible upload attack', -2);
      
      $fname = $f['tmp_name'];
      $this->orginName = $f['name'];
    } else {
      $fname = $f;
      $this->orginName = $f;
    }
    
    if(!file_exists($fname))
      throw new SubtitleException(sprintf('Subtitle file: "%s" don\'t exists',
                                          $this->orginName), 
                                  1);
    
    $c = file_get_contents($fname);
    if($c === false)
      throw new SubtitleException(sprintf('I can\'t load subtitle file: "%s"',
                                          $this->orginName), 
                                  2);
    return $this->load($c);
  }
  //==========================================================================  
  public function openFromMultiUplad($name,$idx){
    return $this->open(Array(
                         'name'=>$_FILES[$name]['name'][$idx],
                         'tmp_name'=>$_FILES[$name]['tmp_name'][$idx],
                         'size'=>$_FILES[$name]['size'][$idx],
                         'error'=>$_FILES[$name]['error'][$idx],
                         'type'=>$_FILES[$name]['type'][$idx]
                       ));
  }
  
  //===========================================================================
  /**
    * Load and parse string with subtitle.
    * Ładuje i przetwarza napisy (podane jako string).
    * 
    * @param string $f: subtitle string
    * @param string $orginName: Orginal filename
    * @return bool parse status
    */
  public function load(&$f, $orginName=null){
    if(!empty($orginName))
      $this->orginName = $orginName;
    
    if($this->encoding != null)
      $f = iconv($this->encoding,'utf-8',$f);
      
    return $this->_parse($f);
  }
  //===========================================================================
  /**
    * Method to parse specified subtitle type.
    * @param string $f: subtitle string
    */
  abstract protected function _parse(&$f);
  /**
    * Get subtitle content.
    * @return: string: Return subtitle after changes.
    */
  abstract public function get();
  //===========================================================================
  /**
    * Save subtitle as file on disk.
    * @param: string $fname: Flename to save on disk
    * @param: bool/string $encoding:
    * - bool: true: add BOM, false: without adds and return conversion
    * - string: 'AUTO': encoding from UTF-8 to self::setEncoding, 
    *   otherwise encoding to setting value
    * @return: bool save status (file_get_contents)
    */
  public function save($fname,$forceUtf8y=false, $verbose = true){
    if(is_bool($forceUtf8y))
      return file_put_contents($fname, 
                  ($forceUtf8y?'ďťż':'').$this->get($verbose));
    
    if($this->encoding != null)
      return file_put_contents($fname, iconv('utf-8',
              strtolower($forceUtf8y)=='auto'?$this->encoding:$forceUtf8y,
              $this->get($verbose)));
    
    return file_put_contents($fname, $this->get($verbose));
  }
  
  
  public function getInEncoding($enc,$verbose){
    return iconv('utf-8', $enc, $verbose);
  }
  //===========================================================================
  /**
    * Decode time from subtitle format to private format. 
    * To convert use class MTime.
    * Przetwarza czas z formatu natywnego napisów na wewnętrzny parsera napisów.
    * Używać klasy MTime metody: from{$format_napisów}, np: MTime::fromMpl
    *
    * @param string $v: czas w formacie natywnym napisów.
    * @return float: czas w formacie wewnętrznym. (timestamp z milisekundami,
    * jako część dziesiętna)
    */
  abstract public function decodeTime($v);
  /**
    * Encode private format to fubtitle format. To convert use class MTime.
    * More info @see:Subtitle::decodeTime
    *
    * @param float $t: time in inner format.
    * @return string formatted time
    */
  abstract public function encodeTime($t);
  //===========================================================================
  /**
    * Shift time.
    * Przesuwa czas napisów o zadaną wartość od czasu $st do czsu $et.
    * @param $t - float liczba sekund o jakie ma być przesunięty czas.
    * @param $st - float od któej sekundy filmu ma być przesuwany czas.
    * @param $et - float do której sekundy filmu ba być przesunięty czas.
    * @return liczba odrzuconych linii (komnetarze, problemy z formatowaniem)
    */
  public function addTime(/*float*/$t, /*float*/$st=0, /*float*/$et=null){
    /*
    * @pa_ram $t - array tablica, której elementami są czas z kórego ma być
    * przesunięcie do czasu do kórego ma być przesunięcie, w formacie natywanym.
    */
    //make sure, that this is copy
    $ev = Array($this->events);
    $ev = array_pop($ev);
    $this->events = Array();
    foreach($ev AS $ll){
      //$ct = $this->decodeTime(trim($ll['start']));
      if($ll['start'] >= $st && (empty($et) || $ll['start'] <= $end)){
        $ll['start'] += $t;
        if(!empty($ll['end']))
          $ll['end'] += $t;
        else 
          $ll['end'] = $ll['start'] + $this->displayTime;
      }
      $this->_addLine($ll);
    }
  }
  //===========================================================================
  /**
    * Zwraca różnicę pomiędzy czasami $ft i $tt, gdzie dane te są 
    * podane w formacie natywnym.
    *
    * @param: string czas 1 w formacie natywnym napisów
    * @param: string czas 2 w formacie natywnym napisów
    * @return float róznica czasów w formacie wewnętrznym
    */
  function diffTime($ft, $tt){
    return $this->decodeTime($tt) - $this->decodeTime($ft);
  }
  //===========================================================================
  /**
    * Dodaje kolejną linię do bufora events.
    * @param: $l - array pojedyncza linia. 
    */
  protected function _addLine($l){
    $k = $l['start']+0.00001;
    while(array_key_exists((string)($k*1000), $this->events))
      $k+=0.00001;
    
    $this->events[(string)($k*1000)] = $l;
  }
  
  //===========================================================================
  final public function getSubTable(){
    ksort($this->events);
    return $this->events;
  }
  final public function setSubTable($t){
    $this->events = $t;
  }
  
/*  final public function callFunc($f, $m=null){
    foreach($this->events as $k=>$ev)
      $this->events[$k] = $m::$f($ev);
  }*/
  final public function fixPunctuation(){
    foreach($this->events as $k=>$ev)
      $this->events[$k] =  MText::fixPunctuation($ev);
  }
  
  final public function getFileName(){
    return $this->orginName;
  }
  final public function setFileName($n){
    $this->orginName = $n;
  }
  
  /**
    # current() - Return the current element in an array
    # end() - Set the internal pointer of an array to its last element
    # prev() - Rewind the internal array pointer
    # reset() - Set the internal pointer of an array to its first element
    */
  final public function reset(){
    ksort($this->events);
    return reset($this->events);
  }
  final public function current(){
    return current($this->events);
  }
  final public function end(){
    return end($this->events);
  }
  final public function next(){
    return next($this->events);
  }
  final public function prev(){
    return prev($this->events);
  }
  
  //===========================================================================
  final public static function getAuthor(){
    // Do not modify this function
    return Array(array_merge(Array('name'=>'Wojciech Bajon', 'contact'=>'callto://wojteb_ess'), self::getGreetings()));
  }
  //===========================================================================
  private static function getGreetings(){
    return Array();
    //return Array(Array('name'=>'nazwa do wyświetlania ', 'contact'=>'adres HREF do kontaktu'));
  }
  public static function getVersion(){
    return '$Id: subtitle.class.php,v 1.8 2009/11/06 08:16:06 wojtekb Exp $';
  }
  
  
  
  
  public static function factory($type){
    switch(strtolower($type)){
      case 'ass':
      case 'ssa':
        return new AssSubtitle();
      case 'srt':
        return new SrtSubtitle();
      case 'mdvd':
      case 'microdvd':
        return new MdvdSubtitle();
      case 'tmp':
        return new TmpSubtitle();
      default:
        throw new UnknownSubtitleException();
        
    }
  }
  
}
  
  
  
<?php

/*
The format has no header, and no footer. Each subtitle has four parts:

Line 1 is a sequential count of subtitles, starting with 1.

Line 2 is the start timecode, followed by the string " --> ", followed by the end timecode. Timecodes are in the format HH:MM:SS,MIL (hours, minutes, seconds, milliseconds). The end timecode can optionally be followed by display coordinates (example " X1:100 X2:600 Y1:050 Y2:100"). Without coordinates displayed, each line of the subtitle will be centered and the block will appear at the bottom of the screen.

Lines 3 onward are the text of the subtitle. New lines are indicated by new lines (i.e. there's no "\n" code). The only formatting accepted are the following:

<b>text</b>: put text in boldface
<i>text</i>: put text in italics
<u>text</u>: underline text
<font color="#00ff00">text</font>: apply green color formatting to the text (you can use the font tag only to change color)



Tags can be combined (and should be nested properly). Note that the SubRip code appears to prefer whole-line formatting (no underlining just one word in the middle of a line).

Finally, successive subtitles are separated from each other by blank lines.

Here is an example of an SRT file:

1
00:02:26,407 --> 00:02:31,356 X1:100 X2:100 Y1:100 Y2:100
<font color="#00ff00">Detta handlar om min storebrors</font>
<b><i><u>kriminella beteende och foersvinnade.</u></i></b>

2
00:02:31,567 --> 00:02:37,164
Vi talar inte laengre om Wade. Det aer
som om han aldrig hade existerat.

//==============================================================================
extend srt:
<f Name='nazwa czcionki', Type='typ', Size='rozmiar'> ...</f> - dla danego tekstu nadpisuje globalne ustawienia dla formatu wyświetlania, zastępując je podanymi.

<a left/> ... - wyrównanie do lewej
<a right/> ... - wyrównanie do prawej
*/

class SrtSubtitle extends Subtitle{
  //  private $events;
  protected function _parse(&$f){
    $x = split("\n",str_replace("\r",'',$f));
    $c = 1;
    $s = Array();
    $t = Array();
    for($i=0, $l=count($x); $i<$l; ++$i){
      if($c == 1)
        ;
      else if($c == 2)
        $t = $this->__parseTimeLine($x[$i]);
      else if(trim($x[$i]) == '')
        $c = $this->_addLine($t);
      else
        $t['text'][]=MText::fromSrt(trim($x[$i]));
      ++$c;
    }
  }
  //==========================================================================
  protected function _addLine($l){
    $l['text'] = join('\N',$l['text']);
    parent::_addLine($l);
    return 0;
  }
  //==========================================================================
  private function __parseTimeLine($l){
    preg_match('/^(\d+:\d+:\d+,\d+) *\-+> *(\d+:\d+:\d+,\d+) *(.*)$/', $l, $o);
    return Array('start' => MTime::fromSrt($o[1]), 
                 'end'   => MTime::fromSrt($o[2]),
                 'pos'   => $o[3]
                 );
  }
  //==========================================================================
  
  public function get(){
    $out = Array();
    ksort($this->events);
    
    $i=1;
    foreach($this->events AS $l){
      $out[] = $i;
      $out[] = MTime::toSrt($l['start']).' --> '.MTime::toSrt($l['end']
                   .(!empty($l['pos'])?(' '.$l['pos']):''));
      $out[] = MText::toSrt($l['text']);
      $out[] = '';
      ++$i;
    }
    return join("\n",$out);
  }
  public function decodeTime($v){
    return MTime::fromSrt($v);
  }
  public function encodeTime($t){
    return MTime::toSrt($t);
  }
  public static function getVersion(){
    return '$Id: subtitle.srt.class.php,v 1.3 2009/11/06 08:15:51 wojtekb Exp $';
  }
}

?>

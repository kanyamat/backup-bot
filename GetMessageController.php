<?php

namespace App\Http\Controllers;

use App\Models\pregnants as pregnants;
use App\Models\RecordOfPregnancy as RecordOfPregnancy;
use App\Models\sequents as sequents;
use App\Models\sequentsteps as sequentsteps;
use App\Models\users_register as users_register;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
class GetMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response

    /**
     * @var GetMessageService
     */
    public function getmessage()
    {         
    
            $httpClient = new CurlHTTPClient('omL/jl2l8TFJaYFsOI2FaZipCYhBl6fnCf3da/PEvFG1e5ADvMJaILasgLY7jhcwrR2qOr2ClpTLmveDOrTBuHNPAIz2fzbNMGr7Wwrvkz08+ZQKyQ3lUfI5RK/NVozfMhLLAgcUPY7m4UtwVwqQKwdB04t89/1O/w1cDnyilFU=');
            $bot = new LINEBot($httpClient, array('channelSecret' => 'f571a88a60d19bb28d06383cdd7af631'));
            // คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
            $content = file_get_contents('php://input');
            // แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
            $events = json_decode($content, true);
            if(!is_null($events)){
            // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
            $replyToken  = $events['events'][0]['replyToken'];
            $user = $events['events'][0]['source']['userId'];
            $userMessage = $events['events'][0]['message']['text'];
            }
            return $this->checkmessage($replyToken,$userMessage,$user);
    }
      public function checkmessage($replyToken,$userMessage,$user)
    {          

           $sequentsteps =  $this->sequentsteps_seqcode($user);
           //$sequentsteps->seqcode

            if ($userMessage =='สนใจ') {
                  $case = 1;
                  $seqcode = '0005';
                  $nextseqcode = '0007';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_insert($user,$seqcode,$nextseqcode);
            }elseif (strpos($userMessage, 'hello') !== false || strpos($userMessage, 'สวัสดี') !== false) {
                   $userMessage  = 'สวัสดีค่ะ ';
                   $case = 1;      
            }elseif ($userMessage == 'เริ่มต้นการใช้งาน') {
                   $delete = $this->delete_data_all($user);
                   $userMessage  = 'คุณสนใจผู้ช่วยอัตโนมัติไหม? ';
                   $case = 6; 
            }elseif ($userMessage == 'ไม่สนใจ'  ) {
                  $userMessage = 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ:) หากคุณสนใจในภายหลังให้พิมพ์ว่า "ต้องการผู้ช่วย';
                  $case = 1; 

            }elseif ($userMessage == 'ไม่ถูกต้อง'  ) {
                  $userMessage = 'กรุณาพิมพ์ใหม่';
                  $case = 1;      
            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0005' ) {
                  $user_name = $userMessage;
                  $case = 1;
                  $seqcode = '0007';
                  $nextseqcode = '0009';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_insert = $this->user_insert($user,$user_name);

            }elseif (is_numeric($userMessage) !== false && $sequentsteps->seqcode == '0007' ) {
                  $answer = $userMessage;
                  $case = 1;
                  $update = 2;
                  $seqcode = '0009';
                  $nextseqcode = '0011';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update);

            }elseif (is_numeric($userMessage) !== false && $sequentsteps->seqcode == '0009' ) {
                  $answer = $userMessage;
                  $case = 1;
                  $update = 3;
                  $seqcode = '0011';
                  $nextseqcode = '0013';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update);

            }elseif (is_numeric($userMessage) !== false && $sequentsteps->seqcode == '0011' ) {
                  $answer = $userMessage;
                  $case = 1;
                  $update = 4;
                  $seqcode = '0013';
                  $nextseqcode = '0015';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update);

            }elseif (is_numeric($userMessage) !== false && $sequentsteps->seqcode == '0013' ) {
                  $answer = $userMessage;
                  $case = 2;
                  $update = 5;
                  $seqcode = '0015';
                  $nextseqcode = '0017';
                  $userMessage  = $this->sequents_question($seqcode);
                  //$sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update);

            }elseif ($userMessage == 'ครั้งสุดท้ายที่มีประจำเดือน'  && $sequentsteps->seqcode == '0013' ) {
                  $answer = $userMessage;
                  $case = 1;
                  // $update = 5;
                  $seqcode = '1015';
                  $nextseqcode = '0017';
                  $userMessage  = 'ขอทราบครั้งสุดท้ายที่คุณมีประจำเดือนเพื่อคำนวณอายุครรภ์ค่ะ (กรุณาตอบวันที่และเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน)';
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update);
            }elseif ($userMessage == 'กำหนดการคลอด'  && $sequentsteps->seqcode == '0013' ) {
                  $answer = $userMessage;
                  $case = 1;
                  // $update = 5;
                  $seqcode = '2015';
                  $nextseqcode = '0017';
                  $userMessage  = 'ขอทราบกำหนดการคลอดของคุณหน่อยค่ะ (กรุณาตอบวันที่และเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน';
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update);
            }elseif ($userMessage == 'อายุครรภ์ถูกต้อง'  && ($sequentsteps->seqcode == '1015' ||  $sequentsteps->seqcode == '2015')  ) {
                  $answer = $sequentsteps->answer;
                  $case = 1;
                  $update = 6;
                  $seqcode = '0017';
                  $nextseqcode = '0019';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif ( is_string($userMessage) !== false && ($sequentsteps->seqcode == '1015' || $sequentsteps->seqcode == '2015') ) {

              //strlen($userMessage) == 5 
                  $seqcode = $sequentsteps->seqcode;
                  $userMessage = $this->pregnancy_calculator($user,$userMessage,$seqcode);

                  if($userMessage == 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง'){
                     $case = 1;
                  }else{
                     $case = 3;
                  }
            }elseif (strlen($userMessage) == 10  && $sequentsteps->seqcode == '0017'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  $update = 7;
                  $seqcode = '0019';
                  $nextseqcode = '0021';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0019'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  $update = 8;
                  $seqcode = '0021';
                  $nextseqcode = '0023';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0021'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  $update = 9;
                  $seqcode = '0023';
                  $nextseqcode = '0025';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0023'  ) {
                  $answer = $userMessage;
                  $case = 9;
                  $update = 10;
                  $seqcode = '0025';
                  $nextseqcode = '0027';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update);

            }elseif ($userMessage == 'แพ้ยา' && $sequentsteps->seqcode == '0025'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  $userMessage  = 'คุณแพ้ยาอะไรคะ?';

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0025'  ) {
                  $answer = $userMessage;
                  $case = 10;
                  $update = 11;
                  $seqcode = '0027';
                  $nextseqcode = '0029';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif ($userMessage == 'แพ้อาหาร' && $sequentsteps->seqcode == '0027'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  $userMessage  = 'คุณแพ้อาหารอะไรคะ?';

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0027'  ) {
                  $answer = $userMessage;
                  $case = 4;
                  $update = 12;
                  $seqcode = '0029';
                  $nextseqcode = '0031';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 


            }elseif ($userMessage == 'ไม่แพ้ยา' && $sequentsteps->seqcode == '0025'  ) {
                  $answer = $userMessage;
                  $case = 10;
                  $update = 11;
                  $seqcode = '0027';
                  $nextseqcode = '0029';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0027'  ) {
                  $answer = $userMessage;
                  $case = 4;
                  $update = 12;
                  $seqcode = '0029';
                  $nextseqcode = '0031';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 


            }elseif ($userMessage == 'ไม่แพ้อาหาร' && $sequentsteps->seqcode == '0025'  ) {
                  $answer = $userMessage;
                  $case = 4;
                  $update = 11;
                  $seqcode = '0029';
                  $nextseqcode = '0031';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $user_update = $this->user_update($user,$answer,$update); 

            }elseif ($userMessage == 'เบา' ||$userMessage == 'ปานกลาง' || $userMessage == 'หนัก' || $userMessage == 'ดูข้อมูล'  ) {

                    if ($userMessage=='หนัก'  ) {
                      $answer= 3;
                    }elseif($userMessage=='ปานกลาง') {
                      $answer = 2;
                    }else{
                      $answer = 1;
                    }
                  $case = 5;
                  $update = 13;
                  $userMessage  = $this->user_data($user);
                  $user_update = $this->user_update($user,$answer,$update);
        
            }elseif ($userMessage == 'ยืนยันข้อมูล' ) {
                  $users_register = $this->users_register_select($user);
                  $preg_week = $users_register->preg_week;

                  $user_weight =  $users_register->user_weight;
                  $user_height =  $users_register->user_height;

                  $bmi  = $this->bmi_calculator($user_weight,$user_height);
                  
                  $user_age =  $users_register->user_age;
                  $active_lifestyle =  $users_register->active_lifestyle;
                  $weight_criteria  = $this->weight_criteria($bmi);
                  $cal  = $this->cal_calculator($user_age,$active_lifestyle,$user_weight,$preg_week);

                if ($bmi>=24.9 ) {
                    $text = 'น้ำหนักของคุณเกินเกณฑ์ ลองปรับการรับประทานอาหารหรือออกกำลังกายดูไหมคะ'."\n".
                       'หากคุณแม่ไม่ทราบว่าจะทานอะไรดีหรือออกกำลังกายแบบไหนดีสามารถกดที่เมนูกิจกรรมด้านล่างได้เลยนะคะ';
                }else{
                    $text = 'หากคุณแม่ไม่ทราบว่าจะทานอะไรดีหรือออกกำลังกายแบบไหนดีสามารถกดที่เมนูกิจกรรมด้านล่างได้เลยนะคะ';
                }

                $RecordOfPregnancy = $this->RecordOfPregnancy_insert($preg_week, $user_weight,$user);

                $format = $this->sequentsteps_update2($user,$cal);
                  
                return $this->replymessage_result($replyToken,$preg_week,$bmi,$cal,$weight_criteria,$text,$user);

            }elseif ($userMessage == 'ทารกในครรภ์') {
                $users_register = $this->users_register_select($user);     
                $preg_week = $users_register->preg_week;
                $pregnants = $this->pregnants($preg_week);
                $descript = $pregnants->descript;
                $userMessage  =  $descript;
                $case = 1; 

            }elseif ($userMessage == 'ข้อมูลโภชนาการ') {
                   $format = $sequentsteps->answer;
                   $meal_planing = $this->meal_planing($format);
                   $userMessage  = $meal_planing;
                   $case = 1;  
            }elseif ($userMessage == 'แก้ไขข้อมูล') {

                   $seqcode = '0040';
                   $nextseqcode = '0000';
                   $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                   $userMessage = 'พิมพ์เพียงแค่เลขตามด้านล่างนี้เพื่อแก้ไข'. "\n".
                                  'พิมพ์ "1" ชื่อ '. "\n".
                                  'พิมพ์ "2" อายุ '. "\n".
                                  'พิมพ์ "3" ส่วนสูง '."\n".
                                  'พิมพ์ "4" น้ำหนักก่อนตั้งครรภ์ '."\n".
                                  'พิมพ์ "5" น้ำหนักปัจจุบัน '."\n".
                                  'พิมพ์ "6" อายุครรภ์ '."\n".
                                  'พิมพ์ "7" เบอร์โทรศัพท์ '."\n".
                                  'พิมพ์ "8" อีเมล '."\n".
                                  'พิมพ์ "9" โรงพยาบาลที่ฝากครรภ์ '."\n".
                                  'พิมพ์ "10" เลขประจำตัวผู้ป่วย '."\n".
                                  'พิมพ์ "11" แพ้ยา '."\n".
                                  'พิมพ์ "12" แพ้อาหาร ';
                   $case = 1;  
            }elseif (is_numeric($userMessage) !== false && $sequentsteps->seqcode == '0040' && $userMessage <=12) {
                switch($userMessage) {
                 case '1' : 
                       $userMessage = 'ขอทราบชื่อและนามสกุลของคุณแม่อีกครั้งค่ะ';

                       $seqcode = '0140' ;
                        $nextseqcode = '0000';
                    
                    break;
                 case '2' : 
                       $seqcode = '0007' ;
                       $userMessage  = $this->sequents_question($seqcode);
                       $seqcode = '0240' ;
                       $nextseqcode = '0000';

        
                    break;
                 case '3' : 
                       $seqcode = '0009' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '0340' ;
                       $nextseqcode = '0000';
                    break;
                 case '4' : 
                       $seqcode = '0011' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '0440' ;
                       $nextseqcode = '0000';
                    break;
                 case '5' : 
                       $seqcode = '0013' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '0540' ;
                       $nextseqcode = '0000';

                    break;
                 case '6' : 
                       $seqcode = '0015' ;
                       $userMessage  = $this->sequents_question($seqcode);
                       $seqcode = '0640' ;
                       $nextseqcode = '0000';
                        $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                        $case = 2;
                        return $this->replymessage($replyToken,$userMessage,$case);
                    break;
                 case '7' : 
                       $seqcode = '0017' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '0740' ;
                       $nextseqcode = '0000';
                    break;
                 case '8' : 
                       $seqcode = '0019' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '0840' ;
                       $nextseqcode = '0000';
                    break;
                 case '9' : 
                       $seqcode = '0021' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '0940' ;
                       $nextseqcode = '0000';
                    break;
                 case '10' : 
                       $seqcode = '0023' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '1040' ;
                       $nextseqcode = '0000';
                    break;
                 case '11' : 
                       $seqcode = '0025' ;
                       $userMessage  = $this->sequents_question($seqcode);
                        $seqcode = '1140' ;
                       $nextseqcode = '0000';
                    break;
                 case '12' : 
                       $seqcode = '0027' ;
                       $userMessage  = $this->sequents_question($seqcode);
                       $seqcode = '1240' ;
                       $nextseqcode = '0000';
                    break;
                }
                   $case = 1;
                    $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0140') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 1;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0240') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 2;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

             }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0340') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 3;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

             }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0440') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 4;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

             }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0540') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 5;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);


             }elseif ($userMessage == 'ครั้งสุดท้ายที่มีประจำเดือน' && $sequentsteps->seqcode == '0640') {
                  $answer = $userMessage;
                  $case = 1;
                  $seqcode = '10640';
                  $nextseqcode = '0000';
                  $userMessage  = 'ขอทราบครั้งสุดท้ายที่คุณมีประจำเดือนเพื่อคำนวณอายุครรภ์ค่ะ (กรุณาตอบวันที่และเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน)';
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

             }elseif ($userMessage == 'กำหนดการคลอด' && $sequentsteps->seqcode == '0640') {
                 $answer = $userMessage;
                  $case = 1;
                  $seqcode = '20640';
                  $nextseqcode = '0000';
                  $userMessage  = 'ขอทราบกำหนดการคลอดของคุณหน่อยค่ะ (กรุณาตอบวันที่และเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน';
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
             }elseif ($userMessage == 'อายุครรภ์ถูกต้อง'  && ($sequentsteps->seqcode == '10640' ||  $sequentsteps->seqcode == '20640')  ) {
                  $answer = $sequentsteps->answer;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 6;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

            }elseif ( is_string($userMessage) !== false   && ($sequentsteps->seqcode == '10640' || $sequentsteps->seqcode == '20640') ) {
                  $seqcode = $sequentsteps->seqcode;
                  $userMessage = $this->pregnancy_calculator($user,$userMessage,$seqcode);

                  if($userMessage == 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง'){
                     $case = 1;
                  }else{
                     $case = 3;
                  }
        
            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0740') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 7;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                      
            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0840') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 8;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '0940') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 9;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                             
            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '1040') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 10;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '1140') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 11;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '1240') {
       
                  $answer = $userMessage;
                  $case = 5;
                  $seqcode = '0040';
                  $nextseqcode = '0000';
                  $update = 12;
                  $user_update = $this->user_update($user,$answer,$update); 
                  $userMessage  = $this->user_data($user);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
            

            }elseif (is_numeric($userMessage) !== false && $sequentsteps->seqcode == '1003' ) {
                  $answer = $userMessage;
                  $case = 8;
                  $sequentsteps_update = $this->sequentsteps_update2($user,$answer);
                  $replymessage = $this->replymessage($replyToken,$userMessage,$case);   
            

            }elseif ($userMessage == 'น้ำหนักถูกต้อง' && $sequentsteps->seqcode == '1003' ) {
                  $case = 7;
                  $seqcode = '0000';
                  $nextseqcode = '0000';
                  $sequentsteps = $this->sequentsteps_seqcode($user);
                  $user_weight = $sequentsteps->answer;

                  $RecordOfPregnancy = $this->RecordOfPregnancy_select($user);
                  $updated_at = $RecordOfPregnancy->updated_at;
               
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  $RecordOfPregnancy = $this->RecordOfPregnancy_update($user_weight,$user,$updated_at);      
                  $userMessage = $user;
                  $replymessage = $this->replymessage($replyToken,$userMessage,$case);  
//*********************************************************************************************
            }elseif (is_string($userMessage) !== false && $sequentsteps->seqcode == '2001'  ) {
                  $answer = $userMessage;
                  $case = 11;
                  // $update = 8;
                  $seqcode = '2002';
                  $nextseqcode = '2003';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update); 
            
            }elseif ($userMessage == 'ทานแล้ว'  && $sequentsteps->seqcode == '2002'  ) {
                  $answer = $userMessage;
                  $case = 12;
                  // $update = 8;
                  $seqcode = '2003';
                  $nextseqcode = '2004';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update);  
            }elseif ($userMessage == 'ยังไม่ได้ทาน' && $sequentsteps->seqcode == '2002'  ) {
                  $answer = $userMessage;
                  $case = 12;
                  // $update = 8;
                  $seqcode = '2003';
                  $nextseqcode = '2004';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update);      
            }elseif ($userMessage == 'ออกแล้ว'  && $sequentsteps->seqcode == '2003'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  // $update = 8;
                  $seqcode = '2004';
                  $nextseqcode = '0000';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update);   
            }elseif ($userMessage == 'ยัง'  && $sequentsteps->seqcode == '2003'  ) {
                  $answer = $userMessage;
                  $case = 1;
                  // $update = 8;
                  $seqcode = '2004';
                  $nextseqcode = '0000';
                  $userMessage  = 'อย่าลืมออกกำลังกายนะคะ';
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                  // $user_update = $this->user_update($user,$answer,$update);   
            }elseif ($userMessage == 'เคยลงทะเบียน' && $sequentsteps->seqcode == '0029'  ) {
                  $case = 13;
                  $seqcode = '3002';
                  $nextseqcode = '0000';
                  $userMessage  = $this->sequents_question($seqcode);
                  $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);

            }elseif ($userMessage == 'ไม่เคยลงทะเบียน' && $sequentsteps->seqcode == '0029'  ) {
                  $case = 1;
                  // $seqcode = '3002';
                  // $nextseqcode = '0000';
                  $userMessage  ='คุณสามารถลงทะเบียนกับ ulife.info เพื่อเชื่อมโยงข้อมูลสุขภาพของคุณได้นะคะ';
                  // $sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);


            }else{

                  $userMessage = 'ฉันไม่เข้าใจค่ะ';
                  $case = 1;
            }
             return $this->replymessage($replyToken,$userMessage,$case);
    }
     public function replymessage($replyToken,$userMessage,$case)
    {
            $httpClient = new CurlHTTPClient('omL/jl2l8TFJaYFsOI2FaZipCYhBl6fnCf3da/PEvFG1e5ADvMJaILasgLY7jhcwrR2qOr2ClpTLmveDOrTBuHNPAIz2fzbNMGr7Wwrvkz08+ZQKyQ3lUfI5RK/NVozfMhLLAgcUPY7m4UtwVwqQKwdB04t89/1O/w1cDnyilFU=');
            $bot = new LINEBot($httpClient, array('channelSecret' => 'f571a88a60d19bb28d06383cdd7af631'));
            
            switch($case) {
     
                 case 1 : 
                        $textMessageBuilder = new TextMessageBuilder($userMessage);
                    break;
                 case 2 : 
                        $actionBuilder = array(
                                          new MessageTemplateActionBuilder(
                                          'ครั้งสุดท้ายที่มีประจำเดือน',
                                          'ครั้งสุดท้ายที่มีประจำเดือน' 
                                          ),
                                           new MessageTemplateActionBuilder(
                                          'กำหนดการคลอด',
                                          'กำหนดการคลอด' 
                                          ) 
                                         );

                        $imageUrl = NULL;
                        $textMessageBuilder = new TemplateMessageBuilder('Button Template',
                        new ButtonTemplateBuilder(
                              $userMessage, 
                              'กรุณาเลือกตอบข้อใดข้อหนึ่งเพื่อให้ทางเราคำนวณอายุครรภ์ค่ะ', 
                               $imageUrl, 
                               $actionBuilder  
                           )
                        );              
                    break;
                 case 3 : 
                         $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'ใช่',
                                        'อายุครรภ์ถูกต้อง'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่ใช่',
                                        'ไม่ถูกต้อง'
                                    )
                                )
                        )
                    ); 
                    break;

                 case 4 : 

                  $textReplyMessage = $userMessage;
                  $textMessage1 = new TextMessageBuilder($textReplyMessage);
                  $textReplyMessage =   "รายละเอียดของระดับ". "\n".
                                        "เบา -  วิถีชีวิตทั่วไป ไม่มีการออกกำลังกาย หรือมีการออกกำลังกายน้อย". "\n".
                                        "ปานกลาง - วิถีชีวิตกระฉับกระเฉง หรือ มีการออกกำลังกายสม่ำเสมอ". "\n".
                                        "หนัก - วิถีชีวิตมีการใช้แรงงานหนัก ออกกำลังกายหนักเป็นประจำ". "\n";
                  $textMessage2 = new TextMessageBuilder($textReplyMessage);
                  $actionBuilder = array(
                                          new MessageTemplateActionBuilder(
                                          'เบา',// ข้อความแสดงในปุ่ม
                                          'เบา' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                          ),
                                           new MessageTemplateActionBuilder(
                                          'ปานกลาง',// ข้อความแสดงในปุ่ม
                                          'ปานกลาง' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                          ),
                                           new MessageTemplateActionBuilder(
                                          'หนัก',// ข้อความแสดงในปุ่ม
                                          'หนัก' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                          ) 
                                         );

                     $imageUrl = NULL;
                    $textMessage3 = new TemplateMessageBuilder('Button Template',
                     new ButtonTemplateBuilder(
                              'ระดับของการออกกำลังกาย', // กำหนดหัวเรื่อง
                              'เลือกระดับด้านล่างได้เลยค่ะ', // กำหนดรายละเอียด
                               $imageUrl, // กำหนด url รุปภาพ
                               $actionBuilder  // กำหนด action object
                         )
                      );                            

                  $multiMessage = new MultiMessageBuilder;
                  $multiMessage->add($textMessage1);
                  $multiMessage->add($textMessage2);
                  $multiMessage->add($textMessage3);
                  $textMessageBuilder = $multiMessage; 

                    break;
                 case 5 : 
                  $text1 = 'คุณต้องการแก้ไขข้อมูลไหม?';
                  $textMessage1 = new TextMessageBuilder($userMessage);
                  $textMessage2 = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $text1 ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'แก้ไข',
                                        'แก้ไขข้อมูล'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ยืนยันข้อมูล',
                                        'ยืนยันข้อมูล'
                                    )
                                )
                        )
                    ); 
                  $multiMessage =     new MultiMessageBuilder;
                  $multiMessage->add($textMessage1);
                  $multiMessage->add($textMessage2);
                  // $multiMessage->add($textMessage3);
                  $textMessageBuilder = $multiMessage; 
                    break;
                  case 6 : 
                  $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'สนใจ',
                                        'สนใจ'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่สนใจ',
                                        'ไม่สนใจ'
                                    )
                                )
                        )
                    ); 

                    break;

                 case 7:
                    
                    $RecordOfPregnancy = $this->RecordOfPregnancy_select($userMessage);
                    $preg_week = $RecordOfPregnancy->preg_week;


                    $actionBuilder = array(new UriTemplateActionBuilder(
                                          'กราฟ', // ข้อความแสดงในปุ่ม
                                          'https://peat.none.codes/graph/'.$userMessage
                                          ),
                                           new MessageTemplateActionBuilder(
                                         'ทารกในครรภ์',// ข้อความแสดงในปุ่ม
                                         'ทารกในครรภ์' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                          )
                                         );

                    $imageUrl = 'https://peat.none.codes/week/'.$preg_week.'.jpg';
                   $textMessageBuilder = new TemplateMessageBuilder('Button Template',
                     new ButtonTemplateBuilder(
                               'ขณะนี้คุณมีอายุครรภ์'.$preg_week.'สัปดาห์', // กำหนดหัวเรื่อง
                               'กราฟน้ำหนักระหว่างการตั้งครรภ์', // กำหนดรายละเอียด
                               $imageUrl, // กำหนด url รุปภาพ
                               $actionBuilder  // กำหนด action object
                         )
                      );  

                    break;

                 case 8 : 
                         $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( 'สัปดาห์นี้คุณมีน้ำหนัก'.$userMessage.'กิโลกรัมใช่ไหมคะ?' ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'ถูกต้อง',
                                        'น้ำหนักถูกต้อง'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่ถูกต้อง',
                                        'ไม่ถูกต้อง'
                                    )
                                )
                        )
                    ); 
                    break;
                     case 9 : 
                  $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'แพ้',
                                        'แพ้ยา'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่แพ้',
                                        'ไม่แพ้ยา'
                                    )
                                )
                        )
                    ); 

                    break;
                     case 10 : 
                  $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'แพ้',
                                        'แพ้อาหาร'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่แพ้',
                                        'ไม่แพ้อาหาร'
                                    )
                                )
                        )
                    ); 

                    break;
                    case 11 : 
                         $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'ทานแล้ว',
                                        'ทานแล้ว'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ยังไม่ได้ทาน',
                                        'ยังไม่ได้ทาน'
                                    )
                                )
                        )
                    ); 
                    break;
                      case 12 : 
                         $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'ออกแล้ว',
                                        'ออกแล้ว'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ยังไม่ได้ออก',
                                        'ยัง'
                                    )
                                )
                        )
                    ); 
                    break;
         
                      case 13 : 
                         $textMessageBuilder = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder( $userMessage ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'ต้องการ',
                                        'ต้องการเชื่อมข้อมูล'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่ต้องการ',
                                        'ไม่ต้องการเชื่อมข้อมูล'
                                    )
                                )
                        )
                    ); 
                    break;
             }
                $response = $bot->replyMessage($replyToken,$textMessageBuilder); 
         
    }

    public function  pregnants($preg_week){
         $pregnants = pregnants::where('week', $preg_week)->first();
        return $pregnants;

    }
    public function sequents_question($seqcode)
    {          
                   $question = sequents::select('question')
                                ->where('seqcode',$seqcode)
                                ->first();
                   return $question->question;
    }
    public function sequentsteps_seqcode($user)
    {          
                   $sequentsteps = sequentsteps::select('seqcode','nextseqcode','answer')
                                ->where('sender_id',$user)
                                ->first();
                   return $sequentsteps;
    }
    public function sequentsteps_insert($user,$seqcode,$nextseqcode)
    {          
        $sequentsteps = sequentsteps::insert(['sender_id'=>$user,'seqcode' => $seqcode,'answer' => 'NULL','nextseqcode' =>$nextseqcode,'status'=>'0','created_at'=>NOW() , 'updated_at'=>NOW()]);
    }
    public function sequentsteps_update($user,$seqcode,$nextseqcode)
    {          
         $sequentsteps = sequentsteps::where('sender_id', $user)
                       ->update(['seqcode' =>$seqcode,'nextseqcode' => $nextseqcode]);
    }
    public function sequentsteps_update2($user,$answer)
    {          
         $sequentsteps = sequentsteps::where('sender_id', $user)
                       ->update(['answer'=>$answer]);
    }
    public function user_insert($user,$user_name)
    {          
         $users_register = users_register::insert(['user_id'=>$user,'user_name' => $user_name ,'status' => '1','user_age'=>'0','user_height'=>'0','user_Pre_weight'=>'0','user_weight'=>'0','preg_week'=>'0', 'phone_number'=>'NULL','email' =>'NULL','hospital_name'=>'NULL','hospital_number'=>'NULL','history_medicine'=>'NULL', 'history_food'=>'NULL','active_lifestyle'=>'0','updated_at' =>NOW()]);
    }
    public function delete_data_all($user)
    {          
         $sequentsteps = sequentsteps::where('sender_id', $user)->delete();
         $users_register = users_register::where('user_id', $user)->delete();
         $RecordOfPregnancy = RecordOfPregnancy::where('user_id', $user)->delete();
    }
    public function users_register_select($user){
        $users_register = users_register::where('user_id',$user)
                                        ->first();
        return $users_register;

    }
    public function bmi_calculator($user_weight,$user_height){

                $height = $user_height*0.01;
                $bmi = $user_weight/($height*$height);
                $bmi = number_format($bmi, 2, '.', '');
            return $bmi;
    }
    public function RecordOfPregnancy_insert($preg_week, $user_weight,$user){
     $RecordOfPregnancy = RecordOfPregnancy::insert(['user_id'=>$user,'preg_week' => $preg_week,'preg_weight' => $user_weight, 'updated_at'=>NOW()]);
    }
    public function RecordOfPregnancy_update($user_weight,$user,$updated_at){

     $RecordOfPregnancy = RecordOfPregnancy::where('user_id', $user)
                       ->where('updated_at', $updated_at)
                       ->update(['preg_weight' =>$user_weight]);
    }
    public function RecordOfPregnancy_select($user){

     $RecordOfPregnancy = RecordOfPregnancy::where('user_id', $user)
                       ->orderBy('updated_at', 'desc')
                       ->first();
     return $RecordOfPregnancy;
    }
    public function weight_criteria($bmi){

               if ($bmi<18.5) {
                      $result="Underweight";
                    } elseif ($bmi>=18.5 && $bmi<24.9) {
                      $result="Nomal weight";
                    } elseif ($bmi>=24.9 && $bmi<=29.9) {
                      $result="Overweight";
                    }else{
                      $result="Obese";
                    }

            return $result;
    }
    public function cal_calculator($user_age,$active_lifestyle,$user_weight,$preg_week){

        if ( $user_age>=10 && $user_age<18) {
          $cal=(13.384*$user_weight )+692.6;
        }elseif ($user_age>18 && $user_age<31) {
          $cal=(14.818*$user_weight )+486.6;
        }else{
          $cal=(8.126*$user_weight )+845.6;
        }

        if ($active_lifestyle=='3'  ) {
          $total = $cal*2.0;
        }elseif($active_lifestyle =='2') {
          $total = $cal*1.7;
        }else{
          $total = $cal*1.4;

        } 

        if ($preg_week >=13 && $preg_week<=40) {
                  $cal1 = $total+300;
                  $cal = number_format($cal1);  
        }else{
                 $cal = number_format($total);
        }
            return  $cal;
    }
    public function user_data($user)
    {          
                   $users_register = users_register::where('user_id',$user)
                                                   ->first();

                   $user_name = $users_register->user_name;
                   $user_age = $users_register->user_age;
                   $user_height = $users_register->user_height;
                   $user_Pre_weight = $users_register->user_Pre_weight;
                   $user_weight = $users_register->user_weight;
                   $preg_week = $users_register->preg_week;
                   $phone_number = $users_register->phone_number;
                   $email = $users_register->email;
                   $hospital_name = $users_register->hospital_name;

                   $hospital_number = $users_register->hospital_number;
                   $history_medicine = $users_register->history_medicine;
                   $history_food = $users_register->history_food;
                    
                   $userMessage = '------สรุปข้อมูล------'. "\n".
                                  '1. ชื่อ: '.$user_name. "\n".
                                  '2. อายุ: '.$user_age.'ปี'. "\n".
                                  '3. ส่วนสูง: '.$user_height."\n".
                                  '4. น้ำหนักก่อนตั้งครรภ์: '.$user_Pre_weight."\n".
                                  '5. น้ำหนักปัจจุบัน: '.$user_weight."\n".
                                  '6. อายุครรภ์: '.$preg_week.'สัปดาห์'."\n".
                                  '7. เบอร์โทรศัพท์: '.$phone_number."\n".
                                  '8. อีเมล: '.$email."\n".
                                  '9. โรงพยาบาลที่ฝากครรภ์: '.$hospital_name."\n".
                                  '10. เลขประจำตัวผู้ป่วย: '.$hospital_number."\n".
                                  '11. แพ้ยา: '.$history_medicine."\n".
                                  '12. แพ้อาหาร: '.$history_food;
                   return $userMessage;
    }
    public function replymessage_result($replyToken,$preg_week,$bmi,$cal,$weight_criteria,$text,$user){

            $httpClient = new CurlHTTPClient('omL/jl2l8TFJaYFsOI2FaZipCYhBl6fnCf3da/PEvFG1e5ADvMJaILasgLY7jhcwrR2qOr2ClpTLmveDOrTBuHNPAIz2fzbNMGr7Wwrvkz08+ZQKyQ3lUfI5RK/NVozfMhLLAgcUPY7m4UtwVwqQKwdB04t89/1O/w1cDnyilFU=');
            $bot = new LINEBot($httpClient, array('channelSecret' => 'f571a88a60d19bb28d06383cdd7af631'));

                  
                  
                  $actionBuilder = array(new UriTemplateActionBuilder(
                                          'กราฟ', // ข้อความแสดงในปุ่ม
                                          'https://peat.none.codes/graph/'.$user
                                          ),
                                           new MessageTemplateActionBuilder(
                                         'ทารกในครรภ์',// ข้อความแสดงในปุ่ม
                                         'ทารกในครรภ์' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                          )
                                         );

                    $imageUrl = 'https://peat.none.codes/week/'.$preg_week.'.jpg';
                    $textMessage1 = new TemplateMessageBuilder('Button Template',
                     new ButtonTemplateBuilder(
                               'ขณะนี้คุณมีอายุครรภ์'.$preg_week.'สัปดาห์', // กำหนดหัวเรื่อง
                               'ค่าดัชนีมวลกายของคุณคือ'.$bmi.'อยู่ในเกณฑ์'. $weight_criteria, // กำหนดรายละเอียด
                               $imageUrl, // กำหนด url รุปภาพ
                               $actionBuilder  // กำหนด action object
                         )
                      );  

                    $actionBuilder2 = array( new UriTemplateActionBuilder(
                                          'ไปยังลิงค์', // ข้อความแสดงในปุ่ม
                                          'http://www.raipoong.com/content/detail.php?section=12&category=26&id=467'
                                          ),
                                           new MessageTemplateActionBuilder(
                                          'ข้อมูลโภชนาการ',// ข้อความแสดงในปุ่ม
                                          'ข้อมูลโภชนาการ' // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                          )
                                         );

                    $imageUrl2 = NULL;
                    $textMessage2 = new TemplateMessageBuilder('Button Template',
                     new ButtonTemplateBuilder(
                               'จำนวนแคลอรี่ที่คุณต้องการต่อวันคือ '.$cal, // กำหนดหัวเรื่อง
                               'รายละเอียดการรับประทานอาหารสามารถกดปุ่มด้านล่างได้เลยค่ะ', // กำหนดรายละเอียด
                               $imageUrl2, // กำหนด url รุปภาพ
                               $actionBuilder2  // กำหนด action object
                         )
                      );                            



                  $textReplyMessage =   $text ;
                  $textMessage3 = new TextMessageBuilder($textReplyMessage);

                  $textMessage4 = new TemplateMessageBuilder('Confirm Template', new ConfirmTemplateBuilder('คุณเคยลงทะเบียนกับ ulife.info ไหม?' ,
                                array(
                                    new MessageTemplateActionBuilder(
                                        'เคย',
                                        'เคยลงทะเบียน'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'ไม่เคย',
                                        'ไม่เคยลงทะเบียน'
                                    )
                                )
                        )
                    ); 


                  $multiMessage = new MultiMessageBuilder;
                  $multiMessage->add($textMessage1);
                  $multiMessage->add($textMessage2);
                  $multiMessage->add($textMessage3);
                  $multiMessage->add($textMessage4);
                  $textMessageBuilder = $multiMessage; 
                  $response = $bot->replyMessage($replyToken,$textMessageBuilder); 

    }
    public function user_update($user,$answer,$update)
    {          

         switch($update) {
                 case 1 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['user_name' => $answer ]);
                    break;
                 case 2 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['user_age' => $answer ]);
                    break;
                 case 3 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['user_height' => $answer ]);
                    break;
                 case 4 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['user_Pre_weight' => $answer ]);
                    break;
                  case 5 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['user_weight' => $answer ]);
                    break;
                 case 6 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['preg_week' => $answer ]);
                    break;
                 case 7 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['phone_number' => $answer ]);
                    break;
                 case 8 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['email' => $answer ]);
                    break;
                 case 9 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['hospital_name' => $answer ]);
                    break;
                 case 10 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['hospital_number' => $answer ]);
                    break;
                 case 11 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['history_medicine' => $answer ]);
                    break;
                 case 12 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['history_food' => $answer ]);
                    break;
                 case 13 : 
                        $users_register = users_register::where('user_id', $user)
                                                          ->update(['active_lifestyle' => $answer ]);
                    break;
         
             }
        
    }
    public function pregnancy_calculator($user,$userMessage,$seqcode)
    {          

                if(strpos($userMessage, '/') !== false ){
                  $pieces = explode("/", $userMessage);
                  $date   = str_replace("","",$pieces[0]);
                  $month  = str_replace("","",$pieces[1]);
                  $today_years= date("Y") ;
                  $today_month= date("m") ;
                  $today_day  = date("d") ;

                if(is_numeric($month) == false){
                    $month = $this->check_month($month);
                  }
                }elseif(strpos($userMessage, ':') !== false ){
                  $pieces = explode(":", $userMessage);
                  $date   = str_replace("","",$pieces[0]);
                  $month  = str_replace("","",$pieces[1]);
                  $today_years= date("Y") ;
                  $today_month= date("m") ;
                  $today_day  = date("d") ;
                if(is_numeric($month) == false){
                    $month = $this->check_month($month);
                  }
                }elseif(strpos($userMessage, '-') !== false ){
                  $pieces = explode("-", $userMessage);
                  $date   = str_replace("","",$pieces[0]);
                  $month  = str_replace("","",$pieces[1]);
                  $today_years= date("Y") ;
                  $today_month= date("m") ;
                  $today_day  = date("d") ;
         
                  if(is_numeric($month) == false){
                    $month = $this->check_month($month);
                  }
                }elseif(strpos($userMessage, ' ') !== false ){
                  $pieces = explode(" ", $userMessage);
                  $date   = str_replace("","",$pieces[0]);
                  $month  = str_replace("","",$pieces[1]);
                  $today_years= date("Y") ;
                  $today_month= date("m") ;
                  $today_day  = date("d") ;

                  if(is_numeric($month) == false){
                    $month = $this->check_month($month);
                     if($month=='00'){
                      $textReplyMessage = 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง';
                      return   $textReplyMessage;
                     }
                  }
                  
                }

        switch($seqcode) {
     
                 case ($seqcode=='1015' || $seqcode =='10640'): 
                                    if($month>$today_month&& $month<=12 && $date<=31  ){
                                        $years = $today_years-1;
                                        $strDate1 = $years."-".$month."-".$date;
                                        $strDate2=date("Y-m-d");
                                        
                                        $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );
                                        $week = $date_pre/7;
                                        $w_preg = number_format($week);
                                        $day = $date_pre%7;
                                        $day_preg = number_format($day);
                                        $age_pre = 'คุณมีอายุครรภ์'.$w_preg .'สัปดาห์'.  $day_preg .'วัน' ;
                                        $this->sequentsteps_update2($user,$w_preg);
                                        return  $age_pre;  
                                           

                                    }elseif($month<=$today_month && $month<=12 && $date<=31){
                                        $strDate1 = $today_years."-".$month."-".$date;
                                        $strDate2=date("Y-m-d");
                                        $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );;
                                        $week = $date_pre/7;
                                        $w_preg = number_format($week);
                                        $day = $date_pre%7;
                                        $day_preg = number_format($day);
                                        $age_pre = 'คุณมีอายุครรภ์'. $w_preg .'สัปดาห์'.  $day_preg .'วัน' ;
                                        $this->sequentsteps_update2($user,$w_preg);
                                        return  $age_pre;    
                                    }else{

                                        $textReplyMessage = 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง';
                                        return   $textReplyMessage;

                                    }
                    break;
                 case ($seqcode=='2015'|| $seqcode=='20640')  : 
                        
                         if( $month < $today_month && $month<=12 && $date<=31){
                                 $years = $today_years+1;
                                 $strDate1 = $years."-".$month."-".$date;
                                 $strDate2=date("Y-m-d");
                                
                                 $date_pre =  (strtotime($strDate1) - strtotime($strDate2))/( 60 * 60 * 24 );
                                 $week = $date_pre/7;
                                 $week_preg =floor($week);
                                 $day = $date_pre%7;
                                 $day_preg = number_format($day);
                                 $w_preg = 39-$week_preg  ;
                                 $d = 7-$day_preg;


                         switch ($d){
                         case '7':
                              $w_preg = $w_preg + 1;
                              $replyData = 'คุณมีอายุครรภ์'.  $w_preg  .'สัปดาห์';
                              $this->sequentsteps_update2($user,$w_preg);
                              return  $replyData;
                          break;
                         default:
                              $replyData = 'คุณมีอายุครรภ์'. $w_preg .'สัปดาห์'.  $d .'วัน' ;
                              $this->sequentsteps_update2($user,$w_preg);
                              return  $replyData;
                          break;
                          }

                         }elseif($month >= $today_month && $month<=12 && $date<=31){
                                 $years = $today_years;
                                 $strDate1 = $years."-".$month."-".$date;
                                 $strDate2=date("Y-m-d");
                                
                                 $date_pre =  (strtotime($strDate1) - strtotime($strDate2))/( 60 * 60 * 24 );
                                 $week = $date_pre/7;
                                 $week_preg =floor($week);
                                 $day = $date_pre%7;
                                 $day_preg = number_format($day);
                                 $w_preg = 39-$week_preg  ;
                                 $d = 7-$day_preg;
                      
                          switch ($d){
                               case '7':
                                  $w_preg = $w_preg + 1;
                                  $replyData ='คุณมีอายุครรภ์'.  $w_preg  .'สัปดาห์';
                                  $this->sequentsteps_update2($user,$w_preg);
                                  return  $replyData;
                                 break;
                               default:
                                  $replyData = 'คุณมีอายุครรภ์'. $w_preg .'สัปดาห์'.  $d .'วัน' ;
                                  $this->sequentsteps_update2($user,$w_preg);
                                 return  $replyData;
                                 break;
                           }
                         }else{

                                        $textReplyMessage = 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง';
                                        return   $textReplyMessage;

                                    }
               
                     break;

                   
      }
    }
    public function meal_planing($format){
         if ($format < 1601) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 8 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 2 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 5 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 6 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                } elseif ($format > 1600 && $format <1701) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 9 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 2 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 5 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 6 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >1700 && $format <1801) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 9 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 6 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 6 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >1800 && $format<1901) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 9 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 6 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >1900 && $format<2001) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 10 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >2000 && $format<2101 ) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 11 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format > 2100 && $format<2201) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 11 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }elseif ($format > 2200 && $format < 2301) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 11 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 9 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }elseif ($format > 2300 && $format <2401) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 12 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 10 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }elseif ($format > 2400 && $format <2501) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 12 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 4 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 8 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 10 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }else {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 12 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 4 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 9 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 11 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }
                return $Nutrition;
    }
    public function check_month($month){

              switch ($month) {
              case ($month == 'มกราคม' || $month == 'ม.ค.' || $month == 'มค' || $month == 'มกรา'):
                  $month = '01';
                  break;
              case ($month == 'กุมภาพันธ์' || $month == 'ก.พ.' || $month == 'กพ'|| $month == 'กุมภา'):
                  $month = '02';
                  break;
              case ($month == 'มีนาคม' || $month == 'มี.ค.'|| $month == 'มีค.'|| $month == 'มีนา'):
                  $month = '03';
                  break;
              case ($month == 'เมษายน' || $month == ' เม.ย.'|| $month == 'เมย'|| $month == 'เมษา'):
                  $month = '04';
                  break;
              case ($month == 'พฤษภาคม' || $month == ' พ.ค.'|| $month == 'พค'|| $month == 'พฤษภา'):
                  $month = '05';
                  break;
              case ($month == 'มิถุนายน' || $month == 'มิ.ย.'|| $month == 'มิย'|| $month == 'มิถุนา'):
                  $month = '06';
                  break;
              case ($month == 'กรกฎาคม' || $month == 'ก.ค.'|| $month == 'กค'|| $month == 'กรกฎา'):
                  $month = '07';
                  break;
              case ($month == 'สิงหาคม' || $month == 'ส.ค.'|| $month == 'สค'|| $month == 'สิงหา'):
                  $month = '08';
                  break;
              case ($month == 'กันยายน' || $month == 'ก.ย.'|| $month == 'กย'|| $month == 'กันยา'):
                  $month = '09';
                  break;
              case ($month == 'ตุลาคม' || $month == 'ต.ค.'|| $month == 'ตค'|| $month == 'ตุลา'):
                  $month = '10';
                  break;
              case ($month == 'พฤศจิกายน' || $month == 'พ.ย.'|| $month == 'พย'|| $month == 'พฤศจิกา'):
                  $month = '11';
                  break;
              case ($month == 'ธันวาคม' || $month == 'ธ.ค.'|| $month == 'ธค'|| $month == 'ธันวา'):
                  $month = '12';
                  break;   
               default:
                   $month = '00';
                   break;   


   }
    return  $month;
 }
}



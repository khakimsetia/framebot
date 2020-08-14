<?php
/*
Coder : @Khakimsetia
Pekalongan, 14 Agustus 2020
==>Penambahan beberapa sample command.

*/
require_once __DIR__.'/src/FrameBot.php';

$bot = new FrameBot('TOKEN', 'BOT_USERNAME');

$id = 196397916; //ID User Admin (you)
$all = '0'; //Set 0 Bot just reply Admin, Set 1 Bot reply All;

// Simple answer
$bot->cmd('*', 'Hi, human! I am a bot.');

// Test ping
$bot->cmd('/ping|!ping|/p|!p|ping|PING|Ping', function () {
    $message = Bot::message();
    $fromid = $message['from']['id'];
    $dt = $message['date'];
    date_default_timezone_set('Asia/Jakarta');
    global $id;
    $time_now = microtime(true);
    $dif = $time_now-$dt;
    $sub = substr($dif, 0, 6);
    $abs = abs($sub);
    $text = "🏓 pong!\n";
    $text .= "proccess time : <pre>$abs</pre> s\n";
    $options = [
        'parse_mode' => 'html',
        'reply' => true
    ];
    if($fromid==$id){
        Bot::sendMessage($text, $options);
    }
    else{
        global $all;
        if($all=='1'){
            Bot::sendMessage($text, $options);
        }
    }
});

// Debug chat (Must reply some message!)
$bot->cmd('/debug|!debug|/d|!d', function () {
    $message = Bot::message();
    $fromid = $message['from']['id'];
    global $id;
    $text = print_r($message, true);
    $options = [
        'parse_mode' => 'html',
        'reply' => true
    ];
    if($fromid==$id){
        if(isset($message['reply_to_message']['from']['id'])){
            Bot::sendMessage($text, $options);
        }
    }
    else{
        global $all;
        if($all=='1'){
            if(isset($message['reply_to_message']['from']['id'])){
                Bot::sendMessage($text, $options);
            }
        }
    }
});

// Get ID n Creator Group (can be used in group)
$bot->cmd('/id|/getid|!getid|!id', function () {
    $message = Bot::message();
    $fromid = $message['from']['id'];
    $type = $message['chat']['type'];
    
    global $id;
    $options = [
        'parse_mode' => 'html',
        'reply' => true
    ];
    
    if($fromid==$id){
        if($type!='private'){
            if(isset($message['reply_to_message']['from']['id'])){
                $user = $message['reply_to_message']['from']['id'];
                
                $text = "🆔 User: <pre>$user</pre>\n";
                if(isset($message['reply_to_message']['from']['username'])){
                    $rplusername = $message['reply_to_message']['from']['username'];
                    $text .= " ├ @$rplusername\n";
                }
                $is_bot = $message['reply_to_message']['from']['is_bot'];
                if($is_bot==1){
                    $text .= " ├ Tipe: 🤖 <i>BOT</i>\n";
                    $fname = $message['reply_to_message']['from']['first_name'];
                    $text .= " └ <b>$fname</b>\n";
                }
                else{
                    $text .= " ├ Tipe: 😆 <s>HUMAN</s>\n";
                    $bahasa = $fname = $message['reply_to_message']['from']['language_code'];
                    $fname = $message['reply_to_message']['from']['first_name'];
                    $lname = "";
                    if(isset($message['reply_to_message']['from']['last_name'])){
                        $lname = $message['reply_to_message']['from']['last_name'];
                    }
                    $text .= " ├ Bahasa $bahasa\n";
                    $text .= " └ <b>$fname $lname</b>\n";
                }
                $text .= "\n";
                $groupid = $message['chat']['id'];
                $grouptitle = $message['chat']['title'];
                $grouptype = $message['chat']['type'];
                $text .= "🆔 Group: <pre>$groupid</pre>\n";
                if(isset($message['chat']['username'])){
                    
                    $usergroup = $message['chat']['username'];
                    $text .= " ├ $grouptype <b>public</b>\n";
                    $text .= " ├ @$usergroup\n";
                }
                else{
                    $text .= " ├ $grouptype <b>private</b>\n";
                }
                $count = Bot::getChatMembersCount($groupid);
                $jsoncount = json_decode($count);
                $jumlah = $jsoncount->result; 
                $text .= " ├ member $jumlah\n";
                $text .= " └ <b>$grouptitle</b>\n\n";
                $text .= "👤 Creator\n";
                
                $idcreator;
                $namacreator;
                $adm = Bot::getChatAdministrators($groupid);
                $jsonadm = json_decode($adm);
                $forjson = $jsonadm->result;
                
                $options = [
                    'parse_mode' => 'html',
                    'reply' => true,
                ];
                
                foreach ($forjson as $key => $d) {
                    if($d->status == 'creator'){
                        $idcreator = $d->user->id;
                        $fname = $d->user->first_name;
                        if(isset($d->user->last_name)){
                            $namacreator = $fname." ".$d->user->last_name;
                        }
                        else{
                            $namacreator = $fname;
                        }
                        if(isset($d->user->username)!=""){
                            $un = $d->user->username;
                            $keyboard = [
                                [
                                    ['text' => "👤 $namacreator", 'url' => "t.me/$un"],
                                ],
                            ];
                            $options = [
                                'parse_mode' => 'html',
                                'reply' => true,
                                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
                            ];
                        }
                        else{
                            $text .= "<a href='tg://user?id=$idcreator'>👤 $namacreator</a>";
                        }
                    }
                }
                Bot::sendMessage($text, $options);
            }
        }
    }
    else{
        global $all;
        if($all=='1'){
            if(isset($message['reply_to_message']['from']['id'])){
                Bot::sendMessage($text, $options);
            }
        }
    }
});

// Link message (Must reply some message!)
$bot->cmd('/link|!link', function () {
    $message = Bot::message();
    $fromid = $message['from']['id'];
    $type = $message['chat']['type'];
    global $id;
    $linknya;
    $link;
    if($fromid==$id){
        if($type=='supergroup' && isset($message['chat']['username'])){
            if(isset($message['reply_to_message']['message_id'])){
                $groupusername = $message['chat']['username'];
                $rplmsgid = $message['reply_to_message']['message_id'];
                $link = "<a href='https://t.me/$groupusername/$rplmsgid'>https://t.me/$groupusername/$rplmsgid</a>";
                $linknya = "https://t.me/$groupusername/$rplmsgid";
            }
        }
    }
    $keyboard = [
        [
            ['text' => '🔗 Link Pesan', 'url' => $linknya],
        ],
    ];
    $options = [
        'parse_mode' => 'html',
        'reply' => true,
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ];
    $text = $link;
    Bot::sendMessage($text, $options);
});

// Out Group
$bot->cmd('/leave|!leave|!out|!kabur|!tinggalkan', function () {
    $message = Bot::message();
    $fromid = $message['from']['id'];
    $type = $message['chat']['type'];
    global $id;
    if($fromid==$id){
        if($type!='private'){
            $cid = $message['chat']['id'];
            $text = "";
            $options = [
                'parse_mode' => 'html',
                'reply' => true,
            ];
            $text = "💨🏂 Bersiap meninggalkan group!";
            Bot::sendMessage($text, $options);
            $out = Bot::leaveChat($cid);
        }
    }
});


// Simple echo command
$bot->cmd('/echo|/say', function ($text) {
    if ($text == '') {
        $text = 'Command usage: /echo [text] or /say [text]';
    }

    return Bot::sendMessage($text);
});

// Simple whoami command
$bot->cmd('/whoami', function () {
    // Get message properties
    $message = Bot::message();
    $name = $message['from']['first_name'];
    $userId = $message['from']['id'];
    $text = 'You are <b>'.$name.'</b> and your ID is <code>'.$userId.'</code>';
    $options = [
        'parse_mode' => 'html',
        'reply'      => true,
    ];

    return Bot::sendMessage($text, $options);
});

// Simple whoareyou (BOT description)
$bot->cmd('/whoareyou|!whoareyou', function () {
    $options = [
        'parse_mode' => 'html',
        'reply' => true
    ];
    $message = Bot::message();
    $fromid = $message['from']['id'];
    global $id;
    $sme = Bot::getMe();
    $me = json_decode($sme);
    $botid = $me->result->id;
    $nama = $me->result->first_name;
    $username = $me->result->username;
    $joingroup = $me->result->can_join_groups;
    $readgroup = $me->result->can_read_all_group_messages;
    $inlinequeries = $me->result->supports_inline_queries;
    
    $text = "🎓 I'm <b>$nama</b> \n";
    $text .= "├ username : @$username\n";
    $text .= "├ user ID      : <pre>$botid</pre>\n";
    $text .= "🔰 Akses\n";
    if($joingroup){
        $text .= "├ join group  : ☑️\n";
    }
    else{
        $text .= "├ join group  : ❌\n";
    }
    if($readgroup){
        $text .= "├ read group : ☑️\n";
    }
    else{
        $text .= "├ read group : ❌\n";
    }
    if($inlinequeries){
        $text .= "└ inline queries : ☑️\n";
    }
    else{
        $text .= "└ inline queries : ❌\n";
    }
    
    if($fromid==$id){
        Bot::sendMessage($text, $options);
    }
    else{
        global $all;
        if($all=='1'){
            Bot::sendMessage($text, $options);
        }
    }
});

// slice text by space
$bot->cmd('/split', function ($one, $two, $three) {
    $text = "First word: $one\n";
    $text .= "Second word: $two\n";
    $text .= "Third word: $three";

    return Bot::sendMessage($text);
});

// simple file upload
$bot->cmd('/upload', function () {
    $file = './composer.json';

    return Bot::sendDocument($file);
});

// Inline keyboard with Callback_Data
$bot->cmd('/keyboard', function () {
	$message = Bot::message();
    $fname = $message['from']['first_name'];
    $keyboard = [
			[
				['text' => 'Ya', 'callback_data' => '/ya'],
				['text' => 'Tidak, Terima kasih', 'callback_data' => '/tidak'],
			],
			[
				['text' => 'PHPTelebot', 'url' => 'https://blog.banghasan.com'],
				['text' => 'Gedebug', 'url' => 'https://telegram.me/gedebugbot'],
			]
		];
	$text = "🙏 Welcome <b>$fname</b>";	
	$options = [
		'parse_mode' => 'html',
		'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
	];
	return Bot::sendMessage($text, $options);	
});

// Handle callback_data inline keyboard
$bot->on('callback', function () {
    $message = Bot::message();
    $message_id = $message['message']['message_id'];
    $chatid = $message['message']['chat']['id'];
    $data = $message['data'];
	$text = "pesan sedang aku proses.. 👀 \n";
	$text .= " ├isi : $data \n";
    $options = [
        'text'  => $text,
        'chat_id' => $chatid,
        'message_id' => $message_id,
        'parse_mode' => 'html'
    ];
	//Edit message
    Bot::editMessageText($options);
	
	/*
	Handle message /ya from callback
	if($data=="/ya"){
		//statement
	}
	*/
	
	//Delete message
    $options = [
        'chat_id' => $chatid,
        'message_id' => $message_id,
    ];
    Bot::deleteMessage($options);    
});

// custom regex
$bot->regex('/\/number ([0-9]+)/i', function ($matches) {
    return Bot::sendMessage($matches[1]);
});

// Inline
$bot->on('inline', function ($text) {
    $results[] = [
        'type'         => 'article',
        'id'           => 'unique_id1',
        'title'        => $text,
        'message_text' => 'Lorem ipsum dolor sit amet',
    ];
    $options = [
        'cache_time' => 3600,
    ];

    return Bot::answerInlineQuery($results, $options);
});

$bot->run();

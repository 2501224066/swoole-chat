<?php

//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 1000);
$ws->set([
    'daemonize' => true
]);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $ws->push($request->fd, json_encode([
        "name" => "Tourist[" . ($request->fd + 10000) . "]",
        "content" => "Successful connection...",
        "time" => date('Y-m-d H:i:s')
    ]));
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $pushId = $frame->fd;
    foreach ($ws->connections as $fd) {
        $ws->push($fd, json_encode([
            "name" => "Tourist[" . ($pushId + 10000) . ']',
            "content" => $frame->data,
            "time" => date('Y-m-d H:i:s')
        ]));
    }
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();
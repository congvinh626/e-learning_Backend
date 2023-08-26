<?php

function statusResponse($statusCode, $message) {
    return response()->json([
        'statusCode' => $statusCode,
        'message' => $message
    ], $statusCode);
}

function statusResponse2($statusCode, $status , $message, $data) {
    return response()->json([
        'statusCode' => $statusCode,
        'message' => $message,
        'data' => $data
    ], $status);
}
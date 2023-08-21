<?php

function statusResponse($statusCode, $message) {
    return response()->json([
        'statusCode' => $statusCode,
        'message' => $message
    ], $statusCode);
}
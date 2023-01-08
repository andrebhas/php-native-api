<?php

namespace Src\System\Response;

class Response
{
    private $headers = [];

    public function __construct()
    {
        array_push($this->headers, "Access-Control-Allow-Origin: *");
        array_push($this->headers, "Content-Type: application/json; charset=UTF-8");
        array_push($this->headers, "Cache-Control: no-cache");
        array_push($this->headers, "Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
        array_push($this->headers, "Access-Control-Max-Age: 3600");
        array_push($this->headers, "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        array_push($this->headers, 'X-Powered-By: -');
    }

    public function sendResponse($data)
    {
        ob_clean();

        header_remove('Set-Cookie');
        if (is_array($this->headers) && count($this->headers)) {
            foreach ($this->headers as $httpHeader) {
                header($httpHeader);
            }
        }
        
        echo json_encode($data);

        exit;
    }

    public function success($data)
    {
        http_response_code(200);
        $data = [
            'success' => true,
            'status' => 200,
            'message' => 'Success',
            'data' => $data
        ];
        $this->sendResponse($data);
    }

    public function badRequest($data)
    {
        http_response_code(400);
        $data = [
            'success' => false,
            'status' => 400,
            'message' => 'Bad Request',
            'error' => $data
        ];
        $this->sendResponse($data);
    }

    public function notFound($message = 'Not Found')
    {
        http_response_code(404);
        $data = [
            'success' => false,
            'status' => 404,
            'message' => $message
        ];

        $this->sendResponse($data);
    }

    public function methodNotAllowed()
    {
        http_response_code(405);
        $data = [
            'success' => false,
            'status' => 405,
            'message' => 'Method Not Allowed',
        ];
        $this->sendResponse($data);
    }

    public function serverError()
    {
        http_response_code(500);
        $data = [
            'success' => false,
            'status' => 500,
            'message' => 'Server Error',
        ];
        $this->sendResponse($data);
    }
}

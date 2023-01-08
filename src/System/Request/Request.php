<?php

namespace Src\System\Request;

class Request {
    
    public $request;

    public function __construct()
    {
        $this->request = (array) json_decode(file_get_contents('php://input'), TRUE);
    }

    public function input()
    {
        return $this->request;
    }
}
<?php namespace App\Libraries;
	
use Response;

class Result {
    protected $error;
    protected $data;
    protected $code;

    public function __construct()
    {
        $this->error = false;
        $this->data = new \stdClass;
        $this->code = 200;
    }

    public static function build()
    {
        $instance = new self();
        return $instance;
    }
    
    public function setError($error)
    {
        $this->error = $error ? true : false;
        return $this;
    }
    
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function asJson(){
	    return 	Response::json([
	    			'error'   => $this->error,
					'data'     => $this->data,
				], $this->code);
    }
}?>
<?php

/*
    Copyright 2013 Jose Rojas
    All rights reserved.
*/

register_shutdown_function( "fatal_handler" );

function fatal_handler() {
    $error = error_get_last();
    if ($error['type'] == 1) {
        $result = array("status" => "error", "error" => "Invalid request");                

        echo json_encode($result);
    }
}

class Str 
{
    public static function replace($toMatch, $toReplace, $replace, $bAll)
    {
        if (Str::isEmpty($toMatch) || Str::isEmpty($toReplace))
            return $toMatch;

        $count = 0;
        do {
            $toMatch = str_replace($toReplace, $replace, $toMatch, $count);
            if ($bAll && $count == 0)
                $bAll = false;
        } while ($bAll);

        return $toMatch;
    }    

    public static function replaceRegex($toMatch, $toReplace, $replace, $bAll)
    {          
        $len = strlen($toReplace);        
        $toReplace = strpos($toReplace,"/") === 0 && strpos($toReplace,"/", $len - 1) === $len -1 ? $toReplace : '/' . str_replace("/", "\/", $toReplace) . '/' ;
        return preg_replace($toReplace, $replace, $toMatch, $bAll ? -1 : 1);        
    }
    
    public static function containsRegex($toMatch, $regex)
    {           
        $regex = strpos($regex,"/") === 0 && strpos($regex,"/", strlen($regex) - 1) === strlen($regex) -1 ? $regex : '/' . str_replace("/", "\/", $regex) . '/' ;                
        return preg_match($regex, $toMatch) > 0;
    }

    public static function isEmpty($s) { 
        return $s == null || strcmp($s,"") == 0; 
    }
}

class ProcessorException extends Exception {
    public function __construct ($message = "", $code = 0, $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}

class BaseProcessor {

    var $args;

    public function __construct()
    {
        $this->args = $_REQUEST;
    }

    function findModelItem($id, $key, $model)
    {
        foreach ($model as $item)
        {
            if ($item[$key] == $id)
                return $item;
        }
        return null;
    }

    function findModelItems($id, $key, $model)
    {
        $retval = array();
        foreach ($model as $item)
        {
            if ($item[$key] == $id)
                $retval[] = $item;
        }
        return $retval;
    }

    function findModelItemsAssoc($id, $key, $model, $assocs)
    {
        $retval = array();
        foreach ($model as $item)
        {            
            //pull in associations
            foreach ($assocs as $k => $v)
            {
                $item[$v['field']] = $this->findModelItem($item[$k], $v['key'], $v['model']);
            }

            $details = $item[$v['field']];
            if ($details[$key] == $id)
                $retval[] = $item;
        }
        return $retval;
    }

    public function getd($key, $default)
    {
        $retval = $this->args[$key];
        if ($retval == NULL)
            $retval = $default;
        return $retval;
    }

    public function process($action, $defaultParam = NULL)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $method = strtolower($method);
        
        $methodAction = $method . "_" . $action;

        $args = $_REQUEST;

        return $this->$methodAction($defaultParam);
    }

    public function checkFound($arr, $msg)
    {
        if ($arr == null || !(count($arr) > 0))
            throw new ProcessorException($msg);
    }
}

class API {

    static $SECTIONS = array(
      "/api/bookings/(.+)" => "BookingsProcessor",
    	"/api/users/(.+)" => "UsersProcessor",
    	"/api/hotels/(.+)" => "HotelsProcessor"
    );

    static public function process()
    {
        $uri= strtok($_SERVER["REQUEST_URI"],'?');
    	
        $section = null;
        $action = null;
        $defaultParam = null;
        $result = null;

     	//parse uri in to section
        foreach (self::$SECTIONS as $k => $v)
        {
            if(Str::containsRegex($uri, $k))
            {
                $section = $v;
                $action = Str::replaceRegex($uri, $k, "$1", false);         
                break;
            }
        }

        if ($action != NULL)
        {
            $parts = explode("/", $action);
            $action = $parts[0];
            $defaultParam = $parts[1];
        }

        if ($section != NULL && $action != NULL)
        {            
            try {
                //load up the section processor
                include_once ('__src/' . $section . '.php');
            
                //process the request further
                $processor = new $section();
                $package = $processor->process($action, $defaultParam);

                if ($package == null)
                    throw new ProcessorException("Invalid Request");
                
                $result = array( "status" => "success", "data" => $package );
            }
            catch (ProcessorException $e)
            {
                $result = array("status" => "error", "error" => $e->getMessage());                
            }
            catch (Exception $e)
            {                
                $result = array("status" => "error", "error" => "Invalid request");                
            }           
        }
        else
        {
            //return invalid request
            $result = array("status" => "error", "error" => "Invalid endpoint");
        }

        //send out as json
        if ($result['error'])
            header('Status: 500');

        echo json_encode($result);
    }

}

API::process();

?>

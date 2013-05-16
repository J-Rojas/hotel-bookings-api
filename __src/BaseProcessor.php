<?php

/*
    Copyright 2013 Jose Rojas
    All rights reserved.
*/


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

?>

<?php

declare(strict_types=1);

namespace Students;

use Exception;
use ReflectionMethod;
use Students\Service\Abstracts\Container;
use Students\Service\Impl\GeneralService;

class JsonRPCPHPServer {
    public $objWithMethods;
    public $methods = "|";
    public $notify = null;
    public $JSONRPC = "2.0";
    public $id = null;
    public $method = "";
    public $params = null;

    public $errs = array(
        "Request: not JSON_RPC", "jsonrpc: missing", "jsonrpc: incorrect type or value",
        "id: incorrect type or value", "method: missing", "method: incorrect type or value"
    );

    function isJsonRpcReq($req) {
        $id = $jsonrpc = $method = $params = null;
        if (array_key_exists("id", $req)) $id = $req["id"];
        if (array_key_exists("jsonrpc", $req)) $jsonrpc = $req["jsonrpc"];
        if (array_key_exists("method", $req)) $method = $req["method"];
        if (array_key_exists("params", $req)) $params = $req["params"];
        if ($jsonrpc == null) return 1;
        if (!is_string($jsonrpc)) return 2;
        if ($jsonrpc != $this->JSONRPC) return 2;
        if ($id == null) return -1;
        if (!(is_int($id) || is_string($id))) return 3;
        $this->id = $id;
        if ($method == null) return 4;
        if (is_string($method)) {
            $this->method = $method;
            $p = strpos($this->methods, "|" . $this->method . "|");
            if (!(is_int($p) && $p >= 0)) return 5;
        } else                              return 5;
        if ($params == null) $params = array();
        $this->params = $params;
        return 0;
    }

    function doPost() {
        header("Content-Type:application/json");
        $isO = $ok = true;
        $req = null;
        date_default_timezone_set("Europe/Bucharest");
        $body = "";
        try {
            $body = trim(file_get_contents('php://input'));
        } catch (Exception $e) {
        }
        try {
            if ($body[0] != '{') $isO = false;
            $req = json_decode($body, $isO);
            if ($req == null || !is_array($req)) $ok = false;
            $r = array();
            if ($ok && !$isO) {
                foreach ($req as $o)
                    $r[] = json_decode(json_encode($o), true);
                $req = $r;
            }
        } catch (Exception $e) {
            $ok = false;
        }
        if ($ok) {
            if ($isO) {
                $listaReq = array();
                $listaReq[] = $req;
            } else {
                $listaReq = $req;
            }
        }
        if (!$ok) {
            $oRes = array();
            $oRes["id"] = "ERR";
            $oRes["jsonrpc"] = $this->JSONRPC;
            $error = array();
            $error["code"] = 0;
            $error["message"] = $this->errs[0];
            $error["data"] = $body;
            $oRes["error"] = $error;
            echo trim(json_encode($oRes));
            return;
        }
        $listaRes = array();
        foreach ($listaReq as $oj) {
            $code = $this->isJsonRpcReq($oj);
            if ($code == -1) {
                try {
                    $body = trim(file_get_contents('php://input'));
                    (new ReflectionMethod($this->objWithMethods, $this->notify))
                        ->invoke($this->objWithMethods, $body);
                } catch (Exception $e) {
                }
                $oRes = array();
                $listaRes[] = $oRes;
                continue;
            }
            if ($code > 0) {
                $oRes = array();
                if ($code <= 3) $oRes["id"] = "ERR"; else $oRes["id"] = $this->id;
                $oRes["jsonrpc"] = $this->JSONRPC;
                $error = array();
                $error["code"] = $code;
                $error["message"] = $this->errs[$code];
                $error["data"] = $body;
                $oRes["error"] = $error;
                $listaRes[] = $oRes;
                continue;
            }
            $mes = null;
            $res = null;
            try {
                $res = (new ReflectionMethod($this->objWithMethods, $this->method))
                    ->invoke($this->objWithMethods, $this->params);
            } catch (Exception $e) {
                $mes = $this->method . " " . $e;
            }
            if ($mes == null && $res != null && (is_int($res) || is_string($res))) {
                $oRes = array();
                $oRes["id"] = $this->id;
                $oRes["jsonrpc"] = $this->JSONRPC;
                $oRes["result"] = json_encode($res);
                $listaRes[] = $oRes;
                continue;
            }
            if ($mes == null && $res != null && json_encode($res) === false)
                $mes = $this->method . "ERR response not JSON: " . $res;
            if ($mes == null && $res != null && array_key_exists($this->method . "ERR", $res))
                $mes = $this->method . ": " . $res[$this->method . "ERR"];
            if ($mes == null) {
                $oRes = array();
                $oRes["id"] = $this->id;
                $oRes["jsonrpc"] = $this->JSONRPC;
                $oRes["result"] = $res;
                $listaRes[] = $oRes;
                continue;
            }
            $oRes = array();
            $oRes["id"] = $this->id;
            $oRes["jsonrpc"] = $this->JSONRPC;
            $error = array();
            $error["code"] = 100;
            $error["message"] = $this->method . "ERR";
            $error["data"] = $mes;
            $oRes["error"] = $error;
            $listaRes[] = $oRes;
        }
        if ($isO) echo trim(json_encode($listaRes[0]));
        else      echo trim(json_encode($listaRes));
    }

    function init($objWithMethods) {
        $this->objWithMethods = $objWithMethods;
        $l = get_class_methods($objWithMethods);
        foreach ($l as $m) {
            $this->methods .= $m . "|";
        }
        $this->doPost();
    }
}

require_once __DIR__ . "\\..\\vendor\\autoload.php";
$container = new Container();
$server = $container->get(JsonRPCPHPServer::class);
$server->init($container->get(GeneralService::class));

//$service = $container->get(StudentService::class);
//$service->deleteStudent("iulia");



<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.17
 * Time: 10:46
 */

namespace EYAML\Test;
use EYAML\EYAML;
use Golafix\Conf\ZipPool;
use Tester\Assert;


require "../vendor/autoload.php";
\Tester\Environment::setup();



$zp = new ZipPool("/tmp");

$ret = $zp->verify("https://github.com/dermatthes/golafix-demo.git#golafix.yml");

Assert::equal("zip:///tmp/85d5b960fe2aecbaa6574494745925501d18bfc8.zip#golafix-demo-master/golafix.yml", $ret);


echo file_get_contents("zip:///tmp/85d5b960fe2aecbaa6574494745925501d18bfc8.zip#golafix-demo-master/golafix.yml");
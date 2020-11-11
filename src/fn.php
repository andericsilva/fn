<?php
$fns=null;
function fn(...$params){
    global $config;
    global $db;
    global $fn;
    global $fns;
    global $root;
    global $site;
    if(isset($params[0])){
        $name=$params[0];
        unset($params[0]);
        $fileName1=$root.'/fn/'.$name.'/'.$name.'.php';
        $fileName2=$root.'/fn/'.$name.'.php';
        $fileName3=$root.'/'.$name.'.php';
    }else{
        die('error:<br>empty $fn name');
    }
    if(file_exists($fileName1)){
        $fileName=$fileName1;
    }elseif(file_exists($fileName2)){
        $fileName=$fileName2;
    }else{
        $fileName=$fileName3;
    }
    if(isset($config)){
        extract(['config',$config]);
    }
    if(isset($db)){
        extract(['db',$db]);
    }
    if(isset($site)){
        extract(['site',$site]);
    }
    if(file_exists($fileName)){
        if(@explode('/',$name)[0]=='view'){//caso seja view
            if(@is_array($params[1])){
                $params[1][]['data']=$params[1];//$params[1] = $data
                extract($params[1]);//sobrescreve as variáveis globais
            }
            ob_start();
            require $fileName;
            $obj=ob_get_contents();
            ob_end_clean();
            print $obj;
        }else{
            //cache
            if(isset($fns[$fileName])){
                $obj=$fns[$fileName];//existe no cache
            }else{
                $obj=require $fileName;
                $fns[$fileName]=$obj;//não existe no cache
            }
            if(count($params)>0){
                return call_user_func_array($obj,$params);
            }else{
                if(is_object($obj)){
                    return call_user_func($obj);
                }else{
                    return $obj;//caso a função não tenha parametros
                }
            }
        }
    }else{
        die('error:<br>$fn '.$fileName.' not found');
    }
}

<?php
class Inspect {

  public static function view($label, $val = "__undefin_e_d__") {
    if($val == "__undefin_e_d__") {

      /* The first argument is not the label but the
               variable to inspect itself, so we need a label.
               Let's try to find out it's name by peeking at
               the source code.
      */
      $val = $label;

      $bt = debug_backtrace();
      $src = file($bt[0]["file"]);
      $line = $src[ $bt[0]['line'] - 1 ];

      // let's match the function call and the last closing bracket
      preg_match( "#Inspect::view\((.+)\)#", $line, $match );

      /* let's count brackets to see how many of them actually belongs
               to the var name
               Eg:   die(inspect($this->getUser()->hasCredential("delete")));
                      We want:   $this->getUser()->hasCredential("delete")
      */
      $max = strlen($match[1]);
      $varname = "";
      $c = 0;
      for($i = 0; $i < $max; $i++) {
        if(     $match[1]{$i} == "(" ) $c++;
        elseif( $match[1]{$i} == ")" ) $c--;
        if($c < 0) break;
        $varname .=  $match[1]{$i};
      }
      $label = $varname;
    }

    // now the actual function call to the inspector method,
    // passing the var name as the label:
    dInspect::dump($label, $val, 10);
    return $val;
  }

  public static function dump($label, $val = "__undefin_e_d__") {
    if($val == "__undefin_e_d__") {

      /* The first argument is not the label but the
               variable to inspect itself, so we need a label.
               Let's try to find out it's name by peeking at
               the source code.
      */
      $val = $label;

      $bt = debug_backtrace();
      $src = file($bt[0]["file"]);
      $line = $src[ $bt[0]['line'] - 1 ];

      // let's match the function call and the last closing bracket
      preg_match( "#Inspect::dump\((.+)\)#", $line, $match );

      /* let's count brackets to see how many of them actually belongs
               to the var name
               Eg:   die(inspect($this->getUser()->hasCredential("delete")));
                      We want:   $this->getUser()->hasCredential("delete")
      */
      $max = strlen($match[1]);
      $varname = "";
      $c = 0;
      for($i = 0; $i < $max; $i++) {
        if(     $match[1]{$i} == "(" ) $c++;
        elseif( $match[1]{$i} == ")" ) $c--;
        if($c < 0) break;
        $varname .=  $match[1]{$i};
      }
      $label = $varname;
    }

    // now the actual function call to the inspector method,
    // passing the var name as the label:
    cInspect::dump($label, $val, 10);
    return $val;
  }

}

class dInspect {

  public static function dump($label, &$val, $max_recursion) {
    $ipath = (substr($label,0,1)=="$")?$label:"$".$label;
    $bt = debug_backtrace();
    $o = '<div class="dinspect">' . dInspect::ddump($val, array('ipath' => $ipath, 'label' => $label, 'max_recursion' => $max_recursion)) .
            '<div class="di-footer">Called from: <span class="di-label">' . $bt[1]["file"] .
            '</span>, line ' . $bt[1]["line"] .
            '<span class="di-credits">Inspector v1.0 by <a href="mailto:grignoli@at.gmail.com">Sebasti&aacute;n Grignoli</a> (c) 2010 </span></div></div>'."\n";
    echo $o;
  }

  private static $script = '
  <script>
  function toggle(elm)
  {
    var elm = document.getElementById(elm);
    if(elm.style.visibility!="visible"){
        elm.style.visibility="visible";
        elm.style.display="block";
    }else{
        elm.style.visibility="hidden";
        elm.style.display="none";
    }
  }
  </script>
  ';

  private static $styles = '
  <style>
  .dinspect {
          color: black;
          background-color:#F0F0F0;
          border:  1px solid;
          padding: 2px 0 0 3px;
          margin: 2px 3px 2px 0px;
          font: 12px/13px Tahoma, Verdana;
      text-align: left;
  }
  .di-body {
          color: black;
          background-color:#FAFAFA;
          margin-left: 3px;
          padding: 2px 0 0 3px;
          border-left: 1px dotted;
  }
  .di-node {
          visibility:hidden;
          display:none;
          /* background-color:white; */
          margin-left: 3px;
          padding-left: 3px;
          padding:2px 0 0px 10px;
  }
  .di-longtext {
          visibility:hidden;
          display:none;
          background:none repeat scroll 0 0 lightyellow;
          border:1px solid #808000;
          font:13px courier new;
          margin:5px 5px 0 0;
          overflow:auto;
          padding:5px;
  }
  .di-label {
          color:navy;
          font:bold 13px/12px courier new;
  }
  .di-value {
          font-weight: bold;
  }
  .di-clickable {
          cursor: pointer;
  }
  div.di-clickable:hover, li.di-clickable:hover {
          background-color: #BFDFFF;
  }
  span.di-ipath:hover {
          background-color: yellow;
  }
  .di-footer {
          border-top: 1px dashed;
          margin-top: 8px;
          padding: 3px;
  }
  .di-credits {
          float: right;
          margin-right: 3px;
  }
  </style>
  ';

  public static function echoScript() {
    static $script_shown = false;
    if ($script_shown)
      return;
    $script_shown = true;
    echo dInspect::$script;
  }

  public static function echoStyles() {
    static $styles_shown = false;
    if ($styles_shown)
      return;
    $styles_shown = true;
    echo dInspect::$styles;
  }

  private static function ipathlink($ipath) {
    return '<span style="cursor:pointer" class="di-ipath" onclick="return prompt(\'Current branch:\', \''.str_replace("'","\&#39;",str_replace('"','&quot;',str_replace("\\","\\\\",str_replace('>','&gt;',$ipath)))).'\') && false;">&gt;</span>';
  }


  public static function ddump(&$val, $params) {

    $ipath = empty($params['ipath'])?null:$params['ipath'];
    $label = !isset($params['label'])?null:$params['label'];
    $type = empty($params['type'])?null:$params['type'];
    $len = !isset($params['len'])?null:$params['len'];
    $units = empty($params['units'])?null:$params['units'];
    $max_recursion = $params['max_recursion'] === null ? 2 : $params['max_recursion'];

    dInspect::echoStyles();
    dInspect::echoScript();
    if (is_null($type))
      $type = ucfirst(gettype($val));
    $o = '';
    if($max_recursion == 0){
      $o .= dInspect::drawNode("**MAXIMUM RECURSION LEVEL REACHED**", $label, $ipath, "Array", count($val), "elements");
    } elseif (is_array($val)) {
      $rndtag = md5(microtime() . rand(0, 1000));
      $o .= '<div class="di-body di-clickable" onclick="toggle(\'' . $rndtag . '\');"> '.dInspect::ipathlink($ipath).' <span class="di-label">' . (!
              ($label === false) ? $label : '...') . '</span> (' . $type . ', ' . count($val) .
              ' elements) <span class="di-value">...</span></div>'."\n".'<div id="' . $rndtag .
              '" class="di-node">';
      foreach ($val as $k => $v) {
        $o .= dInspect::ddump($val[$k], array('ipath' => $ipath.(is_int($k)?'['.$k.']':'[\''.$k.'\']'),
                                              'label' => $k,
                                              'max_recursion' => $max_recursion - 1));
      }
      $o .= "</div>\n";
    } elseif (is_object($val)) {
      $rndtag = md5(microtime() . rand(0, 1000));
      $o .= '<div class="di-body di-clickable" onclick="toggle(\'' . $rndtag . '\');"> '.dInspect::ipathlink($ipath).' <span class="di-label">' . (!
              ($label === false) ? $label . ' ' : '... ') .
              '</span> (Object <span class="di-value">' . get_class($val) .
              '</span>) <span class="di-value">...</span></div>'."\n".'<div id="' . $rndtag .
              '" class="di-node">';

      //$ref = new ReflectionClass(get_class($val));
      $ref = new ReflectionObject($val);

      // datos de la clase
      if ($ref->getFileName()) {
        $o .= dInspect::drawNode($ref->getFileName() . ($ref->getStartLine() ? ", line " .
                        $ref->getStartLine() : ""), "Definition:", $ref->getFileName(), "");
      }

      $parent = get_class($val);
      $ancestors = $parent;
      while ($parent = get_parent_class($parent)) {
        $ancestors = $parent . " > " . $ancestors;
      }
      if ($ancestors != get_class($val)) {
        $o .= dInspect::drawNode($ancestors, "Ancestors:", ">", "");
      }

      // para cada propiedad
      $props = $ref->getProperties();
      foreach ($props as $prop) {
          /*
                         $prop->setAccessible(true);

                        if($prop->isProtected()) $prop->setAccessible(true);
          */
          if($prop->isPublic()) {
            $prop_value = $prop->getValue($val);
            $prop_type = ucfirst(gettype($prop_value));
          } else {
            $prop_value = "";
            $prop_type = " ";
          }
          $o .= dInspect::ddump($prop_value, array('ipath' => $ipath.'->'.$prop->name,
                                                   'label' =>'Property:' . ($prop->isPublic() ? ' public' :
                                                             '') . ($prop->isPrivate() ? ' private' : '') . ($prop->isProtected() ?
                                                             ' protected' : '') . ($prop->isStatic() ? ' static' : '') . ' $' . $prop->name,
                                                   'type' => $prop_type,
                                                   '',
                                                   'max_recursion' => $max_recursion - 1));
      }

      // para cada método
      $methods = $ref->getMethods();
      foreach ($methods as $method) {
        $params = $method->getParameters();
        $strparams = array();
        $optional_params = false;
        foreach ($params as $param) {
          $pdefault = null;
          try {
            $pdefault = $param->getDefaultValue();
          }
          catch (exception $e) {
          }
          $pname = $param->name;
          if ($param->isOptional() && !$optional_params) {
            $optional_params = true;
            $pname = "[" . $pname;
          }

          $strparams[] = $pname . (isset($pdefault) ? " = " . (is_array($pdefault)?"array()":(is_bool($pdefault)?($pdefault?'TRUE':'FALSE'):$pdefault)) : "");
        }
        $strparams = implode(", ", $strparams);
        $emptystring = "";
        $methodSyntax = $method->name . "(" . $strparams .
                ($optional_params ? "]" : "") . ")";


        $o .= dInspect::drawNode($emptystring, "Method: " . ($method->isPublic() ? ' public' :
                '') . ($method->isPrivate() ? ' private' : '') . ($method->isProtected() ?
                ' protected' : '') . ($method->isStatic() ? ' static' : '') . ' '.$methodSyntax,$ipath."->".$methodSyntax, "");
        //print_r($method);
      }
      unset($ref);
      $o .= "</div>\n";
      //dInspect::objectStack("add",$val);

    } elseif (is_bool($val)) {
      $o .= '<div class="di-body"> '.dInspect::ipathlink($ipath).' <span class="di-label">' . (!($label === false) ?
              $label . ' ' : '... ') . '</span>(' . $type . ') <span class="di-value">' . ($val ?
              "TRUE" : "FALSE") . "</span></div>\n";
    } elseif (is_string($val) && $type == 'String') {
      $len = strlen($val);
      $encoding = mb_detect_encoding($val, 'UTF-8, ISO-8859-1', true);
      if ($len < 60) {
        $type = $type . ", ".$encoding;
        $o .= dInspect::drawNode(htmlspecialchars($val,ENT_QUOTES,$encoding), $label, $ipath, $type, $len, is_null($units) ?
                'characters' : $units);
      } else {
        $rndtag = md5(microtime() . rand(0, 1000));
        $o .= '<div class="di-body di-clickable" onclick="toggle(\'' . $rndtag . '\');"> '.dInspect::ipathlink($ipath).' <span class="di-label">' . (!
                ($label === false) ? $label : '...') . '</span> (String, '.$encoding.', ' . $len . ' ' . (is_null
                ($units) ? 'characters' : $units) . ') <span class="di-value">' . htmlspecialchars(substr($val, 0,
                60),ENT_QUOTES,$encoding) . '...</span></div>'."\n".'<pre id="' . $rndtag . '" class="di-longtext">' . htmlspecialchars($val,ENT_QUOTES,$encoding) .
                '</pre>';
      }
    } elseif (is_string($val)) {  /* && $type != 'String', we already know that. */
      $encoding = mb_detect_encoding($val, 'UTF-8, ISO-8859-1', true);
      $o .= dInspect::drawNode(htmlspecialchars($val,ENT_QUOTES,$encoding), $label, $ipath, $type, $len, $units);
    } else {
      $o .= dInspect::drawNode($val, $label, $ipath);
    }
    return $o;
  }

  private static function drawNode($val, $label = null, $ipath = "#", $type = null, $len = null, $units = null) {
    if (gettype($val) == "boolean") $val = $val?'TRUE':'FALSE';
    $type = is_null($type) ? ucfirst(gettype($val)) : $type;
    $label = is_null($label) ? '...': $label;
    $units = is_null($units) ? 'bytes': $units;
    $len = is_null($len) ? '': $len.' '.$units;
    $desc = '';
    if(trim($type))   $desc = trim($len)? $type.', '.$len: $type;
    $desc = '('.$desc.')';
    if($desc == '()') $desc = '';
    return '<div class="di-body">'.dInspect::ipathlink($ipath).' <span class="di-label">' . $label .
            '</span> ' . $desc . ' <span class="di-value">' . $val . "</span></div>\n";
  }
}

class cInspect {

  protected static $indent = 0;

  public static function dump($label, &$val, $max_recursion) {
    $ipath = (substr($label,0,1)=="$")?$label:"$".$label;
    $bt = debug_backtrace();
    $o = '// ********************************************** '."\n".
         cInspect::ddump($val, array('ipath' => $ipath, 'label' => $label, 'max_recursion' => $max_recursion)) .
         '// ******* ^^^ dump called from: ' . $bt[1]["file"] . ', line ' . $bt[1]["line"] . "\n";
    echo $o;
  }

  public static function ddump(&$val, $params) {
    self::$indent++;
    $ipath = empty($params['ipath'])?null:$params['ipath'];
    $label = !isset($params['label'])?null:$params['label'];
    $type = empty($params['type'])?null:$params['type'];
    $len = !isset($params['len'])?null:$params['len'];
    $units = empty($params['units'])?null:$params['units'];
    $max_recursion = $params['max_recursion'] === null ? 2 : $params['max_recursion'];

    if (is_null($type))
      $type = ucfirst(gettype($val));
    $o = '';
    if($max_recursion == 0){
      $o .= cInspect::drawNode("**MAXIMUM RECURSION LEVEL REACHED**", $label, $ipath, "Array", count($val), "elements");
    } elseif (is_array($val)) {
      $o .= self::indent().(
              !($label === false) ? $label : ': ') . ' (' . $type . ', ' . count($val) .
              " elements) : \n";
      foreach ($val as $k => $v) {
        $o .= cInspect::ddump($val[$k], array('ipath' => $ipath.(is_int($k)?'['.$k.']':'[\''.$k.'\']'),
                                              'label' => "[".$k."]",
                                              'max_recursion' => $max_recursion - 1));
      }
    } elseif (is_object($val)) {
      $o .= self::indent().(!
              ($label === false) ? $label . ' ' : ': ') .
              '(Object ' . get_class($val) .
              ") :\n";
      self::$indent++;
      //$ref = new ReflectionClass(get_class($val));
      $ref = new ReflectionObject($val);

      // datos de la clase
      if ($ref->getFileName()) {
        $o .= cInspect::drawNode($ref->getFileName() . ($ref->getStartLine() ? ", line " .
                        $ref->getStartLine() : ""), "Definition:", $ref->getFileName(), "");
      }

      $parent = get_class($val);
      $ancestors = $parent;
      while ($parent = get_parent_class($parent)) {
        $ancestors = $parent . " > " . $ancestors;
      }
      if ($ancestors != get_class($val)) {
        $o .= cInspect::drawNode($ancestors, "Ancestors:", ">", "");
      }

      // para cada propiedad
      $props = $ref->getProperties();
      foreach ($props as $prop) {
          /*
                         $prop->setAccessible(true);

                        if($prop->isProtected()) $prop->setAccessible(true);
          */
          if($prop->isPublic()) {
            $prop_value = $prop->getValue($val);
            $prop_type = ucfirst(gettype($prop_value));
          } else {
            $prop_value = "";
            $prop_type = " ";
          }
          self::$indent--;
          $o .= cInspect::ddump($prop_value, array('ipath' => $ipath.'->'.$prop->name,
                                                   'label' =>'Property:' . ($prop->isPublic() ? ' public' :
                                                             '') . ($prop->isPrivate() ? ' private' : '') . ($prop->isProtected() ?
                                                             ' protected' : '') . ($prop->isStatic() ? ' static' : '') . ' $' . $prop->name,
                                                   'type' => $prop_type,
                                                   '',
                                                   'max_recursion' => $max_recursion - 1));
          self::$indent++;
      }

      // para cada método
      $methods = $ref->getMethods();
      foreach ($methods as $method) {
        $params = $method->getParameters();
        $strparams = array();
        $optional_params = false;
        foreach ($params as $param) {
          $pdefault = null;
          try {
            $pdefault = $param->getDefaultValue();
          }
          catch (exception $e) {
          }
          $pname = $param->name;
          if ($param->isOptional() && !$optional_params) {
            $optional_params = true;
            $pname = "[" . $pname;
          }

          $strparams[] = $pname . (isset($pdefault) ? " = " . $pdefault : "");
        }
        $strparams = implode(", ", $strparams);
        $emptystring = "";
        $methodSyntax = $method->name . "(" . $strparams .
                ($optional_params ? "]" : "") . ")";


        $o .= cInspect::drawNode($emptystring, "Method: " . ($method->isPublic() ? ' public' :
                '') . ($method->isPrivate() ? ' private' : '') . ($method->isProtected() ?
                ' protected' : '') . ($method->isStatic() ? ' static' : '') . ' '.$methodSyntax,$ipath."->".$methodSyntax, "");
        //print_r($method);
      }
      unset($ref);
      //cInspect::objectStack("add",$val);
      self::$indent--;

    } elseif (is_bool($val)) {
      $o .= self::indent().(!($label === false) ?
              $label . ' ' : '... ') . '(' . $type . ') ' . ($val ?
              "TRUE" : "FALSE")."\n";
    } elseif (is_string($val) && $type == 'String') {
      $len = strlen($val);
      $encoding = mb_detect_encoding($val, 'UTF-8, ISO-8859-1', true);
      $type .= ", ".$encoding;
      $o .= cInspect::drawNode('"'.$val.'"', $label, $ipath, $type, $len, is_null($units) ?
              'characters' : $units);
    } elseif (is_string($val)) {  /* && $type != 'String', we already know that. */
      $o .= cInspect::drawNode('"'.$val.'"', $label, $ipath, $type, $len, $units);
    } else {
      $o .= cInspect::drawNode($val, $label, $ipath);
    }
    self::$indent--;
    return $o;
  }

  private static function drawNode($val, $label = null, $ipath = "#", $type = null, $len = null, $units = null) {
    if (gettype($val) == "boolean") $val = $val?'TRUE':'FALSE';
    $type = is_null($type) ? ucfirst(gettype($val)) : $type;
    $label = is_null($label) ? '...': $label;
    $units = is_null($units) ? 'bytes': $units;
    $len = is_null($len) ? '': $len.' '.$units;
    $desc = '';
    if(trim($type))   $desc = trim($len)? $type.', '.$len: $type;
    $desc = '('.$desc.')';
    if($desc == '()') $desc = '';
    return self::indent() . $label .' '. $desc . ($val == "" ? '' : ' => ' . $val ) . "\n";
  }

  private static function indent(){
    return str_repeat("    ", self::$indent);
  }
}


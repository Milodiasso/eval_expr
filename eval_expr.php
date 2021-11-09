<?php
function tab_operation(string $string)
{
    $my_parse = [];
    $my_number = '';
    for ($i = 0; $i <  strlen($string); $i++) {
        if (preg_match("~[0-9]|\.~", $string[$i])) {
            $my_number .= $string[$i];
        } else {
            if ($my_number != '') {
                $my_parse[] = floatval($my_number);
                $my_parse[] = $string[$i];
                $my_number = '';
            } else if ($my_number == '' && !is_float(end($my_parse))) {
                $my_number = $string[$i];
            } else if ($string[$i] != '') {
                $my_parse[] = floatval($string[$i]);
            }
        }
    }
    if ($string[strlen($string) - 1] != ')') {
        $my_parse[] = floatval($my_number);
    }
    return $my_parse;
}

function first_mult($tab)
{
    $result = '';
    foreach ($tab as $key => $value) {
        if ($value == '*') {
            $result = floatval($tab[$key - 1] * $tab[$key + 1]);
            $tab[$key + 1] = $result;
            unset($tab[$key]);
            unset($tab[$key - 1]);
        } else if ($value == '/') {
            $result = floatval($tab[$key - 1] / $tab[$key + 1]);
            $tab[$key + 1] = $result;
            unset($tab[$key]);
            unset($tab[$key - 1]);
        } else if ($value == '%') {
            $result = floatval($tab[$key - 1] % $tab[$key + 1]);
            $tab[$key + 1] = $result;
            unset($tab[$key]);
            unset($tab[$key - 1]);
        }
    }
    $tab = array_values($tab);
    return $tab;
}

function calculateur($tab)
{
    if (count($tab) < 2) {
        $addition = $tab;
    } else {
        $addition = first_mult($tab);
    }

    $result = '';
    if (count($addition) > 2) {
        foreach ($addition as $key => $value) {
            if ($value == '+') {
                $result = floatval($addition[$key - 1] + $addition[$key + 1]);
            }
            if ($value == '-') {
                $result = floatval($addition[$key - 1] - $addition[$key + 1]);
            }
        }
    } else {
        $result = $addition[0];
    }
    return $result;
}

function parseur($string)
{
    $tab = tab_operation($string);
    $result = calculateur($tab);
    return $result;
}

function eval_expr($calcul)
{
    if (preg_match("~\(~", $calcul)) {
        $cut = strrchr($calcul, "(");
        $cut2 = strstr($cut, ')', true) . ')';
        $only_calc = substr($cut2, 1, strlen($cut2) - 2);
        $result = parseur($only_calc);
        $calcul = str_replace($cut2, $result, $calcul);

        if (preg_match("~\(~", $calcul)) {
            eval_expr($calcul);
        }
    }
    if (!preg_match("~\(~", $calcul)) {
        $result = parseur($calcul);
        echo $result . PHP_EOL;
    }
}

eval_expr($argv[1]);

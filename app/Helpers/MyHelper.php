<?php
/**
 *
 * User: weslley ribeiro
 * Date: 31/08/2018
 * Time: 11:55
 * Description: All functions to help
 */

if (! function_exists('money')) {

    function money($value = 0.00)
    {
        if(is_numeric($value)){
            return number_format($value,2,',','.');
        }else{
            return '0,00';
        }
    }
}

//Formata um selectbox
if (! function_exists('format_select')) {

    function format_select($rows, $selected=0, $input_1 = 'id', $input_2 = 'name', $display_zero = false)
    {
        if($display_zero){
            print('<option value="0">Selecione</option>');
        }

        foreach($rows as $row){

            if(is_array($selected)){
                if(in_array($row[$input_1], $selected)){
                    print('<option value="'.$row[$input_1].'" selected="selected">'.$row[$input_2].'</option>');
                } else {
                    print('<option value="'.$row[$input_1].'">'.$row[$input_2].'</option>');
                }
            }else{

                if($row[$input_1] == $selected){
                    print('<option value="'.$row[$input_1].'" selected="selected">'.$row[$input_2].'</option>');
                } else {
                    print('<option value="'.$row[$input_1].'">'.$row[$input_2].'</option>');
                }
            }
        }

        return true;
    }
}

if (! function_exists('br_mysql_date')) {

    function br_mysql_date($date = '00/00/0000')
    {
        return implode('-', array_reverse(explode('/', $date)));
    }
}

if (! function_exists('mysql_br_date')) {

    function mysql_br_date($date = '0000-00-00')
    {
        return implode('/', array_reverse(explode('-', $date)));
    }
}

if (! function_exists('mysql_br_date_time')) {

    function mysql_br_date_time($time = '0000-00-00 00:00:00', $noTime = false)
    {
        if($time){
            $time = explode(' ', $time);

            $horas = explode(':', $time[1]);

            if($noTime){
                return implode('/', array_reverse(explode('-', $time[0])));
            }

            return implode('/', array_reverse(explode('-', $time[0]))).' '.$horas[0].':'.$horas[1];
        }

        return false;
    }
}

if (! function_exists('return_mysql_year')) {

    function return_mysql_year($time = '0000-00-00 00:00:00')
    {
        if($time){
            $time = explode('-', $time);
            return $time[0];
        }

        return false;
    }
}

if (! function_exists('mysql_only_date')) {

    function mysql_only_date($time = '0000-00-00 00:00:00')
    {
        if($time){
            $time = explode(' ', $time);

            return $time[0];
        }

        return false;
    }
}

if (! function_exists('yes_no')) {

    function yes_no($id)
    {
        if($id == 1){
            return 'Sim';
        }

        return 'Não';
    }
}

if (! function_exists('diff_dates')) {

    function diff_dates($end_date, $start_date = false)
    {
        if(!$start_date){
            $start_date = date('Y-m-d');
        }

        $start_time = strtotime($start_date);
        $end_time   = strtotime($end_date);

        // Calcula a diferença de segundos entre as duas datas:
        $diff = $end_time - $start_time;

        // Calcula a diferença de dias
        return (int)floor( $diff / (60 * 60 * 24));
    }
}

if (! function_exists('nl2br2')) {
    function nl2br2($string)
    {
        return str_replace(['\r\n', '\r', '\n', '\n\r'], '<br /><br />', $string);
    }
}

if (! function_exists('only_numbers')) {
    function only_numbers($string = '')
    {
        return preg_replace('/\D/', '', $string);
    }
}

if (! function_exists('return_month_year')) {
    function return_month_year($time = '0000-00-00 00:00:00')
    {
        if($time){
            return meses(date('m', strtotime($time))).' / '.date('Y', strtotime($time));
        }

        return FALSE;
    }
}

if (! function_exists('return_month')) {
    function return_month($time = '0000-00-00 00:00:00')
    {
        if($time){
            return meses(date('m', strtotime($time)));
        }

        return FALSE;
    }
}

if (! function_exists('meses')) {
    function meses($id = false)
    {
        $meses = array(
            '01'	=> 'JAN',
            '02'	=> 'FEV',
            '03'	=> 'MAR',
            '04'	=> 'ABR',
            '05'	=> 'MAI',
            '06'	=> 'JUN',
            '07'	=> 'JUL',
            '08'	=> 'AGO',
            '09'	=> 'SET',
            '10'	=> 'OUT',
            '11'	=> 'NOV',
            '12'	=> 'DEZ'
        );

        if($id){
            return $meses[$id];
        }

        return $meses;
    }
}

if (! function_exists('format_cpf')) {
    function format_cpf($str)
    {
        $str = only_numbers($str);
        $str = str_pad(preg_replace('[^0-9]', '', $str), 11, '0', STR_PAD_LEFT);

        return $str;
    }
}

if (! function_exists('implode_br')){
    function implode_br($array = [])
    {
        if(isset($array) && is_array($array)){
            return implode('<br/>', $array);
        }else{
            return '';
        }
    }
}

if (! function_exists('implode_n')){
    function implode_n($array = [])
    {
        if(isset($array) && is_array($array)){
            return implode('\n', $array);
        }else{
            return '';
        }
    }
}

if (! function_exists('semanas')){
    function semanas($name = false)
    {
        $semanas = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-Feira',
            'Tuesday'   => 'Terça-Feira ',
            'Wednesday' => 'Quarta-Feira',
            'Thursday'  => 'Quinta-Feira',
            'Friday'    => 'Sexta-Feira',
            'Saturday'  => 'Sábado',
        ];

        if($name){
            return $semanas[$name];
        }

        return $semanas;
    }
}

if (! function_exists('icon_list')){
    function icon_list($tipo = false)
    {
        $icons = [
            'pdf'   => 'fa-file-pdf-o',
            'ppt'   => 'fa-file-powerpoint-o',
            'pptx'  => 'fa-file-powerpoint-o',
            'pptm'  => 'fa-file-powerpoint-o',
            'potx'  => 'fa-file-powerpoint-o',
            'potm'  => 'fa-file-powerpoint-o',
            'ppam'  => 'fa-file-powerpoint-o',
            'ppsx'  => 'fa-file-powerpoint-o',
            'ppsm'  => 'fa-file-powerpoint-o',
            'sldx'  => 'fa-file-powerpoint-o',
            'sldm'  => 'fa-file-powerpoint-o',
            'thmx'  => 'fa-file-powerpoint-o',
            'xls'   => 'fa-file-excel-o',
            'xlsx'  => 'fa-file-excel-o',
            'xlsm'  => 'fa-file-excel-o',
            'xltx'  => 'fa-file-excel-o',
            'xltm'  => 'fa-file-excel-o',
            'xlsb'  => 'fa-file-excel-o',
            'xlam'  => 'fa-file-excel-o',
            'word'  => 'fa-file-word-o',
            'doc'   => 'fa-file-word-o',
            'docx'  => 'fa-file-word-o',
            'docm'  => 'fa-file-word-o',
            'dotx'  => 'fa-file-word-o',
            'dotm'  => 'fa-file-word-o',
            'mp4'   => 'fa-play-circle-o',
            'zip'   => 'fa-file-archive-o',
            'rar'   => 'fa-file-archive-o',
            '7zip'   => 'fa-file-archive-o',
            '7z'   => 'fa-file-archive-o',
            'tar'   => 'fa-file-archive-o',
            'gzip'   => 'fa-file-archive-o',
            'bzip2'   => 'fa-file-archive-o',
            'xz'   => 'fa-file-archive-o',
            'wim'   => 'fa-file-archive-o',
            'gz'   => 'fa-file-archive-o',
        ];

        if($tipo){
            return (isset($icons[$tipo])) ? $icons[$tipo] : false;
        }

        return $icons;
    }
}

if (! function_exists('file_ext')){
    function file_ext($filename = '')
    {
        if(!isset($filename)){
            return false;
        }else{
            $filename_sep = explode('.', $filename);

            if(is_array($filename_sep)){
                $file_extension = end($filename_sep);
                return strtolower($file_extension);
            }else{
                return false;
            }
        }
    }
}

if (! function_exists('to_intern')){
    function to_intern()
    {
        return [
            'jpg',
            'jpeg',
            'png'
        ];
    }
}

if (! function_exists('to_select')) {

    function to_select($data, $key = 'id', $value = 'nome')
    {
        $return = [];

        if(count($data) > 0){
            foreach($data as $row){
                $return[$row[$key]] = $row[$value];
            }
        }

        return $return;
    }
}

if (! function_exists('removeAcentos')) {
    function removeAcentos($string, $slug = false)
    {
        $string    = remove_accent($string);
        $string    = mb_strtolower($string);


        // Código ASCII das vogais
        $ascii['a'] = range(224, 230);
        $ascii['e'] = range(232, 235);
        $ascii['i'] = range(236, 239);
        $ascii['o'] = array_merge(range(242, 246), array(240, 248));
        $ascii['u'] = range(249, 252);

        // Código ASCII dos outros caracteres
        $ascii['b'] = array(223);
        $ascii['c'] = array(231);
        $ascii['d'] = array(208);
        $ascii['n'] = array(241);
        $ascii['y'] = array(253, 255);

        foreach ($ascii as $key=>$item) {
            $acentos = '';
            foreach ($item AS $codigo) $acentos .= chr($codigo);
            $troca[$key] = '/['.$acentos.']/i';
        }

        $string = preg_replace(array_values($troca), array_keys($troca), $string);

        // Slug?
        if ($slug) {
            // Troca tudo que não for letra ou número por um caractere ($slug)
            $string = preg_replace('/[^a-z0-9]/i', $slug, $string);
            // Tira os caracteres ($slug) repetidos
            $string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
            $string = trim($string, $slug);
        }

        return $string;
    }
}

if (! function_exists('remove_accent')) {
    function remove_accent($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Œ', 'œ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Š', 'š', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Ÿ', '?', '?', '?', '?', 'Ž', 'ž', '?', 'ƒ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }
}

if (! function_exists('select_status')) {
    function select_status($id = false)
    {
        $data = [
            1 => 'Ativo',
            2 => 'Inativo',
        ];

        if($id){
            return $data[$id];
        }

        return $data;
    }
}

if (! function_exists('array_paginate')) {
    function array_paginate($id = false)
    {
        $data = [
            10 => '10',
            25 => '25',
            50 => '50',
        ];

        if($id){
            return $data[$id];
        }

        return $data;
    }
}

if (! function_exists('dias_semana')) {
    function dias_semana($id = null)
    {
        $data = [
            0   => 'domingo',
            1   => 'segunda',
            2   => 'terca',
            3   => 'quarta',
            4   => 'quinta',
            5   => 'sexta',
            6   => 'sabado',
        ];

        if(!is_null($id)){
            return $data[$id];
        }

        return $data;
    }
}

if(! function_exists('limpa_zero_esquerda')){
    function limpa_zero_esquerda($str)
    {
        $tamanho = strlen($str);
        for($x = 0; $x < $tamanho; $x++ ){
            if( $str[$x] == "0"){
                continue;
            }else{
                return substr($str, $x, ($tamanho-$x));
            }
        }
    }
}

/*
 * Author: Weslley Ribeiro
 * date: 07/11/2017
 * return: array
 * description: lista um array mult dimensional por uma coluna em comum passada por parametro
 * origin: https://pt.stackoverflow.com/questions/71757/ordenar-um-array-multidimensional-por
   -uma-coluna-mantendo-as-mesmas-linhas-do-a
 *
 */
if(! function_exists('arraySortByColunm')){
    function arraySortByColunm($array, $on, $order=SORT_ASC){
        $new_array = array();
        $sortable_array = array();
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array((array)$v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }
}

/*
 * Author: Weslley Ribeiro
 * date: 31/08/2018
 * return: string
 * description: retorna a string obtda pelo padrão desejado
 *
 */
if(! function_exists('pregString')){
    function pregString($pattern = '', $myString){
        $influencerImgRegex = [];
        preg_match_all($pattern, $myString, $influencerImgRegex);
        return $influencerImgRegex[0][0];
    }
}
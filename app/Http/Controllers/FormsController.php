<?php

namespace App\Http\Controllers;

class FormsController extends Controller
{


    /**
     * FormsController constructor.
     * @param string $action
     * @param string $method
     * @param string $encType
     * @param string $title
     * @param string $icon
     * if resource is edit, will need to populate method values.
     */
    public function formBuilder($action = "/example", $method = "null", $encType = "application/x-www-form-urlencoded", $title = "Example Form", $icon = "pencil")
    {
        return ([$action, $method, $encType, $title, $icon]);
    }

    /**
     * @param array $array
     * @return array
     * ['label','name','value','placeholder','required']
     */
    public function text($array = ['textfor', 'textName', 'textValue', 'textPlaceholder', 'textRequired'])
    {
        $row = ['type'=>'text','for'=>$array[0],'id'=>$array[0],'name'=>$array[1],'value'=>$array[2],'placeholder'=>$array[3],'required'=>$array[4]];
        return $row;
    }

    /**
     * @param array $array
     * @return array
     */
    public function multipleText_wCheckbox($label = "Sample Label", $arr = [['acquisition_advance','','Acquisition advance','required','checked'],['acquisition_max_advance','','Acquisition max advance','required'],
        ['acquisition_max','','Acquisition maximum (0 = No Max)','required'], ['acquisition_min','','Acquisition minimum','required']], $numberOfFields = 4)
    {
        $row =['type'=>'multipleText','cells'=>$numberOfFields,'for'=>$label,'id'=>$label];
        for ($i=0; $i<$numberOfFields; $i++) {
            $row[$i]=['name'=>$arr[$i][0],'value'=>$arr[$i][1],'placeholder'=>$arr[$i][2],'required'=>$arr[$i][3]];
            $row['checked']=$arr[0][4];
        }
        return $row;
    }

    /**
     * @param array $array
     * @return array
     */
    public function multipleText($arr = [['acquisition_advance','','Acquisition advance',''],['acquisition_max_advance','','Acquisition max advance',''],
        ['acquisition_max','','Acquisition maximum (0 = No Max)','required'], ['acquisition_min','','Acquisition minimum','required']], $numberOfFields = 4)
    {
        $row =['type'=>'multipleText1','cells'=>$numberOfFields];
        for ($i=0; $i<$numberOfFields; $i++) {
            $row[$i]=['name'=>$arr[$i][0],'value'=>$arr[$i][1],'placeholder'=>$arr[$i][2],'required'=>$arr[$i][3]];
        }
        return $row;
    }



    public function newDocRule(
        $for,
        $hidden = "10",
        $label = "Acquisition",
        $textInputarr = ['acquisition_amount', '', 'Enter acquisition trigger amount'],
        $selectArr = ['acquisition_documents', 'options'=>['option_values'=>1,'option_names'=>"Hello",'selected'=>'selected']]
    ) {
        $row = ['type'=>'documentRule','for'=>$for, 'hidden'=>$hidden, 'label'=>$label, 'textInput'=>$textInputarr, 'selectInput'=>$selectArr];
        return $row;
    }

    public function newDocRule13($for, $hidden = "10", $label = "Acquisition", $selectArr = ['acquisition_documents', 'options'=>['option_values'=>1,'option_names'=>"Hello",'selected'=>'selected']])
    {
        $row = ['type'=>'documentRule13','for'=>$for, 'hidden'=>$hidden,'label'=>$label, 'selectInput'=>$selectArr];
        return $row;
    }


    /**
     * @param array $array
     * @return mixed
     * use for password and password_confirmation fields
     * ['label','name','value','placeholder','required']
     */
    public function password($array)
    {
        $row = ['type'=>'password','for'=>$array[0],'id'=>$array[0],'name'=>$array[1],'value'=>$array[2],'placeholder'=>$array[3],'required'=>$array[4]];
        return $row;
    }

    /**
     * @param array $array
     * @return mixed
     * ['label','name','value','placeholder','required']
     */
    public function textArea($array)
    {
        $row = ['type'=>'textarea','for'=>$array[0],'id'=>$array[0],'name'=>$array[1],'value'=>$array[2],'placeholder'=>$array[3],'required'=>$array[4]];
        return $row;
    }

    /**
     * @param array $array
     * @param key array 'name', 'value', 'optionLabel' in $array
     * @return array
     * ['label','name','value','optionLabel','checked','required']
     * use 'false' as default for 'checked' values
     */
    public function checkbox($array)
    {
        $row = ['type'=>'checkbox','for'=>$array[0],'id'=>$array[0],'required'=>$array[5]];
        $row['name']=$array[1];
        $row['value']=$array[2];
        $row['optionLabel']=$array[3];
        $row['checked']=$array[4];
        return $row;
    }

    /**
     * @param array $array
     * @param key array 'value', 'optionLabel' in $array
     * @return array
     * ['label','name','value','optionLabel','checked','required']
     * use 'false' as default for 'checked' values
     */
    public function radio($array)
    {
        $row = ['type'=>'radio','for'=>$array[0],'id'=>$array[0],'name'=>$array[1],'required'=>$array[5]];
        $row['value']=$array[2];
        $row['optionLabel']=$array[3];
        $row['checked']=$array[4];
        return $row;
    }

    /**
     * @param array $array
     * @param key array 'value', 'optionLabel' in $array
     * @return array
     * ['label','name','value','optionLabel','selected','multiple','required']
     */
    public function selectBox($array)
    {
        $row = ['type'=>'select','for'=>$array[0],'id'=>$array[0],'name'=>$array[1],'multiple'=>$array[5],'required'=>$array[6]];
        $row['value']=$array[2];
        $row['optionLabel']=$array[3];
        $row['selected']=$array[4];
        return $row;
    }

    /**
     * @param array $array
     * @return array
     * ['label','name','accept','required']
     */
    public function fileUpload($array)
    {
        $this->encType = "multipart/form-data";
        $row = ['type'=>'file','for'=>$array[0],'id'=>$array[0],'name'=>$array[1],'accept'=>$array[2],'required'=>$array[3]];
        return $row;
    }

    /**
     * @param array $array
     * @return array
     * ['value']
     */
    public function reset($array)
    {
        $row = ['type'=>'reset', 'name'=>'reset', 'value'=>$array[0]];
        return $row;
    }

    /**
     * @param array $array
     * @return array
     * ['value']
     */
    public function submit($array)
    {
        $row = ['type'=>'submit', 'name'=>'submit', 'value'=>$array[0]];
        return $row;
    }

    /**
     * @param array $array
     * @return array
     * ['name','src']
     */
    public function buttonImage($array)
    {
        $row = ['type'=>'image','name'=>$array[0],'src'=>$array[1]];
        return $row;
    }

    /**
     * @param array $array
     * @return array
     * ['name','value']
     */
    public function hidden($array)
    {
        $row = ['type'=>'hidden','name'=>$array[0],'value'=>$array[1]];
        return $row;
    }
}

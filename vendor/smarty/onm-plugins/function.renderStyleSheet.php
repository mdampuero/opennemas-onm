<?php
/*
 * -------------------------------------------------------------
 * File:        function.renderStyleSheet.php
 * Get properties and render cascading style sheet in a file
 */
use \Onm\Settings as s;

function smarty_function_renderStyleSheet($params, &$smarty)
{

    $output = "";

     // Initialicing parameters
    $current = (isset($params['current']) ? $params['current'] : null);
    $items   = (isset($params['items']) ? $params['items'] : null);


    // Styles to print each item
    $rules = '';
    //content_id | title_catID | serialize(font-family:;font-size:;color:)
    if (is_array($items)) {
        foreach ($items as $k => $item) {
            $element = 'bgcolor_'.$current;
            $bgcolor = $item->getProperty($element);

            $element = 'title'."_".$current;
            $properties = $item->getProperty($element);

            if (!empty($properties)) {
                 $properties = json_decode($properties);

                if (!empty($properties)) {
                    // article#content-81088.onm-new h3.onm-new-title a
                    $rules .="article#content-{$item->pk_content} .nw-title a {\n";
                    foreach ($properties as $property => $value) {
                        if (!empty($value)) {
                                $rules .= "\t{$property}:{$value}; \n";
                        }
                    }
                    $rules .= "}\n";

                    $rules .="article#content-{$item->pk_content} {\n";
                    if (!empty($bgcolor)) {
                            $rules .= "\tbackground-color:{$bgcolor}; \n";
                    }
                    $rules .= "}\n";
                }
            }
        }

    }

    /*
    $file ="{$path}front-$current.css";
    $mtime = '?';
    if (file_exists($file)) {
        $mtime .= filemtime($file);
    }
    $handle = fopen($resource, "a");
    $output ="";
    if ($handle) {
        $datawritten = fwrite($handle, $rules);
        fclose($handle);
    } else {
        echo "There was a problem while trying to log your message.";
    }
    $output ="media="screen,projection" href="{$file}{$mtime}\" type="text/css" rel="stylesheet">";
    */
    $output ="<style type=\"text/css\"> {$rules} </style>";

    return $output;

}


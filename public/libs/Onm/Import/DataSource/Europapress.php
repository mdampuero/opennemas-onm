<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * (c) Copyright Mér Mai 25 17:07:03 2011 Fran Diéguez. All Rights Reserved.
*/
/*
 * class Europapress
 */
namespace Onm\Import\DataSource;

class Europapress {
    
    
    private $data = null;
    
    
    /**
    * Ensures that we always get one single instance
    *
    * @return object, the unique instance object 
    * @author Fran Dieguez <fran@openhsot.es>
    **/
    static public function getInstance($config)
    {

        if ((!self::$instance instanceof self) or
            (count(array_diff($this->config, $config)) > 0))
        { 
            self::$instance = new self($config); 
        } 
        return self::$instance;

    }
    
    /*
     * 
     * 
     * @param $$params
     */
    public function __get($propertyName)
    {
        switch ($propertyName) {
            
            case 'id':
                return (int) $this->data->CODIGO;
                break;
            
            case 'agencyID':
                return (string) $this->data->AGENCIA;
                break;
            
            case 'priority':
                return (string) $this->data->PRIORIDAD;
                break;
            
            case 'service':
                return (string) $this->data->SERVICIO;
                break;
            
            case 'category':
                //return 'as';
                
                
                return self::matchCategoryName((string) $this->data->SECCION);
                break;
            
            case 'informationType':
                return (string) $this->data->TIPOINFO;
                break;
            
            case 'key':
                return (string) $this->data->CLAVE;
                break;
            
            case 'created_time':
    
                $dateFormat = 'd/m/Y H:i:s';
                $originalDate = $this->data->FECHA.' '.$this->data->HORA;
                $date = \DateTime::createFromFormat($dateFormat,
                                                   $originalDate,
                                                   new \DateTimeZone('Europe/Madrid'));
                
                return $date;
                break;
            
            case 'pretitle':
                return (string) $this->data->ANTETITULO;
                break;
            
            case 'title':
                return (string) $this->data->TITULAR;
                break;
            
            case 'body':
                return nl2br((string) $this->data->CONTENIDO);
                break;
            
            case 'summary':
                return (string) $this->data->ENTRADILLA;
                break;
            
            case 'photos':
                return (array) $this->data->PHOTOS;
                break;
            
            case 'personajes':
                return (array) $this->data->PERSONAJES;
                break;
            
            case 'photos':
                return (array) $this->data->PHOTOS;
                break;
            
            case 'people':
                return (array) $this->data->PERSONAJES;
                break;
            
            case 'place':
                return (array) $this->data->LUGAR;
                break;
            
            case 'associatedDocs':
                return (array) $this->data->DOCS;
                break;
            
            case 'categories':
                return (array) $this->data->CATEGORIES;
                break;
            
            case 'dataCastID':
                return (array) $this->data->DATACASTID;
                break;
            
            case 'level':
                return (array) $this->data->LEVEL;
                break;
            
            case 'redactor':
                return (array) $this->data->FIRMA;
                break;
            
        }
    }
    
    /*
     * Return an array of localized categories
     * 
     * @param $arg
     */
    static public function getOriginalCategories()
    {
        return $original_categories =  array(
                                        'ECO' => _('Economy'),
                                        'POL' => _('Polytics'),
                                      );
    }
    
    /*
     * Retrieves a localized string of category from identifier
     * 
     * @param $arg
     */
    static public function matchCategoryName($categoryName)
    {
        if (empty($categoryName)) {
            throw new \ArgumentException;
        }
        
        $categories = self::getOriginalCategories();
        return $categories[(string)$categoryName];
    }
    
    
    
    /*
     * __construct()
     * @param $xmlFile, the XML file that contains information about an EP new
     */
    public function __construct($xmlFile) {
        
        $this->agencyName = __CLASS__;
        
        if(file_exists($xmlFile)) {
            $this->data = simplexml_load_file($xmlFile);
        } else {
            throw new \Exception(sprintf(_("File '%d' doesn't exists."), $xmlFile));
        }
        
        return $this;
    
    }
    
    /*
     * Returns the internal data, use with caution 
     */
    public function getData()
    {
        return $this->data;
    }
    

}


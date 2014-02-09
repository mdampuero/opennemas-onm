<?php

/*
 * This file is part of the onm package.
 * (c) 2009-2012 OpenHost S.L. <contact@openhost.es>
 *
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;

/**
 * Conect with the intance manager to create an instance from the opennemas webpage
 *
 * @package    Oh
 * @subpackage WebPage
 * @author     Toni Martínez <toni@openhost.es>
 **/
class Validator
{
    public $errors               = array();

    private $validateRules       = array();
    private $data                = array();
    private $defaultErrorMessage = "The {field} is not valid";

    /**
     * class constructor
     */
    public function __construct ($data, $instanceManager)
    {
        $this->data = $data;
        $this->instanceManager = $instanceManager;

        $this->validateRules = array(
            'instance_name' => array(
                'validator'=>'required, regex, badWords',
                'params' => array(
                    'regex' => array(
                        'pattern' => '/^[a-zñA-ZÑ0-9 ]{5,}$/',
                        'message' => _('Name of the newspaper must be longer than 5 characters')
                    )
                ),
            ),
            'subdomain' => array(
                'validator'=>'required, regex, badWords, InstanceNameInUse',
                'params' => array(
                    'regex' => array(
                        'pattern'=> '/^[a-zA-Z0-9\.]{4,}$/',
                        'message'=> _(
                            'Url must be longer than 4 characters and only must content characters and numbers'
                        )
                    ),
                    'checkService' => array(
                        'client'   => $this,
                        'function' => 'instances/checkinstancename',
                        'message'  => _('The url that you entered is already in use')
                    )
                ),
                'message' => _('Please enter a valid url')
            ),
            'user_email' => array(
                'validator'=>'required, regex, checkMailInUse',
                'params' => array(
                    'regex' => array(
                        'pattern'=>'/^([a-z0-9_\.\-\+]+)@([\da-z\.\-]+)\.([a-z\.]{2,6})$/',
                    )
                ),
                'message' => _('Please enter a valid email address')
            )
        );
    }

    private function getMessage($field)
    {
        if (isset($this->validateRules[$field]['message'])) {
            return $this->validateRules[$field]['message'];
        }
        return preg_replace('/{field}/', $field, $this->defaultErrorMessage);
    }

    public function validate()
    {
        foreach ($this->validateRules as $field => $rules) {
            if (count($rules['validator'])>0) {
                $validators = preg_split('/[\s,]+/', $rules['validator'], -1, PREG_SPLIT_NO_EMPTY);
                foreach ($validators as $validator) {
                    $function = "validate".ucfirst($validator);
                    $params = isset($rules['params'][$validator])?$rules['params'][$validator]:array();
                    // $t = time();
                    $this->$function($field,$params);
                }
            }
        }

        return $this->errors;
    }

    public function validateRequired($field, $params)
    {
        if (!isset($this->data[$field])) {
            if (isset($params['message'])) {
                $this->errors[$field][] = $params['message'];
            } else {
                $this->errors[$field][] = $this->getMessage($field);
            }
            return false;
        }
        return true;
    }

    public function validateRegex($field, $params)
    {
        if (!preg_match($params['pattern'], $this->data[$field])) {
            if (isset($params['message'])) {
                $this->errors[$field][] = $params['message'];
            } else {
                $this->errors[$field][] = $this->getMessage($field);
            }
            return false;
        }
        return true;
    }

    public function validateEqual($field, $params)
    {
        if (isset($params['field']) && $this->data[$field]!=$this->data[$params['field']]) {
            if (isset($params['message'])) {
                $this->errors[$field][] = $params['message'];
            } else {
                $this->errors[$field][] = $this->getMessage($field);
            }
            return false;
        }
        return true;
    }

    public function validateBadWords($field, $params)
    {
        $badWords  = array(
            'admin','user','macada','sandra', 'operator',
            'fran','alex','openhost','opennemas','prueba','test','probando',
            'testing', 'check', 'retrincos', 'drop', 'create', 'alter',
            'grant', 'asdf', 'qwert'
        );

        foreach ($badWords as $word) {
            similar_text($this->data[$field], $word, $percent);

            if (preg_match('/'.$word.'/i', '/'.$this->data[$field].'/', $badWords)
                || $percent > 70 ) {
                $this->errors[$field][] = _('The name you entered is not allowed. Please try a new one.').$field;
                return false;
            }
        }
        return true;
    }

    public function validateCheckMailInUse($field, $params)
    {
        $exists = $this->instanceManager->checkMailExists($field);

        if ($exists) {
            return array(_('The email that you entered is already in use'));
        }

        return true;
    }

    public function validateInstanceNameInUse($field, $params)
    {
        $exists = $this->instanceManager->checkInstanceExists($field);

        if ($exists) {
            return array(_('The url that you entered is already in use'));
        }

        return true;
    }
}

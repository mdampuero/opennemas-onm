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
    public function __construct($data, $instanceManager)
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
        $badWords  = $this->getBadWords();

        foreach ($badWords as $word) {
            similar_text($this->data[$field], $word, $percent);

            if (preg_match('/'.$word.'/i', '/'.$this->data[$field].'/', $badWords)
                || $percent > 80 ) {
                switch ($field) {
                    case 'instance_name':
                        $this->errors[$field][] = _('Your newspaper name cointains disallowed words.');
                        break;

                    case 'subdomain':
                        $this->errors[$field][] = _('Your desired address contains disallowed words.');
                        break;

                    case 'user_email':
                        $this->errors[$field][] = _('Your user name contains disallowed words.');
                        break;

                    default:
                        $this->errors[$field][] = _('There was an error while validating your data against disallowed words.');
                        break;
                }
                return false;
            }
        }
        return true;
    }

    public function validateCheckMailInUse($field, $params)
    {
        $exists = $this->instanceManager->emailExists($this->data[$field]);

        if ($exists) {
            $this->errors['user_email'][] = _('The email that you entered is already in use');
            return false;
        }

        return true;
    }

    public function validateInstanceNameInUse($field, $params)
    {
        $exists = $this->instanceManager->instanceExists($this->data[$field]);

        if ($exists) {
            $this->errors['subdomain'][] = _('The url that you entered is already in use');
            return false;
        }

        return true;
    }

    public function getBadWords()
    {
        return [
            '4r5e', '5h1t', '5hit', 'God', 'a55', 'a_s_s', 'admin',
            'alter', 'anal', 'anus', 'ar5e', 'arrse', 'arse', 'asdf', 'ass',
            'ass-fucker', 'asses', 'assfucker', 'assfukka', 'asshole',
            'assholes', 'asswhole', 'b!tch', 'b00bs', 'b17ch', 'b1tch',
            'ballbag', 'balls', 'ballsack', 'bastard', 'beastial',
            'beastiality', 'bellend', 'bestial', 'bestiality', 'bi+ch',
            'biatch', 'bitch', 'bitcher', 'bitchers', 'bitches', 'bitchin',
            'bitching', 'bloody', 'blow job', 'blowjob', 'blowjobs', 'boiolas',
            'bollock', 'bollok', 'boner', 'boob', 'boobs', 'booobs', 'boooobs',
            'booooobs', 'booooooobs', 'breasts', 'buceta', 'bugger', 'bum',
            'bunny fucker', 'butt', 'butthole', 'buttmuch', 'buttplug', 'c0ck',
            'c0cksucker', 'carpet muncher', 'cawk', 'check', 'chink', 'cipa',
            'cl1t', 'clit', 'clitoris', 'clits', 'cnut', 'cock', 'cock',
            'cock-sucker', 'cockface', 'cockhead', 'cockmunch', 'cockmuncher',
            'cocks', 'cocksuck ', 'cocksucked ', 'cocksucker', 'cocksucking',
            'cocksucks ', 'cocksuka', 'cocksukka', 'cok', 'cokmuncher',
            'coksucka', 'coon', 'core', 'cox', 'crap', 'create', 'cum', 'cum',
            'cummer', 'cumming', 'cums', 'cumshot', 'cunilingus',
            'cunillingus', 'cunnilingus', 'cunt', 'cuntlick ', 'cuntlicker ',
            'cuntlicking ', 'cunts', 'cyalis', 'cyberfuc', 'cyberfuck ',
            'cyberfucked ', 'cyberfucker', 'cyberfuckers', 'cyberfucking ',
            'd1ck', 'damn', 'dick', 'dick', 'dickhead', 'dildo', 'dildos',
            'dink', 'dinks', 'dirsa', 'dlck', 'dog-fucker', 'doggin',
            'dogging', 'donkeyribber', 'doosh', 'drop', 'duche', 'dyke',
            'ejaculate', 'ejaculated', 'ejaculates ', 'ejaculating ',
            'ejaculatings', 'ejaculation', 'ejakulate', 'f u c k e r',
            'f u c k', 'f4nny', 'f_u_c_k', 'fag', 'fagging', 'faggitt',
            'faggot', 'faggs', 'fagot', 'fagots', 'fags', 'fanny', 'fannyflaps',
            'fannyfucker', 'fanyy', 'fatass', 'fcuk', 'fcuker', 'fcuking',
            'feck', 'fecker', 'felching', 'fellate', 'fellatio', 'fingerfuck ',
            'fingerfucked ', 'fingerfucker ', 'fingerfuckers', 'fingerfucking ',
            'fingerfucks ', 'fistfuck', 'fistfucked ', 'fistfucker ',
            'fistfuckers ', 'fistfucking ', 'fistfuckings ', 'fistfucks ',
            'flange', 'fook', 'fooker', 'fuck', 'fucka', 'fucked',
            'fucker', 'fuckers', 'fuckhead', 'fuckheads', 'fuckin', 'fucking',
            'fuckings', 'fuckingshitmotherfucker', 'fuckme ', 'fucks',
            'fuckwhit', 'fuckwit', 'fudge packer', 'fudgepacker', 'fuk',
            'fuker', 'fukker', 'fukkin', 'fuks', 'fukwhit', 'fukwit', 'fux',
            'fux0r', 'gangbang', 'gangbanged ', 'gangbangs ', 'gaylord',
            'gaysex', 'goatse', 'god-dam', 'god-damned', 'goddamn', 'goddamned',
            'grant', 'hardcoresex ', 'hell', 'heshe', 'hoar', 'hoare', 'hoer',
            'homo', 'hore', 'horniest', 'horny', 'hotsex', 'jack-off ',
            'jackoff', 'jap', 'jerk-off ', 'jism', 'jiz ', 'jizm ', 'jizz',
            'kawk', 'knob', 'knobead', 'knobed', 'knobend', 'knobhead',
            'knobjocky', 'knobjokey', 'kock', 'kondum', 'kondums', 'kum',
            'kummer', 'kumming', 'kums', 'kunilingus', 'l3i+ch', 'l3itch',
            'labia', 'lmfao', 'lust', 'lusting', 'm0f0', 'm0fo', 'm45terbate',
            'ma5terb8', 'ma5terbate', 'macada', 'masochist', 'master-bate',
            'masterb8', 'masterbat*', 'masterbat3', 'masterbate',
            'masterbation', 'masterbations', 'masturbate', 'mo-fo', 'mof0',
            'mofo', 'mothafuck', 'mothafucka', 'mothafuckas',
            'mothafuckaz', 'mothafucked ', 'mothafucker', 'mothafuckers',
            'mothafuckin', 'mothafucking ', 'mothafuckings', 'mothafucks',
            'mother fucker', 'motherfuck', 'motherfucked', 'motherfucker',
            'motherfuckers', 'motherfuckin', 'motherfucking', 'motherfuckings',
            'motherfuckka', 'motherfucks', 'muff', 'mutha', 'muthafecker',
            'muthafuckker', 'muther', 'mutherfucker', 'n1gga', 'n1gger',
            'nazi', 'nigg3r', 'nigg4h', 'nigga', 'niggah', 'niggas', 'niggaz',
            'nigger', 'niggers ', 'nob jokey', 'nob', 'nobhead', 'nobjocky',
            'nobjokey', 'numbnuts', 'nutsack', 'openhost', 'opennemas',
            'operator', 'orgasim ', 'orgasims ', 'orgasm', 'orgasms ', 'p0rn',
            'pawn', 'pecker', 'penis', 'penis', 'penisfucker', 'phonesex',
            'phuck', 'phuk', 'phuked', 'phuking', 'phukked', 'phukking',
            'phuks', 'phuq', 'pigfucker', 'pimpis', 'piss', 'pissed', 'pisser',
            'pissers', 'pisses ', 'pissflaps', 'pissin ', 'pissing', 'pissoff ',
            'poop', 'porn', 'porno', 'pornography', 'pornos', 'prick',
            'pricks ', 'probando', 'pron', 'prueba', 'pube', 'pusse', 'pussi',
            'pussies', 'pussy', 'pussys ', 'qwert', 'rectum', 'retard',
            'retrincos', 'rimjaw', 'rimming', 'root', 's hit', 's.o.b.', 's_h_i_t',
            'sadist', 'schlong', 'screwing', 'scroat', 'scrote',
            'scrotum', 'semen', 'sex', 'sex', 'sh!+', 'sh!t', 'sh1t', 'shag',
            'shagger', 'shaggin', 'shagging', 'shemale', 'shi+', 'shit',
            'shitdick', 'shite', 'shited', 'shitey', 'shitfuck', 'shitfull',
            'shithead', 'shiting', 'shitings', 'shits', 'shitted', 'shitter',
            'shitters ', 'shitting', 'shittings', 'shitty ', 'skank', 'slut',
            'sluts', 'smegma', 'smut', 'snatch', 'son-of-a-bitch', 'spac',
            'spunk', 'staging', 't1tt1e5', 't1tties', 'teets', 'teez', 'test',
            'testical', 'testicle', 'testing', 'tit', 'titfuck', 'tits',
            'titt', 'tittie5', 'tittiefucker', 'titties', 'tittyfuck',
            'tittywank', 'titwank', 'tosser', 'turd', 'tw4t', 'twat',
            'twathead', 'twatty', 'twunt', 'twunter', 'user', 'v14gra',
            'v1gra', 'vagina', 'viagra', 'vulva', 'w00se', 'wang', 'wank',
            'wanker', 'wanky', 'whoar', 'whore', 'willies', 'willy', 'xrated',
            'xxx',
        ];
    }
}

<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Instance;

class Validator
{
    /**
     * The list of errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Initializes the validator
     *
     * @param type variable Description
     *
     * @return type Description
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Returns the list of errors.
     *
     * @return array The list of errors.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Checks if the validator detected errors while validating an instance.
     *
     * @return boolean True if the validator detected errors while validating
     *                 the instance. False, otherwise.
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Validates an instance.
     *
     * @param Instance $instance The instance to validate.
     */
    public function validate($instance)
    {
        $this->validateInternalName($instance->internal_name);
        $this->validateInternalNameInUse($instance->internal_name);
        $this->validateDomains($instance->domains);
        $this->validateEmail($instance->contact_mail);
        $this->validateEmailInUse($instance->contact_mail);
    }

    /**
     * Returns the list of bad words.
     *
     * @return array The list of bad words.
     */
    protected function getBadWords()
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

    /**
     * Validates the instance domains.
     *
     * @param array The instance domains.
     */
    protected function validateDomains($domains)
    {
        foreach ($domains as $domain) {
            if (empty($domain) || !preg_match('/^[a-zA-Z0-9\.]{4,}$/', $domain)) {
                $this->errors['domain'][] =
                    _('Url must be longer than 4 characters and only must content characters and numbers');
            }
        }
    }

    /**
     * Validates the instance email.
     *
     * @param string $email The instance email.
     */
    protected function validateEmail($email)
    {
        if (empty($email) || !preg_match('/^([a-z0-9_\.\-\+]+)@([\da-z\.\-]+)\.([a-z\.]{2,6})$/', $email)) {
            $this->errors['email'][] = _('Please enter a valid email address');
        }
    }

    /**
     * Checks if the instance email is already in use.
     *
     * @param string $email The instance email.
     */
    protected function validateEmailInUse($email)
    {
        try {
            $this->em->getRepository('Instance')
                ->findOneBy(sprintf('contact_mail = "%s"', $email));

            $this->errors['email'][] =
                _('The email that you entered is already in use');
        } catch (\Exception $e) {
        }
    }

    /**
     * Validates the instance internal name.
     *
     * @param string $name The instance internal name.
     */
    protected function validateInternalName($name)
    {
        if (empty($name) || !preg_match('/^[a-zñA-ZÑ0-9 ]{5,}$/', $name)) {
            $this->errors['internal_name'][] =
                _('Name of the newspaper must be longer than 5 characters');
        }

        if (!$this->validateBadWords($name)) {
            $this->errors['internal_name'][] =
                _('Your newspaper name contains disallowed words.');
        }
    }

    /**
     * Checks if the instance internal name is already in use.
     *
     * @param string $name The instance internal name.
     */
    protected function validateInternalNameInUse($name)
    {
        try {
            $this->em->getRepository('Instance')
                ->findOneBy(sprintf('internal_name = "%s"', $name));

            $this->errors['internal_name'][] = _('The url that you entered is already in use');
        } catch (\Exception $e) {
        }
    }

    /**
     * Validates if a field has an invalid value from the list of bad words.
     *
     * @param string $value The value to validate.
     *
     * @return boolean True if the value is valid. False, otherwise.
     */
    protected function validateBadWords($value)
    {
        $badWords  = $this->getBadWords();

        $value = mb_strtolower($value);
        foreach ($badWords as $word) {
            similar_text($value, $word, $percent);

            if ($percent > 80) {
                return false;
            }
        }

        return true;
    }
}

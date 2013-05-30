watch( '((app/.*\.php)|(public/core/.*\.php)|(vendor/Onm/.*\.php)|(app/tests/*/*.php))$' )  {|md|
    system("phpunit -c app/phpunit.xml.dist")
}

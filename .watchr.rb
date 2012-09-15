watch( '((app/.*\.php)|(public/core/.*\.php)|(vendor/Onm/.*\.php))$' )  {|md|
    system("phpunit -c app/phpunit.xml.dist")
}

#!/bin/sh

if [ "$1" ]
then
	if [ $1 == 'native' ]
	then
		../vendor/bin/phpunit-randomizer -c ../phpunit.xml
	else
		../vendor/bin/phpunit-randomizer --order seed:$1 -c ../phpunit.xml
	fi
else
	../vendor/bin/phpunit-randomizer --order rand -c ../phpunit.xml
fi

# Running tests
In order to run the tests, you have to configure your environment:
 1. Create a database for the tests
 2. Install required libraries with Composer

**PLEASE NOTE** There is no need to setup a local installation of Joomla!, we are going to directly fetch it from Github using [git submodules](http://git-scm.com/docs/git-submodule).

### Create a database for the tests
We need a database where we will create all the tables required by the tests. 
The best thing is to provide an empty database, the test suite will create all the needed tables.
Once created, you have to copy the file `Tests/config.dist.php`, rename it to `Tests/config.php` and update its contents, providing the following connection details:
```php
$fofTestConfig = array(
	// Connection details for our local database
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => 'root',
    'db'       => 'unittesting3',
);
```
### Install required libraries with Composer
You simply have to run `php composer.phar install` in order to install all the required libraries.  
If you want to update the composer dependencies, run `php composer.phar update`.

### How to run tests
Inside the `Tests` folder you can find a shell file named `run-tests.sh`.
If you execute it, by default you'll run the whole test suite in random order: in this way we can double check if there is any entagled test polluting the whole suite.
`./run-tests.sh`
```
Time: 3.63 seconds, Memory: 22.75Mb

OK, but incomplete, skipped, or risky tests!
Tests: 951, Assertions: 1954, Incomplete: 3.

Randomized with seed: 6625
```

You can force the execution of the tests with a specific seed, by passing it as argument: `./run-tests.sh 6625`. This is very useful when you find that the suite is failing if tests are ran in a specific order
```
Time: 3.63 seconds, Memory: 22.75Mb

OK, but incomplete, skipped, or risky tests!
Tests: 951, Assertions: 1954, Incomplete: 3.

Randomized with seed: 6625
```

Finally, you can revert to the regular order using the `native` argument. In this case there is no randomization and the tests are ran in the same order as they are coded:
`./run-tests.sh native`
```
Time: 3.63 seconds, Memory: 22.75Mb

OK, but incomplete, skipped, or risky tests!
Tests: 951, Assertions: 1954, Incomplete: 3.
```

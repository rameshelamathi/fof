# Running tests

## Preparing your environment

Run `composer install` on the main folder of the repository to install all development dependencies.

Create a new Joomla 3.x site on your local server. You must be able to have direct filesystem and database access from your local PHP installation. Install the default / recommended sample data.

**IMPORTANT!** Use the database prefix `jos_`.

Then rename the `config.dist.php` file into `config.php` inside the Tests folder. Edit the file to match the location of your Joomla site and its database connection information. 

## Running the tests

Run the `run-tests.sh` shell script in the Tests folder.

Alternatively, you can execute the unit tests using the `phpunit.xml` file in the repository's root.
To run:
- Open command line and change directory to wherever "SubmitInfo.php" is.
- Start the php server by running "php -S localhost:8080" (8080 can be changed)
- Open web browser and go to "http://localhost:8080/SubmitInfo.php"
Pre-requisites:
- This project was coded in PHP v7.4.9.
- Have MongoDB installed and set up. Have PHP installed and set up to be able to use MongoDB.
- - https://pecl.php.net/package/mongodb for downloading the MongoDB.dll file and it's dependencies.
- - https://github.com/mongodb/mongo-php-driver/releases/tag/1.19.3 for downloading MongoDB PHP extension.
- - https://www.mongodb.com/docs/manual/installation/ for installing MongoDB.
- The program uses mongodb://localhost:27017 as the database client.
- Have a database in MongoDB called "local".
- Have a collection in MongoDB called "person", this is where the submitted info will be stored.

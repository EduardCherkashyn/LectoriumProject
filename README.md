
````
composer install
````
Docker
```
docker-compose up -d

docker-compose exec php-fpm bash
```
Project is up at port :8000

Inside container exec:

```
php bin/console d:d:c
php bin/console d:m:m
````


To start a new season put all the courses to the
```` 
 /public/Courses/Courses.txt
 ````
 start each course with a new line.
 
 Example:
 
 ````
 Php
 Java
 JavaScript
 ````
 
 Then update file, each mentor start with a new line
 
 ````
 public/Mentors/Mentrors.txt
 ````
 
 Example:
 ````
 email:password:name:course
 email1:password1:name1:course1
 ````
 
 Do the same thing for the students
 ````
 public/Students/Students.txt
 ````
 
 Hit the command to create a new season:
 
 ````
 php bin/console app:season-create
 ````
# Liquibase 3.5.1
* When run data.xml: Unexpected error running Liquibase: Lexical error at line 49, column 33.  Encountered: "\u00e9" (233), after : ""
* This is é | latin small letter e with acute (U+00E9). Possible encoding problems??
* Report problem to Liquibase

# Liquibase commands

## Maintenance Commands
* version
* tag <tag>
* tagExists <tag>
* validate

## Database Rollback Commands
* rollback <tag>
* rollbackSQL

# Database examples
* MySQL: The [classicmodels](http://www.mysqltutorial.org/mysql-sample-database.aspx) database is a retailer of scale models of classic cars database. 
* Sqlite: [Chinook](http://www.sqlitetutorial.net/sqlite-sample-database/) sample database.

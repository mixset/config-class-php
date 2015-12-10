##Config.class.php 
-----------------

Script allows you to easy manage your ini files.
All you need to do to start using this class is to initialize it like in **index.php** file. 

How class is built?
-----------------

Class has 6 method and 2 properities. 

Methods:
- `init()` basic initializing class
- `setConfig()` public methods, which allows to add new record to ini file
- `getConfig()` public method returns value of key given as a argument
- `isKeyExist()` private method, that check whether key exists in ini file
- `addLog()` private method, which imitates the most simple logger method

Properties:
- `$config` private property, that is consisted of three keys: *fileLog*, *logger*, *extension* (check config.class.php)
- `$file` private property, that includes name of *ini* file

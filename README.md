ZfMuscle
=======================

Introduction
------------
This is a compound module that comes shipped with basic web application functionality.
This application is meant to be used as a major starting place for those
looking to get dirty with ZF2.


The What and Why ?
------------
#### The What
ZfMuscle is a ```Zend Framework 2 Administrative``` module built to help you not worry how to restrict access to pages and limit user actions.
It uses the ```ZfcUserDoctrineORM by @ZF-Commons and BjyAuthorize by @bjyoungblood``` modules as based functionalities; leaving the ```ZfcUser by @ZF-Commons``` module for your own modifications.
Essentially, It is a fully styled/template(d) Back-end module that manages not just access to your custom Back-end modules, but application wide.

#### The Why
As I am always having to work on projects that requires restricted access to resources and having to recode and hard code all these is such a pain, I thought to myself,  
wouldn't it be cool if there was a module somewhere out there that does all these ```(I know, there's BjyAuthorize for ACL by @bjyoungblood, ZfcUser by @ZF-Commons for user management)``` as a package?

A flexible and scalable module that has all these shipped with it? "It'd be cool" my answer after giving it some thought.

Still don't see the WHY ? Well, how about you dig right in and decide for yourself if it is worth all the effort.



Installation
------------

Using Composer (recommended)
----------------------------
Add the following lines to your "composer.json" file

```
//...
"require": {
    "awoyotoyin/zf-muscle": "dev-master"
}
//...
```


Configuration
-------------
Add "ZfMuscle", "DoctrineModule", "DoctrineORMModule", "AssetManager", "ZfcBase", "ZfcUser", 
"ZfcUserDoctrineORM" and "BjyAuthorize" to your application.config.php file.

Example:
```
//...
'modules' => array(
    //...
    'AssetManager',
    'ZendDeveloperTools',
    'DoctrineModule',
    'DoctrineORMModule',
    'ZfcBase',
    'ZfcUser',
    'ZfcUserDoctrineORM',
    'Application',
    'BjyAuthorize',
    'ZfMuscle'
    //...
)
//...
```
Copy the files having ```.dist``` extension in config to your application autoload and remove the ```.dist``` extensions

### Note:
The ```bjyauthorize.global.php.dist``` file allow you to make use of the embedded user permission.


Styling And Other Assets
------------------------
To use the assets meant for this module,
copy/move the
```
//...
public/
    zf-muscle/
    //...
```

directory to your main application public folder, so your folder structure becomes something like:
```
//...
public/
    //...
    zf-muscle/
            css/
            img/
            js/
            //...
```

### URLS
After the above configurations, navigate to:
```
your-host/zfmuscle
```
and you'll be presented an installation form. Complete the form and set your application ready to launch.

Permission Settings
-------------------
After installation, goto:
```
System > Config
```
and click on the ```update``` button for ```System Role Resources``` to automatically import all your controllers and routes (resources).


```TODO:``` Tell the application to use the assets providing the correct paths to them as opposed to moving / copying them around. This could be achieved with the AssetManager Module though

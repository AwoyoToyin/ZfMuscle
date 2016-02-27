ZfMuscle
=======================

Introduction
------------
This is a compound module that comes shipped with basic web application functionality.
This application is meant to be used as a major starting place for those
looking to get dirty with ZF2.


The What and Why ?
------------------
#### The What
ZfMuscle is a ```Zend Framework 2 Administrative``` module built to help you not worry how to restrict access to pages and limit user actions.
It uses the ```ZfcUserDoctrineORM by @ZF-Commons and BjyAuthorize by @bjyoungblood``` modules as based functionality; leaving the ```ZfcUser by @ZF-Commons``` module for your own modifications.
Essentially, It is a fully styled/template(d) Back-end module that manages not just access to your custom Back-end modules, but application wide.

#### The Why
As I am always having to work on projects that requires restricted access to resources and having to recode and hard code all these is such a pain,
I thought to myself, wouldn't it be cool if there was a module somewhere out there that does all these ```(I know, there's BjyAuthorize for ACL by @bjyoungblood, ZfcUser by @ZF-Commons for user management)``` as a package?

A flexible and scalable module that has all these shipped with it? "It'd be cool" my answer after giving it some thought.

Still don't see the WHY ? Well, how about you dig right in and decide for yourself if it is worth all the effort.


Demo Preview
------------
See a demo preview at <https://zfmuscletest.herokuapp.com/>
```TODO:``` install application and open up a demo admin login and password


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
1. Add "ZfMuscle", "DoctrineModule", "DoctrineORMModule", "AssetManager", "ZfcBase", "ZfcUser", "ZfcUserDoctrineORM" and "BjyAuthorize" to your application.config.php file.

    Example:
    ```
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
        'ZfMuscle',
        ...
    )
    ```
2. Copy the files having ```.dist``` extension in config to your application autoload and remove the ```.dist``` extensions.

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
```TODO:``` Tell the application to use the assets providing the correct paths to them as opposed to moving / copying them around. This could be achieved with the AssetManager Module though

Important to Note
-----------------
1. This module essentially takes over the control of your application.
2. It has an installation wizard that you are presented with on installation.
3. It uses the route guard in BjyAuthorize module and hence, all your routes definition must have a default.

    Example:
    ```
    ...
    'edit' => [
       'type' => 'Segment',
       'options' => [
           'route' => '/edit[/:id]',
           'constraints' => [
               'id' => '[0-9]*',
           ],
           'defaults' => [
               'controller' => 'zfmuscle-user',
               'action'     => 'register',
           ],
       ],
    ],
    ...
    ```
4. Since the application automatically imports all defined routes in all loaded modules,
modules without routers &/or routes defined don't need to checked for routes. Hence, there is a little
configuration provided to skip such modules.

To enlist a module to be skipped during this import, open the ```config/autoload/zfmuscle.global.php``` file
and under the ```'skip_modules'``` key, define such a module.

    Example:
    ```
    ...
    // list of modules to skip for acl
    'skip_modules' => [
       'ZendDeveloperTools',
       'DoctrineModule',
       'DoctrineORMModule',
       'AssetManager',
       'ZfcUserDoctrineORM',
       'BjyAuthorize',
       'ZfcBase',
       ...
    ],
    ...
    ```

Permission Settings
-------------------
```NOTE``` This module uses the Route Guard ACL from BjyAuthorize module
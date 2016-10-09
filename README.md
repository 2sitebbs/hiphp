# HiPHP
HiPHP is a lightweight php framework, which based on <strong>MVC</strong> and <strong>DAO</strong>.


## Features
* <em>Small</em>: The core code size is <strong>75.7KB</strong>, and only <strong>25.4KB</strong> after zip.
* <em>Quick</em>: You can easily build a page who can run in less than 100ms.
* <em>Easy</em>: You can read the all core code and build projects with it in an hour.
* <em>Flexible</em>: You can use any original PHP code with HiPHP, but not depends on it too much.


## For who ?
* Fresh PHPers.
* F2E guys, those who want learn some php and mysql skills.
* Site developers who want build the whole site by himself.
* Java developers who want use php to build a site.
* Developers who need a simple php framework.


## Simple DAO functions
<pre>
//add new user
$newUser = array(
    'age' => 10,
    'sex' => 0,
);
$res = <strong>$dao_write->addUser($newUser)</strong>;
print_r($res);

//get user's data from mysql
$conditions = array(
    'age' => '> 10',    //年龄大于10岁
    'sex' => 1,         //性别 1
);
$orderBy = 'name desc';
$limit = "2,10";
$arrUsers = <strong>$dao_read->getUser($conditions, $orderBy, $limit)</strong>;
print_r($arrUsers);

//update user's data by uid
$newData = array(
    'age' => 20,
);
$uid = 10;
$res = <strong>$dao_write->updateUserByUid($uid, $newData)</strong>;
print_r($res);

//delete user's data by uid
$uid = 10;
$res = <strong>$dao_write->deleteUserByUid($uid)</strong>;
print_r($res);
</pre>


## Directory tree of HiPHP
<pre>
│── core
│   │── dao
│   └── inc
│── v1.0
│   │── admin
│   │   │── bin
│   │   │── controller
│   │   │   └── test
│   │   │── dao
│   │   │── db
│   │   │── inc
│   │   └── www
│   │       │── plugins
│   │       │── theme
│   │       │   └── default
│   │       │       │── css
│   │       │       │── js
│   │       │       └── views
│   │       │           │── default
│   │       │           │── demo
│   │       │           └── layout
│   │       └── upload
│   └── home
│       │── bin
│       │── controller
│       │   └── test
│       │── dao
│       │── db
│       │── inc
│       └── www
│           │── plugins
│           │── theme
│           │   └── default
│           │       │── css
│           │       │── js
│           │       └── views
│           │           │── default
│           │           │── demo
│           │           └── layout
│           └── upload
└── www
    └── v1.0
        │── admin -> ../../v1.0/admin/www/
        └── home -> ../../v1.0/home/www/
</pre>


## Rewrite rules for nginx
    if (!-e $request_filename) {
        rewrite ^/(\w+)_(\w+)\.html$ /?controller=$1&action=$2 last;
        rewrite ^/(\w+)/(\w+)_(\w+)\.html$ /?controllergroup=$1&controller=$2&action=$3 last;

        ## REST API support
        rewrite ^/(\w+)$ /?controller=$1&action=index last;
        rewrite ^/(\w+)\/(\w+)$ /?controller=$1&action=index&id=$2 last;
        rewrite ^/(\w+)\/search\/(\w*)$ /?controller=$1&action=index&keyword=$2 last;
    }


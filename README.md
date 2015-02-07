# HiPHP
HiPHP is a lightweight php framework, which base on <strong>MVC</strong> and <strong>DAO</strong>.


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


## HiPHP基本目录结构
<pre>
│── core
│   │── dao
│   └── inc
│── v1.0
│   │── admin
│   │   │── bin
│   │   │── controller
│   │   │── dao
│   │   │── db
│   │   │── inc
│   │   │── test
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
│       │── dao
│       │── db
│       │── inc
│       │── test
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

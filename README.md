# HiPHP
HiPHP is a lightweight php framework, which base on MVC and DAO.


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

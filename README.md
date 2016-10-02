Manala
======

[![Build Status](https://travis-ci.org/manala/manala.svg?branch=master)](https://travis-ci.org/manala/manala)

#### :warning: This project is still in progress, use it carefully.

Installation
-------------

#### Download:
```sh
$ curl -L https://github.com/manala/manala/releases/download/v0.1.1/manala.phar > /usr/local/bin/manala
$ chmod +x /usr/local/bin/manala
```

#### Manual build:
```sh
$ git clone git@github.com:manala/manala
$ make build
$ mv manala.phar /usr/local/bin/manala
$ chmod +x /usr/local/bin/manala
```

Usage
-----

#### Setup
```
$ manala setup ~/my-awesome-project
```

#### Diff

Get the diff:
```
$ manala diff ~/my-awesome-project
```

Apply the diff:
```
$ cd ~/my-awesome-project
$ manala diff | git apply
```

License
-------

This project is licensed under MIT.  
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.

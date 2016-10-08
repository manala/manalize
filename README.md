Manalize
=========

[![Build Status](https://travis-ci.org/manalize/manalize.svg?branch=master)](https://travis-ci.org/manala/manalize)

#### :warning: This project is still in progress, use it carefully.

Installation
-------------

#### Download:
```sh
$ curl -L https://github.com/manala/manalize/releases/download/v0.1.1/manalize.phar > /usr/local/bin/manalize
$ chmod +x /usr/local/bin/manalize
```

#### Manual build:
```sh
$ git clone git@github.com:manala/manalize
$ cd manalize
$ make build
$ mv manalize.phar /usr/local/bin/manalize
$ chmod +x /usr/local/bin/manalize
```

Usage
-----

#### Setup
```
$ manalize setup ~/my-awesome-project
```

#### Diff

Get the diff:
```
$ manalize diff ~/my-awesome-project
```

Apply the diff:
```
$ cd ~/my-awesome-project
$ manalize diff | git apply
```

License
-------

This project is licensed under MIT.  
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.

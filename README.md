Manalize
=========

[![Build Status](https://travis-ci.org/manala/manalize.svg?branch=master)](https://travis-ci.org/manala/manalize)

This project provides ready-to-use environments for various projects.

At this moment, provided environments are based on Vagrant and provisioned through [Manala ansible roles](http://www.manala.io/).  
Some Docker based implementations are planed and should appear really soon.

Why?
----

Because we are too lazy for manually setting up local environments for each project we have to work on.  
In short, we need to:

- Work on any new/existing project from any platform in minuts
- Keep a local environment consistent across projects (practices, tools)
- Enable/disable support for languages, packages or any various utility as well
- Have a local environment as close as possible from the production one
- Destroy/rebuild an environment as needed

What's inside?
--------------

- [Composer Semver](https://github.com/composer/semver)
- [Nikic iter](https://github.com/nikic/iter)
- [Guzzle](https://github.com/guzzle/guzzle)
- The Symfony [Console](https://github.com/symfony/console), [Process](https://github.com/symfony/process), [Filesystem](https://github.com/symfony/filesystem), [Yaml](https://github.com/symfony/yaml) and [Stopwatch](https://github.com/symfony/stopwatch) components

Prerequisites
-------------

- [PHP](http://php.net) 7.0+
- [Vagrant](https://www.vagrantup.com/) 1.8.4+
- [Vagrant Landrush](https://github.com/vagrant-landrush/landrush) 1.0+
- [VirtualBox](https://www.virtualbox.org/) 5.0.20+

Installation
------------

#### Using the installer (recommended):
```
$ curl -LSs https://raw.githubusercontent.com/manala/manalize/master/installer.php | php
```

#### Using composer:
```
$ composer global require manala/manalize
```

#### Using git:
```
$ git clone git@github.com:manala/manalize
$ cd manalize
$ make build
$ mv manalize.phar /usr/local/bin/manalize
$ chmod a+x /usr/local/bin/manalize
```

Usage
-----

### Checking your host requirements

Before using `manalize`, you need to ensure that your host is ready. It can easily be achieved by running the following command:

```sh
$ manalize check:requirements
```

It will give you a list of requirements and recommendations to apply, sort as you can install/update packages depending on your needed, the current state of your host.

### Setting up your environment

Given you have a web project that you clone for the first time and you need to run locally, just use the `setup` command:

```sh
$ manalize setup ~/my-awesome-project
```

This command interactively configures the virtual machine for your project.  
Once this step done, your environment is fully configured. Some files have been added to your project:

- A `Vagrantfile`
- A `Makefile` including some useful tasks that you'll need to use throughout your project
- An `ansible/` directory containing all the configuration related to the VM provisioning
- Eventually some files specific to the chosen environment

At this point, the VM can be provisioned using the following command:

```sh
$ make setup
```

### Working with your environment

Once the [`setup`](#setting-up-your-environment) process is finished (may take a few minuts), your environment is operational and your VM is running.  
To manage it and work with, just use the `vagrant` command-line tool as usual:

```sh
$ vagrant up/halt/reload/ssh
```

### Keeping your environment up-to-date

Given your project's environment is there and your VM works well, its configuration is sticked to what we provided at the moment you created it.  
Since the [manala ansible roles](http://manala.io/) evolve (and the corresponding templates as well), you may want to be aware of the additions and important changes made to, then update your environment accordingly.

To do so, there's two commands to be aware of: `self-update` and `diff`.

#### self-update

```sh
$ manalize self-update
```

Running this command updates your `manalize` binary to the latest release, coming with the latest configuration templates.
After that, you can safely use the `diff` command as shown below.

#### diff

The `diff` command allows you to get a patch representing the diff between your current project configuration and the ones that would have been provided by your current version of the `manalize` binary.

Getting the diff:
```
$ manalize diff ~/my-awesome-project
```

Getting the diff for applying the patch immediately:
```
$ cd ~/my-awesome-project
$ manalize diff | git apply
```

Getting the diff for applying the patch later:
```
$ cd ~/my-awesome-project
$ manalize diff > manalize.patch
$ git apply manalize.patch
```

_Note:_
  
Be careful when applying the patch, any custom change made to your environment configuration will be erased.  
To minimize risks, we recommend you to look at the patch before trying to apply it. 

### Updating an existing environment without immediatly altering its configuration

Sometimes, it can be useful to setup the environment without affecting the existing project files nor adding new ones, when migrating a project which already uses Vagrant and/or Ansible for instance. 

The following command will only update the `ansible/.manalize` metadata file from what you will configure:

```
$ manalize setup --no-update ~/my-awesome-project
```

So you can then apply a patch provided by the [diff](#diff) command.

Troubleshooting
---------------

Before all, please [check your requirements](#checking-your-host-requirements) command to ensure that your issue doesn't come from your host.  

If it doesn't, please consider [opening an issue](https://github.com/manala/manalize/issues/new) on this repository.
We use github issues for tracking bugs, feature requests and ensuring support.

License
-------

This project is licensed under MIT.  
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.

Author information
------------------

Manala (http://www.manala.io/)

> ### *Officially abandoned : head to [SonarQube](http://www.sonarqube.org/) with the [PHP Plugin](http://docs.sonarqube.org/display/PLUG/PHP+Plugin) to ensure efficient code quality assurance.*

[![Version](http://img.shields.io/packagist/v/clorichel/scqat.svg?style=flat-square)](https://packagist.org/packages/clorichel/scqat) [![License](http://img.shields.io/packagist/l/clorichel/scqat.svg?style=flat-square)](https://github.com/clorichel/scqat/blob/master/LICENSE) [![Downloads](http://img.shields.io/packagist/dt/clorichel/scqat.svg?style=flat-square)](https://packagist.org/packages/clorichel/scqat) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/f5334e68-4553-478f-b83f-add159623d9d/mini.png)](https://insight.sensiolabs.com/projects/f5334e68-4553-478f-b83f-add159623d9d)

# SCQAT

This is SCQAT, the **S**tandardized **C**ode **Q**uality **A**ssurance **T**ool, an open source set of utilities that ensures the quality of the code for you, your development team and your boss.

SCQAT is a lightweight way to wrap industry standard code quality analyzers in a simple and efficient tool.

## Installation

You need to run PHP 5.4 or greater.

### Install Composer

SCQAT relies on [Composer](https://getcomposer.org/) magic to manage dependencies. If you haven't already installed it, just grab `composer.phar` running the one-liner install command you will [find here](https://getcomposer.org/download/).

We will assume that you will run `composer.phar` locally placed in a newly created `/path/to/folder` directory.

### Install or update SCQAT

```
php composer.phar require clorichel/scqat:0.5
```

Thanks to each Composer contributor, yes, that was it. First composer require will install everything, another require in the same folder with a new version number will simply update.

#### Globally

Using Linux / Unix / OSX ? Nothing simplest, run the composer require then :

```
ln -s /path/to/folder/vendor/bin/scqat /usr/local/bin/scqat
```

Command failed ? Just run again with sudo.
In OSX ? Create `/usr/local/bin/` folder which may not exist.

There is absolutely no need to run that command again when updating SCQAT.

## Usage

Installed globally ? Just open your console and do :
```
scqat -d /path/to/sourcecode
```

Your code has just been analyzed ! The quality report shown is self-explanatory.
Not installed globally ? You have to add the path, run `/path/to/folder/vendor/bin/scqat`

Have a look to the [CLI Manual](docs/CLI/Manual.md) for detailed usage. Enpower your projects with a simple `.scqat` YAML file to configure nearly everything as described in the [Configuration Manual](docs/Configuration.md).

## What is it about

SCQAT aims to be an universal tool to ensure code QA. Initial development was focused on PHP code language, through a simple tool running command line (CLI).

### How can it help me

| You are | SCQAT will help you |
| ------- | ------------------- |
| A single developer | *Be sure and proud of your code quality, even before committing it* |
| In a team of developers | *And your colleagues to all work on the same standard basis* |
| The team manager | *Ensuring the quality of your team working, without even reading source code* |
| Big boss | *Being confident in your developers team(s), their actual skills, the bleeding edge state of your company code base* |
| End customer | *Knowing the quality level of the job that was done* |

### I would like to add quality checks

Is your favorite language and/or QA tool missing ? You are pleased to open an issue.

Composer familiar, please send a pull request : just implement a `\SCQAT\LanguageAbstract` with a simple `fileNameMatcher` method to determine which filenames your language will handle, and a `\SCQAT\AnalyzerAbstract` that does the job and return a `\SCQAT\Result`.

See `\SCQAT\Language\PHP` and `\SCQAT\Language\PHP\Analyzer\Lint` for concrete examples, and just duplicate them for a quick start.

### Future work estimate

For instance SCQAT does exactly what it says : it's an efficient tool to standardize code quality assurance. You can run it on many platforms. It installs in no time. Reports are clear and clean. One can use it on a git pre-commit hook or add it on a continuous integration stack. Little PHP development skills are required to implement new languages and analyzers.

Short term work is listed in the [CHANGELOG](CHANGELOG.md) with each version.

Mid to long term will be working on reports to be able to export them in some usual formats (TXT, and HTML), rating and ranking with badges, and any other need that could appear. You are all pleased to open issues or pull requests with your ideas and/or needs.

## Uninstall

Nothing has been dispatched everywhere, simply remove your `/usr/local/bin/scqat` symlink (if installed globally) and the `/path/to/folder` install directory. Before doing this, you are pleased to open an issue if something went bad for you ;)

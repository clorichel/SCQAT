[![Version](http://img.shields.io/packagist/v/clorichel/scqat.svg?style=flat-square)](https://packagist.org/packages/clorichel/scqat) [![License](http://img.shields.io/packagist/l/clorichel/scqat.svg?style=flat-square)](https://github.com/clorichel/scqat/blob/master/LICENSE) [![Downloads](http://img.shields.io/packagist/dt/clorichel/scqat.svg?style=flat-square)](https://packagist.org/packages/clorichel/scqat)

# SCQAT

This is SCQAT, the **S**tandardized **C**ode **Q**uality **A**ssurance **T**ool, an open source set of utilities that ensures the quality of the code for you, your development team and your boss.

SCQAT is a lightweight way to wrap industry standard code quality analyzers in a simple and efficient tool.

## Installation

You need to run PHP 5.4 or greater.

### Install Composer

SCQAT relies on [Composer](https://getcomposer.org/) magic to manage dependencies. If you haven't already installed it, just grab `composer.phar` running the one-liner install command you will [find here](https://getcomposer.org/download/).

We will assume that you will run `composer.phar` locally on a new empty directory.

### Install SCQAT

```
php composer.phar require clorichel/scqat:0.1
```

Thanks to each Composer contributor, yes, that was it.

## Usage

Just open your console, get to an existing git repository folder and do :
```
/path/to/folder/vendor/bin/scqat
```
You've just been analyzed ! The quality report shown is self-explanatory.

Have a look to the [CLI Manual](docs/CLI/Manual.md) for detailed usage.

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

Short term work is to add support for PHPUnit and phpDocumentor, improve documentation and/or install process for system wide installation, adding `.scqat` configuration file support with "exclude analyzer" functionnality, ~~add a cli parameter to indicate which folder to analyze (to avoid the need to "be" in that directory)~~ *(done)*, add support for non git folders to analyze.

Mid to long term will be working on reports to be able to export them in some usual formats (TXT, and HTML), rating and ranking with badges, and any other need that could appear.
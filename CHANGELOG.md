## v0.6 (work in progress)

Features :

 - replaced files lists with a progress indicator when lot of files and not in verbose mode
 - added `--diff` CLI option to gather files on a git diff between refs

Bugfixes :

 - return value in write context fatal error for PHP < 5.5

Documentation :

 - added new `--diff` option to the CLI manual

TODO :

 - 

## v0.5 (2015-03-14)

Features :

 - refactored report hooks for efficiency, simplicity and modularity
 - added `.scqat` configuration file support
 - added `--verbose` CLI option with `-v` shortcut
 - added `languageEndOfUse` and `analyzerEndOfUse` report hooks

Bugfixes :

 - pre-commit was not working after CLI refactoring
 - now working with analyzed directories containing spaces
 - [PHP > PhpDoc] analyzer now report error if structure file cannot be generated

Documentation :

 - added updating info in Readme
 - added new `-v` option to the CLI manual
 - added configuration documentation with full dump

TODO :

 - [PHP > PhpDoc] analyzer doesn't work when file has shebang #!/usr/bin/php
 - modularize dependencies with composer suggest (limit to PHP with [PHP > Lint] analyzer)

## v0.4 (2015-03-04)

Features :

 - added `--file` CLI option with `-f` shortcut
 - introduced self-managed file gatherer
 - improved [PHP > PhpCsFixer] result description
 - improved [PHP > PhpCs] result description
 - added [PHP > PhpCpd] copy/paste detector (file by file)
 - added support for non git folders to analyze :
   - default method (no cli option) will try `git ls-files` first
   - if not a git repository, all files will be listed

Bugfixes :

 - fixed all SensioLabsInsight violations for analyze #1
 - ensured runner languages and analyzers classes types

Documentation :

 - added new `-f` option to the CLI manual
 - corrected compared version to HEAD in doc and CLI error returned
 - added SensioLabsInsight medal widget to the Readme
 - updated CLI manual with SCQAT 0.4 output
 - bumped Readme to 0.4

*From now, TODO list will be integrated in this CHANGELOG for better usability*

TODO :

 - add support for PHPUnit
 - ~~add `.scqat` configuration file support with "exclude analyzer" functionnality~~ *(done)*

## v0.3 (2015-02-26)

Features :

 - added phpDocumentor support
   - ignoring file summary
   - ignoring any error on methods which docblock contains "{@inheritdoc}"
 - detecting timezone and defaults to UTC
 - standardized dateFormatLong
 - handling wrong result from languages analyzers with appropriate message

Bugfixes :

 - correctly handling "no files to analyze" case

Documentation :

 - added global installation method
 - added uninstall how-to

## v0.2 (2015-02-25)

Features :

  - renamed rootDirectory to vendorDirectory
  - added `--directory` CLI option with `-d` shortcut
  - sending analyzedDirectory to context
  - [PHP > PhpMd] parsing error output to avoid filename repetition

Documentation :

  - CLI Manual is now in its own `docs\CLI\Manual.md`
  - added new option `-d` to the CLI manual

## v0.1 (2015-02-23)

Features :

  - added CLI command `bin/scqat`
  - CLI command supports `git clone` and `composer require` installation
  - support for multiple git list of files to analyze :
    - *all* : default, all files in git repository through `git ls-files`
    - `--modified` : staged, unstaged and untracked files
    - `--pre-commit` : staged files only
  - support for multiple code languages
  - support for *Meta* code language with analyzers :
    - *Composer* : checks for composer configuration (needs `.lock` if `.json` changed)
  - support for *PHP* code language with analyzers :
    - *Lint* : checks syntax with `php -l`
    - *PhpCs* : PSR-2 Standard checking through `phpcs`
    - *PhpCsFixer* : PSR-2 Standard checking through `php-cs-fixer`
    - *PhpMd* : PHP Mess Detector analysis
  - support for self-managed reports with hooks :
    - `Language_First_Use`, `Analyzer_First_Use`, `Analyzing_File`, and `Analyzer_Result`
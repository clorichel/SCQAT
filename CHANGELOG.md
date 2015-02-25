## v0.2

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
## CLI Manual

### Name

scqat - Run a set of industry standard code quality analyzers on your source code files

### Synopsys

```
'scqat' [-d|--directory="..."] (--[modified|pre-commit])
```

### Description

This analyzes files in the running directory (a GIT repository) with a set of quality tools and shows you a report indicating all possible errors.

Default to "all files" (through a `git ls-files`). One of the options below may be used to determine the files to analyze :

### Options

`-d <path/to/directory>`
`--directory=<path/to/directory>`

Provide the directory you want to analyze (defaults to current directory if none provided)

`--modified`

Analyze all modified files in the repository (staged, unstaged and untracked files changed from local revision to 'refs/remotes/origin/master' revision)

`--pre-commit`

Analyze all staged files changed from local revision to 'refs/remotes/origin/master' revision

### Output

This is a sample `scqat` output when executed on its own source code.

```
[ SCQAT - Standardized Code Quality Assurance Tool (v0.3) ]
2015-02-26 09:35:24 - Starting analysis

Gathering files to analyze... 22 file(s)
 - .gitignore
 - CHANGELOG.md
 - LICENSE
 - Readme.md
 - bin/scqat
 - composer.json
 - docs/CLI/Manual.md
 - src/SCQAT/AnalyzerAbstract.php
 - src/SCQAT/CLI.php
 - src/SCQAT/Context.php
 - src/SCQAT/Language/Meta.php
 - src/SCQAT/Language/Meta/Analyzer/Composer.php
 - src/SCQAT/Language/PHP.php
 - src/SCQAT/Language/PHP/Analyzer/Lint.php
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php
 - src/SCQAT/LanguageAbstract.php
 - src/SCQAT/Report.php
 - src/SCQAT/Result.php
 - src/SCQAT/Runner.php

Running analyzers for language Meta

[Meta > Composer] Checking Composer configuration... Useless, no change

Running analyzers for language PHP

[PHP > Lint] Checking syntax...
 - src/SCQAT/AnalyzerAbstract.php OK
 - src/SCQAT/CLI.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

[PHP > PhpCs] PSR-2 Standard checking through phpcs...
 - src/SCQAT/AnalyzerAbstract.php OK
 - src/SCQAT/CLI.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

[PHP > PhpCsFixer] PSR-2 Standard checking through php-cs-fixer...
 - src/SCQAT/AnalyzerAbstract.php OK
 - src/SCQAT/CLI.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

[PHP > PhpDoc] Checking for documentation completeness...
 - src/SCQAT/AnalyzerAbstract.php OK
 - src/SCQAT/CLI.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

[PHP > PhpMd] PHP Mess Detector analysis...
 - src/SCQAT/AnalyzerAbstract.php OK
 - src/SCQAT/CLI.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

Each configured quality test was green

2015-02-26 09:35:35 - Analysed in 11.098614931107s
[ SCQAT - Standardized Code Quality Assurance Tool (v0.3) ]
```
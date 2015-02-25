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

```
[ SCQAT - Standardized Code Quality Assurance Tool (v0.1) ]
DD/MM/YYYY HH:MM:SS - Starting analysis

Gathering files to analyze... 2 file(s)
 - src/Testing.php
 - src/Purpose.php

Running analyzers for language Meta

[Meta > Composer] Checking Composer configuration... Useless, no change

Running analyzers for language PHP

[PHP > Lint] Checking syntax...
 - src/Testing.php OK
 - src/Purpose.php OK

[PHP > PhpCs] PSR-2 Standard checking through phpcs...
 - src/Testing.php OK
 - src/Purpose.php OK

[PHP > PhpCsFixer] PSR-2 Standard checking through php-cs-fixer...
 - src/Testing.php OK
 - src/Purpose.php OK

[PHP > PhpMd] PHP Mess Detector analysis...
 - src/Testing.php OK
 - src/Purpose.php OK

Each configured quality test was green

DD/MM/YYYY HH:MM:SS - Analysed in X.Ys
[ SCQAT - Standardized Code Quality Assurance Tool (v0.1) ]
```
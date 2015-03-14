## CLI Manual

### Name

scqat - Run a set of industry standard code quality analyzers on your source code files

### Synopsys

```
scqat [ [-f|--file="<file>"] | [-d|--directory="<directory>"] ]
        [--modified] [--pre-commit]
        [-v|--verbose]
```

### Description

This analyzes files in the running directory with a set of quality tools and shows you a report indicating all possible errors.

Default to "all files" (through a `git ls-files` first if directory is a GIT repository). One of the options below may be used to determine the files to analyze :

### Options

`-f <path/to/file>`
`--file=<path/to/file>`

Provide the file you want to analyze. You can use multiple `-f` options, each file will be analyzed. This option has priority over `-d` : if you provide one or more `-f`, these files will be analyzed and `-d` value will be ignored.

`-d <path/to/directory>`
`--directory=<path/to/directory>`

Provide the directory you want to analyze (defaults to current directory if none provided)

`--modified`

Analyze all modified files in the GIT repository (staged, unstaged and untracked files changed from local revision to HEAD revision)

`--pre-commit`

Analyze all staged files changed from local revision to HEAD revision within the GIT repository

`-v`
`--verbose`

Output every files gathered and analyzed (by default, if too many files were gathered, output is stripped for simplicity)

### Output

This is a sample `scqat` output when executed on its own source code.

```
[ SCQAT - Standardized Code Quality Assurance Tool (v0.4) ]
2015-03-04 09:16:20 - Starting analysis

Gathering files to analyze... 25 file(s)
 - .gitignore
 - CHANGELOG.md
 - LICENSE
 - Readme.md
 - bin/scqat
 - composer.json
 - docs/CLI/Manual.md
 - src/SCQAT/AnalyzerAbstract.php
 - src/SCQAT/CLI.php
 - src/SCQAT/CLI/Definition.php
 - src/SCQAT/Context.php
 - src/SCQAT/FileGatherer.php
 - src/SCQAT/Language/Meta.php
 - src/SCQAT/Language/Meta/Analyzer/Composer.php
 - src/SCQAT/Language/PHP.php
 - src/SCQAT/Language/PHP/Analyzer/Lint.php
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php
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
 - src/SCQAT/CLI/Definition.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/FileGatherer.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

[PHP > PhpCpd] Detecting file by file copy/paste...
 - src/SCQAT/AnalyzerAbstract.php OK
 - src/SCQAT/CLI.php OK
 - src/SCQAT/CLI/Definition.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/FileGatherer.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php OK
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
 - src/SCQAT/CLI/Definition.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/FileGatherer.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php OK
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
 - src/SCQAT/CLI/Definition.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/FileGatherer.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php OK
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
 - src/SCQAT/CLI/Definition.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/FileGatherer.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php OK
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
 - src/SCQAT/CLI/Definition.php OK
 - src/SCQAT/Context.php OK
 - src/SCQAT/FileGatherer.php OK
 - src/SCQAT/Language/Meta.php OK
 - src/SCQAT/Language/Meta/Analyzer/Composer.php OK
 - src/SCQAT/Language/PHP.php OK
 - src/SCQAT/Language/PHP/Analyzer/Lint.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCpd.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCs.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpCsFixer.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpDoc.php OK
 - src/SCQAT/Language/PHP/Analyzer/PhpMd.php OK
 - src/SCQAT/LanguageAbstract.php OK
 - src/SCQAT/Report.php OK
 - src/SCQAT/Result.php OK
 - src/SCQAT/Runner.php OK

Each configured quality test was green

2015-03-04 09:16:34 - Analysed in 14.615466833115s
[ SCQAT - Standardized Code Quality Assurance Tool (v0.4) ]
```
## CLI Manual

### Name

scqat - Run a set of industry standard code quality analyzers on your source code files

### Synopsys

```
scqat [ [-f|--file="<file>"] | [-d|--directory="<directory>"] ]
      [ [--modified] | [--pre-commit] | [--diff="ref1[ ref2]"] ]
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

`--diff="ref1[ ref2]"`

Analyze all files in the `git diff ref1[ ref2]` within the GIT repository
See `man git-diff` for references format

`-v`
`--verbose`

Output every files gathered and analyzed (by default, if too many files were gathered, output is stripped for simplicity)

### Output

This is a sample `scqat` output when executed on its own source code.

```
2015-03-14 13:51:49 - Starting analysis

Gathering files to analyze... 31 file(s)
 - too many gathered files to show them here, use -v for verbose output

> Running analyzers for language Meta
[Meta > Composer] Checking Composer configuration... OK

> Running analyzers for language PHP
[PHP > Lint] Checking syntax... OK
[PHP > PhpCpd] Detecting file by file copy/paste... OK
[PHP > PhpCs] PSR-2 Standard checking through phpcs... OK
[PHP > PhpCsFixer] PSR-2 Standard checking through php-cs-fixer... OK
[PHP > PhpDoc] Checking for documentation completeness... OK
[PHP > PhpMd] PHP Mess Detector analysis... OK

Each configured quality test was green

2015-03-14 13:52:07 - Analyzed in 18.094094991684s
[ SCQAT - Standardized Code Quality Assurance Tool (v0.5) ]
```
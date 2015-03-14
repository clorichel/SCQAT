## Configuration

By default, SCQAT do not need any configuration, just indicate a directory to analyze, it will automatically do the job, with all possible languages and analyzers and a set of default configuration.

If you need more power or face complex cases, be happy to know SCQAT is fully configurable through a YAML file called `.scqat`. Simply place one in your analyzed directory and you are done.

### Full dump

Here are all possible options you can use with appropriate comments.

```
# List the report(s) to output. 'print' is the only one supported for now
Reports:

    # Default:
    - print

# White- or blacklisting languages and/or analyzers to run
Analysis:
    Languages:

        # Languages blacklist : specify here all languages to simply ignore
        except:               []

        # Languages whitelist : languages to force usage (except will be ignored if any language here)
        only:

            # Examples:
            - PHP
            - Meta
    Analyzers:

        # Analyzers blacklist : specify here all analyzers to simply ignore (format : LanguageName > AnalyzerName)
        except:

            # Example:
            - PHP > PhpDoc

        # Analyzers whitelist : analyzers to force usage (format : LanguageName > AnalyzerName, except will be ignored if any language here)
        only:

            # Examples:
            - PHP > Lint
            - PHP > PhpMd

# Here you can configure each and every analyzer for each and any language
Analyzers:

    # PHP language and analyzers specific configuration
    PHP:

        # Where is your PHP CLI ?
        command:              php # Required

            # Examples:
            - php
            - /usr/bin/php
```
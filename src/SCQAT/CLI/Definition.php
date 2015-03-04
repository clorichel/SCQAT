<?php

namespace SCQAT\CLI;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * SCQAT command line application definition
 *
 * To be bind to CLI input to assign possible options
 */
class Definition extends InputDefinition
{
    /**
     * Initializing and building parent symfony/console InputDefinition
     */
    public function __construct()
    {
        parent::__construct(array(
            new InputOption("file", "f", InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED),
            new InputOption("directory", "d", InputOption::VALUE_REQUIRED),
            new InputOption("modified", null, InputOption::VALUE_NONE),
            new InputOption("pre-commit", null, InputOption::VALUE_NONE),
        ));
    }
}

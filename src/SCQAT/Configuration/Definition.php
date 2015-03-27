<?php

namespace SCQAT\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This is SCQAT configuration options definition
 */
class Definition implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $scqatNode = $treeBuilder->root("SCQAT");
        $scqatNode
            ->children()
                ->variableNode("Reports")
                    ->info("List the report(s) to output. 'console' is the only one supported for now") // Multiple values can be used to output multiple reports.
                    ->cannotBeEmpty()
                    ->defaultValue(array("console"))
                    ->validate()
                        ->always(function ($values) {
                            foreach ((array) $values as $value) {
                                if (! in_array((string) $value, array("console", "html"))) {
                                    throw new \Symfony\Component\Config\Definition\Exception\InvalidTypeException("Invalid report output value ".$value);
                                }
                            }
                            return (array) $values;
                        })
                    ->end()
                ->end()
                ->arrayNode("Analysis")
                    ->info("White- or blacklisting languages and/or analyzers to run")
                    ->children()
                        ->arrayNode("Languages")
                            ->children()
                                ->arrayNode("except")
                                    ->info("Languages blacklist : specify here all languages to simply ignore")
                                    ->prototype("scalar")
                                    ->end()
                                ->end()
                                ->arrayNode("only")
                                    ->info("Languages whitelist : languages to force usage (except will be ignored if any language here)")
                                    ->example(array("PHP", "Meta"))
                                    ->prototype("scalar")
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode("Analyzers")
                            ->children()
                                ->arrayNode("except")
                                    ->info("Analyzers blacklist : specify here all analyzers to simply ignore (format : LanguageName > AnalyzerName)")
                                    ->example(array("PHP > PhpDoc"))
                                    ->prototype("scalar")
                                    ->end()
                                ->end()
                                ->arrayNode("only")
                                    ->info("Analyzers whitelist : analyzers to force usage (format : LanguageName > AnalyzerName, except will be ignored if any language here)")
                                    ->example(array("PHP > Lint", "PHP > PhpMd"))
                                    ->prototype("scalar")
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->append($this->addAnalyzersNode())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Add the analyzers node to the configuration definition
     */
    public function addAnalyzersNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root("Analyzers");
        $node
            ->info("Here you can configure each and every analyzer for each and any language")
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode("PHP")
                    ->info("PHP language and analyzers specific configuration")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("command")
                            ->info("Where is your PHP CLI ?")
                            ->isRequired()
                            ->example(array("php", "/usr/bin/php"))
                            ->defaultValue("php")
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}

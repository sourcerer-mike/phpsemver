<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:16
 */

namespace PHPSemVer\Assertions\Classes;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class SomeRemoved extends AbstractAssertion implements AssertionInterface
{

    public function process()
    {
        foreach ($this->getPrevious()->namespaces as $namespace => $node) {
            if ( ! isset( $this->getLatest()->namespaces[$namespace] )) {
                continue;
            }

            $prevClasses = array_keys(
                $this->getLatest()->namespaces[$namespace]->classes
            );

            foreach (array_keys($node->classes) as $className) {
                if ( ! in_array($className, $prevClasses)) {
                    $this->appendMessage(
                        sprintf(
                            'Removed class "%s\\%s".',
                            $namespace,
                            $className
                        )
                    );
                }
            }
        }
    }
}
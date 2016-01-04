<?php
/**
 * Contains console command.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Config;
use PHPSemVer\Environment;
use PHPSemVer\Wrapper\AbstractWrapper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Compare command.
 *
 * Compare all files based on two versions.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class CompareCommand extends AbstractCommand {
	protected $_cacheFactory;
	protected $cacheFactory;

	/**
	 * Current builder.
	 *
	 * @deprecated 3.0.0
	 *
	 * @var PHPBuilder
	 */
	protected $currentBuilder  = null;
    protected $parseExceptions = array();

    protected function configure() {
		$this->setName( 'compare' );

		$this->addOption(
			'exclude',
			null,
			InputOption::VALUE_OPTIONAL,
			'Exclude files containing the given regexp (extends the XML config).',
			''
		);

		$this->addOption(
			'type',
			't',
			InputArgument::OPTIONAL,
			'Type of given targets',
			'git'
		);

		$this->addOption(
			'ruleSet',
			'R',
			InputArgument::OPTIONAL,
			'A predefined rule set or XML file.',
			'SemVer2'
		);

		$this->addArgument(
			'previous',
			InputArgument::REQUIRED,
			'Place to lookup the old code'
		);

		$this->addArgument(
			'latest',
			InputArgument::OPTIONAL,
			'Place to lookup the new code',
			'HEAD'
		);
	}

	protected function execute(
		InputInterface $input,
		OutputInterface $output
	) {
        $totalTime = microtime(true);

        $this->compareTrees();

        $this->printTable($input, $output, $this->getEnvironment());

        $output->writeln('');
        $this->verbose(
            sprintf(
                'Total time: %.2f',
                microtime(true) - $totalTime
            )
        );

        $this->verbose('');
    }


    /**
     * Print information as table.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Environment     $environment
     */
    protected function printTable(InputInterface $input, OutputInterface $output, $environment)
    {
        $table   = new Table($output);
        $headers = array('Level', 'Message');

        $table->setHeaders($headers);

        foreach ($environment->getConfig()->ruleSet() as $ruleSet) {
            foreach ($ruleSet->getErrorMessages() as $message) {
                $table->addRow(
                    [
                        $ruleSet->getName(),
                        $message->getMessage(),
                    ]
                );
            }
        }

        $output->writeln('');
        $table->render();
    }
}
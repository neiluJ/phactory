<?php
/**
 * Phactory
 *
 * Copyright (c) 2012-2013, Julien Ballestracci <julien@nitronet.org>.
 * All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5.3
 *
 * @category  Phactory
 * @package   Phactory
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2012-2013 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://github.com/neiluj/phactory
 */
namespace Phactory;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Command\Command;

use \Phar;

/**
 * Make command
 *
 * Actually builds the phar archive.
 *
 * @category Phactory
 * @package  Phactory
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://github.com/neiluj/phactory
 */
class MakeCommand extends Command
{
    /**
     * Command configuration
     *
     * @see Symfony\Component\Console\Command\Command::configure
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName("make")
             ->setDescription("Compiles a directory into a single phar");

        $this->addArgument(
            "directory",
            InputArgument::REQUIRED,
            "Path to project directory"
        );

        $this->addArgument(
            "pharName",
            InputArgument::REQUIRED,
            "Name for the phar archive (without extension)"
        );

        $this->addOption(
            "format",
            "f",
            InputOption::VALUE_REQUIRED,
            "Phar format. Could be phar, tar or zip",
            'phar'
        );

        $this->addOption(
            "compression",
            "c",
            InputOption::VALUE_OPTIONAL,
            "Phar compression. Could be none, gz or bz2",
            null
        );

        $this->addOption(
            "vendors",
            null,
            InputOption::VALUE_NONE,
            "include the 'vendor' directory",
            null
        );

        $this->addOption(
            "stub",
            "s",
            InputOption::VALUE_OPTIONAL,
            "Phar stub file.",
            null
        );
    }

    /**
     * Executes the "make" command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir        = realpath($input->getArgument("directory"));
        $pharName   = $input->getArgument("pharName");
        $current    = getcwd();

        if(!Phar::canWrite()) {
            throw new \RuntimeException(
                sprintf(
                    "Cannot create Phar archives on this environment " .
                    "- please configure your 'phar.readonly' directive."
                )
            );
        }

        $output->writeln("Creating Phar archive <info>$pharName.phar</info> ...");
        $phar = new Phar("$current/$pharName.phar", 0, "$pharName.phar");

        /*
         * Handling format option
         */
        $formatOpt = $input->getOption("format");
        switch(strtolower($formatOpt)) {
            case 'phar':
                $format = Phar::PHAR;
                break;

            case 'zip':
                $format = Phar::ZIP;
                break;

            case 'tar':
                $format = Phar::TAR;
                break;

            default:
                throw new \RuntimeException(
                    sprintf("Invalid Phar format: %s", $formatOpt)
                );
        }

        $output->writeln("Phar format: <comment>$formatOpt</comment>");

        /*
         * Handling compression option
         */
        $compressOpt = $input->getOption("compression");
        switch(strtolower($compressOpt)) {
            case 'gz':
                $compress = Phar::GZ;
                break;

            case 'bz2':
                $compress = Phar::BZ2;
                break;

            case 'none':
            default:
                $compressOpt = "none";
                $compress = Phar::NONE;
                break;
        }

        $output->writeln("Phar compression: <comment>$compressOpt</comment>");

        // start build
        $phar->convertToExecutable($format, $compress);
        $phar->startBuffering();

        $ite = new \RecursiveDirectoryIterator(
            $dir,
            \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator(
            $ite,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // adding project directory
        $output->writeln("Adding project directory: <comment>$dir</comment>");
        $phar->buildFromIterator($iterator, $current);

        /*
         * Handling Vendors
         */
        $vendors = $input->getOption("vendors");
        $vendorsDir = $current . DIRECTORY_SEPARATOR . 'vendor';
        if ($vendors) {
            if(!is_dir($vendorsDir)) {
                throw new \RuntimeException("'vendor' directory not found");
            }

            $ite = new \RecursiveDirectoryIterator(
                $vendorsDir,
                \FilesystemIterator::SKIP_DOTS
            );
            $iterator = new \RecursiveIteratorIterator(
                $ite,
                \RecursiveIteratorIterator::SELF_FIRST
            );

            $output->writeln(
                "Adding vendors directory: <comment>$vendorsDir</comment>"
            );
            $phar->buildFromIterator($iterator, $current);
        }

        /*
         * Handling Stub option
         */
        $stub = $input->getOption("stub");
        if (!empty($stub)) {
            if(!is_file($stub)) {
                throw new \RuntimeException("stub file $stub not found");
            }

            $output->writeln("Adding stub: <comment>$stub</comment>");
            $phar->setStub(file_get_contents($stub));
        }


        // write the phar, our job is done \o/
        $phar->stopBuffering();
        $output->writeln(
            "<info>Phar archive $pharName created successfully</info>"
        );
    }
}
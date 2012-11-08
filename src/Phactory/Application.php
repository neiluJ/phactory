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

use Symfony\Component\Console\Application as BaseConsoleApp;

/**
 * Main Phactory application class
 *
 * This class is a simple shortcut to build the "phactory" application.
 *
 * @category Phactory
 * @package  Phactory
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://github.com/neiluj/phactory
 */
class Application extends BaseConsoleApp
{
    const APP_NAME = 'Phactory';
    const APP_VERSION = '1.0';

    /**
     * Constructor wrapper
     *
     * @param type $name
     * @param type $version
     *
     * @return void
     */
    public function __construct($name = self::APP_NAME,
        $version = self::APP_VERSION
    ) {
        parent::__construct($name, $version);
        $this->add(new MakeCommand());
    }

    /**
     * Shortcut method
     *
     * @return integer
     */
    public static function autorun()
    {
        $class = new self;
        return $class->run();
    }
}
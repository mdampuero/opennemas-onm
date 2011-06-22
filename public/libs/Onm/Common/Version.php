<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Onm\Common;

/**
 * Class to store and retrieve the version of Onm
 *
 * @link    www.opennemas.org
 * @since   0.8
 * @version $Revision$
 * @author  Fran Dieguez <fran@openhost.es>
 */
class Version
{
    /**
     * Current Doctrine Version
     */
    const VERSION = '0.8.1';

    /**
     * Compares a Onm version with the current one.
     *
     * @param string $version Onm version to compare.
     * @return int Returns -1 if older, 0 if it is the same, 1 if version
     *             passed as argument is newer.
     */
    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}

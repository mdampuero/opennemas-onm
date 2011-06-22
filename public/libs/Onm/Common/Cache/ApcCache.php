<?php
/*
 *  $Id$
 *
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

namespace Onm\Common\Cache;

/**
 * APC cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class APCCache extends AbstractCache
{
    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $ci = apc_cache_info('user');
        $keys = array();

        foreach ($ci['cache_list'] as $entry) {
            $keys[] = $entry['info'];
        }

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return apc_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        $found = false;

        apc_fetch($id, $found);

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return (bool) apc_store($id, $data, (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return apc_delete($id);
    }
}

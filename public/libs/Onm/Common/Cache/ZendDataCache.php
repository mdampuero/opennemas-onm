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

namespace Onm\Common\Cache;

/**
 * Zend Data Cache cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class ZendDataCache extends AbstractCache
{
    public function __construct()
    {
        $this->setNamespace('onm::'); // zend data cache format for namespaces ends in ::
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        throw new \BadMethodCallException("getIds() is not supported by ZendDataCache");
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return zend_shm_cache_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return (zend_shm_cache_fetch($id) !== FALSE);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return zend_shm_cache_store($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return zend_shm_cache_delete($id);
    }
}

<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Album extends Content
{
    /**
     * the album id
     */
    public $pk_album = null;

    /**
     * the subtitle for this album
     */
    public $subtitle = null;

    /**
     * the agency which created this album originaly
     */
    public $agency = null;

    /**
     * the id of the image that is the cover for this album
     */
    public $cover_id = null;

    /**
     * Initializes the Album class.
     *
     * @param string $id the id of the album
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Album');
        $this->content_type           = 7;
        $this->content_type_name      = 'album';

        parent::__construct($id);
    }

    /**
     * {@inheritdoc}
     */
    public function load($properties)
    {
        parent::load($properties);

        if (!empty($this->cover_id)) {
            $this->cover_image = getService('entity_repository')
                ->find('Photo', $this->cover_id);

            $this->cover = $this->cover_image->path_file
                . $this->cover_image->name;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        if (empty($id)) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN albums ON pk_content = pk_album WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return;
            }

            $this->load($rs);
            $this->loadPhotos();

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error fetching content with id' . $id . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::create($data);

            $this->pk_content = (int) $this->id;
            $this->pk_album   = (int) $this->id;

            $conn->insert('albums', [
                'pk_album' => $this->id,
                'subtitle' => $data['subtitle'] ?? null,
                'agency'   => $data['agency'] ?? null,
                'cover_id' => $data['cover_id'] ?? null
            ]);

            $this->savePhotos($this->id, $data['photos'] ?? []);
            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            $conn->rollback();

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::remove($id);

            $conn->delete('albums', [ 'pk_album' => $id ]);
            $this->removePhotos($id);

            $conn->commit();
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            $conn->rollback();
        }
    }

    /**
     * Renders the album
     *
     * @param array $params parameters for rendering the content
     * @param null $tpl variable remaining for back compatability
     *
     * @return string the generated HTML
     */
    public function render(array $params, $tpl = null)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;
        $template       = 'frontpage/contents/_album.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        try {
            $html = $tpl->fetch($template, $params);
        } catch (\Exception $e) {
            $html = _('Album not available');
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function update($data)
    {
        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::update($data);

            $conn->update('albums', [
                'subtitle' => $data['subtitle'] ?? null,
                'agency'   => $data['agency'] ?? null,
                'cover_id' => !empty($data['cover_id']) ? $data['cover_id'] : null
            ], [ 'pk_album' => $this->id ]);

            $this->removePhotos($this->id);
            $this->savePhotos($this->id, $data['photos'] ?? []);

            $conn->commit();
            $this->load($data);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            $conn->rollback();
        }

        return false;
    }

    /**
     * Loads all photos in the album.
     */
    protected function loadPhotos() : void
    {
        try {
            $this->photos = getService('dbal_connection')->fetchAll(
                'SELECT DISTINCT pk_photo, description, position'
                . ' FROM albums_photos WHERE pk_album =? ORDER BY position ASC',
                [ $this->id ]
            );
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
            );

            $this->photos = [];
        }
    }

    /**
     * Removes all photos for the album.
     *
     * @param int $id The album id.
     */
    protected function removePhotos(int $id) : void
    {
        getService('dbal_connection')
            ->delete('albums_photos', [ 'pk_album' => $id ]);
    }

    /**
     * Saves photos for the album.
     *
     * @param int   $id     The album id.
     * @param array $photos The list of photos.
     */
    protected function savePhotos(int $id, array $photos) : void
    {
        if (empty($photos)) {
            return;
        }

        $conn = getService('dbal_connection');

        foreach ($photos as $position => $photo) {
            try {
                $conn->insert('albums_photos', [
                    'pk_album'    => $id,
                    'pk_photo'    => $photo['pk_photo'],
                    'position'    => $position,
                    'description' => $photo['description'],
                ]);
            } catch (\Exception $e) {
                getService('error.log')->error(
                    $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString()
                );

                return;
            }
        }
    }
}

<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles video CRUD actions.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Video extends Content
{

    public $pk_video = null;
    public $information  = null;
    public $video_url  = null;
    public $author_name = null;
    public $content_type = null;
    public $content_type_name = null;

    /**
     * Initializes the Video object
     **/
    function __construct($id = null)
    {
        parent::__construct($id);
        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = 'Video';
    }

    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                $uri =  Uri::generate(
                    'video',
                    array(
                        'id' => $this->id,
                        'date' => date('Y-m-d', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug' => $this->slug,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            case 'slug':
                return String_Utils::get_title($this->title);
                break;

            case 'content_type_name':
                $sql = 'SELECT * FROM `content_types`"
                            ." WHERE pk_content_type = "?" LIMIT 1';
                $values = array($this->content_type);
                $contentTypeName = $GLOBALS['application']->conn->Execute(
                    $sql,
                    $values
                );
                if (isset($contentTypeName->fields['name'])) {
                    $returnValue = $contentTypeName;
                } else {
                    $returnValue = $this->content_type;
                }
                $this->content_type_name = $returnValue;
                return $returnValue;

                break;

            default:
                break;
        }
        parent::__get($name);
    }

    public function create($data)
    {
        parent::create($data);

        $sql = "INSERT INTO videos
                    (`pk_video`,`video_url`, `information`, `author_name`) " .
                "VALUES (?,?,?,?)";

        $values = array(
            $this->id, $data['video_url'],
            serialize($data['information']), $data['author_name']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }

        return true;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM videos WHERE pk_video = '.$id;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

        $this->pk_video = $rs->fields['pk_video'];
        $this->video_url = $rs->fields['video_url'];
        $this->author_name = $rs->fields['author_name'];
        $this->information = unserialize($rs->fields['information']);
    }

    public function update($data)
    {
        parent::update($data);
        $sql =  "UPDATE videos"
                ." SET  `video_url`=?, `information`=?, `author_name`=?  "
                ." WHERE pk_video=".$data['id'];
        $values = array(
            $data['video_url'],
            serialize($data['information']),
            $data['author_name']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }

    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM videos WHERE pk_video='.$id;

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }

}

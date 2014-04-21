<?php

class Opinions
{
    public $restler;

    /*
    * @url GET /opinions/complete/:id
    */
    public function complete($id = null)
    {
        $this->validateInt($id);

        // Resolve dirty Id
        $opinionId = Content::resolveID($id);

        // Load opinion
        $opinion = new Opinion($opinionId);

        // Get author information
        $author = new User($opinion->fk_author);
        $opinion->author = $author;

        // Get author name slug
        $opinion->author_name_slug = StringUtils::get_title($opinion->name);

        // Get machine related contents
        $opinion->machineRelated = $this->machineRelated($opinionId);

        //Fetch the other opinions for this author
        $opinion->otherOpinions = $this->others($opinionId);

        // Get external media url
        $opinion->externalMediaUrl = MEDIA_IMG_PATH_WEB;

        return serialize($opinion);
    }

    /*
    * @url GET /opinions/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        $opinion = new Opinion($id);

        return $opinion;
    }

    /*
    * @url GET /opinions/editorialinhome/
    */
    public function editorialInHome()
    {
        $cm = new ContentManager();
        // Fetch last opinions from editorial
        $editorial = $cm->find(
            'Opinion',
            'opinions.type_opinion=1 '.
            'AND contents.content_status=1 '.
            'AND contents.in_home=1 ',
            'ORDER BY created DESC LIMIT 2'
        );

        foreach ($editorial as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
            $opinion->author = new User(1);
            $opinion->author->uri = 'ext'.Uri::generate(
                'opinion_author_frontpage',
                array(
                    'slug' => 'editorial',
                    'id' => 1
                )
            );
        }

        return $editorial;
    }

    /*
    * @url GET /opinions/directorinhome/
    */
    public function directorInHome()
    {
        $cm = new ContentManager();
        // Fetch last opinions from director
        $director = $cm->find(
            'Opinion',
            'opinions.type_opinion=2 '.
            'AND contents.content_status=1 '.
            'AND contents.in_home=1 ',
            'ORDER BY created DESC LIMIT 2'
        );

        foreach ($director as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
            $opinion->author = new User(2);
            $opinion->author->uri = 'ext'.Uri::generate(
                'opinion_author_frontpage',
                array(
                    'slug' => 'director',
                    'id' => 2
                )
            );
        }

        return $director;
    }

    /*
    * @url GET /opinions/authorsinhome/
    */
    public function authorsInHome()
    {
        $cm = new ContentManager();
        // Fetch all available opinions in home of authors
        $opinions = $cm->find(
            'Opinion',
            'in_home=1 and content_status=1 and type_opinion=0',
            'ORDER BY position ASC, starttime DESC '
        );

        foreach ($opinions as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
        }

        return $opinions;
    }

    /*
    * @url GET /opinions/authorsnotinhome/
    */
    public function authorsNotInHome()
    {
        $cm = new ContentManager();

        $opinions = $cm->find(
            'Opinion',
            'in_home=0 and content_status=1 and type_opinion=0',
            'ORDER BY created DESC '
        );

        foreach ($opinions as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
        }

        return $opinions;
    }

    /*
    * @url GET /opinions/authorsnotinhomepaged/:page
    */
    public function authorsNotInHomePaged($page = null)
    {
        $this->validateInt($page);

        $cm = new ContentManager();

        $limit ='LIMIT '.(($page-2)*ITEMS_PAGE).', '.(($page-1)*ITEMS_PAGE);

        $opinions = $cm->find(
            'Opinion',
            'in_home=0 and content_status=1 and type_opinion=0',
            'ORDER BY starttime DESC '.$limit
        );

        foreach ($opinions as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
        }

        return $opinions;
    }

    /*
    * @url GET /opinions/allopinionsauthor/:page/:id
    */
    public function allOpinionsAuthor($page = null, $id = null)
    {
        $this->validateInt($page);

        $cm = new ContentManager();

        $limit=' LIMIT '.(($page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

        // Get the list articles for this author
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.type_opinion=0 AND opinions.fk_author='.$id.
            ' AND contents.content_status=1',
            'ORDER BY created DESC '.$limit
        );

        return $opinions;
    }

    /*
    * @url GET /opinions/allopinionseditorial/:page
    */
    public function allOpinionsEditorial($page = null)
    {
        $this->validateInt($page);

        $cm = new ContentManager();

        $limit=' LIMIT '.(($page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

        // Get the list articles for this author
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.type_opinion=1'
            .' AND contents.content_status=1',
            'ORDER BY created DESC '.$limit
        );

        return $opinions;
    }

    /*
    * @url GET /opinions/allopinionsdirector/:page
    */
    public function allOpinionsDirector($page = null)
    {
        $this->validateInt($page);

        $cm = new ContentManager();

        $limit=' LIMIT '.(($page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

        // Get the list articles for this author
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.type_opinion=2'
            .' AND contents.content_status=1',
            'ORDER BY created DESC '.$limit
        );

        return $opinions;
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countAuthorsNotInHome()
    {
        $cm = new ContentManager();

        $numOpinions = $cm->cache->count(
            'Opinion',
            'in_home=0 and content_status=1 and type_opinion=0',
            'ORDER BY created DESC '
        );

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthoropinions/
    */
    public function countAuthorOpinions($id = null)
    {
        $cm = new ContentManager();

        $numOpinions = $cm->cache->count(
            'Opinion',
            'opinions.type_opinion=0 AND opinions.fk_author='.$id.
            ' AND contents.content_status=1'
        );

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countEditorialOpinions()
    {
        $cm = new ContentManager();

        $countOpinions = $cm->cache->count(
            'Opinion',
            'opinions.type_opinion=1 AND contents.content_status=1'
        );

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countDirectorOpinions()
    {
        $cm = new ContentManager();

        $numOpinions = $cm->cache->count(
            'Opinion',
            'opinions.type_opinion=2 AND contents.content_status=1'
        );

        return $numOpinions;
    }

    /*
    * @url GET /opinions/others/:id
    */
    public function others($id = null)
    {
        $this->validateInt($id);

        $opinion = new Opinion($id);

        if ($opinion->type_opinion == 1) {
            $where=' opinions.type_opinion = 1';
            $opinion->name ='Editorial';
        } elseif ($opinion->type_opinion == 2) {
            $where=' opinions.type_opinion = 2';
            $opinion->name ='Director';
        } else {
            $where=' opinions.fk_author='.($opinion->fk_author);
        }

        $cm = new ContentManager();
        $otherOpinions = $cm->cache->find(
            'Opinion',
            $where.' AND `pk_opinion` <> '.$id.' AND content_status = 1',
            ' ORDER BY created DESC LIMIT 0,9'
        );

        foreach ($otherOpinions as &$otherOpinion) {
            $otherOpinion->author_name_slug  = $opinion->author_name_slug;
            $otherOpinion->uri = 'ext'.$otherOpinion->uri;
        }


        return $otherOpinions;
    }

    /*
    * @url GET /opinions/machinerelated/:id
    */
    public function machineRelated($id = null)
    {
        $this->validateInt($id);

        $opinion = new Opinion($id);

        $machineSearcher = $this->restler->container->get('automatic_contents');

        $suggestedContents = $machineSearcher->SearchSuggestedContents(
            $opinion->metadata,
            'opinion',
            " contents.content_status=1 AND pk_content = pk_fk_content",
            4
        );

        foreach ($suggestedContents as &$element) {
            $origElem = $element;
            $element = new Opinion($origElem['pk_content']);
            if (!empty($element->author)) {
                $origElem['author_name'] = $element->author;
                $origElem['author_name_slug'] = StringUtils::get_title($element->author);
            } else {
                $origElem['author_name_slug'] = "author";
            }
            $origElem['uri'] = 'ext'.Uri::generate(
                'opinion',
                array(
                    'id'       => $origElem['pk_content'],
                    'date'     => date('YmdHis', strtotime($origElem['created'])),
                    'category' => $origElem['author_name_slug'],
                    'slug'     => StringUtils::get_title($origElem['title']),
                )
            );
        }

        return $suggestedContents;
    }

    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }
}

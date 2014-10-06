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

        $er = getService('entity_repository');

        // Resolve dirty Id
        $opinionId = Content::resolveID($id);

        // Load opinion
        $opinion = $er->find('Opinion', $opinionId);

        // Get author information
        $ur = getService('user_repository');
        $author = $ur->find($opinion->fk_author);
        $opinion->author = $author;

        // Get author name slug
        $opinion->author_name_slug = StringUtils::getTitle($opinion->name);

        // Get machine related contents
        $opinion->machineRelated = $this->machineRelated($opinionId);

        //Fetch the other opinions for this author
        $opinion->otherOpinions = $this->others($opinionId);

        // Get external media url
        $opinion->externalMediaUrl = MEDIA_IMG_ABSOLUTE_URL;

        return serialize($opinion);
    }

    /*
    * @url GET /opinions/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        $er = getService('entity_repository');

        $opinion = $er->find('Opinion', $id);

        return $opinion;
    }

    /*
    * @url GET /opinions/editorialinhome/
    */
    public function editorialInHome()
    {
        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 1)),
            'type_opinion'   => array(array('value' => 1)),
            'content_status' => array(array('value' => 1)),
        );

        $order = array(
            'starttime' => 'DESC'
        );
        // Fetch last opinions from editorial
        $editorial = $or->findBy($filters, $order, 2);

        $ur = getService('user_repository');
        foreach ($editorial as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
            $opinion->author = $ur->find(1);
            if (!is_null($opinion->author)) {
                $opinion->author->uri = 'ext'.Uri::generate(
                    'opinion_author_frontpage',
                    array(
                        'slug' => 'editorial',
                        'id' => 1
                    )
                );
            }
        }

        return $editorial;
    }

    /*
    * @url GET /opinions/directorinhome/
    */
    public function directorInHome()
    {
        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 1)),
            'type_opinion'   => array(array('value' => 2)),
            'content_status' => array(array('value' => 1)),
        );

        $order = array(
            'starttime' => 'DESC'
        );
        // Fetch last opinions from editorial
        $director = $or->findBy($filters, $order, 2);

        $ur = getService('user_repository');
        foreach ($director as &$opinion) {
            $opinion->uri = 'ext'.$opinion->uri;
            $opinion->author = $ur->find(2);
            if (!is_null($opinion->author)) {
                $opinion->author->uri = 'ext'.Uri::generate(
                    'opinion_author_frontpage',
                    array(
                        'slug' => 'director',
                        'id' => 2
                    )
                );
            }
        }

        return $director;
    }

    /*
    * @url GET /opinions/authorsinhome/
    */
    public function authorsInHome()
    {
        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 1)),
            'type_opinion'   => array(array('value' => 0)),
            'content_status' => array(array('value' => 1)),
        );

        $order = array(
            'position' => 'ASC',
            'starttime' => 'DESC'
        );
        // Fetch all available opinions in home of authors
        $opinions = $or->findBy($filters, $order);

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
        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 0)),
            'type_opinion'   => array(array('value' => 0)),
            'content_status' => array(array('value' => 1)),
        );

        $order = array(
            'position' => 'ASC',
            'starttime' => 'DESC'
        );
        // Fetch all available opinions in home of authors
        $opinions = $or->findBy($filters, $order);

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

        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 0)),
            'type_opinion'   => array(array('value' => 0)),
            'content_status' => array(array('value' => 1)),
        );

        $order = array(
            'starttime' => 'DESC'
        );
        // Fetch all available opinions in home of authors
        $opinions = $or->findBy($filters, $order, 20, $page);

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
        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 0)),
            'type_opinion'   => array(array('value' => 0)),
            'content_status' => array(array('value' => 1)),
        );

        $numOpinions = $or->countBy($filters);

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthoropinions/
    */
    public function countAuthorOpinions($id = null)
    {
        $or = getService('opinion_repository');

        $filters = array(
            'in_home'        => array(array('value' => 0)),
            'fk_author'      => array(array('value' => $id)),
            'content_status' => array(array('value' => 1)),
        );

        $numOpinions = $or->countBy($filters);

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countEditorialOpinions()
    {
        $or = getService('opinion_repository');

        $filters = array(
            'type_opinion'   => array(array('value' => 1)),
            'content_status' => array(array('value' => 1)),
        );

        $numOpinions = $or->countBy($filters);

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countDirectorOpinions()
    {
        $or = getService('opinion_repository');

        $filters = array(
            'type_opinion'   => array(array('value' => 2)),
            'content_status' => array(array('value' => 1)),
        );

        $numOpinions = $or->countBy($filters);
        return $numOpinions;
    }

    /*
    * @url GET /opinions/others/:id
    */
    public function others($id = null)
    {
        $this->validateInt($id);

        $er = getService('entity_repository');
        $opinion = $er->find('Opinion', $id);

        $filters = array();
        if ($opinion->type_opinion == 1) {
            $filters['type_opinion'] = array(array('value' => 1));
        } elseif ($opinion->type_opinion == 2) {
            $filters['type_opinion'] = array(array('value' => 2));
        } else {
            $filters['type_opinion'] = array(array('value' => 0));
            $filters['fk_author'] = array(array('value' => $opinion->fk_author));
        }

        $or = getService('opinion_repository');
        $filters['pk_opinion']     = array(array('value' => $id, 'operator' => '<>'));
        $filters['content_status'] = array(array('value' => 1));

        $order = array(
            'starttime' => 'DESC'
        );
        // Fetch all available opinions in home of authors
        $otherOpinions = $or->findBy($filters, $order, 9);

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

        $er = getService('entity_repository');

        // Load opinion
        $opinion = $er->find('Opinion', $id);

        $machineSuggestedContents = getService('automatic_contents')->searchSuggestedContents(
            'opinion',
            " pk_content <>".$opinion->id,
            4
        );

        foreach ($machineSuggestedContents as &$element) {
            $origElem = $element;
            // Load opinion
            $element = $er->find('Opinion', $origElem['pk_content']);
            if (!empty($element->author)) {
                $origElem['author_name'] = $element->author;
                $origElem['author_name_slug'] = StringUtils::getTitle($element->author);
            } else {
                $origElem['author_name_slug'] = "author";
            }
            $origElem['uri'] = 'ext'.Uri::generate(
                'opinion',
                array(
                    'id'       => $origElem['pk_content'],
                    'date'     => date('YmdHis', strtotime($origElem['created'])),
                    'category' => $origElem['author_name_slug'],
                    'slug'     => StringUtils::getTitle($origElem['title']),
                )
            );
        }

        return $machineSuggestedContents;
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

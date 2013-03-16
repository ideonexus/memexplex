<?php

/**
 * This messaging object encapsulates the various search parameters gathered
 * from the presentation layer and given to the persistence layer for querying. 
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Warlock
 */
class MemexPlexObjectSearchParameters
{
    /** @var integer Meme, Reference, Schema Id*/
    protected $id;
    /** @var string Tag */
    protected $taxonomy;
    /** @var string Tag */
    protected $author;
    /** @var string Search String */
    protected $searchString;
    /** @var integer Logged-In Curator Id */
    protected $uid;
    /** @var integer Curator Id */
    protected $curatorid;
    /** @var string Filter By */
    protected $searchFilter;
    /** @var string Order By */
    protected $sortFilter;
    /** @var string Page filter */
    protected $pageFilter;
    /** @var string Amazon Category */
    protected $categoryFilter;
    /** @var integer Exclude by Schema Id */
    protected $excludeSchemaId;
    /** @var integer Exclude by Reference Id */
    protected $excludeReferenceId;
    
    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param string $taxonomy
     * @param string $searchString
     * @param integer $uid
     * @param string $searchfilter
     * @param string $sortFilter
     * @param string $pageFilter
     * @param string $categoryFilter
     * @param integer $excludeSchemaId
     * @param integer $excludeReferenceId
     */
    public function __construct(
        $id=null
        ,$taxonomy=null
        ,$searchString=null
        ,$uid=null
        ,$searchfilter=null
        ,$sortFilter=null
        ,$pageFilter=null
        ,$categoryFilter=null
        ,$excludeSchemaId=null
        ,$excludeReferenceId=null
    )
    {
        $this->setId($id);
        $this->setTaxonomy($taxonomy);
        $this->setSearchString($searchString);
        $this->setUid($uid);
        $this->setSearchFilter($searchFilter);
        $this->setSortFilter($sortFilter);
        $this->setPageFilter($pageFilter);
        $this->setCategoryFilter($categoryFilter);
        $this->setExcludeSchemaId($excludeSchemaId);
        $this->setExcludeReferenceId($excludeReferenceId);
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * @param string
     */
    public function setTaxonomy($taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    /**
     * @return string
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * @param string
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;
    }

    /**
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param integer
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }
    
    /**
     * @return integer
     */
    public function getCuratorId()
    {
        return $this->curatorid;
    }

    /**
     * @param integer
     */
    public function setCuratorId($curatorid)
    {
        $this->curatorid = $curatorid;
    }
    
    /**
     * @return string
     */
    public function getSearchFilter()
    {
        return $this->searchFilter;
    }

    /**
     * @param string
     */
    public function setSearchFilter($searchFilter)
    {
        $this->searchFilter = $searchFilter;
    }

    /**
     * @return string
     */
    public function getSortFilter()
    {
        return $this->sortFilter;
    }

    /**
     * @param string
     */
    public function setSortFilter($sortFilter)
    {
        $this->sortFilter = $sortFilter;
    }

    /**
     * @return string
     */
    public function getPageFilter()
    {
        return $this->pageFilter;
    }

    /**
     * @param string
     */
    public function setPageFilter($pageFilter)
    {
        $this->pageFilter = $pageFilter;
    }

    /**
     * @return string
     */
    public function getCategoryFilter()
    {
        return $this->categoryFilter;
    }

    /**
     * @param string
     */
    public function setCategoryFilter($categoryFilter)
    {
        $this->categoryFilter = $categoryFilter;
    }

    /**
     * @return integer
     */
    public function getExcludeSchemaId()
    {
        return $this->excludeSchemaId;
    }

    /**
     * @param integer
     */
    public function setExcludeSchemaId($excludeSchemaId)
    {
        $this->excludeSchemaId = $excludeSchemaId;
    }

    /**
     * @return integer
     */
    public function getExcludeReferenceId()
    {
        return $this->excludeReferenceId;
    }

    /**
     * @param integer
     */
    public function setExcludeReferenceId($excludeReferenceId)
    {
        $this->excludeReferenceId = $excludeReferenceId;
    }

    /**
     * Looks for GET and POST variables sent from the presentation layer
     * and sets the appropriate properties in this object from them.
     */
    public function setPropertiesFromGetAndPost()
    {
		$this->id = null;
        if (isset($_POST['id']))
        {
            $this->id = $_POST['id'];
        }
        elseif (isset($_GET['id']))
        {
            $this->id = $_GET['id'];
        }
        
        $this->author = null;
        if (isset($_POST['author']))
        {
            $this->author = $_POST['author'];
        }
        elseif (isset($_GET['author']))
        {
            $this->author = $_GET['author'];
        }

        $this->taxonomy = null;
        if (isset($_POST['taxonomy']))
        {
            $this->taxonomy = $_POST['taxonomy'];
        }
        elseif (isset($_GET['taxonomy']))
        {
            $this->taxonomy = $_GET['taxonomy'];
        }

        $this->searchString = null;
        if (isset($_POST['searchString']))
        {
            $this->searchString = $_POST['searchString'];
        }
        elseif (isset($_GET['searchString']))
        {
            $this->searchString = $_GET['searchString'];
        }

        if (isset($_GET['domain']))
        {
            ApplicationSession::setValue('DOMAIN',$_GET['domain']);
        }
        
        $this->uid = null;
        if (ApplicationSession::getValue('DOMAIN') == 'curator')
        {
            $this->uid = ApplicationSession::getValue('CURATOR_ID');
        }
        
        $this->curatorid = null;
        if (isset($_POST['curatorid']))
        {
            $this->curatorid = $_POST['curatorid'];
        }
        elseif (isset($_GET['curatorid']))
        {
            $this->curatorid = $_GET['curatorid'];
        }
        
        $this->searchFilter = null;
        if (isset($_POST['searchFilter']))
        {
            $this->searchFilter = $_POST['searchFilter'];
        }
        elseif (isset($_GET['searchFilter']))
        {
            $this->searchFilter = $_GET['searchFilter'];
        }

        $this->sortFilter = null;
        if (isset($_POST['sortFilter']))
        {
            $this->sortFilter = $_POST['sortFilter'];
        }
        elseif (isset($_GET['sortFilter']))
        {
            $this->sortFilter = $_GET['sortFilter'];
        }

        $this->pageFilter = null;
        if (isset($_POST['page']))
        {
            $this->pageFilter = $_POST['page'];
        }
        elseif (isset($_GET['page']))
        {
            $this->pageFilter = $_GET['page'];
        }

        $this->categoryFilter = null;
        if (isset($_POST['categoryFilter']))
        {
            $this->categoryFilter = $_POST['categoryFilter'];
        }
        elseif (isset($_GET['categoryFilter']))
        {
            $this->categoryFilter = $_GET['categoryFilter'];
        }
    }
}

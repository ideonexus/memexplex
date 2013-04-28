<?php

/**
 * Database CRUD management and Object-Relational Mapping (ORM)
 * for the Business Entity.
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author David Lightman
 */

class DataAccessComponentTriple
{
    /**
     * @var string These are the standard SQL columns we need.
     */
    protected $standardsqlcols = "
 s.id                AS id
,s.title             AS title
,s.description       AS description
,s.published         AS published
,s.date_published    AS date_published
,s.date              AS date
,m1.id               AS subject_meme_id
,m1.title            AS subject_meme_title
,m1.text             AS subject_meme_text
,m1.quote            AS subject_meme_quote
,p.id                AS predicate_id
,p.description       AS predicate_description
,m2.id               AS object_meme_id
,m2.title            AS object_meme_title
,m2.text             AS object_meme_text
,m2.quote            AS object_meme_quote
,s.curator_id        AS curator_id
,u.display_name      AS curator_display_name
,u.publish_by_default AS publish_by_default
,u.curator_level_id  AS curator_level_id
    ";

    /**
     * @var string These are the standard SQL tables we will join.
     */
    protected $standardsqlfrom = "
triple s
INNER JOIN meme m1 ON
	s.subject_meme_id = m1.id
INNER JOIN predicate p ON
	s.predicate_id = p.id
INNER JOIN meme m2 ON
	s.object_meme_id = m2.id
INNER JOIN curator u ON
	s.curator_id = u.id
    ";

    /**
     * @var string This is the standard order by clause.
     */
    protected $standardorderby = "date_published DESC,date DESC,title";

    /**
     * @var Pizza ANSI standard pizza.
     */
    protected $standardpizza = "Pepperoni and Mushrooms";
    /**
     * Just seeing if you were paying attention.
     */

    /**
     * Maps query results into a list of Triple entities.
     *
     * @param string $rs Database result set.
     * @return TripleList
     * @throws PersistenceExceptionProvider
     */
    protected function buildTripleListFromQueryResults($rs,$db)
    {
        $tripleList = new TripleList();

        foreach ($rs AS $row)
        {
            $id = $row['id'];
            $sql = "
SELECT
    t.id    AS taxonomy_id
    ,t.text AS taxonomy
FROM
    triple_taxonomy st
    INNER JOIN taxonomy t ON
    	st.taxonomy_id = t.id
WHERE
	st.triple_id = $id;
";
            $trs = $db->fetch_all_array($sql);
            $taxonomyList = new TaxonomyList;
            foreach ($trs AS $trow)
            {
                if (!$taxonomyList[$trow['taxonomy_id']])
                {
                    $taxonomyList[$trow['taxonomy_id']] =
                        new Taxonomy($trow['taxonomy_id'],$trow['taxonomy']);
                }
            }
            
            $subjectid = $row['subject_meme_id'];
            $sql = "
SELECT
    t.id    AS taxonomy_id
    ,t.text AS taxonomy
FROM
    meme_taxonomy mt
    INNER JOIN taxonomy t ON
    	mt.taxonomy_id = t.id
WHERE
	mt.meme_id = $subjectid;
";
            $trs = $db->fetch_all_array($sql);
            $subjectTaxonomyList = new TaxonomyList;
            foreach ($trs AS $trow)
            {
                if (!$subjectTaxonomyList[$trow['taxonomy_id']])
                {
                    $subjectTaxonomyList[$trow['taxonomy_id']] =
                        new Taxonomy($trow['taxonomy_id'],$trow['taxonomy']);
                }
            }
            
            $objectid = $row['object_meme_id'];
            $sql = "
SELECT
    t.id    AS taxonomy_id
    ,t.text AS taxonomy
FROM
    meme_taxonomy mt
    INNER JOIN taxonomy t ON
    	mt.taxonomy_id = t.id
WHERE
	mt.meme_id = $objectid;
";
            $trs = $db->fetch_all_array($sql);
            $objectTaxonomyList = new TaxonomyList;
            foreach ($trs AS $trow)
            {
                if (!$objectTaxonomyList[$trow['taxonomy_id']])
                {
                    $objectTaxonomyList[$trow['taxonomy_id']] =
                        new Taxonomy($trow['taxonomy_id'],$trow['taxonomy']);
                }
            }
            
            if (!$tripleList[$id])
            {
                $tripleList[$id] = new Triple
                (
                    $id
                    ,stripslashes($row['title'])
                    ,new Curator(
                        $row['curator_id']
                    	,null
                        ,$row['curator_display_name']
                        ,null
                        ,$row['publish_by_default']
                        ,new CuratorLevel(
                            $row['curator_level_id']
                            ,null
                            ,null
                        )
                    )
                    ,$row['published']
                    ,$row['date_published']
                    ,$row['date']
                    ,$taxonomyList

                    ,stripslashes($row['description'])
                    ,new Meme(
                        $row['subject_meme_id']
                        ,$row['subject_meme_title']
                        ,null,null,null,null
                        ,$subjectTaxonomyList
                        ,$row['subject_meme_text']
                        ,$row['subject_meme_quote']
                    )
                    ,new Predicate(
                        $row['predicate_id']
                        ,$row['predicate_description']
                    )
                    ,new Meme(
                        $row['object_meme_id']
                        ,$row['object_meme_title']
                        ,null,null,null,null
                        ,$objectTaxonomyList
                        ,$row['object_meme_text']
                        ,$row['object_meme_quote']
                    )
                );
            }
        }
        return $tripleList;
    }

    /**
     * Gets a Triple, duh. Returns one complete triple
     * if you have an id, or returns a list of triples
     * without one.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return TripleList
     */
    public function getTripleList(MemexPlexObjectSearchParameters $searchParameters)
    {
        $id           = $searchParameters->getId();
        $taxonomy     = $searchParameters->getTaxonomy();
        $searchString = $searchParameters->getSearchString();
        $uid          = $searchParameters->getUid();
        $curatorid    = $searchParameters->getCuratorId();
        $searchFilter = $searchParameters->getSearchFilter();
        $sortFilter   = $searchParameters->getSortFilter();
        $pageFilter   = $searchParameters->getPageFilter();

        if ($id)
        {
            $sqlWhere = "";
            $sqlAnd = "";
        }
        else
        {
            $sqlWhere = "WHERE s.published = 1";
            $sqlAnd = "AND s.published = 1";
            if ($uid)
            {
                $sqlWhere = "WHERE s.curator_id = $uid";
                $sqlAnd = "AND s.curator_id = $uid";
            }
            elseif ($curatorid)
            {
                $sqlWhere .= " AND s.curator_id = $curatorid";
                $sqlAnd .= " AND s.curator_id = $curatorid";
            }
            
            if ($searchFilter)
            {
                $sqlQ = "";
                if ($searchFilter == "published")
                {
                    $sqlQ = " AND s.published = 1";
                }
                elseif ($searchFilter == "unpublished")
                {
                    $sqlQ = " AND s.published = 0";
                }
                elseif (is_numeric($searchFilter))
                {
                    $sqlQ = " AND s.predicate_id = $searchFilter";
                }
                $sqlWhere = $sqlWhere.$sqlQ;
                $sqlAnd = $sqlAnd.$sqlQ;
            }
        }

        $orderby = $this->standardorderby;
        if ($sortFilter)
        {
            $orderby = $sortFilter;
        }
        elseif (ApplicationSession::getValue('DOMAIN') == 'curator')
        {
            $orderby = "date DESC,title";
        }
        
        $limitSql = "LIMIT 0,10";
        if ($limitFilter || $pageFilter)
        {
            //$currentPage = $pageFilter*$limitFilter;
            $currentPage = ($pageFilter*10)-10;
            //$limitSql = "LIMIT ".$currentPage.",".$limitFilter;
            $limitSql = "LIMIT ".$currentPage.",10";
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($id)
            {
                $sql = "
SELECT
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
WHERE
	s.id = {$id}
	$sqlAnd
ORDER BY
	$orderby;
                ";
            }
            elseif ($taxonomy)
            {
                $sql = "
SELECT SQL_CALC_FOUND_ROWS
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
    INNER JOIN triple_taxonomy st ON
    	st.triple_id = s.id
    INNER JOIN taxonomy t ON
    	st.taxonomy_id = t.id
WHERE
    t.text = '{$taxonomy}'
    $sqlAnd
ORDER BY
	$orderby
$limitSql;
                ";
            }
            elseif ($searchString)
            {
                $searchString = trim($searchString);
                $searchTermList = explode(' ',$searchString);

                $sql = "
SELECT SQL_CALC_FOUND_ROWS
	$this->standardsqlcols
    ,1 AS relevance
FROM
    $this->standardsqlfrom
WHERE
    s.title = '$searchString'
    $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,2 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        s.title LIKE '%$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,3 AS relevance
    FROM
        $this->standardsqlfrom
        INNER JOIN triple_taxonomy st ON
        	st.triple_id = s.id
        INNER JOIN taxonomy t ON
        	st.taxonomy_id = t.id
    WHERE
        t.text LIKE '%$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,4 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        (
            s.description LIKE '$searchString'
        )
        $sqlAnd";

                $firstLoop = true;
                foreach ($searchTermList AS $searchTerm)
                {
                    if (trim($searchTerm) != '')
                    {
                        if ($firstLoop)
                        {
                            $sql .= "
UNION
    SELECT
        $this->standardsqlcols
        ,5 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        (
            s.title LIKE '%$searchTerm%'
            OR s.description LIKE '$searchTerm'";
                            $firstLoop = false;
                        }
                        else
                        {
                            $sql .= "
            OR s.title LIKE '%$searchTerm%'
            OR s.description LIKE '$searchTerm'";
                        }
                    }
                }
                if (!$firstLoop)
                {
                    $sql .= "
		)
    	$sqlAnd";
                }
                $sql .= "
ORDER BY relevance, $orderby
$limitSql";
            }
            else
            {
                $sql = "
SELECT SQL_CALC_FOUND_ROWS
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
$sqlWhere
ORDER BY
	$orderby
$limitSql;
                ";
            }

            $rs = $db->fetch_all_array($sql);
            $totalRows = $db->fetch_all_array("SELECT FOUND_ROWS();");
            $tripleList = $this->buildTripleListFromQueryResults($rs,$db);
            $tripleList->setTotalRows($totalRows[0]["FOUND_ROWS()"]);
            return $tripleList;
        }
        catch (PersistenceException $e)
        {
            throw new PersistenceExceptionProvider
            (
                $e->getMessage()
                ,$e->getCode()
            );
        }

    }

    /**
     * Gets the TripleList associated to a Meme.
     *
     * @param integer $id A Meme DB id.
     * @return TripleList
     * @throws PersistenceExceptionProvider
     */
    public function getTripleListByMemeId($id=null,$publishedOnly=false)
    {
        $orderby = $this->standardorderby;
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($id)
            {
                $pubsql = "";
                if ($publishedOnly)
                {
                    $pubsql = "AND s.published = 1";
                }
                
                $rs = $db->fetch_all_array
                ("
SELECT
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
WHERE
	s.subject_meme_id = {$id}
	OR s.object_meme_id = {$id}
	$pubsql;
                ");

                return $this->buildTripleListFromQueryResults($rs,$db);
            }
        }
        catch (PersistenceException $e)
        {
            throw new PersistenceExceptionProvider
            (
                $e->getMessage()
                ,$e->getCode()
            );
        }
    }

    /**
     * CUD operations for TripleLists.
     *
     * @param DeltaList $deltaList Populated with Triple Objects.
     * @return integer TripleList id.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function saveTripleList(DeltaList $deltaList)
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            foreach ($deltaList as $delta)
            {
                $item = $delta->getObject();
                $flag = $delta->getFlag();

                $id                  = $item->getId();
                $title               = $item->getTitle();
                $description         = $item->getDescription();
                $subjectMemeId       = $item->getSubjectMeme()->getId();
                $predicateId         = $item->getPredicate()->getId();
                $objectMemeId        = $item->getObjectMeme()->getId();
                $curatorId           = ApplicationSession::getValue('CURATOR_ID');
            	$published           = $item->getPublished();
            	$datePublished       = $item->getDatePublished();
            	$date                = $item->getDate();
            	$taxonomyList        = $item->getTaxonomyList();

                if ($flag == Delta::INSERT)
                {
                	$data = array();
                	$data['id']              = $id;
                	$data['title']           = $title;
                	$data['description']     = $description;
                	$data['subject_meme_id'] = $subjectMemeId;
                	$data['predicate_id']    = $predicateId;
                	$data['object_meme_id']  = $objectMemeId;
                	$data['curator_id']      = $curatorId;
                	$data['published']       = $published;
                	$data['date_published']  = $datePublished;
                	$data['date']            = $date;
                	
                	$test = $db->query_insert("triple", $data);

                	$rs = $db->fetch_all_array
                    ("
SELECT
    MAX(id) AS id
FROM
    triple
                    ");

                    $id = null;
                    foreach ($rs AS $row)
                    {
                        $id = $row['id'];
                    }
                    //GET TAXONOMY ID LIST
                    $taxonomyDAC = new DataAccessComponentTaxonomy();
                    $taxonomyIdArray = $taxonomyDAC->saveTaxonomies($taxonomyList);
                    //ASSOCIATE TAXONOMIES TO MEMEXOBJECT
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        //ASSOCIATE TAXONOMIES TO MEME
                        $rs = $db->query
                        ("
INSERT INTO
    triple_taxonomy
VALUES
    ($taxonomyId,$id)
                        ");
                    }
                    return $id;
                }
                elseif ($flag == Delta::UPDATE)
                {
                	$title       = addslashes($title);
                	$description = addslashes($description);
                	
                    $rs = $db->query
                    ("
UPDATE
    triple
SET
     id                   = $id
    ,title                = '$title'
    ,description          = '$description'
    ,subject_meme_id      = $subjectMemeId
    ,predicate_id         = $predicateId
    ,object_meme_id       = $objectMemeId
    ,curator_id           = $curatorId
    ,published            = $published
    ,date_published       = '$datePublished'
    ,date                 = '$date'
WHERE
    id = $id
                    ");
                    //GET TAXONOMY ID LIST
                    $taxonomyDAC = new DataAccessComponentTaxonomy();
                    $taxonomyIdArray = $taxonomyDAC->saveTaxonomies($taxonomyList);
                    //DELETE EXISTING TAXONOMIES
                    $rs = $db->query
                    ("
DELETE FROM
    triple_taxonomy
WHERE
    triple_id = $id
                    ");
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        //ASSOCIATE TAXONOMIES TO MEME
                        $rs = $db->query
                        ("
INSERT INTO
    triple_taxonomy
VALUES
    ($taxonomyId,$id)
                        ");
                    }
                    return $id;
                }
                elseif ($flag == Delta::DELETE)
                {
                    $rs = $db->query
                    ("
DELETE FROM
    triple
WHERE
    id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    triple_taxonomy
WHERE
    triple_id = $id
                    ");
                    
                }
                
                return $id;
            }

            return true;
        }
        catch (PersistenceException $e)
        {
            throw new PersistenceExceptionProvider
            (
                $e->getMessage(),
                $e->getCode()
            );
        }

    }

    /**
     * Returns a list of Predicates.
     *
     * @return PredicateList
     * @throws PersistenceExceptionProvider
     */
    public function getPredicateList()
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $rs = $db->fetch_all_array
            ("
SELECT
     p.id          AS id
	,p.description AS description
FROM
    predicate p
ORDER BY
	description
            ");

            $predicateList = new PredicateList();

            foreach ($rs AS $row)
            {
                $predicateList[$row->id] = new Predicate
                (
            	    $row['id']
            	    ,$row['description']
            	);
            }

            return $predicateList;
        }
        catch (PersistenceException $e)
        {
            throw new PersistenceExceptionProvider
            (
                $e->getMessage()
                ,$e->getCode()
            );
        }

    }
}

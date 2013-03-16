<?php

/**
 * Database CRUD management and Object-Relational Mapping (ORM)
 * for the Business Entity.
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author Geordi La Forge
 */

class DataAccessComponentSchema
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
,s.curator_id        AS curator_id
,u.display_name      AS curator_display_name
,u.publish_by_default AS publish_by_default
,u.curator_level_id  AS curator_level_id
    ";

    /**
     * @var string These are the standard SQL tables we will join.
     */
    protected $standardsqlfrom = "
schema_map s
INNER JOIN curator u ON
	s.curator_id = u.id
    ";

    /**
     * @var string This is the standard order by clause.
     */
    protected $standardorderby = "date_published DESC,date DESC,title";

    /**
     * Takes database query results and builds a SchemaList out of 'em.
     *
     * @param string $rs Query results.
     * @return SchemaList
     * @throws PersistenceExceptionProvider
     */
    protected function buildSchemaListFromQueryResults($rs,$db)
    {
        $schemaList = new SchemaList();

        foreach ($rs AS $row)
        {
            $id = $row['id'];
            $sql = "
SELECT
    t.id    AS taxonomy_id
    ,t.text AS taxonomy
FROM
    schema_taxonomy st
    INNER JOIN taxonomy t ON
    	st.taxonomy_id = t.id
WHERE
	st.schema_id = $id;
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
            
            //Get MemexPlex Object Counts
            //(Placed here to prevent screwing up earlier row counts
            $memeCount = 0;
            $sql = "
SELECT
    s.id               AS schema_id
	,COUNT(sm.meme_id) AS meme_count
FROM
    schema_map s
	LEFT JOIN schema_meme sm ON s.id = sm.schema_id
WHERE
	s.id = $id
GROUP BY
	schema_id;
";

            $trs = $db->fetch_all_array($sql);
            foreach ($trs AS $trow)
            {
	            $memeCount = $trow['meme_count'];
            }
            
            if (!$schemaList[$id])
            {
                $schemaList[$id] = new Schema
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
                    ,$memeList
                    ,$childSchemaList
                    ,$memeCount
                );
            }
        }
        return $schemaList;
    }

    /**
     * Gets a Schema, duh. Returns one complete schema
     * if you have an id, or returns a list of schemas
     * without one.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return SchemaList
     * @throws PersistenceExceptionProvider
     */
    public function getSchemaList(MemexPlexObjectSearchParameters $searchParameters)
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
                elseif ($searchFilter == "childless")
                {
                    $sqlQ = " AND s.id NOT IN (SELECT sm.schema_id FROM schema_meme sm WHERE s.id = sm.schema_id)";
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
    schema_map s
    INNER JOIN curator u ON
    	s.curator_id = u.id
    INNER JOIN schema_taxonomy st ON
    	st.schema_id = s.id
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
        INNER JOIN schema_taxonomy st ON
        	st.schema_id = s.id
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
        s.description LIKE '%$searchString%'
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
            OR s.description LIKE '%$searchTerm%'";
                            $firstLoop = false;
                        }
                        else
                        {
                            $sql .= "
            OR s.title LIKE '%$searchTerm%'
            OR s.description LIKE '%$searchTerm%'";
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
            $schemaList = $this->buildSchemaListFromQueryResults($rs,$db);
            $schemaList->setTotalRows($totalRows[0]["FOUND_ROWS()"]);
            return $schemaList;
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
     * Fetches a List of Schemas that are Parents to a Schema.
     *
     * @param string $id Not just any ID, but a schema id!
     * @return SchemaList
     * @throws PersistenceExceptionProvider
     */
    public function getSchemaListByParentSchemaId($id=null)
    {
        $orderby = $this->standardorderby;
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($id)
            {
                $rs = $db->fetch_all_array
                ("
SELECT
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
    INNER JOIN schema_parent_child spc ON
    	spc.child_id = s.id
WHERE
	spc.parent_id = {$id}
ORDER BY
	$orderby;
                ");
            }

            return $this->buildSchemaListFromQueryResults($rs,$db);
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
     * Fetches a List of Schemas that are Children to a Schema.
     *
     * @param string $id Not just any ID, but a schema id!
     * @return SchemaList
     * @throws PersistenceExceptionProvider
     */
    public function getSchemaListByChildSchemaId($id=null)
    {
        $orderby = $this->standardorderby;
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($id)
            {
                $rs = $db->fetch_all_array
                ("
SELECT
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
    INNER JOIN schema_parent_child spc ON
    	spc.parent_id = s.id
WHERE
	spc.child_id = {$id}
ORDER BY
	$orderby;
                ");
            }

            return $this->buildSchemaListFromQueryResults($rs,$db);
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
     * Fetches a List of Schemas that use a Meme.
     *
     * @param string $id Meme DB id!
     * @return SchemaList
     * @throws PersistenceExceptionProvider
     */
    public function getSchemaListByMemeId($id=null,$publishedOnly=false)
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
    INNER JOIN schema_meme sm ON
    	sm.schema_id =  s.id
WHERE
	sm.meme_id = {$id}
	$pubsql;
                ");

                return $this->buildSchemaListFromQueryResults($rs,$db);
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
     * CUD operations for SchemaLists.
     *
     * @param DeltaList $deltaList Populated with Schemas.
     * @return integer Schema Id.
     * @throws PersistenceExceptionProvider
     */
    public function saveSchemaList(DeltaList $deltaList)
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
            	$published           = $item->getPublished();
            	$datePublished       = $item->getDatePublished();
            	$date                = $item->getDate();
            	$taxonomyList        = $item->getTaxonomyList();

                if ($flag == Delta::INSERT)
                {
                	$data = array();
                	$data['id']             = $id;
                	$data['title']          = $title;
                	$data['description']    = $description;
                	$data['curator_id']     = ApplicationSession::getValue('CURATOR_ID');
                	$data['published']      = $published;
                	$data['date_published'] = $datePublished;
                	$data['date']           = $date;
                	$test = $db->query_insert("schema_map", $data);

                	$rs = $db->fetch_all_array
                    ("
SELECT
    MAX(id) AS id
FROM
    schema_map
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
    schema_taxonomy
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
    schema_map
SET
     id                   = $id
    ,title                = '$title'
    ,description          = '$description'
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
    schema_taxonomy
WHERE
    schema_id = $id
                    ");
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        //ASSOCIATE TAXONOMIES TO MEME
                        $rs = $db->query
                        ("
INSERT INTO
    schema_taxonomy
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
    schema_map
WHERE
    id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    schema_meme
WHERE
    schema_id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    schema_taxonomy
WHERE
    schema_id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    schema_parent_child
WHERE
    parent_id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    schema_parent_child
WHERE
    child_id = $id
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

}

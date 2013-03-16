<?php

/**
 * Database CRUD management and Object-Relational Mapping (ORM)
 * for the Business Entity.
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author Willow Rosenberg
 */

class DataAccessComponentReference
{

    /**
     * @var string These are the standard SQL columns we need.
     */
    protected $standardsqlcols = "
     r.id                           AS id
	,rsty.id                        AS reference_super_type_id
	,rsty.description               AS reference_super_type_description
    ,r.reference_type_id            AS reference_type_id
	,rty.reference_type_description AS reference_type_description
    ,r.reference_date               AS reference_date
    ,r.title                        AS title
    ,r.published                    AS published
    ,r.date_published               AS date_published
    ,r.date                         AS date
    ,r.publication_location         AS publication_location
    ,r.publisher_periodical         AS publisher_periodical
    ,r.volume_pages                 AS volume_pages
    ,r.url                          AS url
    ,r.reference_service            AS reference_service
    ,r.date_retrieved               AS date_retrieved
    ,r.isbn                         AS isbn
    ,r.ean                          AS ean
    ,r.upc                          AS upc
    ,r.small_image_url              AS small_image_url
    ,r.large_image_url              AS large_image_url
    ,r.asin                         AS asin
    ,r.amazon_url                   AS amazon_url
    ,r.curator_id                   AS curator_id
    ,u.display_name                 AS curator_display_name
	,u.publish_by_default           AS publish_by_default 
    ,u.curator_level_id             AS curator_level_id
    	";

    /**
     * @var string These are the standard SQL tables we will join.
     */
    protected $standardsqlfrom = "
	reference r
    INNER JOIN curator u ON
    	r.curator_id = u.id
    INNER JOIN reference_type rty ON
    	r.reference_type_id = rty.id
    INNER JOIN reference_super_type rsty ON
    	rsty.id = rty.reference_super_type_id
    	";

    /**
     * @var string This is the standard order by clause.
     */
    protected $standardorderby = "date_published DESC,date DESC,title";

    /**
     * Takes database query results and builds a ReferenceList out of 'em.
     *
     * @param string $rs Query results.
     * @return ReferenceList
     * @throws PersistenceExceptionProvider
     */
    protected function buildReferenceListFromQueryResults($rs,$db)
    {
        $referenceList = new ReferenceList();

        foreach ($rs AS $row)
        {
            $id = $row['id'];
            $sql = "
SELECT
    a.id          AS author_id
    ,a.last_name  AS last_name
    ,a.first_name AS first_name
FROM
    reference_author ra
    INNER JOIN author a ON
    	ra.author_id = a.id
WHERE
	ra.reference_id = $id;
";
            $ars = $db->fetch_all_array($sql);
            $authorList = new AuthorList;
            foreach ($ars AS $arow)
            {
                if (!$authorList[$arow['author_id']])
                {
                    $authorList[$arow['author_id']] =
                        new Author(
                            $arow['author_id']
                            ,stripslashes($arow['first_name'])
                            ,stripslashes($arow['last_name'])
                        );
                }
            }
            
            $sql = "
SELECT
    t.id    AS taxonomy_id
    ,t.text AS taxonomy
FROM
    reference_taxonomy rt
    INNER JOIN taxonomy t ON
    	rt.taxonomy_id = t.id
WHERE
	rt.reference_id = $id;
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
    r.id                    AS reference_id
	,COUNT(mr.meme_id) AS meme_count
FROM
    reference r
	LEFT JOIN meme_reference mr ON r.id = mr.reference_id
WHERE
	r.id = $id
GROUP BY
	reference_id;
";

            $trs = $db->fetch_all_array($sql);
            foreach ($trs AS $trow)
            {
	            $memeCount = $trow['meme_count'];
            }
            
            if (!$referenceList[$id])
            {
                $referenceList[$id] = new Reference
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

                    ,new ReferenceSuperType(
                	    $row['reference_super_type_id']
                	    ,$row['reference_super_type_description']
                	)
                    ,new ReferenceType(
                	    $row['reference_type_id']
                	    ,$row['reference_super_type_id']
                	    ,$row['reference_type_description']
                	)
                    ,$authorList
                    ,$row['reference_date']
                    ,stripslashes($row['publication_location'])
                    ,stripslashes($row['publisher_periodical'])
                    ,stripslashes($row['volume_pages'])
                    ,stripslashes($row['url'])
                    ,stripslashes($row['reference_service'])
                    ,$row['date_retrieved']
                    ,$row['isbn']
                    ,$row['ean']
                    ,$row['upc']
                    ,$row['small_image_url']
                    ,$row['large_image_url']
                    ,$row['asin']
                    ,$row['amazon_url']
                    ,$memeCount
                );
            }
        }

        return $referenceList;
    }

    /**
     * Gets the Reference Id associated to a Meme.
     *
     * @param integer $id A Meme DB id.
     * @return integer Reference id.
     * @throws PersistenceExceptionProvider
     */
    public function getReferenceIdByMemeId($id=null)
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
    reference_id AS id
FROM
    meme_reference
WHERE
	meme_id = {$id};
                ");
            }

            $referenceId = null;
            if (count($rs) > 0)
            {
                foreach ($rs AS $row)
                {
                    $referenceId = $row['id'];
                    break;
                }
            }
            //$rs->clear();
            return $referenceId;
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
     * Returns a ReferenceList based on the Search Parameters provided.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return ReferenceList
     * @throws PersistenceExceptionProvider
     */
    public function getReferenceList(MemexPlexObjectSearchParameters $searchParameters)
    {
        $id           = $searchParameters->getId();
        $author       = $searchParameters->getAuthor();
        $taxonomy     = $searchParameters->getTaxonomy();
        $searchString = $searchParameters->getSearchString();
        $uid          = $searchParameters->getUid();
        $curatorid    = $searchParameters->getCuratorId();
        $searchFilter = $searchParameters->getSearchFilter();
        $sortFilter   = $searchParameters->getSortFilter();
        $pageFilter   = $searchParameters->getPageFilter();

        $sqlWhere = "";
        $sqlAnd = "";
        if (!$id)
        {
            $sqlWhere = "WHERE r.published = 1";
            $sqlAnd = "AND r.published = 1";
            
            if ($uid)
            {
                $sqlWhere = "WHERE r.curator_id = $uid";
                $sqlAnd = "AND r.curator_id = $uid";
            }
            elseif ($curatorid)
            {
                $sqlWhere .= " AND r.curator_id = $curatorid";
                $sqlAnd .= " AND r.curator_id = $curatorid";
            }
            
            if ($searchFilter)
            {
                $sqlQ = "";
                if ($searchFilter == "published")
                {
                    $sqlQ = " AND r.published = 1";
                }
                elseif ($searchFilter == "unpublished")
                {
                    $sqlQ = " AND r.published = 0";
                }
                elseif ($searchFilter == "childless")
                {
                    $sqlQ = " AND r.id NOT IN (SELECT mr.reference_id FROM meme_reference mr WHERE r.id = mr.reference_id)";
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
	r.id = {$id}
	$sqlAnd
ORDER BY
	$orderby;
                ";
            }
            elseif ($author)
            {
                $sql = "
SELECT SQL_CALC_FOUND_ROWS
    $this->standardsqlcols
FROM
    reference r
    INNER JOIN curator u ON
    	r.curator_id = u.id
    INNER JOIN reference_author ra ON
    	ra.reference_id = r.id
    INNER JOIN author a ON
    	ra.author_id = a.id
    INNER JOIN reference_type rty ON
    	r.reference_type_id = rty.id
    INNER JOIN reference_super_type rsty ON
    	rsty.id = rty.reference_super_type_id
WHERE
    a.id = '{$author}'
    $sqlAnd
ORDER BY
	$orderby
$limitSql;
                ";
            }
            elseif ($taxonomy)
            {
                $sql = "
SELECT SQL_CALC_FOUND_ROWS
    $this->standardsqlcols
FROM
    reference r
    INNER JOIN curator u ON
    	r.curator_id = u.id
    INNER JOIN reference_taxonomy rt ON
    	rt.reference_id = r.id
    INNER JOIN taxonomy t ON
    	rt.taxonomy_id = t.id
    INNER JOIN reference_type rty ON
    	r.reference_type_id = rty.id
    INNER JOIN reference_super_type rsty ON
    	rsty.id = rty.reference_super_type_id
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
    r.title = '$searchString'
    $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,2 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        r.title LIKE '%$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,3 AS relevance
    FROM
        $this->standardsqlfrom
        INNER JOIN reference_taxonomy rt ON
        	rt.reference_id = r.id
        INNER JOIN taxonomy t ON
        	rt.taxonomy_id = t.id
    WHERE
        t.text LIKE '%$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,4 AS relevance
    FROM
        $this->standardsqlfrom
        LEFT JOIN reference_author ra ON
        	r.id = ra.reference_id
        	LEFT JOIN author a ON
        		ra.author_id = a.id
    WHERE
        (
            a.first_name LIKE '%$searchString%'
            OR a.last_name LIKe '%$searchString%'
            OR r.publication_location LIKE '%$searchString%'
            OR r.publisher_periodical LIKE '%$searchString%'
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
        LEFT JOIN reference_author ra ON
        	r.id = ra.reference_id
        	LEFT JOIN author a ON
        		ra.author_id = a.id
    WHERE
        (
            r.title LIKE '%$searchTerm%'
            OR a.first_name LIKE '%$searchTerm%'
            OR a.last_name LIKe '%$searchTerm%'
            OR r.publication_location LIKE '%$searchTerm%'
            OR r.publisher_periodical LIKE '%$searchTerm%'";
                            $firstLoop = false;
                        }
                        else
                        {
                            $sql .= "
            OR r.title LIKE '%$searchTerm%'
            OR a.first_name LIKE '%$searchTerm%'
            OR a.last_name LIKe '%$searchTerm%'
            OR r.publication_location LIKE '%$searchTerm%'
            OR r.publisher_periodical LIKE '%$searchTerm%'";
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
$limitSql;";
            }

            $rs = $db->fetch_all_array($sql);
            $totalRows = $db->fetch_all_array("SELECT FOUND_ROWS();");
            $referenceList = $this->buildReferenceListFromQueryResults($rs,$db);
            $referenceList->setTotalRows($totalRows[0]["FOUND_ROWS()"]);
            return $referenceList;
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
     * Fetches a List of References that belong to memes that belong to a schema.
     *
     * @param string $schemaid Not just any ID, but a schema id!
     * @return ReferenceList
     */
    public function getReferenceListBySchemaId($schemaid=null)
    {
        $orderby = $this->standardorderby;
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($schemaid)
            {
                $rs = $db->fetch_all_array
                ("
SELECT
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
    INNER JOIN meme_reference mr ON
    	mr.reference_id = r.id
WHERE
	mr.meme_id IN
	(
		SELECT
			sm.meme_id
		FROM
			schema_meme sm
		WHERE
			sm.schema_id = {$schemaid}
	)
ORDER BY
	$orderby;
                ");
            }

            return $this->buildReferenceListFromQueryResults($rs,$db);
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
     * Fetches a List of References that are children of a Reference.
     *
     * @param string $id Not just any ID, but a reference id!
     * @return ReferenceList
     */
    public function getReferenceListByParentReferenceId($id=null)
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
    INNER JOIN reference_parent_child rpc ON
    	rpc.child_id = r.id
WHERE
	rpc.parent_id = {$id}
ORDER BY
	$orderby;
                ");
            }

            return $this->buildReferenceListFromQueryResults($rs,$db);
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
     * Fetches a List of References that are Parents of a Reference.
     * While human beings have two parents in meatspace, in the land of
     * References, they only have one parent. Still, we put that single
     * Parent in a list because everything else returns a ReferenceList,
     * and it makes things consistent.
     *
     * @param string $id Not just any ID, but a reference id!
     * @return ReferenceList
     */
    public function getReferenceListByChildReferenceId($id=null)
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
    INNER JOIN reference_parent_child rpc ON
    	rpc.parent_id = r.id
WHERE
	rpc.child_id = {$id}
ORDER BY
	$orderby;
                ");
            }

            return $this->buildReferenceListFromQueryResults($rs,$db);
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
     * CUD operations for Authors.
     *
     * @param AuthorList $authorList An array of Author Objects.
     * @return array List of Author Ids.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function saveAuthors($authorList)
    {
        if (count($authorList) > 0)
        {
            try
            {
                $db = new MySqlDatabase();
                $db->connect();

                //BUILD AN ARRAY OF EXISTING AUTHOR IDS
                $idArray = array();
                foreach ($authorList as $item)
                {
                    if (trim($item->getLastName()) != ""
                        || trim($item->getFirstName()) != "")
                    {
                        $firstName = addslashes($item->getFirstName());
                    	$lastName = addslashes($item->getLastName());
                        
                        //GET IDS FOR EXISTING AUTHORS
                        $rs = $db->fetch_all_array
                        ("
SELECT
    id as id
FROM
    author
WHERE
    first_name = '$firstName'
    AND last_name = '$lastName'
                        ");
    
                        if (count($rs) > 0)
                        {
                            foreach ($rs AS $row)
                            {
                                $idArray[] = $row['id'];
                            }
                        }
                        else
                        {
                            //INSERT NON-EXISTENT AUTHORS
                            $data = array();
                            $data['id']         = "";
                            $data['first_name'] = $item->getFirstName();
                            $data['last_name']  = $item->getLastName();
                            $db->query_insert("author", $data);
                                    
                            //GET IDS FOR NON-EXISTENT AUTHORS
                            $idrs = $db->fetch_all_array
                            ("
SELECT
    MAX(id) as id
FROM
    author
                            ");
                            
                            foreach ($idrs AS $row)
                            {
                                $idArray[] = $row['id'];
                            }
                        }
                    }
                }

                //RETURN AUTHOR ID LIST
                return $idArray;
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
    
    /**
     * CUD (Mooooo!) operations for ReferenceLists.
     *
     * @param DeltaList $deltaList Populated with Reference Objects.
     * @return integer Reference id.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function saveReferenceList(DeltaList $deltaList)
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
                $referenceTypeId     = $item->getReferenceType()->getId();
                $referenceDate       = $item->getReferenceDate();
                $title               = $item->getTitle();
                $publicationLocation = $item->getPublicationLocation();
                $publisherPeriodical = $item->getPublisherPeriodical();
                $volumePages         = $item->getVolumePages();
                $url                 = $item->getUrl();
                $referenceService    = $item->getReferenceService();
                $dateRetrieved       = $item->getDateRetrieved();
            	$published           = $item->getPublished();
            	$datePublished       = $item->getDatePublished();
            	$date                = $item->getDate();
            	$isbn                = $item->getIsbn();
            	$ean                 = $item->getEan();
            	$upc                 = $item->getUpc();
            	$smallImageUrl       = $item->getSmallImageUrl();
            	$largeImageUrl       = $item->getLargeImageUrl();
            	$asin                = $item->getAsin();
            	$amazonUrl           = $item->getAmazonUrl();
            	
            	$authorList          = $item->getAuthors();
                $taxonomyList        = $item->getTaxonomyList();

                if ($flag == Delta::INSERT)
                {
                	$data = array();
                	$data['id']                   = $id;
                	$data['reference_type_id']    = $referenceTypeId;
                	$data['reference_date']       = $referenceDate;
                	$data['title']                = $title;
                	$data['publication_location'] = $publicationLocation;
                	$data['publisher_periodical'] = $publisherPeriodical;
                	$data['volume_pages']         = $volumePages;
                	$data['url']                  = $url;
                	$data['reference_service']    = $referenceService;
                	$data['date_retrieved']       = $dateRetrieved;
                	$data['curator_id']           = ApplicationSession::getValue('CURATOR_ID');
                	$data['published']            = $published;
                	$data['date_published']       = $datePublished;
                	$data['date']                 = $date;
            		$data['isbn']                 = $isbn;
            		$data['ean']                  = $ean;
            		$data['upc']                  = $upc;
            		$data['small_image_url']      = $smallImageUrl;
            		$data['large_image_url']      = $largeImageUrl;
            		$data['asin']                 = $asin;
            		$data['amazon_url']           = $amazonUrl;
            		
                	$test = $db->query_insert("reference", $data);

                	$rs = $db->fetch_all_array
                    ("
SELECT
    MAX(id) AS id
FROM
    reference
                    ");

                    $id = null;
                    foreach ($rs AS $row)
                    {
                        $id = $row['id'];
                    }
                    
                    //ASSOCIATE AUTHORS TO REFERENCE
                    $authorIdArray = $this->saveAuthors($authorList);
                    foreach ($authorIdArray as $authorId)
                    {
                        $rs = $db->query
                        ("
INSERT INTO
    reference_author
VALUES
    ($id,$authorId)
                        ");
                    }
                    
                    //GET TAXONOMY ID LIST
                    $taxonomyDAC = new DataAccessComponentTaxonomy();
                    $taxonomyIdArray = $taxonomyDAC->saveTaxonomies($taxonomyList);
                    //ASSOCIATE TAXONOMIES TO REFERENCE
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        $rs = $db->query
                        ("
INSERT INTO
    reference_taxonomy
VALUES
    ($taxonomyId,$id)
                        ");
                    }
                    
                    return $id;
                }
                elseif ($flag == Delta::UPDATE)
                {
                	$referenceDate       = addslashes($referenceDate);
                	$title               = addslashes($title);
                	$publicationLocation = addslashes($publicationLocation);
                	$publisherPeriodical = addslashes($publisherPeriodical);
                	$volumePages         = addslashes($volumePages);
                	$url                 = addslashes($url);
                	$referenceService    = addslashes($referenceService);
                	
                    $rs = $db->query
                    ("
UPDATE
    reference
SET
     id                   = $id
	,reference_type_id    = $referenceTypeId
    ,reference_date       = '$referenceDate'
    ,title                = '$title'
    ,publication_location = '$publicationLocation'
    ,publisher_periodical = '$publisherPeriodical'
    ,volume_pages         = '$volumePages'
    ,url                  = '$url'
    ,reference_service    = '$referenceService'
    ,date_retrieved       = '$dateRetrieved'
    ,published            = $published
    ,date_published       = '$datePublished'
    ,date                 = '$date'
    ,isbn                 = '$isbn'
    ,ean                  = '$ean'
    ,upc                  = '$upc'
    ,small_image_url      = '$smallImageUrl'
    ,large_image_url      = '$largeImageUrl'
    ,asin                 = '$asin'
    ,amazon_url           = '$amazonUrl'
WHERE
    id = $id
                    ");
                    
                    //ASSOCIATE AUTHORS TO REFERENCE
                    $authorIdArray = $this->saveAuthors($authorList);
                    //DELETE EXISTING AUTHOR REFERENCES
                    $rs = $db->query
                    ("
DELETE FROM
    reference_author
WHERE
    reference_id = $id
                    ");
                    foreach ($authorIdArray as $authorId)
                    {
                        $rs = $db->query
                        ("
INSERT INTO
    reference_author
VALUES
    ($id,$authorId)
                        ");
                    }
                    
                    //GET TAXONOMY ID LIST
                    $taxonomyDAC = new DataAccessComponentTaxonomy();
                    $taxonomyIdArray = $taxonomyDAC->saveTaxonomies($taxonomyList);
                    //DELETE EXISTING TAXONOMIES
                    $rs = $db->query
                    ("
DELETE FROM
    reference_taxonomy
WHERE
    reference_id = $id
                    ");
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        //ASSOCIATE TAXONOMIES TO MEME
                        $rs = $db->query
                        ("
INSERT INTO
    reference_taxonomy
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
    reference
WHERE
    id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    reference_author
WHERE
    reference_id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    reference_taxonomy
WHERE
    reference_id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    reference_parent_child
WHERE
    parent_id = $id
                    ");

                    $rs = $db->query
                    ("
DELETE FROM
    reference_parent_child
WHERE
    child_id = $id
                    ");

                    $rs = $db->query
                    ("
DELETE FROM
    meme_reference
WHERE
    reference_id = $id
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
     * Returns a list of ReferenceSuperTypes.
     *
     * @return ReferenceSuperTypeList
     * @throws PersistenceExceptionProvider
     */
    public function getReferenceSuperTypeList()
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $rs = $db->fetch_all_array
            ("
SELECT
     rst.id          AS id
	,rst.description AS description
FROM
    reference_super_type rst
ORDER BY
	description
            ");

            $referenceSuperTypeList = new ReferenceSuperTypeList();

            foreach ($rs AS $row)
            {
                $referenceSuperTypeList[$row->id] = new ReferenceSuperType
                (
            	    $row['id']
            	    ,$row['description']
            	);
            }
            //$rs->clear();
            return $referenceSuperTypeList;
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
     * Returns a list of ReferenceTypes.
     *
     * @return ReferenceTypeList
     * @throws PersistenceExceptionProvider
     */
    public function getReferenceTypeList()
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $rs = $db->fetch_all_array
            ("
SELECT
     rt.id                         AS id
    ,rt.reference_super_type_id    AS reference_super_type_id
	,rt.reference_type_description AS reference_type_description
FROM
    reference_type rt
ORDER BY
	reference_type_description
            ");

            $referenceTypeList = new ReferenceTypeList();

            foreach ($rs AS $row)
            {
                $referenceTypeList[$row->id] = new ReferenceType
                (
            	    $row['id']
            	    ,$row['reference_super_type_id']
            	    ,$row['reference_type_description']
            	);
            }
            //$rs->clear();
            return $referenceTypeList;
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

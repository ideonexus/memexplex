<?php

/**
 * This DAC contains all the wild and wonderful Es-Que-El
 * necessary for CRUD'ing memes from the database! Yay!
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author Hermione Granger
 */

class DataAccessComponentMeme
{
    /**
     * These are the standard columns we'll be pulling to
     * populate our meme objects and object lists. This
     * is an example of the Don't Repeat Yourself (DRY) Principle
     * AAAAAAND and example of laziness as a good thing because
     * we were too lazy to change all the columns in all the squirrel
     * statements. Go Laziness!!!
     *
     * @var string
     */
    protected $standardsqlcols = "
 m.id                   AS id
,m.title                AS title
,m.text                 AS text
,m.quote                AS quote
,m.published            AS published
,m.date_published       AS date_published
,m.date                 AS date
,m.curator_id           AS curator_id
,u.display_name         AS curator_display_name
,u.publish_by_default   AS publish_by_default
,u.curator_level_id     AS curator_level_id
                ";

    /**
     * See above comment about DRY Principles and virtuous
     * laziness... Ditto here, but for a squirrel FROM clause.
     *
     * @var string
     */
    protected $standardsqlfrom = "
meme m
INNER JOIN curator u ON m.curator_id = u.id
";

    /**
     * Are you still reading these comments?
     *         SEE ABOVE JERKY!!!
     *             !DRY!
     *           !LAZINESS!
     *    Except this is for ORDER BY!
     *
     * @var string
     */
    protected $standardorderby = "date_published DESC,date DESC,title";
    
    /**
     * Stop Words. Because I like to work harder, not smarter.
     * Obviously.
     *
     * @var string
     */
    protected $stopWords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at"
	, "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by"
	, "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry"
	, "de", "describe", "detail", "do", "done", "down", "due", "during"
	, "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except"
	, "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further"
	, "get", "give", "go"
	, "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred"
	, "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself"
	, "keep"
	, "last", "latter", "latterly", "least", "less", "ltd"
	, "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself"
	, "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere"
	, "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own"
	,"part", "per", "perhaps", "please", "put"
	, "rather", "re"
	, "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system"
	, "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two"
	, "un", "under", "until", "up", "upon", "us"
	, "very", "via"
	, "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would"
	, "yet", "you", "your", "yours", "yourself", "yourselves"
	, "the");
   
    
    /**
     * This method takes a result set from a squirrelly
     * query and builds a MemeList object populated with
     * lots of baby-memes. Like building a family... a
     * single parent family... A sad, lonely single parent
     * struggling to raise all these damn memes so they
     * might one day make it to the presentation layer, where
     * the MemeList can live vacariously through their
     * adventures in web browsers around the world.
     *
     * @param string $rs Query results.
     * @return MemeList
     */
    protected function buildMemeListFromQueryResults($rs,$db)
    {
        $memeList = new MemeList();

        foreach ($rs AS $row)
        {
            $id = $row['id'];
            
            //Get Taxonomies
            $sql = "
SELECT
    t.id    AS taxonomy_id
    ,t.text AS taxonomy
FROM
    meme_taxonomy mt
    INNER JOIN taxonomy t ON
    	mt.taxonomy_id = t.id
WHERE
	mt.meme_id = $id;
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
            $referenceCount = 0;
            $tripleCount = 0;
            $schemaCount = 0;
            $sql = "
SELECT
    m.id                    AS meme_id
	,COUNT(mr.reference_id) AS reference_count
	,COUNT(t.id)            AS triple_count
	,COUNT(sm.schema_id)    AS schema_count
FROM
    meme m
	LEFT JOIN meme_reference mr ON m.id = mr.meme_id
	LEFT JOIN triple t ON m.id = t.subject_meme_id OR m.id = t.object_meme_id
	LEFT JOIN schema_meme sm ON m.id = sm.meme_id
WHERE
	m.id = $id
GROUP BY
	meme_id;
";

            $trs = $db->fetch_all_array($sql);
            foreach ($trs AS $trow)
            {
	            $referenceCount = $trow['reference_count'];
	            $tripleCount = $trow['triple_count'];
	            $schemaCount = $trow['schema_count'];
            }
            
            if (!$memeList[$id])
            {
                $memeList[$id] = new Meme(
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

                    ,stripslashes($row['text'])
                    ,stripslashes($row['quote'])
                    ,$referenceCount
                    ,$tripleCount
                    ,$schemaCount
                );
            }
        }

        return $memeList;
    }

    /**
     * This method's all like, "I got memes yo."
     * And the requesting object's all like, "I got search params yo."
     * And the getMeme method's all like, "Lemme see it bro."
     * And the business object's all like, "Here ya go bro."
     * And the getMeme's all like, "Cool yo. Here's some memes bro."
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return MemeList
     */
    public function getMemeList(MemexPlexObjectSearchParameters $searchParameters)
    {
        $id                 = $searchParameters->getId();
        $taxonomy           = $searchParameters->getTaxonomy();
        $searchString       = $searchParameters->getSearchString();
        $uid                = $searchParameters->getUid();
        $curatorid          = $searchParameters->getCuratorId();
        $searchFilter       = $searchParameters->getSearchFilter();
        $sortFilter         = $searchParameters->getSortFilter();
        $pageFilter         = $searchParameters->getPageFilter();
        $excludeSchemaId    = $searchParameters->getExcludeSchemaId();
        $excludeReferenceId = $searchParameters->getExcludeReferenceId();

        $sqlWhere = "";
        $sqlAnd = "";
        if (!$id)
        {
            $sqlWhere = "WHERE m.published = 1";
            $sqlAnd = "AND m.published = 1";
            
            if ($uid)
            {
                $sqlWhere = "WHERE m.curator_id = $uid";
                $sqlAnd = "AND m.curator_id = $uid";
            }
            elseif ($curatorid)
            {
                $sqlWhere .= " AND m.curator_id = $curatorid";
                $sqlAnd .= " AND m.curator_id = $curatorid";
            }
            
            if ($searchFilter)
            {
                $sqlQ = "";
                if ($searchFilter == "published")
                {
                    $sqlQ = " AND m.published = 1";
                }
                elseif ($searchFilter == "unpublished")
                {
                    $sqlQ = " AND m.published = 0";
                }
                elseif ($searchFilter == "orphaned")
                {
                    $sqlQ = " AND m.id NOT IN (SELECT mr.meme_id FROM meme_reference mr WHERE m.id = mr.meme_id)";
                }
                elseif ($searchFilter == "schemaless")
                {
                    $sqlQ = " AND m.id NOT IN (SELECT sm.meme_id FROM schema_meme sm WHERE m.id = sm.meme_id)";
                }
                $sqlWhere = $sqlWhere.$sqlQ;
                $sqlAnd = $sqlAnd.$sqlQ;
            }

            if ($excludeSchemaId)
            {
                $sqlQ = " AND m.id NOT IN (SELECT ms.meme_id FROM schema_meme ms WHERE ms.schema_id = {$excludeSchemaId})";
                $sqlWhere = $sqlWhere.$sqlQ;
                $sqlAnd = $sqlAnd.$sqlQ;
            }
            elseif ($excludeReferenceId)
            {
                $sqlQ = " AND m.id NOT IN (SELECT mr.meme_id FROM meme_reference mr WHERE mr.reference_id = {$excludeReferenceId})";
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
	m.id = {$id}
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
    meme m
    INNER JOIN curator u ON
    	m.curator_id = u.id
    INNER JOIN meme_taxonomy mt ON
    	mt.meme_id = m.id
    INNER JOIN taxonomy t ON
    	mt.taxonomy_id = t.id
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
/**
 * [TODO: Figure out fulltext natural language search]
//http://devzone.zend.com/article/1304
//http://dev.mysql.com/doc/refman/5.1/en/fulltext-natural-language.html
                    $sql = "
SELECT SQL_CALC_FOUND_ROWS
    $this->standardsqlcols
    ,MATCH(m.title, m.text, m.quote) AGAINST('$searchString') AS relevance
FROM
    $this->standardsqlfrom
WHERE
    MATCH(m.title, m.text, m.quote) AGAINST('$searchString')
    $sqlAnd
ORDER BY relevance,$orderby
$limitSql
                    ";
*/
                    $sql = "
SELECT SQL_CALC_FOUND_ROWS DISTINCT 
	id ,title ,text ,quote ,published ,date_published ,date ,curator_id ,curator_display_name ,publish_by_default ,curator_level_id 
FROM ( 
SELECT
    $this->standardsqlcols
    ,1 AS relevance
FROM
    $this->standardsqlfrom
WHERE
    m.title = '$searchString'
    $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,2 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        m.title LIKE '$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,3 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        m.title LIKE '% $searchString %'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,4 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        m.title LIKE '%$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,5 AS relevance
    FROM
        $this->standardsqlfrom
        INNER JOIN meme_taxonomy mt ON
        	mt.meme_id = m.id
        INNER JOIN taxonomy t ON
        	mt.taxonomy_id = t.id
    WHERE
        t.text LIKE '%$searchString%'
        $sqlAnd
UNION
    SELECT
        $this->standardsqlcols
        ,6 AS relevance
    FROM
        $this->standardsqlfrom
    WHERE
        (
            m.text LIKE '%$searchString%'
            OR m.quote LIKE '%$searchString%'
        )
        $sqlAnd";

                    $firstLoop = true;
                    foreach ($searchTermList AS $searchTerm)
                    {
                        if (trim($searchTerm) != ''
                        	&& !in_array(strtolower(trim($searchTerm)),$this->stopWords))
                        {
                            if ($firstLoop)
                            {
                                $sql .= "
UNION
    SELECT
        $this->standardsqlcols
        ,7 AS relevance
    FROM
        $this->standardsqlfrom
        INNER JOIN meme_taxonomy mt ON
        	mt.meme_id = m.id
        INNER JOIN taxonomy t ON
        	mt.taxonomy_id = t.id
    WHERE
        (
            t.text LIKE '%$searchTerm%'
        	OR m.title LIKE '%$searchTerm%'
            OR m.text LIKE '%$searchTerm%'
            OR m.quote LIKE '%$searchTerm%'";
                                $firstLoop = false;
                            }
                            else
                            {
                                $sql .= "
            OR t.text LIKE '%$searchTerm%'
        	OR m.title LIKE '%$searchTerm%'
            OR m.text LIKE '%$searchTerm%'
            OR m.quote LIKE '%$searchTerm%'";
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
ORDER BY relevance,$orderby
) AS result $limitSql;";
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
            $memeList = $this->buildMemeListFromQueryResults($rs,$db);
            $memeList->setTotalRows($totalRows[0]["FOUND_ROWS()"]);
            return $memeList;
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
     * This method trudges out to the database and gets
     * a list of memes attributed to a reference. It does this
     * purely out of a sense of duty, and not because it enjoys
     * playing gopher for a bunch of ungrateful business objects.
     *
     * @param string $id Not just any ID, but a reference id!
     * @return MemeList
     */
    public function getMemeListByReferenceId($id=null)
    {
    	//Display Memes in Order Entered to Preserve Chronology w/in Reference
        $orderby = "date,title";
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($id)
            {
                $id = $id;
                $rs = $db->fetch_all_array
                ("
SELECT
    $this->standardsqlcols
FROM
    $this->standardsqlfrom
    INNER JOIN meme_reference mr ON
    	mr.meme_id = m.id
WHERE
	mr.reference_id = {$id}
ORDER BY
	$orderby;
                ");
            }

            return $this->buildMemeListFromQueryResults($rs,$db);
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
     * This method, unlike the getMemeListByReferneceId,
     * is more than happy to skip out to the database and
     * fetch all the memes attributed to a specific schema.
     *
     * @param int $id A schema id.
     * @return MemeList
     */
    public function getMemeListBySchemaId($id=null)
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
    INNER JOIN schema_meme ms ON
    	ms.meme_id = m.id
WHERE
	ms.schema_id = {$id}
ORDER BY
	$orderby;
                ");

                return $this->buildMemeListFromQueryResults($rs,$db);
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
     * Create! Read! Update! Destroy!
     * CRUD! CRUD! CRUD!
     * This method does it all (except the R)!
     * Give it a delta list, and it figures out
     * what to do.
     *
     * @param DeltaList $deltaList Populated with Memes.
     * @return integer id of the meme.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function saveMemeList(DeltaList $deltaList)
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            foreach ($deltaList as $delta)
            {
                $item = $delta->getObject();
                $flag = $delta->getFlag();

                $id            = $item->getId();
            	$title         = $item->getTitle();
            	$text          = $item->getText();
                $quote         = $item->getQuote();
            	$published     = $item->getPublished();
            	$datePublished = $item->getDatePublished();
            	$date          = $item->getDate();
                $taxonomyList  = $item->getTaxonomyList();

                if ($flag == Delta::INSERT)
                {
                	$data = array();
                	$data['id']             = $id;
                	$data['title']          = $title;
                	$data['text']           = $text;
                	$data['quote']          = $quote;
                	$data['curator_id']     = ApplicationSession::getValue('CURATOR_ID');
                	$data['published']      = $published;
                	$data['date_published'] = $datePublished;
                	$data['date']           = $date;
                	$db->query_insert("meme", $data);
                    $rs = $db->fetch_all_array
                    ("
SELECT
    MAX(id) as id
FROM
    meme
                    ");

                    $id = null;
                    foreach ($rs AS $row)
                    {
                        $id = $row['id'];
                    }

                    //GET TAXONOMY ID LIST
                    $taxonomyDAC = new DataAccessComponentTaxonomy();
                    $taxonomyIdArray = $taxonomyDAC->saveTaxonomies($taxonomyList);
                    //ASSOCIATE TAXONOMIES TO MEME
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        //ASSOCIATE TAXONOMIES TO MEME
                        $rs = $db->query
                        ("
INSERT INTO
    meme_taxonomy
VALUES
    ($taxonomyId,$id)
                        ");
                    }

                    return $id;
                }
                elseif ($flag == Delta::UPDATE)
                {
                	$title = addslashes($title);
                	$text = addslashes($text);
                	$quote= addslashes($quote);
                    $rs = $db->query
                    ("
UPDATE
    meme
SET
    title                 = '$title'
    ,text                 = '$text'
    ,quote                = '$quote'
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
    meme_taxonomy
WHERE
    meme_id = $id
                    ");
                    foreach ($taxonomyIdArray as $taxonomyId)
                    {
                        //ASSOCIATE TAXONOMIES TO MEME
                        $rs = $db->query
                        ("
INSERT INTO
    meme_taxonomy
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
    meme
WHERE
    id = $id
                    ");
                    
                    $rs = $db->query
                    ("
DELETE FROM
    meme_taxonomy
WHERE
    meme_id = $id
                    ");
                    
                    return $id;
                }
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
     * Publishes all memes related to a schema.
     *
     * @param integer $schemaid Schema Id.
     * @return boolean Success or no.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function publishAllSchemaMemes($schemaid)
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();
            $rs = $db->query
            ("
UPDATE
    meme
SET
    published = 1
WHERE
    id IN
    (
    	SELECT
    		meme_id
   		FROM
   			schema_meme
   		WHERE
   			schema_id = $schemaid
    )
            ");
            

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

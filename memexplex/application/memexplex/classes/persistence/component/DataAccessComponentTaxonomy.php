<?php

/**
 * Database CRUD management and Object-Relational Mapping (ORM)
 * for the Business Entity.
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author John Crichton
 */
class DataAccessComponentTaxonomy
{
    /**
     * CUD operations for TaxonomyLists.
     *
     * @param TaxonomyList $taxonomyList An array of Taxonomy Objects.
     * @return array List of Taxonomy Ids.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function saveTaxonomies($taxonomyList)
    {
        if (count($taxonomyList) > 0)
        {
            try
            {
                $db = new MySqlDatabase();
                $db->connect();

                $separator = "";
                $textString = "";
                foreach ($taxonomyList as $item)
                {
                    if (strtolower(trim($item->getText())) != "")
                    {
                    	$textString .= $separator . "'" . addslashes(strtolower(trim($item->getText()))) . "'";
                        $separator = ",";
                    }
                }
                if ($textString != "")
                {
                    //GET IDS FOR EXISTING TAXONOMIES
                    $rs = $db->fetch_all_array
                    ("
SELECT
    id   as id
    ,text as text
FROM
    taxonomy
WHERE
    text IN ($textString)
                ");

                    //BUILD AN ARRAY OF EXISTING TAXONOMY IDS
                    $idArray = array();
                    foreach ($rs AS $row)
                    {
                        $idArray[] = $row['id'];
                    }

                    //INSERT NON-EXISTENT TAXONOMIES
                    $maxId = null;
                    foreach ($taxonomyList as $item)
                    {
                        $taxonomyText = strtolower(trim($item->getText()));
                        if (!ArrayUtilities::in_multi_array($taxonomyText,$rs))
                        {
                            $data = array();
                            $data['id']    = "";
                            $data['text']  = $taxonomyText;
                            $db->query_insert("taxonomy", $data);
                            //GET IDS FOR NON-EXISTENT TAXONOMIES
                            if (!$maxId)
                            {
                                $idrs = $db->fetch_all_array
                                ("
SELECT
    MAX(id) as id
FROM
    taxonomy
                                ");
                                foreach ($idrs AS $row)
                                {
                                    $maxId = $row['id'];
                                }
                            }
                            else
                            {
                                $maxId++;
                            }
                            $idArray[] = $maxId;
                        }
                    }
                    //RETURN TAXONOMY ID LIST
                    return $idArray;
                }
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

}

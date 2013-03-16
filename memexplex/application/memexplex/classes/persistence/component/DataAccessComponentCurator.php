<?php

/**
 * Database CRUD management and Object-Relational Mapping (ORM)
 * for the Business Entity.
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author Bunsen Honeydew
 */

class DataAccessComponentCurator
{

    /**
     * Builds a list of curator objects from sql results.
     * @param Database results $rs
     * @param Database connection object $db
     */
    protected function buildCuratorListFromQueryResults($rs,$db)
    {
        $curator = null;
        
        if (count($rs) > 0)
        {
            foreach ($rs AS $row)
            {
                $curatorLevel = new CuratorLevel
                (
                    $row['curator_level_id']
                    ,$row['curator_level_code']
                    ,$row['curator_level_description']
                );
                $curator = new Curator
                (
                	$row['id']
                    ,$row['email']
                	,stripslashes($row['display_name'])
                    ,stripslashes($row['password'])
                    ,$row['publish_by_default']
                    ,$curatorLevel
                );
            }
        }
        
        return $curator;
    }
    
    /**
     * Fetches a Curator from the database by email address.
     *
     * @param string $email
     * @return Curator
     * @throws PersistenceExceptionProvider
     */
    public function getCurator($email=null)
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            if ($email)
            {
                $sql = "
SELECT
    u.email               AS email
    ,u.id                 AS id
    ,u.password           AS password
    ,u.display_name       AS display_name
    ,u.publish_by_default AS publish_by_default
    ,u.curator_level_id   AS curator_level_id
    ,ul.code              AS curator_level_code
    ,ul.description       AS curator_level_description
FROM
    curator u
    INNER JOIN curator_level ul ON
    u.curator_level_id = ul.id
WHERE
	u.email = '{$email}';
                ";
            }

            $rs = $db->fetch_all_array($sql);

            return $this->buildCuratorListFromQueryResults($rs,$db);
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
     * Fetches a Curator from the database by memexplex object owner id.
     *
     * @param string $email
     * @return Curator
     * @throws PersistenceExceptionProvider
     */
    public function getCuratorByObjectId(
        $memeid    = null
        ,$refid    = null
        ,$schemaid = null
        ,$tripleid = null
    )
    {
        if ($memeid)
        {
            $objectid = $memeid;
            $objectTable = "meme";
        }
        elseif ($refid)
        {
            $objectid = $refid;
            $objectTable = "reference";
        }
        elseif ($schemaid)
        {
            $objectid = $schemaid;
            $objectTable = "schema_map";
        }
        elseif ($tripleid)
        {
            $objectid = $tripleid;
            $objectTable = "triple";
        }
        else
        {
            return false;
        }
        
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $sql = "
SELECT
    u.email               AS email
    ,u.id                 AS id
    ,u.password           AS password
    ,u.display_name       AS display_name
    ,u.publish_by_default AS publish_by_default
    ,u.curator_level_id   AS curator_level_id
    ,ul.code              AS curator_level_code
    ,ul.description       AS curator_level_description
FROM
    curator u
    INNER JOIN curator_level ul ON
    	u.curator_level_id = ul.id
    INNER JOIN $objectTable ot ON
    	u.id = ot.curator_id
WHERE
	ot.id = $objectid;
            ";

            $rs = $db->fetch_all_array($sql);

            return $this->buildCuratorListFromQueryResults($rs,$db);
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
     * Generates random user ids and checks them against
     * the database until one not in use is found and returns it.
     *
     * @return integer
     * @throws PersistenceExceptionProvider
     */
    public static function getNewCuratorId()
    {
        $db = new MySqlDatabase();
        $db->connect();

        try 
        {
            $idNotVerifiedUnique = true;
            while ($idNotVerifiedUnique)
            {
                $id = rand(1, 1000000000);
                $rs = $db->fetch_all_array(
"SELECT * FROM
	curator
WHERE
	id = $id;"
                );
                if (count($rs) == 0)
                {
                    $idNotVerifiedUnique = false;
                }
            }
            
            return $id;
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
     * CRUD operations for Curator objects, minus the "R"... So, really
     * it's "CUD" operations. Chew on that for awhile. ; )
     *
     * @param Delta $delta Curator wrapped in a delta wrapped in a riddle wrapped in an enigma.
     * @return integer The Curator id.
     * @throws {@link PersistenceExceptionProvider}
     */
    public function saveCurator(Delta $delta)
    {
        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $item = $delta->getObject();

            $id               = $item->getId();
        	$displayname      = $item->getDisplayName();
        	$password         = $item->getPassword();
            $email            = $item->getEmail();
            $publishbydefault = $item->getPublishByDefault();
            $curatorLevelId   = $item->getLevel()->getId();
            $flag             = $delta->getFlag();

            if ($flag == Delta::INSERT)
            {
            	$data = array();
            	$data['id']                 = $id;
            	$data['display_name']       = $displayname;
            	$data['email']              = $email;
            	$data['publish_by_default'] = $publishbydefault;
            	$data['password']           = $password;
            	$data['curator_level_id']   = '0';
            	
            	$db->query_insert("curator", $data);

                return $id;
            }
            elseif ($flag == Delta::UPDATE)
            {
                $rs = $db->query
                ("
UPDATE
    curator
SET
    display_name        = '$displayname'
    ,email              = '$email'
    ,password           = '$password'
    ,curator_level_id   = $curatorLevelId
    ,publish_by_default = $publishbydefault
WHERE
    id = $id
                ");

                return $id;
            }
            elseif ($flag == Delta::DELETE)
            {
                $rs = $db->query
                ("
DELETE FROM
    curator
WHERE
    id = $id
                ");
                return $id;
            }

            return true;
        }
        catch (PersistenceExceptionDuplicateEntry $e)
        {
            throw new PersistenceExceptionDuplicateEntry
            (
                $e->getMessage(),
                $e->getCode()
            );
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

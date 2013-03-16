<?php

/**
 * Database CRUD management and Object-Relational Mapping (ORM)
 * for the Business Entity.
 *
 * Specifically, this DAC focuses on the relational database tables, connecting
 * memes, schemas, and references via parent-child relationships.
 *
 * @package MemexPlex
 * @subpackage Persistence
 * @author Ray Arnold
 */

class DataAccessComponentEntityRelationship
{

    /**
     * Creates a Reference-Meme relationship.
     *
     * @param integer $referenceId Reference database id.
     * @param integer $memeId Meme database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function saveMemeReference($referenceId=null,$memeId=null)
    {
        if ($referenceId == null)
        {
            throw new PersistenceExceptionProvider("Reference ID cannot be null!");
        }
        elseif ($memeId == null)
        {
            throw new PersistenceExceptionProvider("Meme ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

        	$data = array();
        	$data['reference_id'] = $referenceId;
        	$data['meme_id']      = $memeId;
        	$db->query_insert("meme_reference", $data);

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    reference
SET
    date = '$date'
WHERE
    id = $referenceId
            ");
            $rs = $db->query
            ("
UPDATE
    meme
SET
    date = '$date'
WHERE
    id = $memeId
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

    /**
     * Deletes a Reference-Meme relationship.
     *
     * @param integer $referenceId Reference database id.
     * @param integer $memeId Meme database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function deleteMemeReference($referenceId=null,$memeId=null)
    {
        if ($referenceId == null)
        {
            throw new PersistenceExceptionProvider("Reference ID cannot be null!");
        }
        elseif ($memeId == null)
        {
            throw new PersistenceExceptionProvider("Meme ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $rs = $db->query
            ("
DELETE FROM
    meme_reference
WHERE
    meme_id = $memeId
    AND reference_id = $referenceId
LIMIT 1;
            ");

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    reference
SET
    date = '$date'
WHERE
    id = $referenceId
            ");
            $rs = $db->query
            ("
UPDATE
    meme
SET
    date = '$date'
WHERE
    id = $memeId
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

    /**
     * Saves a Reference-Reference Parent-Child relationship.
     *
     * @param integer $parentId Parent Reference database id.
     * @param integer $childId Child Reference database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function saveReferenceParentChild($parentId=null,$childId=null)
    {
        if ($parentId == null)
        {
            throw new PersistenceExceptionProvider("Schema Parent ID cannot be null!");
        }
        elseif ($childId == null)
        {
            throw new PersistenceExceptionProvider("Schema Child ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

        	$data = array();
        	$data['parent_id'] = $parentId;
        	$data['child_id']  = $childId;
        	$db->query_insert("reference_parent_child", $data);

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    reference
SET
    date = '$date'
WHERE
    id = $parentId
            ");
            $rs = $db->query
            ("
UPDATE
    reference
SET
    date = '$date'
WHERE
    id = $childId
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

    /**
     * Deletes a Reference-Reference Parent-Child relationship.
     *
     * @param integer $parentId Parent Reference database id.
     * @param integer $childId Child Reference database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function deleteReferenceParentChild($parentId=null,$childId=null)
    {
        if ($parentId == null)
        {
            throw new PersistenceExceptionProvider("Parent Reference ID cannot be null!");
        }
        elseif ($childId == null)
        {
            throw new PersistenceExceptionProvider("Child Reference ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $db->query("
DELETE FROM
    reference_parent_child
WHERE
    parent_id = $parentId
    AND child_id = $childId
LIMIT 1;
            ");

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    reference
SET
    date = '$date'
WHERE
    id = $parentId
            ");
            $rs = $db->query
            ("
UPDATE
    reference
SET
    date = '$date'
WHERE
    id = $childId
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

    /**
     * Creates a Schema-Meme relationship.
     *
     * @param integer $schemaId Parent Schema database id.
     * @param integer $memeId Child Meme database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function saveSchemaMeme($schemaId=null,$memeId=null)
    {
        if ($schemaId == null)
        {
            throw new PersistenceExceptionProvider("Schema ID cannot be null!");
        }
        elseif ($memeId == null)
        {
            throw new PersistenceExceptionProvider("Meme ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

        	$data = array();
        	$data['schema_id'] = $schemaId;
        	$data['meme_id']   = $memeId;
        	$db->query_insert("schema_meme", $data);

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    schema_map
SET
    date = '$date'
WHERE
    id = $schemaId
            ");
            $rs = $db->query
            ("
UPDATE
    meme
SET
    date = '$date'
WHERE
    id = $memeId
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

    /**
     * Destroys a Schema-Meme relationship.
     *
     * @param integer $schemaId Parent Schema database id.
     * @param integer $memeId Child Meme database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function deleteSchemaMeme($schemaId=null,$memeId=null)
    {
        if ($schemaId == null)
        {
            throw new PersistenceExceptionProvider("Schema ID cannot be null!");
        }
        elseif ($memeId == null)
        {
            throw new PersistenceExceptionProvider("Meme ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $db->query("
DELETE FROM
    schema_meme
WHERE
    schema_id = $schemaId
    AND meme_id = $memeId
LIMIT 1;
            ");

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    schema_map
SET
    date = '$date'
WHERE
    id = $schemaId
            ");
            $rs = $db->query
            ("
UPDATE
    meme
SET
    date = '$date'
WHERE
    id = $memeId
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

    /**
     * Creates a Schema-Schema Parent-Child relationship.
     *
     * @param integer $parentId Parent Schema database id.
     * @param integer $childId Child Schema database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function saveSchemaParentChild($parentId=null,$childId=null)
    {
        if ($parentId == null)
        {
            throw new PersistenceExceptionProvider("Schema Parent ID cannot be null!");
        }
        elseif ($childId == null)
        {
            throw new PersistenceExceptionProvider("Schema Child ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

        	$data = array();
        	$data['parent_id'] = $parentId;
        	$data['child_id']  = $childId;
        	$db->query_insert("schema_parent_child", $data);

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    schema_map
SET
    date = '$date'
WHERE
    id = $parentId
            ");
            $rs = $db->query
            ("
UPDATE
    schema_map
SET
    date = '$date'
WHERE
    id = $childId
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

    /**
     * Destroys a Schema-Schema Parent-Child relationship.
     *
     * @param integer $parentId Parent Schema database id.
     * @param integer $childId Child Schema database id.
     * @return boolean Success indicator.
     * @throws PersistenceExceptionProvider
     */
    public function deleteSchemaParentChild($parentId=null,$childId=null)
    {
        if ($parentId == null)
        {
            throw new PersistenceExceptionProvider("Parent Schema ID cannot be null!");
        }
        elseif ($childId == null)
        {
            throw new PersistenceExceptionProvider("Child Schema ID cannot be null!");
        }

        try
        {
            $db = new MySqlDatabase();
            $db->connect();

            $db->query("
DELETE FROM
    schema_parent_child
WHERE
    parent_id = $parentId
    AND child_id = $childId
LIMIT 1;
            ");

        	$date = date("Y-m-d H:i:s",time());
            $rs = $db->query
            ("
UPDATE
    schema_map
SET
    date = '$date'
WHERE
    id = $parentId
            ");
            $rs = $db->query
            ("
UPDATE
    schema_map
SET
    date = '$date'
WHERE
    id = $childId
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

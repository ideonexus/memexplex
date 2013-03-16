<?php
/**
 * "Curator" is our fancy term for "User." I think it sounds so much better.
 * Don't you? A "user" is a word you apply to people doing drugs or consumers
 * you want to dehumanize through commodization.
 * 
 * A "curator," on the other hand, that sounds respectful. It acknowledges the
 * contribution the MemexPlex community member makes to the collective. A
 * curator is someone with taste, selecting the best memes for showcasing;
 * hopefully, for others to appreciate as well.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Master Control Program
 */
class Curator
{

    /**
     * @var integer Id for database key.
     */
    protected $id;

    /**
     * @var string Email address for the account.
     */
    protected $email;

    /**
     * @var string The name displayed to the community.
     */
    protected $displayName;

    /**
     * @var string Password, usually as a sha1 hash.
     */
    protected $password;

    /**
     * @var boolean User preference to disseminate by default.
     */
    protected $publishByEefault;

    /**
     * @var CuratorLevel A rating. 0 for unverified, 6 as normal.
     */
    protected $level;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param string $email
     * @param string $displayName
     * @param string $password
     * @param CuratorLevel $level
     */
    public function __construct
    (
        $id                = null
    	,$email            = null
        ,$displayName      = null
        ,$password         = null
        ,$publishByDefault = null
        ,$level            = null
    )
    {
        $this->setId($id);
    	$this->setEmail($email);
        $this->setDisplayName($displayName);
        $this->setPassword($password);
        $this->setPublishByDefault($publishByDefault);
        $this->setLevel($level);
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }
    
    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPublishByDefault()
    {
        return $this->publishByDefault;
    }

    /**
     * @param string
     */
    public function setPublishByDefault($publishByDefault)
    {
        $this->publishByDefault = $publishByDefault;
    }

    /**
     * @return CuratorLevel
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param CuratorLevel $level
     */
    public function setLevel($level = null)
    {
        if ($level)
        {
            if (!$level instanceof CuratorLevel)
            {
                throw new BusinessExceptionInvalidArgument
                (
                    'Attempt to add non CuratorLevel object to Curator.'
                );
                return false;
            }
            else
            {
                $this->level = $level;
            }
        }
    }
    
    /**
     * This function compares the display names of two
     * Curators and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the CuratorList to sort by name.
     *
     * @param this $a
     * @param this $b
     * @return bool
     */
    public static function compare(
        self $a
        ,self $b
    )
    {
        $compare = strnatcasecmp
        (
            $a->displayName
            ,$b->displayName
        );

        return $compare;
    }

}

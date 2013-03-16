<?php

/**
 * <todo:description>
 *
 * @package Framework
 * @subpackage Business.Entity
 * @author Ryan Somma
 */
class ApplicationAdministrator
{

    /**
     * @var string Admin name.
     */
    protected $name;

    /**
     * @var string Admin email.
     */
    protected $email;

    /**
     * <todo:description>
     *
     * @param string $name
     * @param string $email
     */
    public function __construct(
        $name             = null
        ,$email           = null
    )
    {
        $this->name  = $name;
        $this->email = $email;
    }

    /**
     * @return string Admin name.
     */
    public function setName($name)
    {
        $this->name  = $name;
    }

    /**
     * @return string Admin email.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    /**
     * @return string Admin name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Admin email.
     */
    public function getEmail()
    {
        return $this->email;
    }


}

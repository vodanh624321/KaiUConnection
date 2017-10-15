<?php
namespace Plugin\KaiUConnection\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * KaiU connection
 */
class Config extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;


    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setToken($subData)
    {
        $this->token = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setName($subData)
    {
        $this->name = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setUrl($subData)
    {
        $this->url = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setEmail($subData)
    {
        $this->email = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return $this
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param  \DateTime $updateDate
     * @return $this
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }
}

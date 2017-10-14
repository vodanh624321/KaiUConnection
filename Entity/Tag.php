<?php
namespace Plugin\KaiUConnection\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * KaiU connection
 */
class Tag extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $site_name;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $site_id;

    /**
     * @var string
     */
    private $site_url;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var int
     */
    private $del_flg;

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
    public function setId($subData)
    {
        $this->id = $subData;

        return $this;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setSiteId($subData)
    {
        $this->site_id = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getSiteId()
    {
        return $this->site_id;
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
    public function setSiteName($subData)
    {
        $this->site_name = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->site_name;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setSiteUrl($subData)
    {
        $this->site_url = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->site_url;
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
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setTag($subData)
    {
        $this->tag = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set del_flg.
     *
     * @param int $delFlg
     *
     * @return $this
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg.
     *
     * @return int
     */
    public function getDelFlg()
    {
        return $this->del_flg;
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'site_url',
        )));
    }

}

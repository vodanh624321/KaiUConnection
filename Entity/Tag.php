<?php
namespace Plugin\KaiUConnection\Entity;

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
    private $tag_name;

    /**
     * @var string
     */
    private $tag_value;

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
    public function setTagName($subData)
    {
        $this->tag_name = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->tag_name;
    }

    /**
     * Set sub_data
     *
     * @param  string $subData
     * @return $this
     */
    public function setTagValue($subData)
    {
        $this->tag_value = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string
     */
    public function getTagValue()
    {
        return $this->tag_value;
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

}

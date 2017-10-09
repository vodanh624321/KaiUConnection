<?php
namespace Plugin\KaiUConnection\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ConfigPlugin
{
    /**
     * @var ArrayCollection
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($array)
    {
        $this->tags = $array;

        return $this;
    }
}
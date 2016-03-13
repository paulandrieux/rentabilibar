<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Progress
 *
 * @ORM\Table(name="progress")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProgressRepository")
 */
class Progress
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="seconds", type="integer")
     */
    private $seconds;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_to", type="string", length=255)
     */
    private $rawTo;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_from", type="string", length=255)
     */
    private $rawFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;
    }

    /**
     * @return string
     */
    public function getRawTo()
    {
        return $this->rawTo;
    }

    /**
     * @param string $rawTo
     */
    public function setRawTo($rawTo)
    {
        $this->rawTo = $rawTo;
    }

    /**
     * @return string
     */
    public function getRawFrom()
    {
        return $this->rawFrom;
    }

    /**
     * @param string $rawFrom
     */
    public function setRawFrom($rawFrom)
    {
        $this->rawFrom = $rawFrom;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }


}


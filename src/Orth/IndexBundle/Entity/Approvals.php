<?php

namespace Orth\IndexBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Approvals
 */
class Approvals
{
    /**
     * @var integer
     */
    private $requestedBy;

    /**
     * @var integer
     */
    private $approvedBy;

    /**
     * @var integer
     */
    private $approved;

    /**
     * @var \DateTime
     */
    private $approvalRequestDate;

    /**
     * @var \DateTime
     */
    private $approvalEndDate;

    /**
     * @var string
     */
    private $message;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Orth\IndexBundle\Entity\Users
     */
    private $requestuser;

    /**
     * @var \Orth\IndexBundle\Entity\Users
     */
    private $approveduser;


    /**
     * Set requestedBy
     *
     * @param integer $requestedBy
     * @return Approvals
     */
    public function setRequestedBy($requestedBy)
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    /**
     * Get requestedBy
     *
     * @return integer 
     */
    public function getRequestedBy()
    {
        return $this->requestedBy;
    }

    /**
     * Set approvedBy
     *
     * @param integer $approvedBy
     * @return Approvals
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy
     *
     * @return integer 
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * Set approved
     *
     * @param integer $approved
     * @return Approvals
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return integer 
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set approvalRequestDate
     *
     * @param \DateTime $approvalRequestDate
     * @return Approvals
     */
    public function setApprovalRequestDate($approvalRequestDate)
    {
        $this->approvalRequestDate = $approvalRequestDate;

        return $this;
    }

    /**
     * Get approvalRequestDate
     *
     * @return \DateTime 
     */
    public function getApprovalRequestDate()
    {
        return $this->approvalRequestDate;
    }

    /**
     * Set approvalEndDate
     *
     * @param \DateTime $approvalEndDate
     * @return Approvals
     */
    public function setApprovalEndDate($approvalEndDate)
    {
        $this->approvalEndDate = $approvalEndDate;

        return $this;
    }

    /**
     * Get approvalEndDate
     *
     * @return \DateTime 
     */
    public function getApprovalEndDate()
    {
        return $this->approvalEndDate;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Approvals
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
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
     * Set requestuser
     *
     * @param \Orth\IndexBundle\Entity\Users $requestuser
     * @return Approvals
     */
    public function setRequestuser(\Orth\IndexBundle\Entity\Users $requestuser = null)
    {
        $this->requestuser = $requestuser;

        return $this;
    }

    /**
     * Get requestuser
     *
     * @return \Orth\IndexBundle\Entity\Users 
     */
    public function getRequestuser()
    {
        return $this->requestuser;
    }

    /**
     * Set approveduser
     *
     * @param \Orth\IndexBundle\Entity\Users $approveduser
     * @return Approvals
     */
    public function setApproveduser(\Orth\IndexBundle\Entity\Users $approveduser = null)
    {
        $this->approveduser = $approveduser;

        return $this;
    }

    /**
     * Get approveduser
     *
     * @return \Orth\IndexBundle\Entity\Users 
     */
    public function getApproveduser()
    {
        return $this->approveduser;
    }
}

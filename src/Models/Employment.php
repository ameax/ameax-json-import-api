<?php

namespace Ameax\AmeaxJsonImportApi\Models;

class Employment extends BaseModel
{
    /**
     * Constructor initializes an empty employment object
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Populate the model with data using setters
     *
     * @param array $data
     * @return $this
     *
     */
    protected function populate(array $data): self
    {
        if (isset($data['job_title'])) {
            $this->setJobTitle($data['job_title']);
        }

        if (isset($data['department'])) {
            $this->setDepartment($data['department']);
        }

        // Handle any other fields
        foreach ($data as $key => $value) {
            if (!in_array($key, ['job_title', 'department'])) {
                $this->set($key, $value);
            }
        }

        return $this;
    }



    /**
     * Set the job title
     *
     * @param string|null $jobTitle The job title or null to remove
     * @return $this
     *
     */
    public function setJobTitle(?string $jobTitle): self
    {
        if ($jobTitle === null) {
            return $this->set('job_title', null);
        }

        return $this->set('job_title', $jobTitle);
    }

    /**
     * Set the department
     *
     * @param string|null $department The department or null to remove
     * @return $this
     *
     */
    public function setDepartment(?string $department): self
    {
        if ($department === null) {
            return $this->set('department', null);
        }

        return $this->set('department', $department);
    }

    /**
     * Get the job title
     *
     * @return string|null
     */
    public function getJobTitle(): ?string
    {
        return $this->get('job_title');
    }

    /**
     * Get the department
     *
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->get('department');
    }
}

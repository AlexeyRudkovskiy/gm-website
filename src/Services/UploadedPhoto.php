<?php


namespace App\Services;


class UploadedPhoto
{

    /** @var string */
    protected $filename;

    /** @var string */
    protected $directory;

    /** @var string */
    protected $size;

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    public function toArray()
    {
        return [
            'filename' => $this->getFilename(),
            'directory' => $this->getDirectory(),
            'size' => $this->getSize()
        ];
    }


}

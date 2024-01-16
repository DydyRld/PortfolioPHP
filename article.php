<?php
class Article
{
    private $id;
    private $title;
    private $content;
    private $datePublished;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDatePublished()
    {
        return $this->datePublished;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDatePublished($datePublished)
    {
        $this->datePublished = $datePublished;
    }
}


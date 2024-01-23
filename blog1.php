<?php
require_once 'database.php';

//Classe blog et ses fonctions
class Blog {
    private $database;

    public function __construct() {
        $this->database = new Database('localhost', 'root', '', 'portfoliophp');
    }

    public function addArticle(Article $article) {
        return $this->database->insertArticle($article);
    }

    public function getArticles() {
        return $this->database->getArticlesFromDatabase();
    }

    public function deleteArticle($articleId) {
        return $this->database->deleteArticle($articleId);
    }
}

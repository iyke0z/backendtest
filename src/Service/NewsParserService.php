<?php 

// src/Service/NewsParserService.php

namespace App\Service;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NewsParserService
{
    private $entityManager;
    private $httpClient;
    

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    public function parseNews()
   { {
    $response = $this->httpClient->request('GET', 'https://oxylabs.io/blog');
    $content = $response->getContent();

    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($content);

    // var_dump($dom);
        
        // Extract the necessary information such as title, description, picture, and date added
        $newsElements = $dom->getElementsByTagName('body');
            
        foreach ($newsElements as $newsElement) {

                $titleElement = $newsElement->getElementsByTagName('h2')->item(0);
                $title = $titleElement ? $titleElement->textContent : null;
                $descriptionElement = $newsElement->getElementsByTagName('p')->item(0);
                $description = $descriptionElement ? $descriptionElement->textContent : null;
                $dateAddedElement = $newsElement->getElementsByTagName('time')->item(0);
                $dateAdded = $dateAddedElement ?$dateAddedElement->getAttribute('time') : null;                            
                $pictureElement = $newsElement->getElementsByTagName('img')->item(0);
                $picture = $pictureElement ? $pictureElement->getAttribute('src') : null;
            
            if ($title) {
                // Check if the news article already exists in the database by checking if the title exists
                $existingNews = $this->entityManager->getRepository(News::class)->findOneBy(['title' => $title]);
                if ($existingNews) {
                    // If the news article exists, update the date and time of the last update
                    $existingNews->setLastUpdated(new \DateTime());
                } else {
                    // If the news article does not exist, save the news data to the database using Doctrine ORM
                    $news = new News();
                    $news->setTitle($title);
                    $news->setDescription($description);
                    $news->setPicture($picture);
                    $news->setDateAdded(new \DateTime());
                    $this->entityManager->persist($news);
                }
            }
        }
        
        $this->entityManager->flush();
        
    }}
}

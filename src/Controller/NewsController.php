<?php

// src/Controller/NewsController.php
namespace App\Controller;


use App\Entity\News;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
class NewsController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    

    #[Route('/news', methods: ['GET'],  name: 'news_list')]
    public function list(Request $request)
    {
        $page = $request->query->get('page', 1);
        $pageSize = 10;
        $entityManager = $this->em;
        $news = $entityManager->getRepository(News::class)->findBy([], ['dateAdded' => 'desc'], $pageSize, ($page - 1) * $pageSize);
        $count = $entityManager->createQueryBuilder()
        ->select('COUNT(n)')
        ->from(News::class, 'n')
        ->getQuery()
        ->getSingleScalarResult();

        return $this->render('news/index.html.twig', [
            'news' => $news,
            'page' => $page,
            'pageSize' => $pageSize,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/news/{id}", name="news_show")
     * @IsGranted("view", subject="news")
     */
    
    public function show(News $news)
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/news/delete/{id}", name="news_delete")
     * @IsGranted("delete", subject="news")
     */
    #[Route('/news/delete/{id}', methods:['POST', 'DELETE'], name: 'news_delete')]

    public function delete(News $news)
    {
        $entityManager = $this->em;
        $entityManager->remove($news);
        $entityManager->flush();

        $this->addFlash('success', 'News article deleted.');

        return $this->redirectToRoute('news_list');
    }
}

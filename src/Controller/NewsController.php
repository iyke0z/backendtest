<?php

// src/Controller/NewsController.php
namespace App\Controller;


use App\Entity\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @Route("/news", name="news_list")
     * @IsGranted("view", subject="news")
     */
    public function list(Request $request)
    {
        $page = $request->query->get('page', 1);
        $pageSize = 10;
        $repository = $this->getDoctrine()->getRepository(News::class);
        $news = $repository->findBy([], ['dateAdded' => 'desc'], $pageSize, ($page - 1) * $pageSize);
        $count = $repository->count([]);

        return $this->render('news/list.html.twig', [
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
    public function delete(News $news)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($news);
        $entityManager->flush();

        $this->addFlash('success', 'News article deleted.');

        return $this->redirectToRoute('news_list');
    }
}

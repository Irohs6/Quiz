<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SearchBarController extends AbstractController
{
    #[Route('/search/bar/{srch}', name: 'app_search_bar', methods:['GET'])]
    public function search(Request $request, QuizRepository $quizRepository, SerializerInterface $serializer): JsonResponse
    {
        $srch = $request->attributes->get('srch');
        $result = $quizRepository->findByMultiple($srch);
        
       
        return $this->json($result, 200, [], ['groups' => ['title', 'id', 'category', 'level']]);
    }
}

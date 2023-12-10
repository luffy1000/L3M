<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Repository\DestinationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DestinationController extends AbstractController
{
    /**
     * @Route("/destinations", name="destinations")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $destinations  = $em->getRepository(Destination::class)->findAll();
        
        return $this->render('destinations/index.html.twig', [
            'destinations' => $destinations,
        ]);
    }

   /**
    * @Route("destination/{slug}", name="details")
    */
        public function details(string $slug,DestinationRepository $destinationRepository): Response
    {    
        $destination = $destinationRepository->findOneBy(['slug'=>$slug]);
        
        
        return $this->render('destinations/details.html.twig', compact('destination'));
    }
   
}
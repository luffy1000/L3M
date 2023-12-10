<?php

namespace App\Controller\Admin;

use App\Entity\Destination;
use App\Entity\Images;
use App\Form\DestinationFormType;
use App\Repository\DestinationRepository;
use App\Service\PictureService;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin/destinations", name="admin_destinations_")
 */
class DestinationController extends AbstractController
{


   /**
    * @Route("/", name="index")
    */
    public function index(DestinationRepository $destinationRepository): Response
    {
        $destinations = $destinationRepository->findAll();
        return $this->render('admin/destinations/index.html.twig', compact('destinations'));
    }

    /**
    * @Route("/ajout", name="add")
    */
    public function add(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {


        //On cree une "nouvelle destination"
        $destination = new Destination();


        //On cree le formulaire 
        $destinationForm = $this->createForm(DestinationFormType::class, $destination);

        // on traite la requete du formulaire
        $destinationForm->handleRequest($request);

        //On verifie si le formulaire soumis ET Valide
        if ($destinationForm->isSubmitted() && $destinationForm->isValid()) {

            // on recupere les images

            $images = $destinationForm->get('images')->getData();

            foreach ($images as $image) {
                // on definit le dossier de destination
                $folder = 'destinations';

                // on appelle le service d'ajouts
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $destination->addImage($img);
            }

            $slug = new Slugify();

            // On genere le slug
            $destination->setSlug($slug->slugify($destination->getDestination()));


            $em->persist($destination);
            $em->flush();

            $this->addFlash('success', 'destination ajoute avec succes');

            // On redirige vers le menu

            return $this->redirectToRoute('admin_destinations_index');
        }




        return $this->render('admin/destinations/add.html.twig', [
            'destinationForm' => $destinationForm->createView()
        ]);
    }

    /**
     * @Route("/edition/{id}", name="edit")
     */
    public function edit(Destination $destination, EntityManagerInterface $em, Request $request, PictureService $pictureService): Response
    {


        // On cree le formulaire
        $destinationForm = $this->createForm(DestinationFormType::class, $destination);

        $destinationForm->handleRequest($request);

        //On verifie si le formulaire soumis ET Valide
        if ($destinationForm->isSubmitted() && $destinationForm->isValid()) {

            $images = $destinationForm->get('images')->getData();

            foreach ($images as $image) {
                // on definit le dossier de destination
                $folder = 'destinations';

                // on appelle le service d'ajouts
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $destination->addImage($img);
            }


            $slug = new Slugify();

            // On genere le slug
            $destination->setSlug($slug->slugify($destination->getDestination()));


            $em->persist($destination);
            $em->flush();

            $this->addFlash('success', 'Destination modifier avec succes');

            // On redirige vers le menu principale

            return $this->redirectToRoute('admin_destinations_index');
        }



        return $this->render('admin/destinations/edit.html.twig', [
            'destinationForm' => $destinationForm->createView(),
            'destination' => $destination
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CompanyRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class APIController extends AbstractController
{
    /**
     * @Route("/", name="api")
     */
    public function index()/*: Response*/
    {
/*        return $this->render('api/index.html.twig', [
            'controller_name' => 'APIController',
        ]);*/
    }

    /**
     * @Route("/api/company", name="AllCompany", methods={"GET"})
     */
    public function lstCompany(CompanyRepository $companyRepository)
    {
        $companys = $companyRepository->findAll();

        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($companys, 'json', [
            'circular_reference_handler' => function ($object) {
            return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);

        $response->headers->set('content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/profile", name="AllProfile", methods={"GET"})
     */
    public function lstProfile(ProfileRepository $profileRepository)
    {
        $profiles = $profileRepository->findAll();

        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($profiles, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);

        $response->headers->set('content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/profile/{id}", name="Profile", methods={"GET"})
     */
    public function getProfile(ProfileRepository $profile){
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($profile, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/api/addProfile", name="addProfile", methods={"POST"})
     */
    public function addProfile(Request $request)
    {
       /* if($request->isXmlHttpRequest()) {*/

            $profile = new Profile();


            $donnees = json_decode($request->getContent());
            dump($donnees);

            $profile->setName($donnees->name);
            $profile->setSurname($donnees->surname);
            $profile->setPhone($donnees->phone);
            $profile->setCompany($donnees->company);
            $profile = $this->getDoctrine()->getRepository(Profile::class)->findOneBy(["id" => 1]);
            $profile->setProfiles($profile);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();

            return new Response('ok', 201);
      /*  }
        return new Response('Failed', 404);*/
    }

    /**
     * @Route("/api/editprofile/{id}", name="edit", methods={"PUT"})
     */
    public function editProfile(?Profiles $profile, Request $request)
    {

        if($request->isXmlHttpRequest()) {

            $donnees = json_decode($request->getContent());

            $code = 200;

            if(!$profile){

                $profile = new Profiles();

                $code = 201;
            }

            $profile->setName($donnees->name);
            $profile->setSurname($donnees->Surname);
            $profile->setCompany($donnees->Company);
            $profile->setFeaturedImage($donnees->Phone);
            $profile = $this->getDoctrine()->getRepository(Profile::class)->findOneBy(["id" => 1]);
            $profile->setProfiles($profile);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();

            return new Response('ok', $code);
        }
        return new Response('Failed', 404);
    }

    /**
     * @Route("/api/deleteProfile/{id}", name="delete", methods={"DELETE"})
     */
    public function removeProfile(Profiles $profile)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($profile);
        $entityManager->flush();
        return new Response('ok');
    }
}

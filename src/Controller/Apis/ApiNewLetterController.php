<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\String\toString;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api/newsletter')]
class ApiNewLetterController extends ApiInterface
{
    #[Route('/', name: 'api_newsletter', methods: ['GET'])]
    /**
     * Affiche toutes les newsletters.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Newsletter::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function getAll(NewsletterRepository $newsletterRepository): Response
    {
        try {

            $newsletters = $newsletterRepository->findAll();
            $response = $this->response($newsletters);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_newsletter_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function getOne(?Newsletter $newsletter)
    {
        /*  $newsletter = $newsletterRepository->find($id);*/
        try {
            if ($newsletter) {
                $response = $this->response($newsletter);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($newsletter);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_newsletter_create', methods: ['POST'])]
    /**
     * Permet de créer une newsletter.
     *
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function create(Request $request, NewsletterRepository $newsletterRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
            if (preg_match($regex, $data->email)) {
                $newsletter = $newsletterRepository->findOneBy(array('email' => $data->email));


                if ($newsletter == null) {
                    $newsletter = new Newsletter();
                    $newsletter->setEmail($data->email);
                    $newsletter->setNom($data->nom);




                    // On sauvegarde en base
                    $newsletterRepository->save($newsletter, true);

                    // On retourne la confirmation
                    $response = $this->response($newsletter);
                } else {
                    $this->setMessage("cette ressource existe deja en base");
                    $this->setStatusCode(300);
                    $response = $this->response(null);
                }
            } else {
                $this->setMessage("l'email n'est pas valide");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', name: 'api_newsletter_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une newsletter.
     *
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function update(Request $request, NewsletterRepository $newsletterRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $newsletter = $newsletterRepository->find($id);
            if ($newsletter != null) {


                $newsletter->setNom($data->nom);
                $newsletter->setEmail($data->email);


                // On sauvegarde en base
                $newsletterRepository->save($newsletter, true);

                // On retourne la confirmation
                $response = $this->response($newsletter);
            } else {
                $this->setMessage("cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_newsletter_delete', methods: ['POST'])]
    /**
     * permet de supprimer une newsletter en offrant un identifiant.
     *
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, NewsletterRepository $newsletterRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $newsletter = $newsletterRepository->find($id);
            if ($newsletter != null) {

                $newsletterRepository->remove($newsletter, true);

                // On retourne la confirmation
                $response = $this->response($newsletter);
            } else {
                $this->setMessage("cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_newsletter_active', methods: ['GET'])]
    /**
     * Permet d'activer une newsletter en offrant un identifiant.
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function active(?Newsletter $newsletter, NewsletterRepository $newsletterRepository)
    {
        /*  $newsletter = $newsletterRepository->find($id);*/
        try {
            if ($newsletter) {

                //$newsletter->setCode("555"); //TO DO nous ajouter un champs active
                $newsletterRepository->save($newsletter, true);
                $response = $this->response($newsletter);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/active/multiple', name: 'api_newsletter_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Newsletter")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, NewsletterRepository $newsletterRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeNewsletters = $newsletterRepository->findAllByListId($data->ids);
            foreach ($listeNewsletters as $listeNewsletter) {
                //$listeNewsletter->setCode("555");  //TO DO nous ajouter un champs active
                $newsletterRepository->add($listeNewsletter, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}

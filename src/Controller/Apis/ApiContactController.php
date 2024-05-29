<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Contact;
use App\Repository\ContactRepository;
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

#[Route('/api/contact')]
class ApiContactController extends ApiInterface
{
    #[Route('/', name: 'api_contact', methods: ['GET'])]
    /**
     * Affiche toutes les contacts.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Contact::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function getAll(ContactRepository $contactRepository): Response
    {
        try {

            $contacts = $contactRepository->findAll();
            $response = $this->response($contacts);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_contact_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function getOne(?Contact $contact)
    {
        /*  $contact = $contactRepository->find($id);*/
        try {
            if ($contact) {
                $response = $this->response($contact);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($contact);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_contact_create', methods: ['POST'])]
    /**
     * Permet de créer une contact.
     *
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function create(Request $request, ContactRepository $contactRepository)
    {
        try {
            $data = json_decode($request->getContent());

            /*   $contact = $contactRepository->findOneBy(array('code' => $data->code));
            if ($contact == null) { */
            $contact = new Contact();
            $contact->setNom($data->nom);
            $contact->setPrenoms($data->prenoms);
            $contact->setSujet($data->sujet);
            $contact->setCorps($data->corps);

            // On sauvegarde en base
            $contactRepository->save($contact, true);

            // On retourne la confirmation
            $response = $this->response($contact);
            /*  } else {
                $this->setMessage("Cette ressource existe deja en base");
                $this->setStatusCode(300);
                $response = $this->response(null);
            } */
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', name: 'api_contact_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une contact.
     *
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function update(Request $request, ContactRepository $contactRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $contact = $contactRepository->find($id);
            if ($contact != null) {


                $contact->setNom($data->nom);
                $contact->setPrenoms($data->prenoms);
                $contact->setSujet($data->sujet);
                $contact->setCorps($data->corps);


                // On sauvegarde en base
                $contactRepository->save($contact, true);

                // On retourne la confirmation
                $response = $this->response($contact);
            } else {
                $this->setMessage("Cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_contact_delete', methods: ['POST'])]
    /**
     * permet de supprimer une contact en offrant un identifiant.
     *
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, ContactRepository $contactRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $contact = $contactRepository->find($id);
            if ($contact != null) {

                $contactRepository->remove($contact, true);

                // On retourne la confirmation
                $response = $this->response($contact);
            } else {
                $this->setMessage("Cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_contact_active', methods: ['GET'])]
    /**
     * Permet d'activer une contact en offrant un identifiant.
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function active(?Contact $contact, ContactRepository $contactRepository)
    {
        /*  $contact = $contactRepository->find($id);*/
        try {
            if ($contact) {

                //$contact->setCode("555"); //TO DO nous ajouter un champs active
                $contactRepository->save($contact, true);
                $response = $this->response($contact);
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


    #[Route('/active/multiple', name: 'api_contact_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Contact")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, ContactRepository $contactRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeContacts = $contactRepository->findAllByListId($data->ids);
            foreach ($listeContacts as $listeContact) {
                //$listeContact->setCode("555");  //TO DO nous ajouter un champs active
                $contactRepository->add($listeContact, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
